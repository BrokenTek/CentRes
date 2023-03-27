<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->

<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<?php
    $sql = "SELECT * FROM Employees WHERE (roleLevel & 8) <> 0";
    $result = connection()->query($sql);
    if (mysqli_num_rows($result) == 0) {
        header("Location: ../CreateAdminView/CreateAdminView.php");
    }

    $cookie_name = "804288a34eb7a49b349be68fc6437621cbf25e10d82f4268bb795eca277adedb6a3367add5bfb7cbffb50df150e2e78d26b276f37d32d96cd76746065df58a30cde25c4d9803aa7214dc8f6a985bf8643c341f229b5834964b0f371915d5677e4b579fbab42844cd63ddc3148e4250591277cfc521906bc30cfedd765974c2009ae5fe451ab1890e5ebbfa120ad18934c972618dbe3e";
    
    if (isset($_POST['logoutUsername'])) {

		$db = connection();
		$sql = $db->prepare("CALL logout(?)");
		$sql->bind_param("s", $_POST['logoutUsername']);
		$sql->execute();
		

		$_COOKIE[$cookie_name] = NULL;		
	}

    // Barring hacking attempts, you are here after selecting which role to login as
    if (isset($_POST['route'])) {
        // before officially logging in, check that $_POST['validatedPassword'] matches what is stored in db.
        // Otherwise hackers could supply a bogus value for validated password, and if not checked, allows
        // anybody to bypass the password verification step.
        $db = connection();
        $sql = $db->prepare("SELECT userPasswordHash(?) AS userPasswordHash;");
        $sql->bind_param("s", $_POST['username']);
        $sql->execute();
        
        $passResult = $sql->get_result()->fetch_assoc()['userPasswordHash'];

        if ($passResult ==  $_POST['validatedPassword']) {
            // check if somebody is already logged in on this machine, if so, log them out.
			// Only 1 person is allowed to be logged in at a time on a device.
			$sql = "SELECT * FROM Employees WHERE accessToken = '" .$_COOKIE[$cookie_name]. "'
                    AND accessTokenExpiration > NOW()";
            $existingLocalSession = connection()->query($sql);
            if (mysqli_num_rows($existingLocalSession) == 1) {
                $username = $existingLocalSession->fetch_assoc()['userName'];
                $sql = "CALL logout('$username');";
                connection()->query($sql);
            }

            // generate session token
            $sessionToken = password_hash($_POST['username'] . time(), PASSWORD_BCRYPT);
                    
            // login to database and set session token, otherwise sessionToken is already in use or incorrect role selected.
            $db = connection();
            $sql = $db->prepare("CALL login(?, ?, ?);");
            $sql->bind_param("sis", $_POST['username'], $_POST['role'], $sessionToken);
            $sql->execute();
            
            // LOGIN SUCCESSFUL.....

            if (isset($_POST['nph'])) {
                // change the password hash to the new value.
                $db = connection();
                $sql = $db->prepare("UPDATE Employees SET passwordBCrypt = ? WHERE userName = ?;");
                $sql->bind_param("ss", $_POST['nph'], $_POST['username']);
                $sql->execute();
            }
            
            $sql = "SELECT sessionTimeoutInMins FROM Config;";
            $timeoutLength = connection()->query($sql)->fetch_assoc()['sessionTimeoutInMins'];
            
            setcookie($cookie_name, $sessionToken, time() + ($timeoutLength * 60), "/"); // value is in seconds... 86,400 per day, 60 per minute, 3600 per hour

            header("Location: " .$_POST['route']);
        }
        else {
            $errorMessage = "Invalid Password Insertion Detected!";
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <!-- gives you access to setVar, getVar, removeVar, 
        clearVars, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        <script>
            function allElementsLoaded() {
                <?php
                    if(isset($_POST['message'])) {
                        echo("setTimeout(alert('" .$_POST['message']. "'),2000);");
                    } 
                ?>
            }

            //Place your JavaScript Code here

            function autoLogin(setIndex = null) {
                let cboLoginRole = document.querySelector('#cboLoginRole');
                if (setIndex !== null) {
                    cboLoginRole.selectedIndex = setIndex;
                }
                
                setVar('route', cboLoginRole.options[cboLoginRole.selectedIndex].getAttribute('route'));
                document.getElementById('loginForm').submit();
            }

            function clearFields() {
                location.replace("LoginView.php");
            }

            

        </script>
        <style>
            #loginForm {
                display: grid;
                grid-template-columns: min-content min-content;
                grid-auto-rows: min-content;
                grid-gap: .25rem;
            }
            #errorMessage {
                font-size: 1.5rem;
                font-weight: bold;
                grid-column: 1 / span 2;
                margin-inline: auto;
                padding: .25rem;
                border-radius: .25rem;
            }
            #message {
                font-size: 1.5rem;
                font-weight: bold;
                grid-column: 1 / span 2;
                margin-inline: auto;
                padding: .25rem;
                border-radius: .25rem;
            }
            #cboLoginRole {
                width: 100%;
                height: 100%;
                margin: 0;
            }
        </style>
    </head>
    <body id="loginBody" onload="allElementsLoaded()">

        <div id="loginContainer">
            <div id="loginHeader">
                <img src="../Resources/Images/centresLogo.png" id="lgoSession" width=50 height=50>
                <div id="loginTitle">CentRes&nbsp;Employee&nbsp;Login</div>
            </div>
            <div>
                <!-- this form submits to itself -->
                <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <!-- PLACE YOUR PHP LAYOUT LOGIC CODE HERE -->
                    <?php if (isset($_POST['username']) && (isset($_POST['password']) || isset($_POST['validatedPassword']))): ?>    
                        <?php 
                            //removes characters that mess with the echo command in sessionHeader.php.
                            $_POST['username'] = str_replace(array('"', '\\', '&', ';', '{', '}', '(', ')', '[', ']', '<', '>'), '', $_POST['username']);

                            // Validate credentials
                            try {
                                // confirm valid username & get password hash, otherwise invalid username.
                                $db = connection();
                                $sql = $db->prepare("SELECT userPasswordHash(?) AS userPasswordHash;");
                                $sql->bind_param("s", $_POST['username']);
                                $sql->execute();
                                
                                $passResult = $sql->get_result()->fetch_assoc()['userPasswordHash'];

                                $isPassphrase = substr($passResult,0,1) != '$';
                                if ($isPassphrase) {
                                    $passResult = strrev($passResult);
                                }

                                // confirm entered password matches stored password, otherwise invalid password entered.
                                if (!(isset($_POST['validatedPassword']) || password_verify($_POST['password'], $passResult))) {
                                    $errorMessage = "Invalid Password Entered";
                                }
                                elseif ($isPassphrase) {
                                    // on the client side, set the password hash and reset a new password hash.
                                    $_POST['validatedPassword'] = $passResult;
                                    echo("<script>
                                            setVar('validatedPassword', '$passResult');
                                        </script>");
                                }
                                else {
                                    // on the client side, set the password hash and reset a new password hash.
                                    $_POST['validatedPassword'] = $passResult;
                                    if (isset($_POST['password'])) {
                                        $newPasswordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
                                        echo("<script>
                                            setVar('validatedPassword', '$passResult');
                                            setVar('nph', '$newPasswordHash');
                                        </script>");
                                    }
                                    else {
                                        echo("<script>
                                                setVar('validatedPassword', '$passResult');
                                            </script>");
                                        }
                                }
                            }
                            catch (Exception $e) {
                                $errorMessage = $e->getMessage();
                            }
                        ?>
                        <?php if (isset($errorMessage)): ?>
                            <label id='lblUsername' for='txtUsername'>Username</label>
                            <input id='txtUsername' name='username' type='text' value='<?php echo $_POST['username']; ?>' required>
                            <label id='lblPassword' for='password'>Password</label>
                            <input id='pwdPassword' name='password' type=password value='<?php echo $_POST['password']; ?>' required>
                            <input id='btnClear' type='submit' class='button' value='Clear' onpointerdown='clearFields()'>
                            <input id='btnLogin' type='submit' class='button' value='Login'>
                        <?php else: ?>
                            <label id='lblUsername' for='txtUsername'>Username</label>
                            <input id='txtUsername' name='username' type='text' value='<?php echo $_POST['username']; ?>' readonly required>
                            <label id='lblPassword' for='password'>Password</label>
                            <input id='pwdPassword' name='password' type=password placeholder='Validated' readonly>

                            <?php if($isPassphrase): ?>
                                <?php if(isset($_POST['newPassword']) && isset($_POST['newPasswordConfirm'])): ?>

                                    <?php
                                        if ($_POST['newPassword'] != $_POST['newPasswordConfirm']) {
                                            $errorMessage = "Confirmation Password Does Not Match";
                                        }
                                        else {
                                            // before password change, check that $_POST['validatedPassword'] matches what is stored in db.
                                            // Otherwise hackers could supply a bogus value for validated password, and if not checked, allows
                                            // anybody to arbitrarily change any password.
                                            $db = connection();
                                            $sql = $db->prepare("SELECT userPasswordHash(?) AS userPasswordHash;");
                                            $sql->bind_param("s", $_POST['username']);
                                            $sql->execute();
                                            
                                            $passResult = $sql->get_result()->fetch_assoc()['userPasswordHash'];

                                            if (strrev($passResult) ==  $_POST['validatedPassword']) {
                                                // change the password hash to the new value.
                                                $newPasswordHash = password_hash($_POST['newPassword'], PASSWORD_BCRYPT);
                                                $db = connection();
                                                $sql = $db->prepare("UPDATE Employees SET passwordBCrypt = ? WHERE userName = ?;");
                                                $sql->bind_param("ss", $newPasswordHash, $_POST['username']);
                                                $sql->execute();
                                                $message = "Password&nbsp;Updated";
                                                echo("<script>setVar('validatedPassword','$newPasswordHash');</script>");
                                            }
                                            else {
                                                $errorMessage = "Invalid Password Insertion Detected!"; 
                                            }
                                        }
                                    ?>
                                    <?php if (isset($errorMessage)): ?>
                                        <label id='lblNewPassword' for='newPassword'>New&nbsp;Password</label>
                                        <input id='pwdNewPassword' name='newPassword' type=password required>
                                        <label id='lblNewPasswordConfirm' for='newPasswordConfirm'>Confirm&nbsp;New&nbsp;Password</label>
                                        <input id='pwdNewPasswordConfirm' name='newPasswordConfirm' type=password required>
                                        <input id='btnClear' type='submit' class='button' value='Clear' onpointerdown='clearFields()'>
                                        <input id='btnSetPassword' type='submit' class='button' value='Set Password'>
                                    <?php else: ?>
                                        <label id='lblRole' for='cboLoginRole'>Role</label>
                                        <select id='cboLoginRole' name='role' onchange='autoLogin()'>
                                            <option>Select Your Role</option>
                                            <?php
                                                //get user roles and populate the window
                                                $db = connection();
                                                $sql = $db->prepare("SELECT roleLevel from Employees WHERE username = ?;");
                                                $sql->bind_param("s", $_POST['username']);
                                                $sql->execute();
                                                $result = $sql->get_result();
                                                $allowedRoles = $result->fetch_assoc()['roleLevel'];

                                                $sql = "SELECT * FROM LoginRouteTable;";
                                                $definedRoles = connection()->query($sql);
                                
                                                $allowedRoleCount = 0;
                                                $allowedRoute = "";
                                                while($row = $definedRoles->fetch_assoc()) {
                                                    if((intval($row['id']) & intval($allowedRoles)) == intval($row['id'])) {
                                                        echo ('<option route="' .$row['route']. '" value=' .$row['id']. '>' .$row['title']. '</option>');
                                                        $allowedRoleCount += 1;
                                                        $allowedRoute = $row['route'];
                                                    }
                                                }
                                            ?>
                                        </select>
                                        <input id='btnClear' type='submit' class='button' value='Clear' onpointerdown='clearFields()'>
                                        <?php
                                            if ($allowedRoleCount == 1) {
                                                echo("<script>setTimeout(function() { autoLogin(1); }, 1500);</script>");
                                            }
                                        ?>
                                    <?php endif; ?> 
                                <?php else: ?>
                                    <label id='lblNewPassword' for='newPassword'>New&nbsp;Password</label>
                                    <input id='pwdNewPassword' name='newPassword' type=password required>
                                    <label id='lblNewPasswordConfirm' for='newPasswordConfirm'>Confirm&nbsp;New&nbsp;Password</label>
                                    <input id='pwdNewPasswordConfirm' name='newPasswordConfirm' type=password required>
                                    <input id='btnClear' type='submit' class='button' value='Clear' onpointerdown='clearFields()'>
                                    <input id='btnSetPassword' type='submit' class='button' value='Set Password'>
                                <?php endif; ?>
                            <?php else: ?>
                                <label id='lblRole' for='cboLoginRole'>Role</label>
                                <select id='cboLoginRole' name='role' onchange='autoLogin()'>
                                    <option>Select Your Role</option>
                                    <?php
                                        //get user roles and populate the window
                                        $db = connection();
                                        $sql = $db->prepare("SELECT roleLevel from Employees WHERE username = ?;");
                                        $sql->bind_param("s", $_POST['username']);
                                        $sql->execute();
                                        $result = $sql->get_result();
                                        $allowedRoles = $result->fetch_assoc()['roleLevel'];

                                        $sql = "SELECT * FROM LoginRouteTable;";
                                        $definedRoles = connection()->query($sql);
                        
                                        $allowedRoleCount = 0;
                                        $allowedRoute = "";
                                        while($row = $definedRoles->fetch_assoc()) {
                                            if((intval($row['id']) & intval($allowedRoles)) == intval($row['id'])) {
                                                echo ('<option route="' .$row['route']. '" value=' .$row['id']. '>' .$row['title']. '</option>');
                                                $allowedRoleCount += 1;
                                                $allowedRoute = $row['route'];
                                            }
                                        }
                                    ?>
                                </select>
                                <input id='btnClear' type='submit' class='button' value='Clear' onpointerdown='clearFields()'>
                                <?php
                                    if ($allowedRoleCount == 1 && !$isPassphrase) {
                                        echo("<script>autoLogin(1);</script>");
                                    }
                                ?>
                            <?php endif; ?>
                        <?php endif; ?>    
                    <?php else: ?>
                        <label id='lblUsername' for='txtUsername'>Username</label>
                        <input id='txtUsername' name='username' type='text' required>
                        <label id='lblPassword' for='password'>Password</label>
                        <input id='pwdPassword' name='password' type=password required>
                        <input id='btnClear' type='submit' class='button' value='Clear' onpointerdown='clearFields()'>
                        <input id='btnLogin' type='submit' class='button' value='Login'>
                    <?php endif; ?>
                    <?php 
                        if (isset($errorMessage)) {
                            echo("<div id='errorMessage' class='highlighted'>$errorMessage</div>");
                        } elseif (isset($message)) {
                            echo("<div id='message'>$message</div>");
                        }
                    ?>
                </form>
            </div>
        </div>
    </body>
</html>
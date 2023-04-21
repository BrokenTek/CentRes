

<?php require_once '../Resources/PHP/dbConnection.php'; ?>
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
    if (isset($_POST['route']) && isset($_POST['username'])) {
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
            
            if (isset($_POST['prepRoute'])) {
                // terminal login. login with transient credentials that are destroyed after routing
                // Transient login to database and set session token, otherwise sessionToken is already in use or incorrect role selected.
                $_POST['transientName'] = $_POST['username'] .'~'. $_POST['prepRoute'];
                $sql = "CALL transientLogin(
                    '".$_POST['transientName']."',
                    ".$_POST['role'].",
                    '".$sessionToken."');";
                $result = connection()->query($sql);
            }
            else {
                // login to database and set session token, otherwise sessionToken is already in use or incorrect role selected.
                $db = connection();
                $sql = $db->prepare("CALL login(?, ?, ?);");
                $sql->bind_param("sis", $_POST['username'], $_POST['role'], $sessionToken);
                $sql->execute();
            }
            
            // LOGIN SUCCESSFUL.....
            
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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script> 
        <script>
            function allElementsLoaded() {
                <?php if (isset($_POST['message'])): ?>
                    setTimeout(alert('<?php echo $_POST['message']; ?>'),2000);
                <?php endif; ?>
                <?php if (isset($_POST['prepRoute'])): ?>
                    location.replace('<?php echo $_POST['route']; ?>');
                    <?php $_POST['route'] = $_POST['prepRoute']; ?>
                <?php endif; ?>
            }

            //Place your JavaScript Code here

            function autoLogin(setIndex = null) {
                let cboLoginRole = document.querySelector('#cboLoginRole');
                if (setIndex !== null) {
                    cboLoginRole.selectedIndex = setIndex;
                }

                if (cboLoginRole.options[cboLoginRole.selectedIndex].getAttribute('value') == 1) {
                    with (document.getElementById('cboRoute')) {
                        document.getElementById('lblRoute').removeAttribute('style');
                        removeAttribute('style');
                        if (length == 1) {
                            autoRoute();
                        }
                    }
                }
                else {
                    varSet('route', cboLoginRole.options[cboLoginRole.selectedIndex].getAttribute('route'));
                    document.getElementById('loginForm').submit();
                }
            }

            function autoRoute() {
                with (document.getElementById('loginForm')) {
                    document.getElementById("cboRoute").setAttribute("name", "prepRoute");
                    varSet('route', cboLoginRole.options[cboLoginRole.selectedIndex].getAttribute('route'));
                    document.getElementById('loginForm').submit();
                }
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
            #cboLoginRole, #cboRoute {
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
                
                <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    
                    <?php if (isset($_POST['username']) && (isset($_POST['password']) || isset($_POST['validatedPassword']))): ?>    
                        <?php 
                            //removes characters that mess with the echo command in sessionHeader.php.
                            $_POST['username'] = str_replace(array('~', '"', '\\', '&', ';', '{', '}', '(', ')', '[', ']', '<', '>'), '', $_POST['username']);

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
                                            varSet('validatedPassword', '$passResult');
                                        </script>");
                                }
                                else {
                                    // on the client side, set the password hash and reset a new password hash.
                                    $_POST['validatedPassword'] = $passResult;
                                    if (isset($_POST['password'])) {
                                        $newPasswordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);
                                        echo("<script>
                                            varSet('validatedPassword', '$passResult');
                                        </script>");
                                    }
                                    else {
                                        echo("<script>
                                                varSet('validatedPassword', '$passResult');
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
                            <button id='btnClear' class='button' onpointerdown='clearFields()'>Clear</button>
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
                                                echo("<script>varSet('validatedPassword','$newPasswordHash');</script>");
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
                                                        if (isset($_POST['role']) && $_POST['role'] == $row['id']) {
                                                            echo ('<option route="' .$row['route']. '" value="' .$row['id']. '" selected>' .$row['title']. '</option>');
                                                        }
                                                        else {
                                                            echo ('<option route="' .$row['route']. '" value=' .$row['id']. '>' .$row['title']. '</option>');
                                                        }
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
                                                if (isset($_POST['role']) && $_POST['role'] == $row['id']) {
                                                    echo ('<option route="' .$row['route']. '" value=' .$row['id']. '>' .$row['title']. ' selected</option>');
                                                }
                                                else {
                                                    echo ('<option route="' .$row['route']. '" value=' .$row['id']. '>' .$row['title']. '</option>');
                                                }
                                                $allowedRoleCount += 1;
                                                $allowedRoute = $row['route'];
                                            }
                                        }                                        
                                    ?>
                                </select>
                                <label id='lblRoute' for='cboRoute' style='display: none;'>Route</label>
                                <select id='cboRoute' name='prepRoute2' onchange='autoRoute()' style='display: none;'>
                                    <option>Select a Route</option>
                                    <?php
                                        //get routes
                                        $sql = "SELECT route FROM MenuItems WHERE route IS NOT NULL GROUP BY route;";
                                        $result = connection()->query($sql);

                                        while($row = $result->fetch_assoc()) {
                                            echo ('<option value=' .$row['route']. '>' .$row['route']. '</option>');
                                        }    
                                    ?>
                                </select>
                                <input id='btnClear' type='submit' class='button' value='Clear' onpointerdown='clearFields()'>
                                <?php
                                    if (isset($_POST['prepRoute'])) {
                                        //echo("<script>autoRoute();</script>");
                                    }
                                    else if ($allowedRoleCount == 1 && !$isPassphrase) {
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
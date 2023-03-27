<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->

<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<?php
    $sql = "SELECT * FROM Employees WHERE (roleLevel & 8) <> 0";
    $result = connection()->query($sql);
    if (mysqli_num_rows($result) > 0) {
        header("Location: ../LoginView/LoginView.php");
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
                
            }

            //Place your JavaScript Code here

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
            <div id="loginTitle">CentRes&nbsp;Employee&nbsp;Portal</div>
        </div>
        <div>
            <!-- this form submits to itself -->
            <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <!-- PLACE YOUR PHP LAYOUT LOGIC CODE HERE -->
                <?php $processCreateUser = true; ?>
                <?php if (isset($_POST['firstName']) && isset($_POST['lastName'])): ?>
                    <label id='lblFirstName' for='txtFirstName'>First&nbsp;Name</label>
                    <input id='txtFirstName' name='firstName' type='text' value='<?php echo $_POST['firstName']; ?>' required>
                    <label id='lblLastName' for='txtLastName'>Last&nbsp;Name</label>
                    <input id='txtLastName' name='lastName' type='text' value='<?php echo $_POST['lastName']; ?>' required>   
                <?php else: ?>
                    <?php $processCreateUser = false; ?>
                    <label id='lblFirstName' for='txtFirstName'>First&nbsp;Name</label>
                    <input id='txtFirstName' name='firstName' type='text' required>
                    <label id='lblLastName' for='txtLastName'>Last&nbsp;Name</label>
                    <input id='txtLastName' name='lastName' type='text' required>
                <?php endif; ?>
                <?php if(isset($_POST['username'])): ?>
                    <?php
                        $_POST['username'] = str_replace(array('"', '\\', '&', ';', '{', '}', '(', ')', '[', ']', '<', '>'), '', $_POST['username']);
                        $db = connection();
                        $sql = $db->prepare("SELECT * from Employees WHERE userName = ?;");
                        $sql->bind_param("s", $_POST['username']);
                        $sql->execute();
                        $result = $sql->get_result();
                        if (mysqli_num_rows($result) > 0) {
                            $errorMessage = "Username already in use.";
                        }
                    ?>
                    <label id='lblUsername' for='txtUsername'>Username</label>
                    <input id='txtUsername' name='username' type='text' value='<?php echo $_POST['username']; ?>' required>
                <?php else: ?>
                    <?php $processCreateUser = false; ?>
                    <label id='lblUsername' for='txtUsername'>Username</label>
                    <input id='txtUsername' name='username' type='text' required>
                <?php endif; ?>
                <?php 
                    if(isset($_POST['newPassword']) && isset($_POST['newPasswordConfirm'])) {
                        if ($_POST['newPassword'] != $_POST['newPasswordConfirm']) {
                            $errorMessage = "Confirmation Password Does Not Match";
                        }
                    } 
                ?>
                <label id='lblNewPassword' for='newPassword'>Password</label>
                <input id='pwdNewPassword' name='newPassword' type=password required>
                <label id='lblNewPasswordConfirm' for='newPasswordConfirm'>Confirm&nbsp;Password</label>
                <input id='pwdNewPasswordConfirm' name='newPasswordConfirm' type=password required>
                <input id='btnClear' type='submit' class='button' value='Clear' onpointerdown='clearFields()'>
                <input id='btnCreate' type='submit' class='button' value='Create'> 
                <?php
                    if (isset($errorMessage)) { $processCreateUser = false; }

                    if ($processCreateUser) {
                        try {
                            $hash = password_hash($_POST['newPassword'], PASSWORD_BCRYPT);
                            $allowedRoles = 16777215;
                            $sql = "INSERT INTO Employees (userName, lastName, firstName, passwordBCrypt, roleLevel) VALUES (".
                                "'" .$_POST['username']. "', ".
                                "'" .$_POST['lastName']. "', ".
                                "'" .$_POST['firstName']. "', ".
                                "'" .$hash. "', ".
                                $allowedRoles.");";
                            connection()->query($sql);
                            $message = "1st Manager Profile Created";

                            echo("<script>
                                    setTimeout(function() { 
                                        with (document.getElementById('pwdNewPassword')) {
                                            setAttribute('name', 'validatedPassword');
                                            setAttribute('value', '$hash');
                                        }
                                        with (document.getElementsByTagName('form')[0]) {
                                            setAttribute('action', '../LoginView/LoginView.php');
                                            submit();
                                        }
                                    }, 3000);
                                  </script>"
                                );
                        }
                        catch (Exception $e) {
                            $errorMessage = $e->getMessage();
                        }
                    }


                    if (isset($errorMessage)) {
                        echo("<div id='errorMessage' class='highlighted'>$errorMessage</div>");
                    } elseif (isset($message)) {
                        echo("<div id='message'>$message</div>");
                    }
                ?>  
                
                
            </form>
        </div>
    </body>
</html>
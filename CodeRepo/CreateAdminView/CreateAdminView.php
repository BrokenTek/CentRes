

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
        
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        <script>
            function allElementsLoaded() {
                
            }

            //Place your JavaScript Code here

            function clearFields() {
                location.replace("CreateAdminView.php");
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
                <div id="loginTitle">CentRes&nbsp;Admin&nbsp;Setup</div>
            </div>
            <div>
                
                <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    
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
                    <?php
                        if (isset($errorMessage)) { $processCreateUser = false; }

                        if ($processCreateUser) {
                            try {
                                $hash = password_hash($_POST['newPassword'], PASSWORD_BCRYPT);
                                $allowedRoles = 65535;
                                $sql = "INSERT INTO Employees (userName, lastName, firstName, passwordBCrypt, roleLevel) VALUES (".
                                    "'" .$_POST['username']. "', ".
                                    "'" .$_POST['lastName']. "', ".
                                    "'" .$_POST['firstName']. "', ".
                                    "'" .$hash. "', ".
                                    $allowedRoles.");";
                                connection()->query($sql);
                                $message = "Admin&nbsp;Profile&nbsp;Created";

                                echo("<script>
                                        document.getElementById('txtFirstName').setAttribute('readonly','');
                                        document.getElementById('txtLastName').setAttribute('readonly','');
                                        document.getElementById('txtUsername').setAttribute('readonly','');
                                        setTimeout(function() { 
                                            
                                            with (document.getElementsByTagName('form')[0]) {
                                                setAttribute('action', '../LoginView/LoginView.php');
                                                submit();
                                            }
                                        }, 1000);
                                    </script>"
                                    );
                            }
                            catch (Exception $e) {
                                $errorMessage = $e->getMessage();
                            }
                        }
                    ?> 
                    <?php if(!isset($errorMessage) && $processCreateUser): ?>
                        <label id='lblNewPassword' for='pwdNewPassword'>Password</label>
                        <input id='pwdNewPassword' name='newPwd' type=password readonly required placeholder='Validated'>
                        <input type='hidden' name='validatedPassword' value='<?php echo $hash; ?>'>
                    <?php else: ?>
                        <label id='lblNewPassword' for='pwdNewPassword'>Password</label>
                        <input id='pwdNewPassword' name='newPassword' type=password required>
                        <label id='lblNewPasswordConfirm' for='pwdNewPasswordConfirm'>Confirm&nbsp;Password</label>
                        <input id='pwdNewPasswordConfirm' name='newPasswordConfirm' type=password required>
                        <button id='btnClear' class='button' onpointerdown='clearFields()'>Clear</button>
                        <input id='btnCreate' type='submit' class='button' value='Create'> 
                    <?php endif; ?>
                    <?php if(isset($errorMessage)): ?>
                        <div id='errorMessage' class='highlighted'><?php echo $errorMessage; ?></div>
                    <?php elseif(isset($message)): ?>
                        <div id='message'><?php echo $message; ?></div>
                    <?php endif; ?>                
                    
                </form>
            </div>
        </div>
    </body>
</html>
<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->


<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <base href="http://localhost/CentRes/CodeRepo/">
        <!-- gives you access to setVar, getVar, removeVar, 
        clearVars, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
        <!-- demonstration on how to use getVar, setVar, updateDisplay for just this page -->
        <!-- remove this script tag -->
        <script>
            function allElementsLoaded() {
                
            }

            //Place your JavaScript Code here
        </script>
        <style>
            form {
                display: grid;
                grid-template-columns: 1fr;
                grid-auto-rows: min-content;
                grid-template-areas: "loginHeader"
                                     "loginBody";
                margin: auto auto auto auto;
            }
            #loginHeader {
                grid-area: loginHeader;
            }
            #loginBody {
                grid-area: loginBody;
                display: grid;
                grid-template-columns: min-content min-content;
                padding: 2rem 2rem 2rem 2rem;
            }
        </style>
    </head>
    <body onload="allElementsLoaded()">
        <!-- this form submits to itself -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div id="loginHeader">
            </div>
            <div id="loginBody">
                <?php if (isset($_POST['username']) && isset($_POST['verifiedPassword']) && isset($_POST['role'])): ?>

                <?php elseif (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['passwordVerify'])): ?>
            
                <?php elseif (isset($_POST['username']) && isset($_POST['password'])): ?>
                    <!-- VERITFY USERNAME AND PASSWORD -->

                <?php else: ?>
                    <label for="txtUsername" id="lblUsername">Enter Your Username</label>
			        <input type=text id='txtUsername' name='username' required>
                    <label for="pwdPassword" id="lblPassword">Enter Your Password</label>
			        <input type=password id='pwdPassword' name='password' required>
                    <button id='btnClearLogin' onpointerdown='clearLogin()' >Clear</button>
                    <input type="submit" value="Select Role" id="btnSelectRole">
                <?php endif; ?>
                
                
            </div>
            
            <?php require_once '../Resources/php/display.php'; ?>
           
        </form>
    </body>
</html>
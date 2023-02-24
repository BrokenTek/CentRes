<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->
<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <!-- gives you access to setVar, getVar, removeVar, 
        clearVars, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script>      
    </head>
    <body>
        <!-- change the action to you filename -->
        <form action="displayTemplate.php" method="POST">
            <!-- retain any POST vars. When updateDisplay() is called, these variables
            will be carried over -->
            <?php require_once '../Resources/php/display.php'; ?>
            <!-- PLACE YOUR CODE HERE -->
        </form>
        
        <!-- demonstration on how to use getVar, setVar, updateDisplay for just this page -->
        <!-- remove this script tag -->
        <script defer>
            function testDisplayInterface() {
                if (getVar("greeting") != null) {
                    alert("var retained after refresh:\n" + getVar("greeting"));
                }
                else {
                    alert("Greeting not set yet:\n" + getVar("greeting"));
                    setVar("greeting", "Hello World");
                    alert("Greeting set:\n" + getVar("greeting"));
                    removeVar("greeting");
                    alert("Greeting unset:\n" + getVar("greeting"));

                    setVar("greeting", "Hello Again. I'm still here");
                    alert("updatingDisplay. greeting will be carried over");
                    updateDisplay();
                }
            }

            document.addEventListener("DOMContentLoaded", testDisplayInterface());
        </script>
    </body>
</html>
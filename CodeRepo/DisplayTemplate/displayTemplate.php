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
        
        <!-- demonstration on how to use getVar, setVar, updateDisplay for just this page -->
        <!-- remove this script tag -->
        <script>
            function allElementsLoaded() {
                if (getVar("greeting") != null) {
                    // variable carried over after updateDisplay() is called
                    alert('After Refresh/Submit:\ngetVar("greeting")  >>>> ' + getVar("greeting"));
                }
                else {
                    // show getVar returns undefined for anything that hasn't been set yet
                    alert('getVar("greeting") >>>> ' + getVar("greeting"));

                    // use setVar and show that getVar returns the value
                    setVar("greeting", "Hello World");
                    alert('setVar("greeting", "Hello World");\ngetVar("greeting") >>>> ' + getVar("greeting"));
                    
                    // use removeVar and show that getVar once again returns undefined
                    removeVar("greeting");
                    alert('removeVar("greeting");\ngetVar("greeting") >>>> ' + getVar("greeting"));

                    // set greeting to "Hello Again" and updateDisplay(). Alert user page is about to submit/refresh
                    // using updateDisplay()
                    setVar("greeting", "Hello Again. Im still here");
                    alert('setVar("greeting, "Hello Again". Im still here");\nupdateDisplay();\n\nvar will be retained after refresh/submit');
                    updateDisplay();
                }
            }

            //Place your JavaScript Code here
        </script>
    </head>
    <body onload="allElementsLoaded()">
        <!-- change the action to you filename -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <!-- PLACE YOUR PHP LAYOUT LOGIC CODE HERE -->
            
            <!-- If you want to forget/not carry over variables, use PHP unset function
            to remove these variables -->
            <?php unset($thisVariableIWantToForget) ?>;

            <!-- retain any POST vars. When updateDisplay() is called or the form is submitted,
            these variables will be carried over -->
            <?php require_once '../Resources/php/display.php'; ?>
           
        </form>
    </body>
</html>
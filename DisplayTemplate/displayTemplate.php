<?php
    /*
        ============================================================
        DISPLAY TEMPLATE
        This template includes starter code that allows
        you to use display.php and display.js
        ============================================================

        ensures you are logged in before rendering page, and are logged in under the correct role.
        If you aren't logged in, it will reroute to the login page.
        If you are logged in but don't have the correct role to view this page,
        you'll be routed to whatever the home page is for your specified role level
    */

    // CHANGE 255 TO THE ALLOWED ROLE LEVEL FOR THE PAGE
    require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(255, $GLOBALS['role']); 
?>

<!DOCTYPE html>
<?php require_once '../Resources/PHP/dbConnection.php'; ?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script> 
        
        <script>
            function allElementsLoaded() { }

        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php
                // ================================================================================
                //      PLACE YOUR PHP LAYOUT LOGIC CODE HERE
                // ================================================================================
                //      REMOVE ANY VARIABLES YOU DON'T WANT TO GET CARRIED OVER
                        unset($_POST['thisVariableIWantToForget'], $_POST['thisOtherVariableIDontNeed']);
                // ================================================================================
                //      retain any POST vars. When updateDisplay() is called or the form is submitted,
                //      these variables will be carried over
                        require_once '../Resources/PHP/display.php';
                // ================================================================================
            ?>
        </form>
    </body>
</html>
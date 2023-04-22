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
    require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']);
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
        <style>
            #sessionForm {
                display: grid;
            }
            #frmCloseBusinessDay {
                background-color: #777;
            }
            #content {
                width: 100%;
                height: 100%;
            }
            h1, h2 {
                color: white; 
            }
            .error {
                color: #F6941D;
            }
            fieldset {
                margin-inline: auto;
                margin-block: 1rem;
            }
        </style>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        
        <form id="frmCloseBusinessDay" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php require_once "../Resources/PHP/sessionHeader.php"; ?>
            <div id="content">
                <fieldset>
                    <?php
                        $sql = "SELECT businessDay FROM Config;";
                        $businessDay = connection()->query($sql)->fetch_assoc()['businessDay'];
                    ?>
                    <?php if (isset($_POST['confirmClose'])): ?>
                        <?php
                            $loc = realpath('../BusinessDays');
                            if (strpos($loc, "//") > 0) {
                                $loc .= "/";
                            }
                            else {
                                $loc .= "\\\\";
                            }
                            $loc = str_replace($loc, "\\", "\\\\");
                            $sql = "CALL closeBusinessDay('$loc');";
                            try {
                                $result = connection()->query($sql);

                                // log all employees out except for yourself if you were successfully able to close
                                // the business day.
                                $sql = "UPDATE Employees SET accessToken = NULL, accessTokenExpiration  = NULL WHERE id <> " .$GLOBALS['userId']. ";";
                                $result = connection()->query($sql);
                                $sql = "DELETE FROM ActiveEmployees WHERE employeeId <> " .$GLOBALS['userId']. ";";
                                $result = connection()->query($sql);

                                echo("<h1>The business day for $businessDay been closed</h1>");
                                echo("<hr><h2>Ticket information with financial data<br>saved to file.</h2>");
                                echo("<h2>Any active employees have been logged out.</h2>");
                            }
                            catch (Exception $e) {
                                echo("<h1 class='error'>An error has occured closing the business day for $businessDay!</h1>");
                                echo("<h1 class='error'>" .$e->getMessage(). "</h1>");
                                echo("<h2>Has this operation already been performed today?</h2>");
                            }
                        ?>
                    <?php else: ?>
                        <fieldset>
                            <legend>
                            Are you sure you want to close the business day for <?php echo($businessDay); ?>?
                            <br>
                            This action can only be performed once per calendar date!
                            </legend>
                            <input type="submit" class="button" name="confirmClose" value="Close Business Day">
                    <?php endif; ?>
                </fieldset>    
            </div>
        </form>
    </body>
</html>
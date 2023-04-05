<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <!-- gives you access to varSet, varGet, varRem, 
        varClr, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
    
        <script>
            function allElementsLoaded() {
                
            }

            //Place your JavaScript Code here
        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        <h1><code>activeTicketGroupConnector.php<br></h1>
            Expected Variables<br>
            atgHash, activeGroupId, ticketItemNumber</code>
        <?php
            if (isset($_POST['ticketItemNumber'])) {
                $sql = "call toggleTicketItemReadyState(?);";                
                    $sql2 = connection()->prepare($sql);
                    $sql2->bind_param("i", $_POST['ticketItemNumber']);
                    $sql2->execute();
                    $sql = str_replace("?", $_POST['ticketItemNumber'], $sql);
                    echo("<h2>$sql<h2>");
                    echo("<h2>Statement Executed.<h2>");
            }
            unset($_POST['ticketItemNumber']);
        ?>
        
        <!-- this form submits to itself -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <!-- retain any POST vars. When updateDisplay() is called or the form is submitted,
            these variables will be carried over -->
            <?php require_once '../Resources/php/display.php'; ?>
           
        </form>
    </body>
</html>
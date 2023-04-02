<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->

<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <!-- gives you access to varSet, varGet, varRem, 
        varClr, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
        <!-- demonstration on how to use varGet, varSet, updateDisplay for just this page -->
        <!-- remove this script tag -->
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
            if  (!isset($_POST['atgHash']) && !isset($_POST['activeGroupId'])) {
                echo("<h1>Awaiting Valid Credentials<h1>");
                if (isset($_POST['ticketItemNumber'])) {
                    echo("<h2>Request Revoked! Valid Credentials Required<h2>");
                }
            }
            else {
                if (isset($_POST['ticketItemNumber'])) {
                    $sql = "SELECT * FROM ActiveTicketGroups WHERE id = ? AND atgHash = ?;";                
                    $sql = connection()->prepare($sql);
                    $sql->bind_param("ds", $_POST['activeGroupId'], $_POST['atgHash']);
                    $sql->execute();
                    $result = $sql->get_result();
                    if (mysqli_num_rows($result) == 0) {
                        echo("<h1>Credentials Invalid<h1>");
                        echo("<h2>Request Revoked! Valid Credentials Required<h2>");
                    }
                    else {
                        echo("<h1>Credentials Confirmed<h1>");
                        // this code does not validate to see if ticket item is part of activeTicketGroupId
                        $sql = "call toggleTicketItemReadyState(?);";                
                        $sql2 = connection()->prepare($sql);
                        $sql2->bind_param("i", $_POST['ticketItemNumber']);
                        $sql2->execute();
                        $sql = str_replace("?", $_POST['ticketItemNumber'], $sql);
                        echo("<h2>$sql<h2>");
                        echo("<h2>Statement Executed.<h2>");
                    }
                }
                else {
                    echo("<h1>Credentials Verification Skipped<h1>");
                }
            }
            unset($_POST['ticketItemNumber']);
        ?>
        <!-- this form submits to itself -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <!-- PLACE YOUR PHP LAYOUT LOGIC CODE HERE -->
            
            <!-- If you want to forget/not carry over variables, use PHP unset function
            to remove these variables -->
            <?php unset($_POST['thisVariableIWantToForget'], $_POST['thisOtherVariableIDontNeed']) ?>

            <!-- retain any POST vars. When updateDisplay() is called or the form is submitted,
            these variables will be carried over -->
            <?php require_once '../Resources/php/display.php'; ?>
           
        </form>
    </body>
</html>
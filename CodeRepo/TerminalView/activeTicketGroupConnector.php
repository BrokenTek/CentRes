<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
    
        <script>
            function allElementsLoaded () { }

            document.toggleTicketItemStatus = function(event) {
                varSet("ticketItemNumber",this.ticketItemNumber, null, true);
            }
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
            }
        ?>
        
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            
        </form>
    </body>
</html>
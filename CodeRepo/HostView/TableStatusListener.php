<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(4, $GLOBALS['role']); ?>

<!DOCTYPE html>
<?php require_once '../Resources/PHP/dbConnection.php'; ?>


<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script> 
        <script>
            function allElementsLoaded() {
                <?php
                    if(!isset($_POST['tableLogIndex'])) {
                        $_POST['tableLogIndex'] = 0;
                    }
                    unset($_POST['updatedTables']);
                    $sql = "SELECT IFNULL(MAX(id),-1) as tableLogIndex FROM TableLog";
                    $newIndex = connection()->query($sql)->fetch_assoc()['tableLogIndex'];

                    if ($newIndex > $_POST['tableLogIndex']) {
                        $sql = "SELECT DISTINCT tableId, status FROM tableLog INNER JOIN Tables
                            ON tableLog.tableId = Tables.id WHERE tableLog.id > " .$_POST['tableLogIndex']. ";";
                        $updatedTables = connection()->query($sql);
                        if (mysqli_num_rows($updatedTables) > 0) {
                            $table = $updatedTables->fetch_assoc();
                            $updateString = $table['tableId'] .",". $table['status'];
                            while ($table = $updatedTables->fetch_assoc()) {
                                $updateString .= ",". $table['tableId'] .",". $table['status'];
                            }
                            
                            echo("varSet('updatedTables', '$updateString');");
                        }

                        echo("varSet('tableLogIndex', '$newIndex');");
                    }

                    

                    disconnect();
                ?>
            }
        </script>
    </head>
    <body onload="allElementsLoaded()">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php require_once '../Resources/PHP/display.php'; ?>
        </form>
    </body>
</html>
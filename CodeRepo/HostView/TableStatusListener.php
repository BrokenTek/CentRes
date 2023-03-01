<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(4, $GLOBALS['role']); ?>

<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>


<html>
    <head>
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        <script>
            function allElementsLoaded() {
                <?php
                    if(!isset($_POST['gIndex'])) {
                        $_POST['gIndex'] = 0;
                    }

                    $sql = "SELECT DISTINCT tableId, status FROM g INNER JOIN Tables
                            ON g.tableId = Tables.id WHERE g.id > " .$_POST['gIndex']. ";";

                    $updatedTables = connection()->query($sql);
                    if (mysqli_num_rows($result) > 0) {
                        $table = $updatedTables->fetch_assoc();
                        $updateString = $table['tableId'] .",". $table['status'];
                        while ($table = $updatedTables->fetch_assoc()) {
                            $updateString .= $table['tableId'] .",". $table['status'];
                        }
                        echo("setVar('updatedTables', '$updateString')");
                    }

                    $sql = "SELECT IFNULL(MAX(id),-1) as gIndex FROM g";
                    $newIndex = connection()->query($sql)->fetch_assoc()['gIndex'];
                    echo("setVar('gIndex', '$newIndex')");

                    disconnect();
                ?>
            }
        </script>
    </head>
    <body onload="allElementsLoaded()">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        </form>
    </body>
</html>
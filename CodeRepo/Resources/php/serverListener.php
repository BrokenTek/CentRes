<!DOCTYPE html>
<html>
    <head>
        <script>
            function reload() {
                document.getElementById("tableSelectorForm").submit();
            }
            setTimeout(reload, 1000);
        </script>
    </head>
    <body>
        <form id="tableSelectorForm" method="POST">
            <?php
                require_once 'connect_disconnect.php';
                $_POST['tableList'] = "";
                if (isset($_POST['staticTableId'])) { 
                    $sql = "SELECT id FROM Tickets WHERE tableId = '" .$_POST['staticTableId']. "';";
                    $tick = connection()->query($sql);
                    if (mysqli_num_rows($tick) == 1) {
                        $tick = $tick->fetch_assoc()['id'];
                        $_POST['tableList'] = $_POST['staticTableId'] .",". $tick ;
                    }
                    else {
                        $_POST['tableList'] = $_POST['staticTableId'] .",";
                    }
                    
                }
                elseif (isset($_POST['username']) || isset($_POST['employeeId'])) {
                    $userStr = (isset($_POST['employeeId']) ? $_POST['employeeId'] : "idFromUsername('" .$_POST['username']. "')" );
                    
                    $_POST['tableList'] = "";
                    
                    if (isset($_POST['showAllTables'])) {
                        $sql = "SELECT tableId FROM TableAssignments WHERE employeeId = $userStr;";
                    }
                    else {
                        $sql = "SELECT Tickets.tableId AS tableId, Tickets.id AS ticketNumber  FROM TableAssignments INNER JOIN Tickets 
                                                                ON TableAssignments.tableId = Tickets.tableId
                                                                WHERE TableAssignments.employeeId = $userStr ORDER BY Tickets.tableId;";
                        
                    }
                    $ownedTables = connection()->query($sql);
                    if (mysqli_num_rows($ownedTables) > 0) {
                        $row = $ownedTables->fetch_assoc();
                        if (isset($_POST['tableIdOnly'])) {
                            $_POST['tableList'] = $row['tableId'];
                            while($row = $ownedTables->fetch_assoc()) {
                                $_POST['tableList'] .= "," . $row['tableId'];
                            }
                        }
                        else {
                            $_POST['tableList'] = $row['tableId'] .",". $row['ticketNumber'];
                            while($row = $ownedTables->fetch_assoc()) {
                                $_POST['tableList'] .= "," . $row['tableId'] .",". $row['ticketNumber'];
                            }
                        }
                    }
                    else {
                        unset($_POST['tableList']);
                    }
                    
                }
                else {
                    unset($_POST['tableList']);
                    
                }

                if (isset($_POST['ticket'])) {
                    $sql = "SELECT partySize, tableId FROM Tickets WHERE id = " .$_POST['ticket']. ";";
                    $result = connection()->query($sql)->fetch_assoc();

                    $_POST['maxSeat'] = $result['partySize'];
                    
                    $sql = "SELECT DISTINCT splitFlag FROM TicketItems WHERE ticketId = " .$_POST['ticket']. " ORDER BY splitFlag;";
                    $splits = connection()->query($sql);
                    
                    $_POST['maxSplit'] = 1;
                    while ($splitCurr = $splits->fetch_assoc()) {
                        $val = intval(log($splitCurr['splitFlag'],2));
                        if ($val == 0) {
                            $_POST['maxSplit'] = 9;
                            break;
                        }
                        else {
                            $_POST['maxSplit'] = $val;
                        }
                    }
                    if (mysqli_num_rows($splits) > 0) {
                        $_POST['maxSplit'] = intval(fmod($_POST['maxSplit'] + 1,10));
                    }
                    
                }
                else {
                    unset($_POST['maxSeat'], $_POST['maxSplit']);
                }
                require_once "display.php";
                disconnect();
            ?>
        </form>
    </body>
</html>
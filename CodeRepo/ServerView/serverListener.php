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
                include '../Resources/php/connect_disconnect.php';
                if (isset($_POST['username'])) {
                    $_POST['tableList'] = "";
                    echo("Assigned Ticket/Table event listener");
                    echo("<br>Username: " .$_POST['username']);
                    connection();
                    $sql = "SELECT Tickets.tableId AS tableId, Tickets.id AS ticketNumber  FROM TableAssignments INNER JOIN Tickets 
                                                                ON TableAssignments.tableId = Tickets.tableId
                                                                WHERE TableAssignments.employeeId = idFromUsername('" .$_POST['username']. "') ORDER BY Tickets.tableId;";
                    $ownedTables = connection()->query($sql);
                    if (mysqli_num_rows($ownedTables) > 0) {
                        $row = $ownedTables->fetch_assoc();
                        $_POST['tableList'] = $row['tableId'] .",". $row['ticketNumber'];
                        while($row = $ownedTables->fetch_assoc()) {
                            $_POST['tableList'] .= "," . $row['tableId'] .",". $row['ticketNumber'];
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
                    $_POST['maxSeat'] = 2;
                    $sql = "SELECT DISTINCT seat FROM TicketItems WHERE ticketId = " .$_POST['ticket']. " ORDER BY seat;";
                    $seats = connection()->query($sql);
                    
                    if (mysqli_num_rows($seats) > 0) {
                        while ($seatCurr = $seats->fetch_assoc()) {
                            $_POST['maxSeat'] = intval($seatCurr['seat']) + 2;
                        }
                    }

                    $_POST['maxSplit'] = 2;
                    $sql = "SELECT DISTINCT splitFlag FROM TicketItems WHERE ticketId = " .$_POST['ticket']. " ORDER BY splitFlag;";
                    $splits = connection()->query($sql);
                    
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
                require "../Resources/php/display.php";
                disconnect();
            ?>
        </form>
    </body>
</html>
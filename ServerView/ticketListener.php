<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <script>
            function reload() {
                document.getElementById("ticketSelectorForm").submit();
            }
            // if the ticket number has been injected into the frame, reload
            // and to enable timestamp retrieval... Otherwise start listening for changes to ticket.
            <?php
                if (isset($_POST['ticketNumber']) && !isset($_POST['modificationTime'])) {
                    echo("addEventListener('load',reload);");
                }
                elseif (isset($_POST['ticketNumber']) && isset($_POST['modificationTime'])) {
                    echo("setTimeout(reload, 1000);");
                }
            ?>
        </script>
    </head>
    <body>
        <form id="ticketSelectorForm"  action="ticketListener.php" method="POST">
            <?php
                require_once '../Resources/PHP/dbConnection.php';

                // if the ticket number has been injected, get the timestamp.
                if (isset($_POST['ticketNumber'])) {
                    
                    $sql = "SELECT tableId, timeModified FROM Tickets WHERE id = " .$_POST['ticketNumber']. ";";
                    $result = connection()->query($sql)->fetch_assoc();
                    $_POST['modificationTime'] = $result['timeModified'];
                    if($result['tableId'] != null) {
                        $_POST['tableId'] = $result['tableId'];
                    }
                    else {
                        $_POST['ticketRemoved'] = "yes";
                        unset($_POST['tableId']);
                    }
                     
                    $sql = "SELECT splitId, totalAmountPaid FROM Splits WHERE ticketId = " .$_POST['ticketNumber']. ";";
                    $ticketPaidStatuses = connection()->query($sql);
                    if (mysqli_num_rows($ticketPaidStatuses) > 0) {
                        $paidStatus = $ticketPaidStatuses->fetch_assoc();
                        $_POST['paidStatuses'] = intval($paidStatus['splitId']) .",". ( $paidStatus['totalAmountPaid'] == null ? "Unpaid" : "Paid");
                        while($paidStatus = $ticketPaidStatuses->fetch_assoc()) {
                            $_POST['paidStatuses'] .= ",". intval($paidStatus['splitId']) .",". ( $paidStatus['totalAmountPaid'] == null ? "Unpaid" : "Paid");
                        }
                    }
                    else {
                        $_POST['paidStatuses'] = "No Splits";
                    }
                    require_once '../Resources/PHP/display.php';
                } 
                else {
                    unset($_POST['modificationTime']);
                } 
                disconnect();             
            ?>
        </form>
    </body>
</html>

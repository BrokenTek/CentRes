<!DOCTYPE html>
<html>
    <head>
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
                include '../Resources/php/connect_disconnect.php';

                // if the ticket number has been injected, get the timestamp.
                if (isset($_POST['ticketNumber'])) {
                    connection();
                    $sql = "SELECT timeModified FROM Tickets WHERE id = " .$_POST['ticketNumber']. ";";
                    $modifiedTime = connection()->query($sql)->fetch_assoc()['timeModified'];
                    $_POST['modificationTime'] = $modifiedTime;
                     
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
                    include '../Resources/php/display.php';
                } 
                else {
                    unset($_POST['modificationTime']);
                }               
            ?>
        </form>
    </body>
</html>

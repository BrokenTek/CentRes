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
                else {
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
                    include '../Resources/php/display.php';
                }                
            ?>
        </form>
    </body>
</html>
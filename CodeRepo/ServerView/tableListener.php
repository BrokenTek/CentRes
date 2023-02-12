<html>
    <head>
        <script>
            function reload() {
                document.getElementById("tableSelectorForm").submit();
            }
            setTimeout(reload, 5000);
        </script>
    </head>
    <body>
        <form id="tableSelectorForm" method="POST">
            <?php
                include '../Resources/php/connect_disconnect.php';
                if (isset($_POST['username'])) {
                {
                    echo("Assigned Ticket/Table event listener");
                    echo("<br>Username: " .$_POST['username']);
                    echo("<input type='hidden' name='username' value='" .$_POST['username']. "'>");
                    connection();
                    $sql = "SELECT Tickets.tableId AS tableId, Tickets.id AS ticketNumber  FROM TableAssignments INNER JOIN Tickets 
                                                                ON TableAssignments.tableId = Tickets.tableId
                                                                WHERE TableAssignments.employeeId = idFromUsername('" .$_POST['username']. "');";
                    $ownedTables = connection()->query($sql);
                    if (mysqli_num_rows($ownedTables) > 0) {
                        while($row = $ownedTables->fetch_assoc()) {
                            echo('<h1>' .$row['tableId']. " - " .$row['ticketNumber']);
                        }
                    }
                }
            }
            ?>
        </form>
    </body>
</html>
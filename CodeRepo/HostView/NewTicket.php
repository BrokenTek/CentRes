<!DOCTYPE html>
<html>
<head>
    <title>Create a Ticket</title>
</head>
<body>
    <?php
    require '../CodeRepo/Resources/php/connect_disconnect.php';
    $conn = connection();

    if (isset($_POST["createTicket"])) {
        $nickname = $_POST["nickname"];
        $partySize = $_POST["partySize"];
        try {
			// Call a stored procedure to create a new ticket and store its number in a MySQL user-defined variable
            $sql = "CALL CreateTicket('$nickname', $partySize, NOW(), @newTicketNumber)";
            $conn->query($sql);
			// Retrieve the new ticket number and the time it was requested from the Tickets table
            $sql = "SELECT @newTicketNumber AS newTicketNum, DATE_FORMAT(timeRequested, '%H:%i') AS timeRequested FROM Tickets WHERE id = @newTicketNumber";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $newTicketNum = $row["newTicketNum"];
            $timeRequested = $row["timeRequested"];
            echo "<p>Party Name: " . $nickname . "</p>";
            echo "<p>Party Size: " . $partySize . "</p>";
            echo "<p>Time Requested: " . $timeRequested . "</p>";
            echo "<p>New ticket number: " . $newTicketNum . "</p>";
        } catch (mysqli_sql_exception $e) {
            echo "Error executing SQL query: " . $e->getMessage();
        }
    }

    disconnect();    
    ?>
    <fieldset>
        <legend>Create a Ticket</legend>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="nickname">Party Nickname:</label>
            <input type="text" name="nickname" id="nickname" required>
            <br>
            <label for="partySize">Party Size:</label>
            <input type="number" name="partySize" id="partySize" required>
            <br>
            <button type="submit" name="createTicket">Create Ticket</button>
            <button type="button" onclick="location.href='WaitList.php'">Back</button>
        </form>
    </fieldset>
</body>
</html>

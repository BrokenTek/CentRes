<!DOCTYPE html>
<html>
<head>
    <title>Create a Ticket</title>
    <style>
        fieldset {
            display: grid;
            grid-template-columns: min-content min-content;
            grid-auto-rows: min-content;
        }

        fieldset > * {
            margin-bottom: .125rem;
        }
        label {
            margin-left: auto;
            padding-right: .5rem;
        }
        input[type="checkbox"] {
            margin-right: auto;
        }
        #lblTimeRequested {
            grid-row: 4;
        }
        #dtmTimeRequested {
            grid-row: 4;
        }
        #divButtons {
            grid-row: 5;
            display: grid;
            grid-template-columns: 1fr min-content min-content;
            grid-column: 1 / span 2;
        }
        #btnBack {
            grid-column: 2;
        }
        #btnCreate {
            grid-column: 3;
        }
    </style>
    <script>
        var chkReservation;
        function declareReservationCheck() {
            chkReservation = document.getElementById("chkReservation");
        }

        function setTimeRequestedToNow() {
            if (chkReservation.checked) {
                let dtmReservationTime = document.createElement("input");
                with (dtmReservationTime) {
                    setAttribute("id", "dtmTimeRequested");
                    setAttribute("name", "timeRequested");
                    setAttribute("type", "datetime-local");
                    setAttribute("required",'');
                }

                let lbl = document.createElement("label");
                with(lbl) {
                    setAttribute("id", "lblTimeRequested");
                    setAttribute("for","timeRequested");
                    innerText="Date/Time:";
                }

                with (document.getElementsByTagName("fieldset")[0]) {
                    appendChild(lbl);
                    appendChild(dtmReservationTime);
                }
            }
            else {
                document.getElementById("lblTimeRequested").remove();
                document.getElementById("dtmTimeRequested").remove();
            /*
            */
           }
        }
    </script>
</head>
<body onload="declareReservationCheck()">
    <?php
    require '../Resources/php/connect_disconnect.php';
    date_default_timezone_set('America/New_York');

    if (isset($_POST["createTicket"])) {
        $nickname = $_POST["nickname"];
        $partySize = $_POST["partySize"];
        $timeRequested = isset($_POST['timeRequested']) ? str_replace("T", " ",$_POST["timeRequested"]) : "";
        try {
			// Call a stored procedure to create a new ticket and store its number in a MySQL user-defined variable
            $sql = 
                isset($_POST['timeRequested']) ? 
                "CALL createReservation('$nickname', $partySize, '$timeRequested', @newTicketNumber);" :
                "CALL createTicket('$nickname', $partySize, @newTicketNumber);";
            
            connection()->query($sql);
			// Retrieve the new ticket number and the time it was requested from the Tickets table
            $sql = "SELECT @newTicketNumber AS newTicketNum, timeRequested FROM Tickets WHERE id = @newTicketNumber;";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $newTicketNum = $row["newTicketNum"];
            $timeRequested = $row["timeRequested"];
            echo "<p>Ticket created for $nickname: $partySize</p>";
        } catch (mysqli_sql_exception $e) {
            echo "An error has occured.\nTry changing the party name.";
        }
    }

    disconnect();    
    ?>
    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <fieldset>
            <legend>Create a Ticket</legend>
            <label id="lblPartyNickname" for="nickname">Party&nbsp;Nickname:</label>
            <input id="txtNickname" type="text" name="nickname" required>
            
            <label id="lblPartySize" for="partySize">Party&nbsp;Size:</label>
            <input id="numPartySize" type="number" name="partySize" required>
            <label id="lblReservation" for="chkReservation">Reservation:</label>
            <input id="chkReservation" type="checkbox" onclick="setTimeRequestedToNow()">
            <div id="divButtons">
                <button id="btnBack" type="button" onclick="location.href='WaitList.php'">Back</button>
                <button id="btnCreate" type="submit" name="createTicket">Create Ticket</button>
            </div>
        </fieldset>
    </form>
   
</body>
</html>

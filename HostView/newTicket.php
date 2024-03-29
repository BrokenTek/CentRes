
<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(4, $GLOBALS['role']); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Create a Ticket</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
    <style>
        body > * {
            background-color: black;
            color: white;
        }
        fieldset {
            display: grid;
            grid-template-columns: min-content min-content;
            grid-auto-rows: min-content;
            border: none;
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
        .message, .errorMessage {
            margin-top: 1rem;
            margin-inline: auto;
            max-width: 20rem;
            border: .25rem solid white;
            padding: .5rem;
        }
        .disappear, .errorMessage.disappear {
            animation: fadeOut .33s ease-in-out 1 forwards;
        }

    </style>
    <script>
        var chkReservation;
        function declareReservationCheck() {
            chkReservation = document.getElementById("chkReservation");

            setTimeout(() => {
                    var msg = document.getElementsByClassName("message");
                    var err = document.getElementsByClassName("highlighted");
                    if (msg.length > 0) {
                        msg[0].classList.add("disappear");
                    }
                    if (err.length > 0) {
                        err[0].classList.add("disappear");
                    }
                }, 3000);
        }

        function setTimeRequestedToNow() {
            if (!chkReservation.checked) {
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
                    innerText="When:";
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
    require_once '../Resources/PHP/dbConnection.php';
    date_default_timezone_set('America/New_York');
    $message = "";
    if (isset($_POST["createTicket"])) {
        //filters out certain characters to eliminate the possibility of a XSS attack
        $nickname = str_replace(array('"', '\\', '&', ';', '{', '}', '(', ')', '[', ']', '<', '>'), '', $_POST["nickname"]);
        $partySize = $_POST["partySize"];
        $timeRequested = isset($_POST['timeRequested']) ? str_replace("T", " ",$_POST["timeRequested"]) : "";
        try {
			// Call a stored procedure to create a new ticket and store its number in a MySQL user-defined variable
            //CHANGE THIS to allow for single quotes. 

            $sql = 
                isset($_POST['timeRequested']) ? 
                /*"CALL createReservation('$nickname', $partySize, '$timeRequested', @newTicketNumber);" :
                "CALL createTicket('$nickname', $partySize, @newTicketNumber);";*/
                "CALL createReservation(?, $partySize, '$timeRequested', @newTicketNumber);":
                "CALL createTicket(?, $partySize, @newTicketNumber);";
            
            $sql = connection()->prepare($sql);
            $sql->bind_param("s", $nickname);
            $sql->execute();
			// Retrieve the new ticket number and the time it was requested from the Tickets table
            $sql = "SELECT @newTicketNumber AS newTicketNum, timeRequested FROM Tickets WHERE id = @newTicketNumber;";
            $result = connection()->query($sql);
            $row = $result->fetch_assoc();
            $newTicketNum = $row["newTicketNum"];
            $timeRequested = $row["timeRequested"];
            $message = "<div class='message'>Ticket created for $nickname: $partySize</div>";
        } catch (mysqli_sql_exception $e) {
            $message =  "<div class='errorMessage'>An error has occured.\nTry changing the party name.</div>";
        }
    }

    disconnect();    
    ?>
    
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <fieldset>
            <legend>Create a Ticket</legend>
            <label id="lblPartyNickname" for="nickname">Name:</label>
            <input id="txtNickname" type="text" name="nickname" required placeholder="Letters and Numbers">
            
            <label id="lblPartySize" for="partySize">Size:</label>
            <input id="numPartySize" type="number" name="partySize" min=1 required>
            <label id="lblReservation" for="chkReservation">Reserve:</label>
            <input id="chkReservation" type="checkbox" onpointerdown="setTimeRequestedToNow()">
            <div id="divButtons">
                <button id="btnBack" type="button" onpointerdown="location.href='waitList.php'">Back</button>
                <button id="btnCreate" type="submit" name="createTicket">Create Ticket</button>
            </div>
        </fieldset>
        <?php if (isset($message)) { echo($message); } ?>
    </form>
   
</body>
</html>

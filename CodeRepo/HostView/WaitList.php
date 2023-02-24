<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->
<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <link rel="stylesheet" href="../CSS/baseStyle.css">
        <!-- gives you access to setVar, getVar, removeVar, 
        clearVars, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script>  
        <script>
            function createEventHandlers() {
                let rows = document.getElementsByClassName("ticket");
                for (var i = 0; i < rows.length; i++) {
                    rows[i].addEventListener("pointerdown" , Hello);
                }
            }

            function Hello() {
                if (getVar("selectedTicket") == this.id) {
                    this.classList.remove("selected");
                    removeVar("selectedTicket");
                }
                else {
                   
                    let oldSelection = document.querySelector(".ticket.selected");
                    if (oldSelection != null) {
                        oldSelection.classList.remove("selected");
                    }
                    setVar("selectedTicket", this.id);
                    this.classList.add("selected");
                }
            }
        </script>
    </head>
    <body onload="createEventHandlers()">
        <legend>Wait List</legend>
        <!-- change the action to you filename -->
        <form action="WaitList.php" method="POST">
            <!-- retain any POST vars. When updateDisplay() is called, these variables
            will be carried over -->
            <?php require_once '../Resources/PHP/display.php'; ?>
            
            <!-- PLACE YOUR CODE HERE -->
            <?php
                if (isset($_POST['removeTicket'])) {
                    $sql = "DELETE FROM Tickets WHERE id = " .substr($_POST['removeTicket'], 6). ";";
                    connection()->query($sql);
                }

                $sql = "SELECT id as ticketNumber, timeRequested, timeReserved, nickname, partySize FROM Tickets WHERE timeSeated IS NULL
                        ORDER BY timeRequested, timeReserved";
                $parties = connection()->query($sql);
                echo("<table><tr><th>Time</th><th>Name</th><th>#</th></tr>");
                echo("<tr id='addNewTicket'><td colspan=3>Add a New Ticket</td></tr>");
                while ($party = $parties->fetch_assoc()) {
                    $selStr = (isset($_POST['selectedTicket']) && $_POST['selectedTicket'] == "ticket" && $party['ticketNumber'] ? " ".$_POST['selectedTicket'] : "");
                    if ($party['timeRequested'] > $party['timeReserved']) {
                        echo("<tr class='ticket reservation" .$selStr. "' id='ticket" .$party['ticketNumber']. "'>");
                    }
                    else {
                        echo("<tr class='ticket" .$selStr. "' id='ticket" .$party['ticketNumber']. "'>");
                    }
                    echo("<td>" .substr($party['timeRequested'],11,5). "</td>");
                    echo("<td>" .$party['nickname']. "</td>");
                    echo("<td>" .$party['partySize']. "</td>");
                }
                echo("</table>");
            ?>
            <button type="button" onclick="location.href='NewTicket.php'">Add</button>
            <button class="disabled" type="button" onclick="removeSelectedTicket()" disabled>Remove</button>
        </form>
    </body>
</html>
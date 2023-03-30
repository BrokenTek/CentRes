<!-- ensures you are logged in before rendering page.
Otherwise will reroute to logon page -->
<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(4, $GLOBALS['role']); ?>
<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<?php
    if (isset($_POST['removeTicket'])) {
        unset($_POST['selectedTicket']);
        $sql = "DELETE FROM Tickets WHERE id = " .substr($_POST['removeTicket'], 6). ";";
        connection()->query($sql);
    }
?>
<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/waitListStructure.css">
        <!-- gives you access to varSet, varGet, varRem, 
        varClr, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script>  
        <script>
            function createEventHandlers() {
                let selectedTicket = varGet("selectedTicket");
                if (selectedTicket !== undefined) {
                    let ticket = document.getElementById(selectedTicket);
                    if (ticket != null) {
                        ticket.classList.add("selected");
                    }
                    else {
                        varRem("selectedTicket");
                        varRem("ticketId");
                    }
                }
                rememberScrollPosition();
                let rows = document.getElementsByClassName("ticket");
                for (var i = 0; i < rows.length; i++) {
                    rows[i].addEventListener("pointerdown" , pressTicket);
                }

            }

            function removeSelectedTicket() {
                varSet('removeTicket', varGet('selectedTicket'));
                updateDisplay();
            }

            function pressTicket() {
                if (varGet("selectedTicket") == this.id) {
                    this.classList.remove("selected");
                    varRem("selectedTicket");
                    varRem("ticketId");
                    with (document.querySelector("#btnRemoveSelectedTicket")) {
                        setAttribute('disabled', '');
                        classList.add('disabled');
                    }
                }
                else {
                   
                    let oldSelection = document.querySelector(".ticket.selected");
                    if (oldSelection != null) {
                        oldSelection.classList.remove("selected");
                    }
                    varSet("selectedTicket", this.id);
                    varSet("ticketId", this.id.substring(6));
                    this.classList.add("selected");

                    with (document.querySelector("#btnRemoveSelectedTicket")) {
                        removeAttribute('disabled');
                        classList.remove('disabled');
                    }
                }
            }
        </script>
    </head>
    <body onload="createEventHandlers()" class="intro">
        <legend>
            <div onpointerdown="updateDisplay()">Wait List</div>
            <button type="button" onpointerdown="location.href='NewTicket.php'" id="btnAddTicket">Add</button>
            <button class="disabled" type="button" onpointerdown="removeSelectedTicket()" disabled id="btnRemoveSelectedTicket" onpointerdown="removeSelectedTicket()">Remove</button>
        </legend>
        <!-- change the action to you filename -->
        <form action="WaitList.php" method="POST" id="frmWaitList">
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
                        AND timeClosed IS NULL AND timeRequested <= ADDTIME(NOW(), '24:0:0') ORDER BY timeRequested, timeReserved;";
                $parties = connection()->query($sql);
                echo("<table id='tblWaitList'><tr><th>Time</th><th>Name</th><th>#</th></tr>");
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
        </form>
    </body>
</html>
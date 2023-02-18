
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            background-color: transparent;
        }
    </style>
<link rel="stylesheet" href="../CSS/baseStyle.css">
<link rel="stylesheet" href="../CSS/ticketStructure.css">
<link rel="stylesheet" href="../CSS/ticketStyle.css">
<script src="../JavaScript/displayInterface.js"></script>
<script>
    // ========================= TASKS WHEN TICKET IS LOADED ==============================
    // after the ticket has loaded
    function loaded() {
        var newTime;
        var paidStatuses;
        try {
            newTime = getVar("modificationTime", "ticketListener");
            paidStatuses = getVar("paidStatuses", "ticketListener");
        }
        catch (err) {
            setTimeout(loaded, 250);
            return;
        }
        
        // set the ticket timestamp, so anything listening to it can update.
        if (getVar("paidStatuses") != paidStatuses) {
            setVar("paidStatuses", paidStatuses);
        }
        if (newTime != getVar("recordedModificationTime")) {
            setVar("lastUpdate", Date.now());
        }
        
        var tick = getVar("ticket");
        
        // if the ticket number has been specified
        if (tick != null) {
            setVar("ticketNumber", tick, "ticketListener");
            updateDisplay("ticketListener");
            
            var oldTime = getVar("recordedModificationTime");
            

            // but you haven't yet retrieved a timestamp
            if (getVar("recordedModificationTime") == null) { 
                //  get the timestamp from the listener
                setVar("recordedModificationTime", newTime);
            }


            // if you previusly had items selected
            var selItems = getVar("selectedTicketItem");
            if (selItems != null) {
                selItems = selItems.split(",");
                // and there were more than 1 selected,
                if (selItems.length > 1) {
                    for(let i = 0; i < selItems.length; i++){
                        // Some of them might be visible or exist anymore
                        // check if you can still see them (exists and visible), and if so
                        // set them as "multiselect" 
                        var lookAt = document.querySelector("#" + selItems[i]);
                        if (lookAt != null) {
                            lookAt.classList.add("selected");
                            lookAt.classList.add("multiselect");
                        }
        	            
    	            }
                }
                // otherwise the selected item mgiht not be visible or exist anymore
                // check if you can still see it (exists and visible), and if so
                // set it as "selected"
                else {
                    let lookAt = document.querySelector("#" + selItems);
                    if (lookAt != null) {
                        lookAt.classList.add("selected");
                    }
                }
            }

            // if we just added a ticket item, we need to ensure we scroll to the bottom and select it.
            <?php 
                if(isset($_POST['command']) && $_POST['command'] == "add" ) {
                    echo("selectLast();");
                }
            ?>

            //begin listening for updates to the ticket.
            setInterval(checkExternalTicketUpdate, 1000);
            rememberScrollPosition();
            setState();
        }
        

        //create ticket item select listeners
        var elements = document.getElementsByClassName('ticketItem');
        if (elements != null) {
            for (var i = 0; i < elements.length; i++) {
                elements[i].addEventListener('pointerdown',pointerDown);
                elements[i].addEventListener('pointerup', pointerUp);
            }
        }

       
    }
    addEventListener('load', loaded);

    // function that listens for an external change in the ticket timestamp.
    // if a change has been detected, reload with the changes.
    function checkExternalTicketUpdate() {
        var oldTime = getVar("recordedModificationTime");
        var newTime;
        var paidStatuses;
        try {
            newTime = getVar("modificationTime", "ticketListener");
            paidStatuses = getVar("paidStatuses", "ticketListener");
        }
        catch (err) {
            setTimeout(checkExternalTicketUpdate, 250);
            return;
        }
        if (getVar("ignoreUpdate") != null) {
            //removeVar("ignoreUpdate");
            setVar("recordedModificationTime", newTime);
            return;
        }
        
        if (paidStatuses != getVar(paidStatuses)) {
            setVar("paidStatuses", paidStatuses);
        }
        if (oldTime != newTime && newTime != null) {
            setVar("recordedModificationTime", newTime);
            if (oldTime != null) {
                document.getElementById("ticketForm").submit(); 
            }
        }
    }

    function setState() {
        // this will set variables to control the state of the buttons on the serverView
        var enabledButtons = "";
        if (document.querySelectorAll(".pending").length > 0) {
            enabledButtons += "CancelSubmit";
        }
        if (document.querySelectorAll(".selected").length  > 0) {
            // can edit
            if (document.querySelectorAll(".selected.editable").length > 0 && document.querySelectorAll(".multiselect.editable").length == 0) {
                enabledButtons += "Edit";
            }
            // can remove
            if (document.querySelectorAll(".selected.removable").length == document.querySelectorAll(".selected").length) {
                enabledButtons += "Remove";
            }
            // can move
            if (document.querySelectorAll(".selected.moveable").length == document.querySelectorAll(".selected").length) {
                enabledButtons += "Move";
            }
            if (document.querySelectorAll(".selected.splittable").length == document.querySelectorAll(".selected").length) {
                enabledButtons += "Split";
            }
        }
        setVar("enabledButtons", enabledButtons)
        
    }
    
    // ========================= TICKET ITEM SELECT FUNCTIONS ==============================
   
    const LONG_TIME_TOUCH_LENGTH = 250;
    var targetTicketItem = null;
    var longTouchEnabled = false;
    var longTouchTimer = null;
	function pointerDown() {
        if (this === undefined) { return; }
        targetTicketItem = this;
        targetTicketItem.classList.add("selected");
        if (getVar("selectedTicketItem") != null && getVar("selectedTicketItem") != this.id) {
            longTouchTimer = setTimeout(longTouch, LONG_TIME_TOUCH_LENGTH);
        }
	}

    // if oyu pressed on a ticket item, you already have another one selected, and the minimum required time
    // for multiselect has elapsed, change the selected item to "multiselect" 
    function longTouch() {
         longTouchEnabled = true;
         targetTicketItem.classList.add("multiselect");

        // if there is exactly 1 other item selected, make it multi-select as well.
         var alreadySelected = getVar("selectedTicketItem");
         if (alreadySelected != null && alreadySelected.indexOf(",") == -1) {
            document.getElementById(alreadySelected).classList.add("multiselect");
         }
    }

    // when you've made your current selection
    function pointerUp() {
        if (targetTicketItem == null) { return; }
        
        if (longTouchTimer != null) {
            clearTimeout(longTouchTimer);
        }

        var oldSelectedItems = document.getElementsByClassName("ticketItem");
        // if you only have 1 item selected, adjust the state of applicable ticket items to reflect that.
        if (!longTouchEnabled) {
    	    /*this iterates through the list returned, if there is no case where multiple items are selected concurrently,
    	    you can just use oldSelectedItems[0].classList.remove("selected"); instead*/
    	    for(let i = 0; i < oldSelectedItems.length; i++){
        	    oldSelectedItems[i].classList.remove("selected");
                oldSelectedItems[i].classList.remove("multiselect");
    	    }
    	    targetTicketItem.classList.add("selected");
            targetTicketItem.classList.remove("multiselect");
            setVar("selectedTicketItem", targetTicketItem.id);
        }
        // or you have multiple items selected
        else {
            setVar("selectedTicketItem", getVar("selectedTicketItem") + "," + targetTicketItem.id); 
        }

        // set the ticket timestamp so anything listening to it can update.
        setVar("lastUpdate", Date.now()); 
        targetTicketItem = null;
        longTouchEnabled = false;
        setState();
    }

    function selectLast() {
        var ticks = document.getElementsByClassName("ticketItem");
        if (ticks.length > 0) {
            for(let i = 0; i < ticks.length; i++){
        	    ticks[i].classList.remove("selected");
                ticks[i].classList.remove("multiselect");
    	    }
    	    ticks[ticks.length - 1].classList.add("selected");
            ticks[ticks.length - 1].classList.remove("multiselect");
            setVar("selectedTicketItem", ticks[ticks.length - 1].id);
        }
    }

</script>
</head>
<body>
<form id="ticketForm" action="ticket.php" method="post" class= "ticketForm">

    <?php
        include 'connect_disconnect.php';
		if (isset($_POST['command'])) {
			try {
				if ($_POST['command'] == 'add' ) {
					$sql = "CALL createTicketItem(" .$_POST['ticket']. ", "
                                                      .$_POST['seat']. ", "
                                                     .$_POST['split']. ", '"
                                                   .$_POST['menuItem']. "');";
                    connection()->query($sql);
				}
                elseif ($_POST['command'] == 'cancelPending') {
                    $sql = "CALL cancelPendingTicketItems(" .$_POST['ticket']. ", ". (isset($_POST['split']) ? $_POST['split'] : "10") . ");";
                    echo("<h1>" .$sql. "</h1>");
                    connection()->query($sql);
				}
                elseif ($_POST['command'] == 'submitPending') {
					$sql = "CALL submitPendingTicketItems(" .$_POST['ticket']. ", ". (isset($_POST['split']) ? $_POST['split'] : "10") . ");";
                    connection()->query($sql);
				}
				elseif (isset($_POST['ticketItem'])) {
                    $ticketItems = explode(",", $_POST['ticketItem']);
                    foreach ($ticketItems as $ticketItem) {
                        if ($_POST['command'] == 'modify') {
                            $sql = "CALL modifyTicketItem("                 .$ticketItem. ", '" 
                                                            .$_POST['modificationNotes']. "');";
                        }
                        elseif ($_POST['command'] == 'override') {
                            $sql = "CALL overrideTicketItemPrice("                     .$ticketItem. ", " 
                                                                           .$_POST['overrideValue']. ", '"
                                                                            .$_POST['overrideNote']. "','" 
                                                                   .$_POST['authorizationUsername']. "');";
                        }
                        elseif ($_POST['command'] == 'remove') {
                            $sql = "CALL removeTicketItem(" .$ticketItem. ");";
                            
                        }
                        elseif ($_POST['command'] == 'moveToSeat') {
                            $sql = "CALL moveTicketItemToSeat("         .$ticketItem. ", "
                                                                   .$_POST['toSeat']. ");";
                        }
                        elseif ($_POST['command'] == 'moveToSplit') {
                            $sql = "CALL moveTicketItemToSplit("          .$ticketItem. ", " 
                                                                    .$_POST['toSplit']. ");";
                        }
                        elseif ($_POST['command'] == 'removeFromSplit') {
                            $sql = "CALL removeTicketItemFromSplit(" .$ticketItem. ", ";
                            
                        }
                        elseif ($_POST['command'] == 'addToSplit') {
                            $sql = "CALL addTicketItemToSplit("          .$ticketItem. ", " 
                                                                   .$_POST['toSplit']. ");";
                        }
                        elseif ($_POST['command'] == 'markAsReady') {
                            $sql = "CALL markTicketItemAsReady(" .$ticketItem. ");";
                        }
                        connection()->query($sql);
                    }
                }

                // signal an update was made to the ticket, so all listeners can recognize the
                // change and act appropriately 
                $sql = "UPDATE Tickets SET timeModified = NOW() WHERE id = " .$_POST['ticket']. ";";
                connection()->query($sql);
					
			}
            catch (Exception $e) {
            //    $errorMessage = $e->getMessage();
            echo("<h1>" .$e->getMessage(). "</h1>");
             
            }				
		} 
        if (isset($_POST['ticket']) && isset($_POST['recordedModificationTime'])) {
            $sql = "SELECT *, ticketItemStatus(TicketItems.id) as status, ticketItemPayStatus(TicketItems.id) as splitPayStatus
                    FROM ticketItems WHERE TicketItems.ticketId = " .$_POST['ticket'];

            $header = "";
                    

            if (isset($_POST['seat'])) {
                $sql .= " AND seat=" .$_POST['seat'];
            }
            else {
                $header = "<u>Seat</u>";
            }
            if (isset($_POST['split'])) {
                $bitMask = POW(2,$_POST['split']);
                $sql .= " AND (splitFlag & " .$bitMask. ") = "  .$bitMask;
            }
            elseif ($header == "") {
                $header = "<u>Split</u>";
            }
            else {
                $header .= " and <u>Split</u>";
            }
            $sql .= ";";

            if ($header != "") {
                $header = "Choose a " .$header. " to Add Menu Items";
            }

            $ticketItems = connection()->query($sql);

            if (mysqli_num_rows($ticketItems) == 0 && $header == "") {
                $header = "Add a Menu Item to Get Started";
            }
            
            if ($header != "") {
                echo("<h1 class='message' id='ticketHeader'>" .$header. "</h1>");
            }

           
            while($ticketItem = $ticketItems->fetch_assoc()) {
                $sql = "SELECT ticketItemStatus(" .$ticketItem['id']. ") as status;";
                $status = connection()->query($sql)->fetch_assoc()['status'];
                
                $sql = "SELECT * FROM menuItems WHERE quickCode = '" .$ticketItem['menuItemQuickCode']. "'";
                $menuItem = connection()->query($sql)->fetch_assoc();
                if ($ticketItem['status'] == "Hidden") {
                    break;
                }

                $selectedFlag = "";
                if (isset($_POST['selectedTicketItem']) && strpos($_POST['selectedTicketItem'], "ticketItem".$ticketItem['id']) ) {
                    $selectedFlag .= " selected";
                    if (strpos($_POST['selectedTicketItem'],",")) {
                        $selectedFlag .= " multiselect";
                    } 
                }
                if ($ticketItem['splitPayStatus'] == "Paid") {
                    echo('<div id="ticketItem' .$ticketItem['id']. '" class="ticketItem paid' .$selectedFlag. '">');
                    echo('<div class="ticketItemStatus"></div>');
                }
                elseif ($ticketItem['splitPayStatus'] == "Partial") {
                    echo('<div id="ticketItem' .$ticketItem['id']. '" class="ticketItem partialPay' .$selectedFlag. '">');
                    echo('<div class="ticketItemStatus"></div>');
                }
                else {

                    // when implementation is defined, calculate this value.
                    $hasMods = true;
                    //
                    $moveable = " moveable splittable";
                    switch($ticketItem['status']) {
                        case "n/a":
                            echo('<div id="ticketItem' .$ticketItem['id']. '" class="ticketItem unpaid removable' .$moveable. ' untracked' .$selectedFlag. '">');
                            echo('<div class="ticketItemStatus"></div>');
                            break;
                        case "Delivered":
                            echo('<div class="ticketItem" id="ticketItem' .$ticketItem['id']. '" class="ticketItem unpaid' .$moveable. ' delivered' .$selectedFlag. '">');
                            echo('<div class="ticketItemStatus">✔✔</div>');
                            break;
                        case "Ready":
                            echo('<div class="ticketItem" id="ticketItem' .$ticketItem['id']. '" class="ticketItem unpaid' .$moveable. ' ready' .$selectedFlag. '">');
                            echo('<div class="ticketItemStatus">✔</div>');
                            break;
                        case "Pending":
                            // if item has mods
                            if ($hasMods) {
                                echo('<div id="ticketItem' .$ticketItem['id']. '" class="ticketItem unpaid editable removable' .$moveable. ' pending' .$selectedFlag. '">');
                                echo('<div class="ticketItemStatus">✎</div>');
                            }
                            else {
                                echo('<div id="ticketItem' .$ticketItem['id']. '" class="ticketItem unpaid removable' .$moveable. ' pending' .$selectedFlag. '">');
                                echo('<div class="ticketItemStatus"></div>');
                            }
                            break;
                        case "Preparing":
                            if ($hasMods) {
                                echo('<div id="ticketItem' .$ticketItem['id']. '" class="ticketItem unpaid editable removable' .$moveable. ' preparing' .$selectedFlag. '">');
                                echo('<div class="ticketItemStatus">✎<br>⧖</div>');
                            }
                            else {
                                echo('<div id="ticketItem' .$ticketItem['id']. '" class="ticketItem unpaid removable' .$moveable. ' preparing' .$selectedFlag. '">');
                                echo('<div class="ticketItemStatus">⧖</div>');
                            }
                            break;
                        case "Updated":
                            if ($hasMods) {
                                echo('<div id="ticketItem' .$ticketItem['id']. '" class="ticketItem unpaid editable removable' .$moveable. ' updated' .$selectedFlag. '">');
                                echo('<div class="ticketItemStatus">✎⧖⚠</div>');
                            }
                            else {
                                //this is an unreachable path. Cant be updated if there aren't any mods.
                                //echo('<div id="ticketItem' .$ticketItem['id']. '" class="ticketItem removable moveable' .$selectedFlag. '">');
                                //echo('<div class="ticketItemStatus">⧖⚠</div>');
                            }
                            break;
                        case "Removed":
                            echo('<div id="ticketItem' .$ticketItem['id']. '" class="ticketItem unpaid removed' .$selectedFlag. '">');
                            echo('<div class="ticketItemStatus">🞮</div>');
                            break;
                        case "Hidden":
                            echo('<div id="ticketItem' .$ticketItem['id']. '" class="ticketItem unpaid removed hidden' .$selectedFlag. '">');
                            echo('<div class="ticketItemStatus">🞮</div>');
                            break;
                    }
                }

                echo('<div class="ticketItemNumber">' .$ticketItem['itemId']. '</div>
                    <div class="ticketItemText">' .$menuItem['title']. "</div>");
                
                if (is_null($ticketItem['overridePrice'])) {
                    echo('<div class="ticketItemPrice">' .$ticketItem['calculatedPrice']. '</div>');
                }
                else {
                    echo('<div class="ticketItemPrice old-info">' .$ticketItem['calculatedPrice']. '</div>');
                    echo('<div class="ticketItemOverrideNote">' .$ticketItem['overrideNote']. '</div>');
                    echo('<div class="ticketItemOverridePrice">');
                    if ($ticketItem['overridePrice'] < 0) {
                        // discount applied
                        echo($ticketItem['overridePrice']);
                    }
                    elseif ( $ticketItem['overridePrice'] >= 1 ) {
                         // price set to a value
                         echo($ticketItem['overridePrice']);
                    }
                    elseif ( $ticketItem['overridePrice'] == 0 ) {
                        // free
                        echo('FREE');
                    }
                    else {
                        // percent discount applied
                       echo(((1 - $ticketItem['overridePrice']) * 100) ."% off");
                    }
                    echo('</div>');
                }
                if (!is_null($ticketItem['modificationNotes'])) {
                    $mods = explode(",", $ticketItem['modificationNotes']);

                    foreach ($mods as $modQuickCode) {
                        $sql = "SELECT * FROM MenuModificationItems WHERE quickCode = '" .str_replace("'", "''",$modQuickCode). "'";
                        $modItemRows = connection()->query($sql);
                        if (mysqli_num_rows($modItemRows) == 0) {
                            // custom mod, deleted mod. Treat as custom mod.
                            echo('<div class="modCustom">' .$modQuickCode. '</div>');
                        }
                        else {
                            $modItem = $modItemRows->fetch_assoc();
                            echo('<div class="modText">' .$modItem['title']. '</div>');
                            if (!is_null($modItem['priceOrModificationValue'])) {
                                echo('<div class="modPrice">');
                              
                                if ($modItem['priceOrModificationValue'] < 0) {
                                    // discount applied
                                    echo($modItem['priceOrModificationValue']);
                                }
                                elseif ( $modItem['priceOrModificationValue'] >= 1 ) {
                                     // price set to a value
                                     echo($modItem['priceOrModificationValue']);
                                }
                                elseif ( $modItem['priceOrModificationValue'] == 0 ) {
                                    // free
                                    echo('FREE');
                                }
                                else {
                                    // percent discount applied
                                   echo(((1 - $modItem['priceOrModificationValue']) * 100) ."% off");
                                }
                                echo('</div>');
                            }

                        }
                    }
                }

                echo("</div>");

            }
        }
        else {
            echo("<h1 class='message' id='ticketHeader'>No Ticket/Table Selected</h1>");
            unset($_POST['recordedModificationTime'], $_POST['recordedModificationTime'], $_POST['seat'], $_POST['split'], $_POST['selectedTicketItem']);
            $_POST['enabledButtons'] = "";
        }
        unset($_POST['command'], $_POST['modificationNotes'], $_POST['overrideValue'], $_POST['overrideNote'], $_POST['authorizationUsername'], $_POST['toSeat'], $_POST['toSplit']);
        include 'display.php';
    ?>
    </form>
    <iframe id="ticketListener" frameborder='0' width=100% height=100% src="../../ServerView/ticketListener.php" style="display: none;"></iframe>
    </body>
    </html>
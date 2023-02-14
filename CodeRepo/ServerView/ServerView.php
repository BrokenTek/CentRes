<?php require "../Resources/php/sessionLogic.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStructure.css">
        <script src="../Resources/JavaScript/displayInterface.js"></script>
        <script type="text/javascript">
            var updateLoopTimer;
           
            function startUpdateLoopTimer() {
                updateLoopTimer = setInterval(updateLoop, 250);
            }
            function stopUpdateLoopTimer() {
                clearInterval(updateLoopTimer);
            }

            function updateLoop() {
                stopUpdateLoopTimer();

                // check the loaded "assigned" tables and check against
                // what is being reported by the server listener.
                checkTableAssignments();

                // if a seat and split are selected and the mod window is not open,
                // check if a menu item was selected. otherwise ignore if you clicked a menu item.
                if (cboTable.selectedIndex > 0 && cboSeat.selectedIndex > 0 && cboSplit.selectedIndex > 0) {
                    checkMenuItemSelected();
                }
                else {
                    removeVar("selectedMenuItem", "menuContainer");
                }

                populateSeats();
                populateSplits();

                // check if the selected menu item has changed.
                // if so, this function will trigger stateChanged()
                getSelectedTicketItem();

                //configureView();

                startUpdateLoopTimer();
            }

            function loaded() {
                
                
                // initialize the table listener
                setVar('username', USERNAME, 'serverListener');
                updateDisplay('serverListener');
                checkTableAssignments();

                
                //btnSubmit.addEventListener('pointerUp', submitButtonPressed);
                //btnCancel.addEventListener('pointerUp', cancelButtonPressed);
                //btnEdit.addEventListener('pointerup', editButtonPressed);
                //btnRemove.addEventListener('pointerup', removeButtonPressed);
                //btnAction.addEventListener('pointerup', actionButtonPressed);


                startUpdateLoopTimer();
            }
            addEventListener("load", loaded);

            // check for the server's current table assignments
            function checkTableAssignments() {
                var checkStr;
                try {
                    checkStr = getVar("tableList", "serverListener");
                }
                catch (err) {
                     // due to the async nature of components, some requests might fail
                    setTimeout(checkTableAssignments, 250);
                    return;
                }

                var cboTable = document.querySelector("#cboTable");

                var tablesAdded = [];
                var ticketsAdded = [];
                var tablesRemoved = [];
                var loggedTableElems = cboTable.options;
                var currTables = [];
                
                var checkAgainst = (checkStr == null ? [] : checkStr.split(","));

                for (let i = 0; i < loggedTableElems.length; i++) {
                    currTables.push(loggedTableElems[i].text);
                   // if the server was assigned a table, but was just removed from it
                    if (checkAgainst.indexOf(loggedTableElems[i].id) == -1 &&
                        loggedTableElems[i].id != "selectTable") {
                            tablesRemoved.push(loggedTableElems[i].id);
                    }
                }

                for (var i = 0; i < checkAgainst.length; i+=2) {
                    // if the server has a new table assigned to them
                   if (currTables.indexOf(checkAgainst[i]) == -1) {
                    tablesAdded.push(checkAgainst[i]);
                    ticketsAdded.push(checkAgainst[i+1]);
                   } 
                }

                if (tablesAdded.length > 0 || tablesRemoved.length > 0) {
                    //disable cboTableSelector
                    cboTable.disabled = true;

                    //add all of the new table assignments
                    for (let i = 0; i < tablesAdded.length; i++) {
                        var newTableElem = document.createElement('option');
                        with (newTableElem) {
                            setAttribute("name", "selectedTable");
                            setAttribute("value", ticketsAdded[i]);
                            setAttribute("id", tablesAdded[i]);
                            text = tablesAdded[i];
                        }
                        cboTable.appendChild(newTableElem);
                    }    


                    //get the selected table
                    var selectedTable = (cboTable.selectedIndex) > 0 ? cboTable[cboTable.selectedIndex].text : "";

                    //remove all the tables server is no longer is assigned to
                    for (let i = 0; i < tablesRemoved.length; i++) {
                        // if the table the server is viewing was deleted,
                        // hide the menu and disable all of the controls

                        if (tablesRemoved[i] == selectedTable) {
                            removeVar("ticket", "ticketContainer");
                            updateDisplay("ticketContainer");
                            document.querySelector("#cboSeat").disabled = true;
                            document.querySelector("#cboSeat").innerHTML = "";
                            
                            document.querySelector("#cboSplit").disabled = true;
                            document.querySelector("#cboSplit").innerTML = "";
                            
                            document.querySelector("#btnSubmit").disabled = true;
                            document.querySelector("#btnCancel").disabled = true;
                            document.querySelector("#btnPrintReceipt").disabled = true;
                            
                            document.querySelector("#btnEdit").disabled = true;
                            document.querySelector("#btnRemove").disabled = true;
                            document.querySelector("#btnSplitWith").disabled = true;
                            document.querySelector("#btnMoveTo").disabled = true;
                            document.querySelector("#cboMoveTicketItem").disabled = true;

                        }
                        document.querySelector("#" + tablesRemoved[i]).remove();
                    }

                    //items may no longer be sorted in alphabetical order....
                    //reposition all items to make them sorted
                    cboTable.appendChild(document.querySelector("#selectTable"));
                    for (var i = 0; i < checkAgainst.length; i+=2) {
                        cboTable.appendChild(cboTable.removeChild(document.querySelector("#" + checkAgainst[i])));
                    }

                    if (cboTable.options.length == 1) {
                        document.querySelector("#selectTable").text="No Tables";
                        cboTable.disabled = true;                         
                    }
                    else {
                        document.querySelector("#selectTable").text="Select Table";
                        cboTable.disabled = false;
                    }

                }
                if (cboTable.options.length == 1) {
                    cboTable.text = "No Tables"; 
                    cboTable.disabled = true;                         
                }
                else {
                    cboTable.text = "Select Table";
                    cboTable.disabled = false;
                }
            }
            
            // listen for menu item selection
            function checkMenuItemSelected() {
                var selectedMenuItem;
                try {
                    selectedMenuItem = getVar("selectedMenuItem", "menuContainer");
                }
                catch (err) {
                    setTimeout(checkMenuItemSelected, 250);
                    return;
                }
               
                // if a menu item was selected
                if (selectedMenuItem != null) {
                   
                    // menu item selection acknowledged.
                    removeVar("selectedMenuItem", "menuContainer");
                    
                    // signal the ticketContainer that a menu item was selected and needs to be added to the ticket
    			    setVar('command', 'add', 'ticketContainer');
    			    setVar('menuItem', selectedMenuItem, 'ticketContainer');

                    // scroll down to bottom
                    setVar('scrollY', Number.MAX_SAFE_INTEGER , 'ticketContainer');

                    // make the ticketContaner commit the added item to the database
                    //mitigateMenuFlicker();
                    updateDisplay('ticketContainer');
                }
                
    			
            }
            
            // listen for ticket item selection change
            var lastTicketUpdate = 0;
            var selectedTicketItem = [];
            function getSelectedTicketItem() {
                // listner was reloading at time of request.
                var lookAtTimeStamp;
                var selectedItems;
                try {
                    lookAtTimeStamp = parseInt(getVar("lastUpdate", "ticketContainer"));
                    selectedItems = getVar("selectedTicketItem", "ticketContainer");
                }
                catch (err) {
                    setTimeout(getSelectedTicketItem, 250);
                    return;
                }

                // if the selected ticket item(s) have changed
                if (lookAtTimeStamp > lastTicketUpdate) {
                    lastTicketUpdate = lookAtTimeStamp;
                    // record the changes
                    var ticketContainer = document.getElementById('ticketContainer');
                    
                    
                    // no items are selected
                    if (selectedItems == null) {
                        selectedTicketItem = [];
                    }
                    else { // one or more items are selected
                        selectedTicketItem = selectedItems.split(",");
                    }
                    // configure controls

                    if (selectedItems == null) {
                        //alert("nothing selected");
                    }
                    else {
                        //alert("something selected");
                    }
                   
                }
            }

            function tableSelectionChanged() {
                //no table selected
                if (document.querySelector("#cboTable").selectedIndex == 0) {
                    removeVar("ticket", "ticketContainer");
                   
                    updateDisplay("ticketContainer");                    
                    
                    document.querySelector("#cboSplit").disabled = true;
                    document.querySelector("#cboSplit").innerHTML = "";
                    
                    document.querySelector("#btnSubmit").disabled = true;
                    document.querySelector("#btnCancel").disabled = true;
                    document.querySelector("#btnPrintReceipt").disabled = true;
                    
                    document.querySelector("#btnEdit").disabled = true;
                    document.querySelector("#btnRemove").disabled = true;
                    document.querySelector("#btnSplitWith").disabled = true;
                    document.querySelector("#btnMoveTo").disabled = true;
                    document.querySelector("#cboMoveTicketItem").disabled = true;

                    document.querySelector("#ticketHeaderText").innerHTML = "Ticket:&nbsp;n/a";

                    removeVar("ticket","serverListener");
                    updateDisplay("serverListener");
                    
                    removeVar("ticket","ticketContainer");
                    updateDisplay("ticketContainer");


                    populateSeats();
                    populateSplits();
                }
                else {
                    //mitigateMenuFlicker();
                    setVar("ticket",document.querySelector("#cboTable").value,"serverListener");
                    setTimeout(updateDisplay("serverListener"), 250);
                    
                    setVar("ticket",document.querySelector("#cboTable").value,"ticketContainer");
                    updateDisplay("ticketContainer");

                    populateSeats();
                    populateSplits();

                    document.querySelector("#ticketHeaderText").innerHTML = "Ticket:&nbsp;" + document.querySelector("#cboTable").value;
                }
            }

            function populateSeats() {
                var maxSeat;
                try {
                    maxSeat = getVar("maxSeat", "serverListener");
                }
                catch (err) {
                    setTimeout(populateSeats, 250);
                    return;
                }
                
                var cboSeat = document.querySelector("#cboSeat");
                if (maxSeat == null && cboSeat.options.length > 1) {
                    cboSeat.disabled = true;
                    cboSeat.innerHTML = "<option id='allSeats' name='selectedSeat' value='allSeats'>Seat</option>";
                }
                else if (maxSeat > cboSeat.options.length - 1) {
                    let index = cboSeat.options.length; 
                    for (let i = index; i <= maxSeat; i++) {
                        var newSeatOption = document.createElement('option');
                        with (newSeatOption) {
                            setAttribute("name", "selectedSeat");
                            setAttribute("value", i);
                            setAttribute("id", "seat" + i);
                            text = "Seat " + i;
                        }
                        cboSeat.appendChild(newSeatOption);
                        cboSeat.options[0].text = "All Seats";
                    }
                    cboSeat.disabled = false;
                }
                else if (maxSeat < cboSeat.options.length - 1) {
                    while (maxSeat < cboSeat.options.length - 1) {
                        cboSeat.options[cboSeat.options.length - 1].remove();
                    }
                    if (cboSeat.selectedIndex >= cboSeat.options.length - 2) {
                        //seat is no longer valid
                        updateDisplay("ticketContainer");
                    }
                }                
            }

            function populateSplits() {
                var maxSplit;
                try {
                    maxSplit = getVar("maxSplit", "serverListener");
                }
                catch (err) {
                    setTimeout(populateSplits, 250);
                    return;
                }
                
                var cboSplit = document.querySelector("#cboSplit");
                if (maxSplit == null && cboSplit.options.length > 1) {
                    cboSplit.disabled = true;
                    cboSplit.innerHTML = "<option id='allSplits' name='selectedSplit' value='allSplits'>Split</option>";
                }
                else if (maxSplit > cboSplit.options.length - 1) {
                    let index = cboSplit.options.length; 
                    for (let i = index; i <= maxSplit; i++) {
                        var newSplitOption = document.createElement('option');
                        with (newSplitOption) {
                            setAttribute("name", "selectedSplit");
                            setAttribute("value", i);
                            setAttribute("id", "split" + i);
                            text = "Split " + i;
                        }
                        cboSplit.appendChild(newSplitOption);
                        cboSplit.options[0].text = "All Splits";
                    }
                    cboSplit.disabled = false;
                }
                else if (maxSplit < cboSplit.options.length - 1) {
                    while (maxSplit < cboSplit.options.length - 1) {
                        cboSplit.options[cboSplit.options.length - 1].remove();
                    }
                    if (cboSplit.selectedIndex >= cboSplit.options.length - 2) {
                        //split is no longer valid
                        updateDisplay("ticketContainer");
                    }
                }                
            }

            function selectedSeatChanged() {
                var cboSeat = document.querySelector("#cboSeat");
                if (cboSeat.selectedIndex == 0) {
                    removeVar("seat", "ticketContainer");
                }
                else {
                    setVar("seat",cboSeat.selectedIndex, "ticketContainer");
                }
                updateDisplay("ticketContainer");
            }

            function selectedSplitChanged() {
                var cboSplit = document.querySelector("#cboSplit");
                if (cboSplit.selectedIndex == 0) {
                    removeVar("split", "ticketContainer");
                }
                else {
                    setVar("split",cboSplit.selectedIndex, "ticketContainer");
                }
                updateDisplay("ticketContainer");
            }


            function mitigateMenuFlicker() {
                with (document.querySelector("#ticketFlickerBackdrop")) {
                    classList.remove("hiding");
                    classList.remove("hidden");
                    classList.add("hiding");
                }
            }

            function actionButtonPressed() {
                if (this.id == "btnToSplit") {
                    this.id = "btnToSeat";
                }
                else {
                    alert(this.id);
                }
            }

            

        </script>
        <script src="../InDev/cwpribble.js"></script>
        <script src="../InDev/dbutshudiema.js"></script>
        <script src="../InDev/dlmahan.js"></script>
        <script src="../InDev/kcdine.js"></script>
        <script src="../InDev/sashort.js"></script>
    </head>
    <body>
        <form id="serverViewSession" class="sessionContainer" action="ServerView.php" method="POST">
            <!-- session.php must be included after the opening for tag. It adds  -->
            <?php require "../Resources/php/sessionHeader.php"; ?>
            <div id="serverViewContainer" class="sessionBody">
                <div id="serverViewHeader">
                    <select name="table" id="cboTable" onchange="tableSelectionChanged()">
                        <option value="selectTable" id="selectTable" value="selectTable">Getting Your Tables</option>
                        <!-- options are dynamically added and removed here with JavaScript -->
                    </select>
                    <div id="headerButtonGroup">
                        <button type="button" id="btnSubmit">SUBMIT</button>
                        <button type="button" id="btnCancel">CANCEL</button>
                        <button type="button" id="btnPrintReceipt">PRINT RECIEPT</button>
                    </div>
                </div>
            
                <div id="menuTitle">Menu</div>
            
                <iframe id="menuContainer" frameborder='0' src="menu.php">
                </iframe>
                
                
                <div id="ticketHeader">
                    <div id="ticketHeaderText">Ticket:&nbsp;n/a</div>
                    <select id="cboSeat" name="seatNumber" onchange="selectedSeatChanged()" disabled>
                        <option value="selectSeat" id="selectSeat" value="selectSeat">Seat</option>
                        <!-- options are dynamically added and removed here with JavaScript -->
                    </select>
                    <select id="cboSplit" onchange="selectedSplitChanged()" disabled>
                        <option value="selectSplit" id="selectSplit" value="selectSplit">Split</option>
                        <!-- options are dynamically added and removed here with JavaScript -->
                    </select>
                </div>
                <iframe id="ticketContainer" frameborder='0' src="../Resources/php/ticket.php">
                </iframe>
                <div id="ticketFlickerBackdrop"></div>
                <div>LOADING</div>
                <div id="modsContainer" style='display: none;'>
                    <?php require "loadModsWindow.php"; ?>
                </div>
                <div id="ticketFooter">
                    <button type="button" id="btnEdit">Edit</button>
                    <button type="button" id="btnRemove">Remove</button>
                    <button type="button" id="btnSplitWith">Split With</button>
                    <button type="button" id="btnMoveTo">Move To</button>
                    
                    <select id="cboMoveTicketItem">
                        <option value="">Select Split</option>
                        <option value="Split 1">Split 1</option>
                        <option value="2">Split 2</option>
                        <option value="3">Split 3</option>
                    </select>
                </div>
            </div>
        </form>
        <!-- TEMPORARY buttons to toggle between TICKETCONTAINER and MODSCONTAINER -->
        <div ">
        <br>
        <button type='button' id='getTicketContainer'>View Ticket Container</button>
        <button type='button' id='getModsContainer'>View Mods Container</button>
        <!-- Event Listeners: Currently Only Functions as a toggle between TICKETCONTAINER and MODSCONTAINER -->
        <script src="../Resources/JavaScript/eventListeners.js"> </script>
        
        
        <iframe id="serverListener" src="serverListener.php" style="color: white;">
        </div>
    </body>
</html>
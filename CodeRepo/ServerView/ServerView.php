<?php require "../Resources/php/sessionLogic.php"; ?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStructure.css">
        <script src="../Resources/JavaScript/displayInterface.js"></script>
        <script type="text/javascript">
            // ================ ON LOAD FUNCTIONS =========================
            function loaded() {
                document.querySelector("#btnSubmit").addEventListener('pointerup', (event) => {submitButtonPressed(event)});
                document.querySelector("#btnCancel").addEventListener('pointerup', (event) => {cancelButtonPressed(event)});
                document.querySelector("#btnEdit").addEventListener('pointerup', (event) => {editButtonPressed(event)});
                document.querySelector("#btnRemove").addEventListener('pointerup', (event) => {removeButtonPressed(event)});
                document.querySelector("#btnMove").addEventListener('pointerup', (event) => {moveButtonPressed(event)});
                document.querySelector("#btnSplit").addEventListener('pointerup', (event) => {splitButtonPressed(event)});
                
                setVar('username', USERNAME, 'serverListener');
                updateDisplay('serverListener');
                checkTableAssignments();

                setVar("enabledButtons", "");
                updateButtonStates();

                // initialize the table listener
                

                
                

                startUpdateLoopTimer();
            }
            addEventListener("load", loaded);
            
            // ================ MAIN EVENT LOOP =========================
            var updateLoopTimer;
           
            function startUpdateLoopTimer() {
                updateLoopTimer = setInterval(updateLoop, 250);
            }
            function stopUpdateLoopTimer() {
                clearInterval(updateLoopTimer);
            }

            function updateLoop() {
                stopUpdateLoopTimer();
            
                var cboTable = document.querySelector("#cboTable");
                var cboSeat = document.querySelector("#cboSeat");
                var cboSplit = document.querySelector("#cboSplit");
                var ticketContainer = document.querySelector("#ticketContainer");

                // check the loaded "assigned" tables and check against
                // what is being reported by the server listener.
                if (document.querySelector("#modEditorContainer.active") == null) {
                    checkTableAssignments();
                   
                    getSelectedTicketItem();
                    // if a seat and split are selected and the mod window is not open,
                    // check if a menu item was selected. otherwise ignore if you clicked a menu item.
                    try {
                            if (!(getVar("selectedMenuItem", "menuContainer") === undefined)) {
                                if (cboSeat.selectedIndex == 0 || cboSplit.selectedIndex == 0) {
                                    document.querySelector("#ticketContainer").contentWindow.document.getElementById("ticketHeader").classList.add("highlighted");
                                    setTimeout(() => {
                                    document.querySelector("#ticketContainer").contentWindow.document.getElementById("ticketHeader").classList.remove("highlighted");
                                }, 1100);
                                removeVar("selectedMenuItem", "menuContainer");
                                }
                            }
                        }
                    catch (err) { }
                    populateSeats(cboTable.selectedIndex > 0 && cboSeat.options.length == 1);
                    populateSplits(cboTable.selectedIndex > 0 && cboSplit.options.length == 1); 
                    if (cboTable.selectedIndex > 0 && cboSeat.selectedIndex > 0 && cboSplit.selectedIndex > 0) {
                        checkMenuItemSelected();
                    }
                }
                else { 
                    try {
                        removeVar("selectedMenuItem", "menuContainer");
                    } 
                    catch (err) { }
                    hideModWindow();
                }

                //update check
                var ticketRefresh = false;
                // verify ticket, seats, and splits have loaded... If not, attempt to reload
                try {
                    if (cboTable.selectedIndex > 0 && (getVar("ticket", "ticketContainer") == null )) { 
                        setVar("ticket", cboTable.value, "ticketContainer" );
                        ticketRefresh = true;                    
                    }
                    if (cboTable.selectedIndex > 0 &&
                        ticketContainer.contentWindow.document.getElementById("ticketHeader") != null &&
                        ticketContainer.contentWindow.document.getElementById("ticketHeader").innerText == "No Ticket/Table Selected") {
                            ticketContainer.contentWindow.document.getElementById("ticketHeader").innerText = "Well this is embarrasing!<br>Sit Tight!<br>Fetching Ticket.".
                            setVar("ticket", cboTable.value, "ticketContainer" );
                            ticketRefresh = true;
                    }
                }
                catch (err) { 
                    //ticketContainer.contentWindow.document.getElementById("ticketHeader").innerText = "Attempting to Retrieve Ticket";
                }

                 // verify the seat is set
                try {
                    if (cboTable.selectedIndex > 0 && cboSeat.options.length == 1) { 
                        //alert("seat no load");
                        populateSeats(true);
                    }
                }
                catch (err) {
                    
                }

                 // verify the split is set
                try {
                    if (cboTable.selectedIndex > 0 && cboSplit.options.length < 2) { 
                        //alert("split no load");
                        populateSplits(true);
                    }
                }
                catch (err) {
                    
                }

                if (ticketRefresh) {
                    try {
                        updateDisplay("ticket");
                    }
                    catch(err) {
                        alert("refresh failed");
                     }
                }
                startUpdateLoopTimer();
            }

            function showModWindow() {
                try {
                    stopUpdateLoopTimer();
                    document.querySelector("#ticketContainer").classList.add("clear");
                    selTicket = getVar("selectedTicketItem", "ticketContainer");
                    setVar("selectedItem",selTicket.replace("ticketItem",""), "modEditorContainer");
                    updateDisplay("modEditorContainer");
                    document.querySelector("#modEditorContainer").classList.add("active");
                    document.querySelector("#ticketContainer").classList.add("hidden");
                    document.querySelector("#cboTable").disabled = true;
                    document.querySelector("#cboSeat").disabled = true;
                    document.querySelector("#cboSplit").disabled = true;
                    document.querySelector("#btnSubmit").disabled = true;
                    document.querySelector("#btnCancel").disabled = true;
                    document.querySelector("#btnPrintReceipt").disabled = true;
                    
                    document.querySelector("#btnEdit").disabled = true;
                    document.querySelector("#btnRemove").disabled = true;
                    document.querySelector("#btnSplit").disabled = true;
                    document.querySelector("#btnMove").disabled = true;
                    document.querySelector("#cboMove").disabled = true;
                    startUpdateLoopTimer();
                }
                catch (err) {
                    setTimeout(showModWindow, 500);
                }
            }

            function hideModWindow() {
                try {
                    var status = getVar("status", "modEditorContainer");
                    if (status == 'await') {
                        //setVar("recordedModificationTime", Date.now() + 6000, "ticketContainer");
                        setTimeout(() => {
                        document.querySelector("#ticketContainer").classList.remove("clear")} ,750);
                        setVar("ignoreUpdate", "yes please", "ticketContainer");
                        updateDisplay("ticketContainer");
                        document.querySelector("#modEditorContainer").setAttribute("src", "../Resources/php/modsWindowCARSON.php");
                        document.querySelector("#modEditorContainer").classList.remove("active"); 
                        document.querySelector("#cboTable").removeAttribute("disabled");
                        document.querySelector("#cboSeat").removeAttribute("disabled");
                        document.querySelector("#cboSplit").removeAttribute("disabled");
                        updateButtonStates();
                        document.querySelector("#ticketContainer").classList.remove("hidden");
                    }
                }
                catch (err) {
                    setTimeout(hideModWindow, 250);
                }
            }
            

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
                            document.querySelector("#btnSplit").disabled = true;
                            document.querySelector("#btnMove").disabled = true;
                            document.querySelector("#cboMove").disabled = true;
                            updateButtonStates();

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
                if (!(selectedMenuItem === undefined)) {
                    
                    // menu item selection acknowledged.
                    removeVar("selectedMenuItem", "menuContainer");
                    // signal the ticketContainer that a menu item was selected and needs to be added to the ticket
    			    setVar('command', 'add', 'ticketContainer');
    			    setVar('menuItem', selectedMenuItem, 'ticketContainer');

                    // scroll down to bottom
                    setVar('scrollY', Number.MAX_SAFE_INTEGER , 'ticketContainer');
                    setVar("ignoreUpdate", "Yes please" ,"ticketContainer");
                    updateDisplay('ticketContainer');

                    // make the ticketContaner commit the added item to the database
                    //mitigateMenuFlicker();
                    //setVar("ignoreUpdate", "ticketContainer");
                    //document.querySelector("#ticketContainer").classList.remove("clear");

                    showTicketContainer();
                    
                    
                    
                }	
                
            }

            function showTicketContainer() {
                try {
                    getVar("ticket", "ticketContainer");
                    document.querySelector("#ticketContainer").classList.remove("clear");
                }
                catch (err) {
                    try {
                        setTimeout(showTicketContainer, 250);
                    }
                    catch (err) {
                        alert("menu load failed");
                    }
                }
            }
            

            function updateButtonStates() {
                try {
                    var updatedButtons = getVar("enabledButtons", "ticketContainer");
                    setVar("enabledButtons", updatedButtons);
                    document.querySelector("#btnSubmit").disabled = updatedButtons.indexOf("Submit") == -1;
                    document.querySelector("#btnCancel").disabled = updatedButtons.indexOf("Cancel") == -1;
                    document.querySelector("#btnEdit").disabled = updatedButtons.indexOf("Edit") == -1;
                    document.querySelector("#btnSubmit").disabled = updatedButtons.indexOf("Submit") == -1;
                    document.querySelector("#btnRemove").disabled = updatedButtons.indexOf("Remove") == -1;
                    document.querySelector("#btnMove").disabled = updatedButtons.indexOf("Move") == -1;
                    document.querySelector("#btnSplit").disabled = updatedButtons.indexOf("Split") == -1;

                    document.querySelector("#btnRemove").disabled = updatedButtons.indexOf("Remove") == -1;
                    document.querySelector("#btnRemove").disabled = updatedButtons.indexOf("Remove") == -1;
                    document.querySelector("#btnRemove").disabled = updatedButtons.indexOf("Remove") == -1;
                    document.querySelector("#cboMove").disabled = updatedButtons.indexOf("Move") == -1 && updatedButtons.indexOf("Split");
                }
                catch (err) {
                    setTimeout(updateButtonStates, 250);
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
                    updateButtonStates();
                   
                }
            }

            function tableSelectionChanged() {
                //no table selected
                removeVar("selectedTicketItem", "ticketContainer");
                updateButtonStates();
            
                setVar("enabledButtons", "");
                var cboSeat = document.querySelector("#cboSeat");
                var cboSplit = document.querySelector("#cboSplit");
                if (document.querySelector("#cboTable").selectedIndex == 0) {
                    
                    cboSeat.disabled = true;
                    cboSeat.options[0].text = "Seat";

                    cboSplit.disabled = true;
                    cboSplit.options[0].text = "Split";
                    
                    document.querySelector("#btnSubmit").disabled = true;
                    document.querySelector("#btnCancel").disabled = true;
                    document.querySelector("#btnPrintReceipt").disabled = true;
        
                    document.querySelector("#btnEdit").disabled = true;
                    document.querySelector("#btnRemove").disabled = true;
                    document.querySelector("#btnSplit").disabled = true;
                    document.querySelector("#btnMove").disabled = true;
                    document.querySelector("#cboMove").disabled = true;

                    document.querySelector("#ticketHeaderText").innerHTML = "Ticket:&nbsp;n/a";
                    document.querySelector("#cboMove").innerHTML = "";

                    removeVar("ticket", "ticketContainer");
                    //setVar("ignoreUpdate", "ticketContainer");
                    updateDisplay("ticketContainer"); 
                    
                    removeVar("ticket","serverListener");
                    updateDisplay("serverListener");
                   
                                       
                }
                else {
                    setVar("ticket",document.querySelector("#cboTable").value,"serverListener");
                    try {
                        updateDisplay("serverListener");
                        setVar("ticket",document.querySelector("#cboTable").value,"ticketContainer");
                        updateDisplay("ticketContainer");
                    }
                    catch (err) {
                        alert("Fail");
                    }
                    document.querySelector("#ticketContainer").classList.add("clear");
                    setTimeout(() => {
                        document.querySelector("#ticketContainer").classList.remove("clear")} ,1500
                    );
                                      
                    //setVar("ignoreUpdate", "ticketContainer");

                    document.querySelector("#ticketHeaderText").innerHTML = "-&nbsp;-&nbsp;-";
                    
                    cboSeat.disabled = false;
                    cboSeat.options[0].text = "All Seats";

                    cboSplit.disabled = false;
                    cboSplit.options[0].text = "All Splits";
                    
                    populateSeats(true);
                    populateSplits(true);

                    setTimeout(() => {
                    var cboSeat = document.querySelector("#cboSeat");
                    var cboSplit = document.querySelector("#cboSplit");
                    if (cboSplit.options.length == 3 && cboSeat.options.length == 3) {
                        setVar("seat",1,"ticketContainer");
                        setVar("split",1,"ticketContainer");
                        cboSeat.selectedIndex = 1;
                        cboSplit.selectedIndex = 1;
                    }
                    else {
                        removeVar("seat","ticketContainer");
                        removeVar("split","ticketContainer");
                        cboSeat.selectedIndex = 0;
                        cboSplit.selectedIndex = 0;
                        document.querySelector("#ticketHeaderText").innerHTML = "Ticket:&nbsp;" + document.querySelector("#cboTable").value;
                    }
                   }, 1250);

                }
                
                
                
            }

            function populateSeats(forceReset = false) {
                var maxSeat;
                try {
                    maxSeat = getVar("maxSeat", "serverListener");
                }
                catch (err) {
                    setTimeout(populateSeats, 250);
                    return;
                }
                var changed = false;
                var cboSeat = document.querySelector("#cboSeat");
                if (forceReset) {
                    if (maxSeat == null) {
                        setTimeout(() => {
                            populateSeats(true);
                        }, 250);
                        return;
                    }
                    cboSeat.innerHTML = "<option id='allSeats' name='selectedSeat' value='allSeats'>Seat</option>";
                    for (let i = 1; i <= maxSeat; i++) {
                        var newSeatOption = document.createElement('option');
                        with (newSeatOption) {
                            setAttribute("name", "selectedSeat");
                            setAttribute("value", i);
                            setAttribute("id", "seat" + i);
                            text = "Seat " + i;
                        }
                        cboSeat.appendChild(newSeatOption);
                        return;
                    }
                }
                if (maxSeat == null && cboSeat.options.length > 1) {
                    cboSeat.innerHTML = "<option id='allSeats' name='selectedSeat' value='allSeats'>Seat</option>";
                    changed = true;
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
                        changed = true;
                    }
                }
                else if (maxSeat < cboSeat.options.length - 1) {
                    while (maxSeat < cboSeat.options.length - 1) {
                        cboSeat.options[cboSeat.options.length - 1].remove();
                        changed = true;
                    }
                    if (cboSeat.selectedIndex >= cboSeat.options.length - 2) {
                        //seat is no longer valid
                        updateDisplay("ticketContainer");
                    }
                }            
            }

            function populateSplits(forceReset = false) {
                var maxSplit;
                try {
                    maxSplit = getVar("maxSplit", "serverListener");
                    if (maxSplit == 0) {
                        maxSplit = 10;
                    }
                }
                catch (err) {
                    setTimeout(populateSplits, 250);
                    return;
                }
                var changed = false;
                var cboSplit = document.querySelector("#cboSplit");
                if (forceReset) {
                    if (maxSplit == null) {
                        setTimeout(() => {
                            populateSplits(true);
                        }, 250);
                        return;
                    }
                    cboSplit.innerHTML = "<option id='allSplits' name='selectedSplit' value='allSplits'>Split</option>";
                    for (let i = 1; i <= maxSplit; i++) {
                        var newSplitOption = document.createElement('option');
                        with (newSplitOption) {
                            setAttribute("name", "selectedSplit");
                            setAttribute("value", i % 10);
                            setAttribute("id", "split" + (i % 10));
                            text = "Split " + (i % 10);
                        }
                        cboSplit.appendChild(newSplitOption);
                        return;
                    }
                }
                if (maxSplit == null && cboSplit.options.length > 1) {
                    cboSplit.innerHTML = "<option id='allSplits' name='selectedSplit' value='allSplits'>Split</option>";
                    changed = true;
                }
                else if (maxSplit > cboSplit.options.length - 1) {
                    let index = cboSplit.options.length; 
                    for (let i = index; i <= maxSplit; i++) {
                        var newSplitOption = document.createElement('option');
                        with (newSplitOption) {
                            setAttribute("name", "selectedSplit");
                            setAttribute("value", (i % 10));
                            setAttribute("id", "split" + (i % 10));
                            text = "Split " + (i % 10);
                        }
                        cboSplit.appendChild(newSplitOption);
                        changed = true;
                    }
                }
                else if (maxSplit < cboSplit.options.length - 1) {
                    while (maxSplit < cboSplit.options.length - 1) {
                        cboSplit.options[cboSplit.options.length - 1].remove();
                        changed = true;
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
                updateButtonStates();
            }

            function selectedSplitChanged() {
                var cboSplit = document.querySelector("#cboSplit");
                if (cboSplit.selectedIndex == 0) {
                    removeVar("split", "ticketContainer");
                }
                else if (cboSplit.selectedIndex < 10)  {
                    setVar("split",cboSplit.selectedIndex, "ticketContainer");
                }
                else {
                    setVar("split",0, "ticketContainer");
                }
                updateDisplay("ticketContainer");
                updateButtonStates();
            }

            // =========================== BUTTON PRESS EVENTS =====================================

            function submitButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') {
                    return;
                }
                alert("Submit");

            }

            function cancelButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') {
                    return;
                }
                alert("Cancel");

            }

            function editButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') {
                    return;
                }
                showModWindow();
            }

            function removeButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') {
                    return;
                }
                alert("Remove");
                getVar()

            }

            function moveButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') {
                    return;
                }
                alert("Move");

            }

            function splitButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') {
                    return;
                }
                alert("Split");
                
            }


             //btnSubmit.addEventListener('pointerUp', submitButtonPressed);
                //btnCancel.addEventListener('pointerUp', cancelButtonPressed);
                //btnEdit.addEventListener('pointerup', editButtonPressed);
                //btnRemove.addEventListener('pointerup', removeButtonPressed);
                //btnAction.addEventListener('pointerup', actionButtonPressed);

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
                <iframe id="modEditorContainer" frameborder='0' width="100%" height="100%" src="../Resources/php/modsWindowCARSON.php">
                </iframe>
                <div id="ticketFlickerBackdrop"></div>
                <div>LOADING</div>
                <div id="modsContainer" style='display: none;'>
                    <?php require "loadModsWindow.php"; ?>
                </div>
                <div id="ticketFooter">
                    <div></div>
                    <button type="button" id="btnEdit" disabled>Edit</button>
                    <button type="button" id="btnRemove" disabled>Remove</button>
                    <button type="button" id="btnSplit" disabled>Split With</button>
                    <button type="button" id="btnMove" disabled>Move To</button>
                    
                    <select id="cboMove" disabled></select>
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
        
        
        <iframe id="serverListener" src="serverListener.php" style="display: none;">
        </div>
    </body>
</html>
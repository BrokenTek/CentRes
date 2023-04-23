<?php require_once "../Resources/PHP/sessionLogic.php"; restrictAccess(2, $GLOBALS['role']); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>CentRes: Host Station</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStructure.css">
        <script src="../Resources/JavaScript/display.js"></script>
        <script type="text/javascript">
            var cboTable;
            var cboSeat;
            var cboSplit;
            var ticketContainer;
            var menuContainer;
            var modEditorContainer;
            var btnClose;
            var btnDeliver;
            var btnSubmit;
            var btnCancel;
            var btnEdit;
            var btnRemove;
            var btnMove;
            var btnSplit;
            var ticketHeader;

            // ================ ON LOAD FUNCTIONS =========================
            function loaded() {
                // if a manager is logged in here, allow them to select/remove items in the ticket
                if (ROLE & 8) {
                    varSet("isManager", "true","ticketContainer");
                    updateDisplay("ticketContainer");
                }
                
                cboTable = document.getElementById("cboTable");
                cboSeat = document.getElementById("cboSeat");
                cboSplit = document.getElementById("cboSplit");
                ticketContainer = document.getElementById("ticketContainer");
                modEditorContainer = document.getElementById("modEditorContainer");
                menuContainer = document.getElementById("menuContainer");
                btnClose = document.getElementById("btnClose");
                btnDeliver = document.getElementById("btnDeliver"); 
                btnSubmit = document.getElementById("btnSubmit");
                btnCancel = document.getElementById("btnCancel");
                btnEdit = document.getElementById("btnEdit");
                btnRemove = document.getElementById("btnRemove");
                btnMove = document.getElementById("btnMove");
                btnSplit = document.getElementById("btnSplit");
                ticketHeader = document.getElementById("ticketHeaderText");

                btnClose.addEventListener('pointerup', (event) => {closeButtonPressed(event)});
                btnDeliver.addEventListener('pointerup', (event) => {deliverButtonPressed(event)});
                btnSubmit.addEventListener('pointerup', (event) => {submitButtonPressed(event)});
                btnCancel.addEventListener('pointerup', (event) => {cancelButtonPressed(event)});
                btnEdit.addEventListener('pointerup', (event) => {editButtonPressed(event)});
                btnRemove.addEventListener('pointerup', (event) => {removeButtonPressed(event)});
                btnMove.addEventListener('pointerup', (event) => {moveButtonPressed(event)});
                btnSplit.addEventListener('pointerup', (event) => {splitButtonPressed(event)});
                
                varSet('username', USERNAME, 'serverListener', true);
                let staticTableId = varGet('staticTableId');
                if (staticTableId !== undefined) {
                    varSet('staticTableId', staticTableId, 'serverListener', true);
                }
                
                checkTableAssignments();

                varSet("enabledButtons", "");
                updateButtonStates();

                startUpdateLoopTimer();

                ignoreUpdates = false;

                document.querySelector("#btnHideMessage").addEventListener('click', hideAlertMsg);
                
                setTitle("CentRes POS: Server Window", "Server Window");

            }
            addEventListener("load", loaded);

            function hideAlertMsg() {
                document.querySelector("#alertDiv").classList.remove('visible');
                recAddedTables = [];
                recRemovedTables = [];
            }
            
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

                try {
                    if (varExists("staticTableId") && varGetOnce("ticketRemoved","ticketContainer") !== undefined) {
                        with (document.querySelector("#btnHideMessage")) {
                            removeEventListener('click', hideAlertMsg);
                            addEventListener('click', function() {
                                location.replace(document.getElementById("mgrNavHostView").getAttribute("value"));
                            });  
                        } 
                        showAlertDiv("Ticket " + varGet("ticket", "ticketContainer") + " is no longer assigned to this table!<br>Redirecting back to Host Station.");
                        return;
                    }
                }
                catch (err) {  
                    startUpdateLoopTimer();
                }
            
               

                // check the loaded "assigned" tables and check against
                // what is being reported by the server listener.
                if (document.querySelector("#modEditorContainer.active") == null) {
                    checkTableAssignments();
                   
                    getSelectedTicketItem();
                    // if a seat and split are selected and the mod window is not open,
                    // check if a menu item was selected. otherwise ignore if you clicked a menu item.
                    try {
                        if (varExists("selectedMenuItem", "menuContainer")) {
                            if (cboSeat.selectedIndex == 0 || cboSplit.selectedIndex == 0) {
                                varRem("selectedMenuItem", "menuContainer");
                                with ( ticketContainer.contentWindow.document.getElementById("ticketHeader")) {
                                    classList.add("highlighted");
                                    scrollIntoView(true)
;                                    setTimeout(() => {
                                        classList.remove("highlighted");
                                    }, 1100);
                                }                            
                            }
                        }
                    }
                    catch (err) { }
                    var tick;
                    var seat;
                    var split;
                    try {
                        tick = varGet("ticket", "ticketContainer");
                        seat = varGet("seat", "ticketContainer");
                        split = varGet("split", "ticketContainer");
                        populateSeats((cboTable.selectedIndex > 0 || tick != null) && cboSeat.options.length == 1);
                        populateSplits((cboTable.selectedIndex > 0 || tick != null) && cboSplit.options.length == 1);
                        if (cboTable.selectedIndex > 0 && cboSeat.selectedIndex > 0 && cboSplit.selectedIndex > 0) {
                            checkMenuItemSelected();
                        }
                        if (seat != null && cboSeat.selectedIndex != seat) {
                            cboSeat.selectedIndex = seat;
                        }
                        if (split != null && cboSplit.selectedIndex != split) {
                            cboSplit.selectedIndex = split;
                        }
                    }
                    catch (err) {
                        setTimeout(updateLoop, 250);
                    }
                }
                else { 
                    try {
                        varRem("selectedMenuItem", "menuContainer");
                    } 
                    catch (err) { }
                    hideModWindow();
                }
                if (cboTable.selectedIndex == 0) {
                    cboSeat.options[0].text = "Seat";
                    cboSplit.options[0].text = "Split";
                }
                else {
                    cboSeat.options[0].text = "All Seats";
                    cboSplit.options[0].text = "All Splits";
                }

                //update check
                var ticketRefresh = false;
                // verify ticket, seats, and splits have loaded... If not, attempt to reload
                try {
                    if (cboTable.selectedIndex > 0 && (varGet("ticket", "ticketContainer") == null )) { 
                        varSet("ticket", cboTable.value, "ticketContainer" );
                        ticketRefresh = true;                    
                    }
                    if (cboTable.selectedIndex > 0 &&
                        ticketContainer.contentWindow.document.getElementById("ticketHeader") != null &&
                        ticketContainer.contentWindow.document.getElementById("ticketHeader").innerText == "No Ticket/Table Selected") {
                            ticketContainer.contentWindow.document.getElementById("ticketHeader").innerText = "Well this is embarrasing!<br>Sit Tight!<br>Fetching Ticket.".
                            varSet("ticket", cboTable.value, "ticketContainer" );
                            ticketRefresh = true;
                    }
                }
                catch (err) { 
                    //ticketContainer.contentWindow.document.getElementById("ticketHeader").innerText = "Attempting to Retrieve Ticket";
                }

                 // verify the seat is set
                try {
                    if (cboTable.selectedIndex > 0 && cboSeat.options.length == 1) { 
                        populateSeats(true);
                    }
                }
                catch (err) {
                    
                }

                 // verify the split is set
                try {
                    if (cboTable.selectedIndex > 0 && cboSplit.options.length < 2) { 
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
                    }
                }
                updateButtonStates();
                startUpdateLoopTimer();
            }

            function showModWindow() {
                try {
                    stopUpdateLoopTimer();
                    ticketContainer.classList.add("clear");
                    selTicket = varGet("selectedTicketItem", "ticketContainer");
                    varSet("selectedTicketItemId",selTicket.replace("ticketItem",""), "modEditorContainer", true);
                    updateDisplay("modEditorContainer", true);
                    modEditorContainer.classList.add("active");
                    ticketContainer.classList.add("hidden");
                    cboTable.disabled = true;
                    cboSeat.disabled = true;
                    cboSplit.disabled = true;
                    btnSubmit.disabled = true;
                    btnCancel.disabled = true;
                    
                    btnEdit.disabled = true;
                    btnRemove.disabled = true;
                    btnSplit.disabled = true;
                    btnMove.disabled = true;

                    btnMove.classList.remove("toggled");
                    btnSplit.classList.remove("toggled");
                    startUpdateLoopTimer();
                }
                catch (err) {
                    setTimeout(showModWindow, 500);
                }
            }

            function hideModWindow() {
                try {
                    var status = varGet("status", "modEditorContainer");
                    if (status == 'await' && modEditorContainer.classList.contains("active")) {
                        modEditorContainer.classList.remove("active"); 
                        //varSet("recordedModificationTime", Date.now() + 6000, "ticketContainer");
                        setTimeout(() => { ticketContainer.classList.remove("clear")} ,750);
                        varSet("ignoreUpdate", "yes please", "ticketContainer", true);
                        modEditorContainer.setAttribute("src", "modsWindow.php");
                        cboTable.removeAttribute("disabled");
                        cboSeat.removeAttribute("disabled");
                        cboSplit.removeAttribute("disabled");
                        updateButtonStates();
                        ticketContainer.classList.remove("hidden");
                        updateButtonStates(true);
                    }
                }
                catch (err) {
                    setTimeout(hideModWindow, 250);
                }
            }
            
            var recAddedTables = [];
            var recRemovedTables = [];
            // check for the server's current table assignments
            function checkTableAssignments() {
                var checkStr;
                try {
                    checkStr = varGet("tableList", "serverListener");
                }
                catch (err) {
                     // due to the async nature of components, some requests might fail
                    return;
                }

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
                            cboTable.selectedIndex = 0;
                            varRem("ticket", "ticketContainer");
                            varRem("seat", "ticketContainer");
                            varRem("split", "ticketContainer");
                            tableSelectionChanged();
                            updateDisplay("ticketContainer");
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
                        if (varGet("seat", "ticketContainer") != null) {
                            varRem("ticket", "ticketContainer");
                            varRem("seat", "ticketContainer");
                            varRem("split", "ticketContainer");
                            tableSelectionChanged();
                            updateDisplay("ticketContainer");
                        }
                        cboTable.disabled = true;                     
                    }
                    else {
                        document.querySelector("#selectTable").text="Select Table";
                        cboTable.disabled = false;
                    }

                }
                if (cboTable.options.length == 1) {
                    document.querySelector("#selectTable").text = "No Tables"; 
                    
                    if (varGet("seat", "ticketContainer") != null) {
                        varRem("ticket", "ticketContainer");
                        varRem("seat", "ticketContainer");
                        varRem("split", "ticketContainer");
                        tableSelectionChanged();
                        updateDisplay("ticketContainer");
                    }
                    cboTable.disabled = true;                         
                }
                else if (cboTable.options.length == 2) {
                    cboTable.text = "Select Table";
                    cboTable.disabled = true;
                    if (cboTable.selectedIndex != 1) {
                        cboTable.selectedIndex = 1;
                        tableSelectionChanged();
                    }
                }
                else {
                    cboTable.text = "Select Table";
                    cboTable.disabled = false;
                }

                if (tablesAdded.length > 0 || tablesRemoved.length > 0) {
                    for (let i = 0; i < tablesAdded.length; i++) {
                        let append = recRemovedTables.indexOf(tablesAdded[i]) == -1;
                        recRemovedTables = recRemovedTables.filter(e => e !== tablesAdded[i]);
                        if (append) { recAddedTables.push(tablesAdded[i]); }
                        
                    }
                    for (let i = 0; i < tablesRemoved.length; i++) {
                        let append = recAddedTables.indexOf(tablesRemoved[i]) == -1;
                        recAddedTables = recAddedTables.filter(e => e !== tablesRemoved[i]);
                        if (append) { recRemovedTables.push(tablesRemoved[i]); }
                    }
                                        
                    let addedStr = "";
                    for (let i = 0; i < recAddedTables.length; i++) {
                        addedStr += "," + recAddedTables[i];    
                    }
                    
                    let removedStr = "";
                    for (let i = 0; i < recRemovedTables.length; i++) {
                        removedStr += "," + recRemovedTables[i];    
                    }
                    
                    let msg = "Your table assignments have changed.<hr><br>" +
                        (addedStr.length > 0 ? "Tables Added: " + addedStr.substring(1) + "<br>" : "") +
                        (removedStr.length > 0 ? "Tables Removed: " + removedStr.substring(1) + "<br>" : "");
                    
                    if (recAddedTables.length > 0 || recRemovedTables.length > 0 ) {
                        showAlertDiv(msg);
                    }
                    else {
                        document.querySelector("#alertDiv").classList.remove('visible');
                    }
                }
                if (!loaded) {
                    recAddedTables = [];
                    recRemovedTables = [];
                }
                loaded = true;
            }
            
            // listen for menu item selection
            function checkMenuItemSelected() {
                if (varXfrRen("selectedMenuItem", "menuContainer", "menuItem", "ticketContainer")) {
                    varSet('command', 'add', 'ticketContainer');
                    varSet('scrollY', Number.MAX_SAFE_INTEGER, 'ticketContainer');
                    varSet("ignoreUpdate", "Yes please" ,"ticketContainer");
                    showTicketContainer();
                    updateDisplay("ticketContainer");
                }                
            }

            function showTicketContainer() {
                try {
                    varGet("ticket", "ticketContainer");
                    updateDisplay("ticketContainer");
                    ticketContainer.classList.remove("clear");
                    updateButtonStates();
                }
                catch (err) {
                    try {
                        setTimeout(showTicketContainer, 250);
                    }
                    catch (err) {
                        showAlertDiv("menu load failed");
                    }
                }
            }
            

            function updateButtonStates(forceUpdate = false) {
                try {                   
                    if (varCpy("enabledButtons", "ticketContainer", null, false, true) || forceUpdate) {
                        var updatedButtons = varGet("enabledButtons");
                        if (updatedButtons === undefined) {updatedButtons = '';}
                        btnClose.disabled = updatedButtons.indexOf("Close") == -1;
                        btnDeliver.disabled = updatedButtons.indexOf("Deliver") == -1; 
                        btnSubmit.disabled = updatedButtons.indexOf("Submit") == -1;
                        btnCancel.disabled = updatedButtons.indexOf("Cancel") == -1;
                        btnEdit.disabled = updatedButtons.indexOf("Edit") == -1;
                        btnSubmit.disabled = updatedButtons.indexOf("Submit") == -1;
                        btnRemove.disabled = updatedButtons.indexOf("Remove") == -1;
                        btnMove.disabled = updatedButtons.indexOf("Move") == -1;
                        btnSplit.disabled = updatedButtons.indexOf("Split") == -1;

                        if (btnMove.disabled) {
                            btnMove.classList.remove("toggled");
                        }
                        if (btnSplit.disabled) {
                            btnSplit.classList.remove("toggled");
                        }
                    }
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
                    lookAtTimeStamp = parseInt(varGet("lastUpdate", "ticketContainer"));
                    selectedItems = varGet("selectedTicketItem", "ticketContainer");
                }
                catch (err) {
                    setTimeout(getSelectedTicketItem, 250);
                    return;
                }

                // if the selected ticket item(s) have changed
                if (lookAtTimeStamp > lastTicketUpdate) {
                    lastTicketUpdate = lookAtTimeStamp;
                    // record the changes
 
                    
                    // no items are selected
                    if (selectedItems == null) {
                        selectedTicketItem = [];
                    }
                    else { // one or more items are selected
                        selectedTicketItem = selectedItems.split(",");
                    }
                    // configure controls

                    
                    updateButtonStates();
                   
                }
            }

            function tableSelectionChanged() {
                //no table selected
                varRem("selectedTicketItem", "ticketContainer");
                varRem("ticket", "ticketContainer");
                varRem("seat","ticketContainer");
                varRem("split","ticketContainer");
                updateButtonStates();
            
                varSet("enabledButtons", "");
                if (cboTable.selectedIndex == 0) {
                    
                    cboSeat.disabled = true;
                    cboSeat.options[0].text = "Seat";

                    cboSplit.disabled = true;
                    cboSplit.options[0].text = "Split";
                    
                    btnSubmit.disabled = true;
                    btnCancel.disabled = true;
        
                    btnEdit.disabled = true;
                    btnRemove.disabled = true;
                    btnSplit.disabled = true;
                    btnMove.disabled = true;

                    populateSeats(true);
                    populateSplits(true);

                    btnMove.classList.remove("toggled");
                    btnSplit.classList.remove("toggled");

                    ticketHeader.innerHTML = "Ticket:&nbsp;n/a";

                    updateDisplay("ticketContainer");

                    //varSet("ignoreUpdate", "ticketContainer");
                   
                                       
                }
                else {
                    
                    try {
                        varSet("ticket",cboTable.value,"serverListener", true);
                        varSet("ticket",cboTable.value,"ticketContainer");
                        varSet("ignoreUpdate", "yes please", "ticketContainer", true);
                    }
                    catch (err) {
                        
                    }
                    
                    updateButtonStates();
                    ticketHeader.innerHTML = "-&nbsp;-&nbsp;-";
                    
                    var seat = varGet("seat", "ticketContainer");
                    var split = varGet("split", "ticketContainer");

                    cboSeat.disabled = false;
                    cboSplit.disabled = false;
                    
                    cboSeat.selectedIndex = (seat == null ? 0 : seat);
                    cboSplit.selectedIndex = (split == null ? 0 : split);
                   
                    ticketHeader.innerHTML = "Ticket:&nbsp;" + cboTable.value;
                    
                }
                
                
                
                
            }

            function populateSeats(forceReset = false) {
                var maxSeat;
                try {
                    maxSeat = varGet("maxSeat", "serverListener");
                }
                catch (err) {
                    setTimeout( function() {populateSeats(forceReset); }, 250);
                    return;
                }
                var changed = false;
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
                        varRem("seat", "ticketContainer", true);
                    }
                }            
            }

            function populateSplits(forceReset = false) {
                var maxSplit;
                try {
                    maxSplit = varGet("maxSplit", "serverListener");
                    if (maxSplit == 0) {
                        maxSplit = 10;
                    }
                }
                catch (err) {
                    setTimeout( function() {populateSplits(forceReset); }, 250);
                    return;
                }
                var changed = false;
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
                try {
                    var toggledControl = document.getElementsByClassName("toggled");
                    var prevSel = varGet("seat", "ticketContainer");
                    var selSplit = varGet("split", "ticketContainer");
                    var command = "";
                    if (toggledControl.length == 1 && cboSeat.selectedIndex > 0) {
                        let selItems = ticketContainer.contentWindow.document.getElementsByClassName("selected");
                        let str = "";
                        for (let i = 0; i < selItems.length; i++) {
                            str += "," + selItems[i].id;
                        }
                        str = str.replaceAll("ticketItem","").substring(1);
                        command = "moveToSeat";
                        varSet("ignoreUpdate", "yes please", "ticketContainer");
                        varSet("command", command, "ticketContainer");
                        varSet("ticketItem", str, "ticketContainer");
                        varSet("toSeat", cboSeat.selectedIndex, "ticketContainer");   
                    }
                    if (prevSel == null && selSplit == null && command != "") {
                        cboSeat.selectedIndex = 0;
                    }
                    else {
                        if (cboSeat.selectedIndex == 0) {
                            varRem("seat", "ticketContainer", true);
                        }
                        else {
                            varSet("seat",cboSeat.selectedIndex, "ticketContainer", true);
                        }
                        
                    }
                    updateDisplay("ticketContainer");
                    
                    setTimeout(updateButtonStates, 1000);

                    cboSeat.disabled = false;
                    cboSplit.disabled = false;
                    btnMove.classList.remove("toggled");
                }
                catch (err) {
                    setTimeout(selectedSeatChanged, 250);
                }
            }

            function selectedSplitChanged() {
                try {
                    var toggledControl = document.getElementsByClassName("toggled");
                    var prevSel = varGet("split", "ticketContainer");
                    var selSeat = varGet("seat", "ticketContainer");
                    var command = "";
                    if (toggledControl.length == 1 && cboSplit.selectedIndex > 0) {
                        let selItems = ticketContainer.contentWindow.document.getElementsByClassName("selected");
                        let str = "";
                        for (let i = 0; i < selItems.length; i++) {
                            str += "," + selItems[i].id;
                        }
                        str = str.replaceAll("ticketItem","").substring(1);
                        
                        if (toggledControl[0].id == "btnMove") {
                            command = "moveToSplit";
                            varSet("command", command, "ticketContainer");
                            let fromSplit = varGet("split", "ticketContainer");
                            if (fromSplit == null) {
                                varSet("fromSplit", 10, "ticketContainer");
                            }
                            else {
                                varSet("fromSplit", prevSel, "ticketContainer");
                            }
                            
                        }
                        else {
                            command = "addToSplit";
                            varSet("command", command, "ticketContainer");
                        }
                        varSet("toSplit", (cboSplit.selectedIndex < 10 ? cboSplit.selectedIndex : 0), "ticketContainer");
                        varSet("ticketItem", str, "ticketContainer");
                        varSet("ignoreUpdate", "yes please", "ticketContainer");

                        btnMove.classList.remove("toggled");
                        btnSplit.classList.remove("toggled");
                        cboSeat.disabled = false;
                    }
                    if (prevSel == null && selSeat == null && command != "") {
                        cboSplit.selectedIndex = 0;
                    }
                    else {
                        if (cboSplit.selectedIndex == 0) {
                            varRem("split", "ticketContainer", true);
                        }
                        else if (cboSplit.selectedIndex < 10)  {
                            varSet("split",cboSplit.selectedIndex, "ticketContainer", true);
                        }
                        else {
                            varSet("split",0, "ticketContainer", true);
                        }
                    }
                    updateDisplay("ticketContainer");
                    
                    setTimeout(updateButtonStates, 250);

                   
                }
                catch (err) {
                    setTimeout(selectedSplitChanged, 250);
                }
            }

            // =========================== BUTTON PRESS EVENTS =====================================

            function closeButtonPressed(e){
                if (e.target.getAttribute("disabled") == '') { return; }
                varSet("ignoreUpdate", "yes please", "ticketContainer");
                varSet("command", "close", "ticketContainer", true);
                updateButtonStates();
            }

            function deliverButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                let deliverString = "";
                with (document.getElementById("ticketContainer").contentWindow.document.querySelectorAll(".ready")) {
                    for (let i = 0; i < length; i++) {
                        deliverString += "," + item(i).id;
                    }
                    varSet("ignoreUpdate", "yes please", "ticketContainer");
                    varSet("command", "deliver", "ticketContainer");
                    varSet("ticketItem",deliverString.substring(1).replaceAll("ticketItem",""),"ticketContainer", true);
                }
                updateButtonStates();
            }

            function submitButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                varSet("ignoreUpdate", "yes please", "ticketContainer");
                varSet("command", "submitPending" ,"ticketContainer", true);
                updateButtonStates();
            }

            function cancelButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                varSet("ignoreUpdate", "yes please", "ticketContainer");
                varSet("command", "cancelPending" ,"ticketContainer", true);
                updateButtonStates();
            }

            function editButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                showModWindow();
                cboSeat.disabled = true;
                cboSplit.disabled = true;
                btnMove.classList.remove("toggled");
                btnSplit.classList.remove("toggled");
                updateButtonStates();
            }

            function removeButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                let str = "";
                let selItems = ticketContainer.contentWindow.document.getElementsByClassName("selected");
                for (let i = 0; i < selItems.length; i++) {
                    str += "," + selItems[i].id;
                }
                str = str.replaceAll("ticketItem","").substring(1);
                //varSet("ignoreUpdate", "yes please", "ticketContainer");

               
                varSet("command", "remove", "ticketContainer");
                varSet("ticketItem", str, "ticketContainer");
                varSet("ignoreUpdate", "yes please", "ticketContainer", true);

                cboSeat.disabled = false;
                cboSplit.disabled = false;
                updateButtonStates();
            }

            function moveButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                btnSplit.classList.remove("toggled");
                btnMove.classList.toggle("toggled");

                cboSeat.disabled = false;
                updateButtonStates();        
            }

            function splitButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                btnMove.classList.remove("toggled");
                btnSplit.classList.toggle("toggled");

                cboSeat.disabled = (document.getElementsByClassName("toggled").length == 1);

                updateButtonStates();
            }

            function showAlertDiv(message) {
                document.querySelector("#alertMessage").innerHTML = message;
                document.querySelector("#alertDiv").classList.add("visible");
            }

        </script>
    </head>
    <body class="alertWrapper">
        <form id="sessionForm" action="ServerView.php" method="POST">
            
            <?php require_once "../Resources/PHP/sessionHeader.php"; ?>
            <div id="sessionBody">
                <div id="serverViewHeader">
                    <select name="table" id="cboTable" onchange="tableSelectionChanged()">
                        <option value="selectTable" id="selectTable" value="selectTable">Getting Your Tables</option>
                        <!-- options are dynamically added and removed here with JavaScript -->
                    </select>
                    <div id="headerButtonGroup">
                        <button type="button" id="btnClose" disabled>CLOSE TICKET</button>
                        <button type="button" id="btnDeliver" disabled>DELIVER</button>
                        <button type="button" id="btnSubmit" disabled>SUBMIT</button>
                        <button type="button" id="btnCancel" disabled>CANCEL</button>
                    </div>
                </div>
            
                <div id="menuTitle">Menu</div>
            
                <iframe id="menuContainer" frameborder='0' src="menu.php"></iframe>
                
                
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
                <iframe id="ticketContainer" frameborder='0' src="ticket.php"></iframe>
                <iframe id="modEditorContainer" frameborder='0' width="100%" height="100%" src="modsWindow.php"></iframe>
                <div id="ticketFlickerBackdrop"></div>
                <div id="ticketFooter">
                    <div></div>
                    <button type="button" id="btnEdit" disabled>Edit</button>
                    <button type="button" id="btnRemove" disabled>Remove</button>
                    <button type="button" id="btnSplit" disabled>Split With</button>
                    <button type="button" id="btnMove" disabled>Move To</button>
                    
                </div>
                
            </div>
            <?php require_once '../Resources/PHP/display.php'; ?>
           
            
        </form>
        <div id="alertDiv">
            <div id="alertBox" style="border-radius: 1rem;">
                <div id="alertMessage">
                    <!-- content dynamically inserted and removed here -->
                </div>
                <div id="alertButtonContainer">
                    <button class="button" id="btnHideMessage">OK</button>
                </div>
            </div>
        </div>
        <iframe id="serverListener" src="../Resources/PHP/serverListener.php" style="display: none;">
    </body>
</html>
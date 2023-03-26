<?php require_once "../Resources/php/sessionLogic.php"; restrictAccess(2, $GLOBALS['role']); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>CentRes: Host View</title>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStructure.css">
        <script src="../Resources/JavaScript/displayInterface.js"></script>
        <script type="text/javascript">
            var cboTable;
            var cboSeat;
            var cboSplit;
            var ticketContainer;
            var menuContainer;
            var modEditorContainer;
            var btnSubmit;
            var btnCancel;
            var btnEdit;
            var btnRemove;
            var btnMove;
            var btnSplit;
            var ticketHeader;

            // ================ ON LOAD FUNCTIONS =========================
            function loaded() {
                cboTable = document.getElementById("cboTable");
                cboSeat = document.getElementById("cboSeat");
                cboSplit = document.getElementById("cboSplit");
                ticketContainer = document.getElementById("ticketContainer");
                modEditorContainer = document.getElementById("modEditorContainer");
                menuContainer = document.getElementById("menuContainer");
                btnSubmit = document.getElementById("btnSubmit");
                btnCancel = document.getElementById("btnCancel");
                btnEdit = document.getElementById("btnEdit");
                btnRemove = document.getElementById("btnRemove");
                btnMove = document.getElementById("btnMove");
                btnSplit = document.getElementById("btnSplit");
                ticketHeader = document.getElementById("ticketHeaderText");

                btnSubmit.addEventListener('pointerup', (event) => {submitButtonPressed(event)});
                btnCancel.addEventListener('pointerup', (event) => {cancelButtonPressed(event)});
                btnEdit.addEventListener('pointerup', (event) => {editButtonPressed(event)});
                btnRemove.addEventListener('pointerup', (event) => {removeButtonPressed(event)});
                btnMove.addEventListener('pointerup', (event) => {moveButtonPressed(event)});
                btnSplit.addEventListener('pointerup', (event) => {splitButtonPressed(event)});
                
                setVar('username', USERNAME, 'serverListener', true);
                let staticTableId = getVar('staticTableId');
                if (staticTableId !== undefined) {
                    setVar('staticTableId', staticTableId, 'serverListener', true);
                }
                
                checkTableAssignments();

                setVar("enabledButtons", "");
                updateButtonStates();

                startUpdateLoopTimer();

                ignoreUpdates = false;
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

                if (getVar("staticTableId") !== undefined && getVarOnce("ticketRemoved","ticketContainer") !== undefined) {
                    alert("Ticket " + getVar("ticket", "ticketContainer") + " is not longer assigned to this table!\nRedirecting back to Host View.");
                    location.replace(document.getElementById("mgrNavHostView").getAttribute("value"));
                }
            
               

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
                                    ticketContainer.contentWindow.document.getElementById("ticketHeader").classList.add("highlighted");
                                    setTimeout(() => {
                                    ticketContainer.contentWindow.document.getElementById("ticketHeader").classList.remove("highlighted");
                                }, 1100);
                                removeVar("selectedMenuItem", "menuContainer");
                                }
                            }
                        }
                    catch (err) { }
                    var tick;
                    var seat;
                    var split;
                    try {
                        tick = getVar("ticket", "ticketContainer");
                        seat = getVar("seat", "ticketContainer");
                        split = getVar("split", "ticketContainer");
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
                        removeVar("selectedMenuItem", "menuContainer");
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
                startUpdateLoopTimer();
            }

            function showModWindow() {
                try {
                    stopUpdateLoopTimer();
                    ticketContainer.classList.add("clear");
                    selTicket = getVar("selectedTicketItem", "ticketContainer");
                    setVar("selectedItem",selTicket.replace("ticketItem",""), "modEditorContainer", true);
                    modEditorContainer.classList.add("active");
                    ticketContainer.classList.add("hidden");
                    cboTable.disabled = true;
                    cboSeat.disabled = true;
                    cboSplit.disabled = true;
                    btnSubmit.disabled = true;
                    btnCancel.disabled = true;
                    document.querySelector("#btnPrintReceipt").disabled = true;
                    
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
                    var status = getVar("status", "modEditorContainer");
                    if (status == 'await' && modEditorContainer.classList.contains("active")) {
                        modEditorContainer.classList.remove("active"); 
                        //setVar("recordedModificationTime", Date.now() + 6000, "ticketContainer");
                        setTimeout(() => { ticketContainer.classList.remove("clear")} ,750);
                        setVar("ignoreUpdate", "yes please", "ticketContainer", true);
                        modEditorContainer.setAttribute("src", "../Resources/php/modsWindowCARSON.php");
                        cboTable.removeAttribute("disabled");
                        cboSeat.removeAttribute("disabled");
                        cboSplit.removeAttribute("disabled");
                        updateButtonStates();
                        ticketContainer.classList.remove("hidden");
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
                            removeVar("ticket", "ticketContainer");
                            removeVar("seat", "ticketContainer");
                            removeVar("split", "ticketContainer");
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
                        if (getVar("seat", "ticketContainer") != null) {
                            removeVar("ticket", "ticketContainer");
                            removeVar("seat", "ticketContainer");
                            removeVar("split", "ticketContainer");
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
                    
                    if (getVar("seat", "ticketContainer") != null) {
                        removeVar("ticket", "ticketContainer");
                        removeVar("seat", "ticketContainer");
                        removeVar("split", "ticketContainer");
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
                    let msg = "Your table assignments have changed.\n\n";
                    if (tablesAdded.length > 0) {
                        var addedStr = tablesAdded[0];
                        for (let i = 1; i < tablesAdded.length; i++) {
                            addedStr += "," + tablesAdded[i];
                        }
                        msg = "Tables Added: " + addedStr;
                    }
                    if (tablesRemoved.length > 0) {
                        var removedStr = tablesRemoved[0];
                        for (let i = 1; i < tablesRemoved.length; i++) {
                            removedStr += "," + tablesRemoved[i];
                        }
                        msg += (msg == "" ? "" : '\n') + "Tables Removed: " + removedStr;
                    }
                    alert(msg);
                }
                loaded = true;
            }
            
            // listen for menu item selection
            function checkMenuItemSelected() {
                if (varXfrRen("selectedMenuItem", "menuContainer", "menuItem", "ticketContainer")) {
                    setVar('command', 'add', 'ticketContainer');
                    setVar('scrollY', Number.MAX_SAFE_INTEGER , 'ticketContainer');
                    setVar("ignoreUpdate", "Yes please" ,"ticketContainer");
                    showTicketContainer();
                    updateDisplay("ticketContainer");
                }                
            }

            function showTicketContainer() {
                try {
                    getVar("ticket", "ticketContainer");
                    ticketContainer.classList.remove("clear");
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
                    if (btnSPlit.disabled) {
                        btnSplit.classList.remove("toggled");
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
                removeVar("selectedTicketItem", "ticketContainer");
                removeVar("ticket", "ticketContainer");
                removeVar("seat","ticketContainer");
                removeVar("split","ticketContainer");
                updateButtonStates();
            
                setVar("enabledButtons", "");
                if (cboTable.selectedIndex == 0) {
                    
                    cboSeat.disabled = true;
                    cboSeat.options[0].text = "Seat";

                    cboSplit.disabled = true;
                    cboSplit.options[0].text = "Split";
                    
                    btnSubmit.disabled = true;
                    btnCancel.disabled = true;
                    document.querySelector("#btnPrintReceipt").disabled = true;
        
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

                    //setVar("ignoreUpdate", "ticketContainer");
                   
                                       
                }
                else {
                    
                    try {
                        setVar("ticket",cboTable.value,"serverListener", true);
                        setVar("ticket",cboTable.value,"ticketContainer");
                        setVar("ignoreUpdate", "yes please", "ticketContainer", true);
                    }
                    catch (err) {
                        
                    }
                    
                    updateButtonStates();
                    ticketHeader.innerHTML = "-&nbsp;-&nbsp;-";
                    
                    var seat = getVar("seat", "ticketContainer");
                    var split = getVar("split", "ticketContainer");

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
                    maxSeat = getVar("maxSeat", "serverListener");
                }
                catch (err) {
                    setTimeout(populateSeats, 250);
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
                        removeVar("seat", "ticketContainer", true);
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
                    var prevSel = getVar("seat", "ticketContainer");
                    var selSplit = getVar("split", "ticketContainer");
                    var command = "";
                    if (toggledControl.length == 1 && cboSeat.selectedIndex > 0) {
                        let selItems = ticketContainer.contentWindow.document.getElementsByClassName("selected");
                        let str = "";
                        for (let i = 0; i < selItems.length; i++) {
                            str += "," + selItems[i].id;
                        }
                        str = str.replaceAll("ticketItem","").substring(1);
                        command = "moveToSeat";
                        setVar("ignoreUpdate", "yes please", "ticketContainer");
                        setVar("command", command, "ticketContainer");
                        setVar("ticketItem", str, "ticketContainer");
                        setVar("toSeat", cboSeat.selectedIndex, "ticketContainer");   
                    }
                    if (prevSel == null && selSplit == null && command != "") {
                        cboSeat.selectedIndex = 0;
                    }
                    else {
                        if (cboSeat.selectedIndex == 0) {
                            removeVar("seat", "ticketContainer", true);
                        }
                        else {
                            setVar("seat",cboSeat.selectedIndex, "ticketContainer", true);
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
                    var prevSel = getVar("split", "ticketContainer");
                    var selSeat = getVar("seat", "ticketContainer");
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
                            setVar("command", command, "ticketContainer");
                            let fromSplit = getVar("split", "ticketContainer");
                            if (fromSplit == null) {
                                setVar("fromSplit", 10, "ticketContainer");
                            }
                            else {
                                setVar("fromSplit", prevSel, "ticketContainer");
                            }
                            
                        }
                        else {
                            command = "addToSplit";
                            setVar("command", command, "ticketContainer");
                        }
                        setVar("toSplit", (cboSplit.selectedIndex < 10 ? cboSplit.selectedIndex : 0), "ticketContainer");
                        setVar("ticketItem", str, "ticketContainer");
                        setVar("ignoreUpdate", "yes please", "ticketContainer");

                        btnMove.classList.remove("toggled");
                        btnSplit.classList.remove("toggled");
                        cboSeat.disabled = false;
                    }
                    if (prevSel == null && selSeat == null && command != "") {
                        cboSplit.selectedIndex = 0;
                    }
                    else {
                        if (cboSplit.selectedIndex == 0) {
                            removeVar("split", "ticketContainer", true);
                        }
                        else if (cboSplit.selectedIndex < 10)  {
                            setVar("split",cboSplit.selectedIndex, "ticketContainer", true);
                        }
                        else {
                            setVar("split",0, "ticketContainer", true);
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

            function submitButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                setVar("ignoreUpdate", "yes please", "ticketContainer");
                setVar("command", "submitPending" ,"ticketContainer", true);
            }

            function cancelButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                setVar("ignoreUpdate", "yes please", "ticketContainer");
                setVar("command", "cancelPending" ,"ticketContainer", true);
            }

            function editButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                showModWindow();
                cboSeat.disabled = true;
                cboSplit.disabled = true;
                btnMove.classList.remove("toggled");
                btnSplit.classList.remove("toggled");
            }

            function removeButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                let str = "";
                let selItems = ticketContainer.contentWindow.document.getElementsByClassName("selected");
                for (let i = 0; i < selItems.length; i++) {
                    str += "," + selItems[i].id;
                }
                str = str.replaceAll("ticketItem","").substring(1);
                //setVar("ignoreUpdate", "yes please", "ticketContainer");

                setVar("ignoreUpdate", "yes please", "ticketContainer");
                setVar("command", "remove", "ticketContainer");
                setVar("ticketItem", str, "ticketContainer", true);

                cboSeat.disabled = false;
                cboSplit.disabled = false;

            }

            function moveButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                btnSplit.classList.remove("toggled");
                btnMove.classList.toggle("toggled");

                cboSeat.disabled = false;        
            }

            function splitButtonPressed(e) {
                if (e.target.getAttribute("disabled") == '') { return; }
                btnMove.classList.remove("toggled");
                btnSplit.classList.toggle("toggled");

               
                cboSeat.disabled = (document.getElementsByClassName("toggled").length == 1);
            }
        </script>
        <script src="../InDev/cwpribble.js"></script>
        <script src="../InDev/dbutshudiema.js"></script>
        <script src="../InDev/dlmahan.js"></script>
        <script src="../InDev/kcdine.js"></script>
        <script src="../InDev/sashort.js"></script>
    </head>
    <body>
        <form id="sessionForm" action="ServerView.php" method="POST">
            <!-- session.php must be included after the opening for tag. It adds  -->
            <?php require_once "../Resources/php/sessionHeader.php"; ?>
            <div id="sessionBody">
                <div id="serverViewHeader">
                    <select name="table" id="cboTable" onchange="tableSelectionChanged()">
                        <option value="selectTable" id="selectTable" value="selectTable">Getting Your Tables</option>
                        <!-- options are dynamically added and removed here with JavaScript -->
                    </select>
                    <div id="headerButtonGroup">
                        <button type="button" id="btnSubmit">SUBMIT</button>
                        <button type="button" id="btnCancel">CANCEL</button>
                        <button type="button" id="btnPrintReceipt" style="display: none;">PRINT RECIEPT</button>
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
                <div id="ticketFooter">
                    <div></div>
                    <button type="button" id="btnEdit" disabled>Edit</button>
                    <button type="button" id="btnRemove" disabled>Remove</button>
                    <button type="button" id="btnSplit" disabled>Split With</button>
                    <button type="button" id="btnMove" disabled>Move To</button>
                    
                </div>
            </div>
            <?php require_once '../Resources/php/display.php'; ?>
        </form>
        <iframe id="serverListener" src="../Resources/php/serverListener.php" style="display: none;">
        </div>
    </body>
</html>
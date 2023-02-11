<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStructure.css">
        <script src="../Resources/JavaScript/displayInterface.js"></script>
        <script type="text/javascript">
            var updateLoopTimer;
            function stateChanged() {
                //alert("State Changed");
            }

            function startUpdateLoopTimer() {
                updateLoopTimer = setInterval(updateLoop, 250);
            }
            function stopUpdateLoopTimer() {
                clearInterval(updateLoopTimer);
            }

            function updateLoop() {
                stopUpdateLoopTimer();

                // check if the servers tables have changed.
                    // remember the currently selectd table
                    //

                // if a seat and split are selected, check if a menu item was selected.
                // otherwise ignore if you clicked a menu item.
                if (true) {
                    checkMenuItemSelected();
                }
                else {
                    removeDisplayVariable("menuContainer", selectedMenuItem);
                }

                // check if the selected menu item has changed.
                // if so, this function will trigger stateChanged()
                getSelectedTicketItem();

                startUpdateLoopTimer();
            }

            function loaded() {
                
                
                // initialize the table listener
                setDisplayVariable('username', USERNAME, 'tableListener');
                updateDisplay('tableListener');

                startUpdateLoopTimer();
            }
            addEventListener("load", loaded);
            
            // listen for menu item selection
            function checkMenuItemSelected() {
                var selectedMenuItem = getDisplayVariable("selectedMenuItem", "menuContainer");
               
                // if a menu item was selected
                if (selectedMenuItem != null) {
                   
                    // menu item selection acknowledged.
                    removeDisplayVariable("selectedMenuItem", "menuContainer");
                    
                    // signal the ticketContainer that a menu item was selected and needs to be added to the ticket
    			    setDisplayVariable('command', 'add', 'ticketContainer');
    			    setDisplayVariable('menuItem', selectedMenuItem, 'ticketContainer');

                    // testing variables REMOVE WEHN CONTROLS HAVE BEEN FULLY IMPLEMENTED
                    setDisplayVariable('ticket', 1, 'ticketContainer');
    			    setDisplayVariable('seat', 1, 'ticketContainer');
    			    setDisplayVariable('split', 1, 'ticketContainer');

                    // scroll down to bottom
                    setDisplayVariable('scrollY', Number.MAX_SAFE_INTEGER , 'ticketContainer');

                    // make the ticketContaner commit the added item to the database
                    updateDisplay('ticketContainer');
                }
                
    			
            }
            
            // listen for ticket item selection change
            var lastTicketUpdate = 0;
            var selectedTicketItem = [];
            function getSelectedTicketItem() {
                var lookAtTimestamp = parseInt(getDisplayVariable("lastUpdate", "ticketContainer"));
                // if the selected ticket item(s) have changed
                if (lookAtTimestamp > lastTicketUpdate) {
                    lastTicketUpdate = lookAtTimestamp;
                    // record the changes
                    var ticketContainer = document.getElementById('ticketContainer');
                    var selectedItems = getDisplayVariable("selectedTicketItem", "ticketContainer");
                    
                    // no items are selected
                    if (selectedItems == null) {
                        selectedTicketItem = [];
                    }
                    else { // one or more items are selected
                        selectedTicketItem = selectedItems.split(",");
                    }
                    stateChanged();
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
            <?php require "../Resources/php/session.php"; ?>
            <div id="serverViewContainer" class="sessionBody">
                <div id="serverViewHeader">
                <select name="table" id="cboTable">
                <option value="volvo">Volvo</option>
                <option value="saab">Saab</option>
                <option value="mercedes">Mercedes</option>
                <option value="audi">Audi</option>
            </select>
                    <select id="cboSeat" name="seatNumber" onchange="stateChanged()">
                        <option value="">Select Seat</option>
                        <option value="1">Table 1</option>
                        <option value="2">Table 2</option>
                        <option value="3">Table 3</option>
                    </select>
                    <div id="headerButtonGroup">
						<button type="button" id="btnSubmit">SUBMIT</button>
                        <button type="button" id="btnCancel">CANCEL</button>
                        <button type="button id="btnPrintReceipt">PRINT RECIEPT</button>
                    </div>
                </div>
                
                    <div id="menuTitle">Menu</div>
                
                    <iframe id="menuContainer" frameborder='0' width=100% height=100% src="menu.php">
                    </iframe>
                 
                    <div id="ticketHeader">
                    <div id="ticketHeaderText">Ticket&nbsp;Number&nbsp;Goes&nbsp;Here</div>
                    <select id="cboSplit">
                        <option value=1>1</option>
                        <option value=2>2</option>
                        <option value=3>3</option>
                        <option value=4>4</option>
                        <option value=5>5</option>
                        <option value=6>6</option>
                        <option value=7>7</option>
                    </select>
                    </div>
                    <iframe id="ticketContainer" frameborder='0' width=100% height=100% src="../Resources/php/ticket.php">
                    </iframe>
                    <div id="modsContainer" style='display: none;'>
                    <?php require "loadModsWindow.php"; ?>
                    </div>
                    <div id="ticketFooter">
                        <button type="button">Edit</button>
                        <div></div>
                        <button type="button">Move To</button>
                        <button type="button">Split With</button>
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
        <br>
        <button type='button' id='getTicketContainer'>View Ticket Container</button>
        <button type='button' id='getModsContainer'>View Mods Container</button>
        <!-- Event Listeners: Currently Only Functions as a toggle between TICKETCONTAINER and MODSCONTAINER -->
        <script src="../Resources/JavaScript/eventListeners.js"> </script>
        
        
        <iframe id="tableListener" src="tableListener.php" ">
    </body>
</html>
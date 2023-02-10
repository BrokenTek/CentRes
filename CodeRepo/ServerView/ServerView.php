<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStructure.css">
        <script src="../Resources/JavaScript/displayInterface.js"></script>
        <script type="text/javascript">
            function maxSeatNumber() { return 100; }
            function createMenuSelectEventHandlers() {}
            function selectMenuItem(id) {}
            function selectTicketItem() {}
            function moveTicketItem() {}
            function removeTicketItem() {}
            function stateChanged() {}
            function editTicketItem() {}
            function configureModificationWindow() {}
            function updateTicketItem() {}
            function templateFunction(a) { alert("You override the template function in your file"); }

            function reloadPage() {
                document.getElementById("form-container").submit();
            }

            function maxSplitNumber() {
                return 9;
            }

            function stateChanged() {
                alert("State Changed");
            }

            addEventListener("load", loadListeners);
            function loadListeners() {
                document.getElementById("ticketContainer").addEventListener("selectedTicketItemChanged", stateChanged);
                setDisplayVariable('username', USERNAME, 'tableListener');
                updateDisplay('tableListener');
            }


            var selectedTicketItem = null;
            function getSelectedTicketItem() {
                alert("S");
                var ticketContainer = document.getElementById('ticketContainer');
                var selectedItems = ticketContainer.contentWindow.document.getElementsByClassName('selected');
                var newSel = null;
                if (selectedItems.length == 1) {
                    newSel = selectedItems[0];
                }
                if (selectedTicketItem != newSel) {
                    selectedTicketItem = newSel;
                    if (newSel == null) {
                        removeDisplayVariable('selectedTicketItem', id)
                    } else {
                        setDisplayVariable('selectedTicketItem', id), "ticketContainer")
                        alert("Selected");
                    }
                }
            }
            var updateSelTick = setInterval(getSelectedTicketItem, 5000);

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
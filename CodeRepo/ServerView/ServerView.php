<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/serverStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/serverStructure.css">
        <script type="text/javascript">
            function templateFunction( a ) { 
            	alert("You override the template function in your file"); 
            }
            
            function reloadPage() {
            	document.getElementById("form-container").submit();
            }

            function configureTicketIframe() {
                alert('a');
                setTicketDisplayVariable('wow', 32);
                setTicketDisplayVariable('a', 1);
                setTicketDisplayVariable('b', 2);
                setTicketDisplayVariable('c', 3);
                setTicketDisplayVariable('d', 4);
                alert();
                removeTicketDisplayVariable('wow');
                clearTicketDisplayVariables();
                
            }

            function setTicketDisplayVariable(variableName, value) {
                var ticketContainer = document.getElementById('ticketContainer');
                var ticketForm = ticketContainer.contentWindow.document.getElementById('ticketForm');
                var variableElement = ticketContainer.contentWindow.document.getElementById(variableName);
                
                if (variableElement != null) {
                    variableElement.remove();
                }
                
                variableElement = document.createElement('input');
                variableElement.setAttribute('type', 'hidden');
                variableElement.setAttribute('class', 'variable');
                variableElement.setAttribute('id', variableName);
                variableElement.setAttribute('name', variableName);
                variableElement.setAttribute('value', value);

                ticketForm.appendChild(variableElement);

                updateTicketDisplay();
            }

            function removeTicketDisplayVariable(variableName, value) {
                var ticketContainer = document.getElementById('ticketContainer');
                var ticketForm = ticketContainer.contentWindow.document.getElementById('ticketForm');
                var variableElement = ticketContainer.contentWindow.document.getElementById(variableName);
                
                if (variableElement != null) {
                    variableElement.remove();
                }
            }

            function clearTicketDisplayVariables() {
                var ticketContainer = document.getElementById('ticketContainer');
                var ticketForm = ticketContainer.contentWindow.document.getElementById('ticketForm');
                var vars = ticketForm.getElementsByClassName('variable');
                for(var i = vars.length - 1; i >= 0; i--)
                {
                    vars[i].remove();
                }
            }

            function updateTicketDisplay() {
                var ticketContainer = document.getElementById('ticketContainer');
                var ticketForm = ticketContainer.contentWindow.document.getElementById('ticketForm');
                ticketForm.submit();
            }

            

            
            function maxSeatNumber() { return 100; }
            function maxSplitNumber() { return 9; }
            function createMenuSelectEventHandlers() {}
            function selectMenuItem( id ) {}
            function selectTicketItem() {alert("select");}
            function moveTicketItem() {}
            function removeTicketItem() {}
            function stateChanged() {}
            function editTicketItem() {}
            function configureModificationWindow() {}
            function updateTicketItem() {}

            
           
            
        </script>
        <script src="../InDev/cwpribble.js"></script>
        <script src="../InDev/dbutshudiema.js"></script>
        <script src="../InDev/dlmahan.js"></script>
        <script src="../InDev/kcdine.js"></script>
        <script src="../InDev/sashort.js"></script>
        <script src="../InDev/OVERRIDEEXAMPLE.js"></script>
        <script>templateFunction("Hello World");</script>
    <body>
        <form id="serverViewSession" class="sessionContainer" action="ServerView.php" method="POST">
            <!-- session.php must be included after the opening for tag. It adds  -->
            <?php require "../Resources/php/session.php"; ?>
            <div id="serverViewContainer" class="sessionBody">
                <div id="serverViewHeader">
                    <select id="cboTable" name="tableNumber" onchange="reloadPage()">
                        <option value="">Select Table</option>
                        <option value="1">Table 1</option>
                        <option value="2">Table 2</option>
                        <option value="3">Table 3</option>
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
                    <div id="menuContainer">
                    <iframe id="menuContainer" frameborder='0' width=100% height=100% src="menu.php">
                    </iframe>
                    </div>
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
                    <iframe id="ticketContainer" frameborder='0' width=100% height=100% src="ticket.php">
                    </iframe>
                    <div id="modsContainer" style='display: none;'>
                    <?php require "loadModsWindow.php"; ?>
                    </div>
                    <div id="ticketFooter">
                        <button type="button">Edit</button>
                        <div></div>
                        <button type="button" onclick="">Move To</button>
                        <button type="button" onclick="">Split With</button>
                        <select id="cboMoveTicketItem">
                            <option value="">Select Split</option>
                            <option value="Split 1">Split 1</option>
                            <option value="2">Split 2</option>
                            <option value="3" style="background-color: red;">Split 3</option>
                        </select>
                    </div>
                
            </div>
        </form>
        <!-- TEMPORARY buttons to toggle between TICKETCONTAINER and MODSCONTAINER -->
        <br>
        <button type='button' id='getTicketContainer'>View Ticket Container</button>
        <button type='button' id='getModsContainer'>View Mods Container</button>
        <button type='button' onclick="configureTicketIframe()">Test</button>
        <!-- Event Listeners: Currently Only Functions as a toggle between TICKETCONTAINER and MODSCONTAINER -->
        <script src="../Resources/JavaScript/eventListeners.js"> </script>
        
        

    </body>
</html>
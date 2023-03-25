<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']); ?>

<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
    <link rel="stylesheet" href="../Resources/CSS/ticketStyle.css">
    <style>
        #sessionContainer {
            height: 95%;
            width: 100%;
		}
        #sessionBody {
            
            display: grid;
            grid-template-areas: ". tabHeader    tabHeader             ."
                                 ". masterResets selectedMnuItmOptions ."
                                 ". tblInventory tblInventory          .";
            grid-template-columns: 1fr min-content max-content 1fr;
            grid-template-rows: min-content min-content min-content;
            background-color: black;
            color: white;
            padding-bottom: 1rem;
        }
        #tabHeader {
            grid-area: tabHeader;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 1rem auto 3rem auto;
            border-bottom: .25rem solid white;
        }
        #fstMasterResets {
            grid-area: masterResets;
            display: grid;
            grid-template-columns: min-content;
            grid-auto-rows: min-content;
            border: .125rem solid white;
        }
        #fstSelMenuItmOptions {
            grid-area: selectedMnuItmOptions;
            
            border: .125rem solid white;
        }
        #tblInventory {
            margin-top: 2rem;
            grid-area: tblInventory;
        }
        .conflict{
            background-color:yellow;
        }
        
        #numQty {
            max-width: 7rem;
        }

        
    </style>
        <!-- gives you access to setVar, getVar, removeVar, 
        clearVars, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script>
        

        <script>
            function tableHeaderClicked() {
                let tableId = document.getElementsByTagName("table")[0].id;
                toggleSortKey(tableId, this.getAttribute("sqlColumnId"));
            }
            function buttonClicked(){
                setVar("command", this.getAttribute("command"));
                updateDisplay();
            }

            //=========================FUNCTIONS INVOLVING MENU ITEM SELECTION============================
            const LONG_TIME_TOUCH_LENGTH = 250;
            var targetMenuItem = null;
            var longTouchEnabled = false;
            var longTouchTimer = null;
	        function pointerDown() {
                if (this === undefined || this.classList.contains('disabled')) { return; }
                targetMenuItem = this;
                targetMenuItem.classList.add("selected");
                if (getVar("selectedItem") != null && getVar("selectedItem") != this.id) {
                    longTouchTimer = setTimeout(longTouch, LONG_TIME_TOUCH_LENGTH);
                }
	        }

            // if you pressed on a menu item, you already have another one selected, and the minimum required time
            // for multiselect has elapsed, change the selected item to "multiselect" 
            function longTouch() {
                longTouchEnabled = true;
                targetMenuItem.classList.add("multiselect");

                // if there is exactly 1 other item selected, make it multi-select as well.
                var alreadySelected = getVar("selectedItem");
                if (alreadySelected != null && alreadySelected.indexOf(",") == -1) {
                    document.getElementById(alreadySelected).classList.add("multiselect");
                }
            }

            function pointerUp() {
                if (targetMenuItem == null) { return; }
        
                    if (longTouchTimer != null) {
                        clearTimeout(longTouchTimer);
                    }

                var oldSelectedItems = document.getElementsByClassName("menuItem");
                // if you only have 1 item selected, adjust the state of applicable menu items to reflect that.
                if (!longTouchEnabled) {
    	            /*this iterates through the list returned, if there is no case where multiple items are selected concurrently,
    	            you can just use oldSelectedItems[0].classList.remove("selected"); instead*/
    	            for(let i = 0; i < oldSelectedItems.length; i++){
        	            oldSelectedItems[i].classList.remove("selected");
                        oldSelectedItems[i].classList.remove("multiselect");
    	            }
    	            targetMenuItem.classList.add("selected");
                    targetMenuItem.classList.remove("multiselect");
                    setVar("selectedItem", targetMenuItem.id);
                }
                // or you have multiple items selected
                else {
                    setVar("selectedItem", getVar("selectedItem") + "," + targetMenuItem.id); 
                }

                targetMenuItem = null;
                longTouchEnabled = false;
                setQtyBoxState();
            }
            function setQtyBoxState(){
                let sameQuantity = true;
                let anyUntracked = false;
                let anyTracked = false;
                let quantityToCompare = -1;
                let itemsToCheck = getVar("selectedItem");
                let updateInput = document.getElementById("numQty")
                let checkBox = document.getElementById("chkQtyTracked");
                if(itemsToCheck == null){
                    document.getElementById("chkQtyTracked").checked = false;
                    updateInput.disabled = true;
                    updateInput.placeholder = "None";
                    return;
                }
                itemsToCheck = itemsToCheck.split(',');
                for(let i= 0; i < itemsToCheck.length; i++){
                    let theItem = document.getElementById(itemsToCheck[i]);
                    if(theItem.getElementsByClassName("qty")[0].innerText==""){
                        anyUntracked = true;
                    }
                    else{
                        anyTracked = true;
                        if(quantityToCompare == -1){
                            quantityToCompare = parseInt(theItem.getElementsByClassName("qty")[0].innerText);
                        }
                        else if(quantityToCompare != parseInt(theItem.getElementsByClassName("qty")[0].innerText)) {sameQuantity = false;}
                    }
                }
                
                document.getElementById("chkQtyTracked").checked = anyTracked;
                if(checkBox.checked != anyTracked){
                    checkbox.change();
                }
                updateInput.disabled = !anyTracked;

                if(sameQuantity&&quantityToCompare != -1){
                    updateInput.placeholder = "qty";
                    updateInput.value = quantityToCompare;
                }
                else{
                    updateInput.placeholder = "Multiple";
                }
                if(anyUntracked){
                    updateInput.placeholder = "Untracked";
                    updateInput.value=null;
                }
                if(anyUntracked && anyTracked){
                    updateInput.classList.add("conflict");
                    updateInput.placeholder = "Multiple";
                }

            }
            //functions to execute once the body has loaded
            function allElementsLoaded(){
                //add the unicode characters to table headers with sort keys.
                let tableHeaders = document.getElementsByTagName("th");
                if(getVar('tblInventory_SortKey1')!=null){
                    let keyIndex = 1;
                    let unicodeBase = 9311;
                    let keyPrefix = 'tblInventory_SortKey';
                    while(getVar(keyPrefix + keyIndex)!= null){
                        let keyToScan = getVar(keyPrefix + keyIndex);
                        for(let i = 0; i < tableHeaders.length; i++){
                            if(keyToScan.indexOf(tableHeaders[i].getAttribute("sqlColumnId"))!= -1){
                                tableHeaders[i].innerText = String.fromCharCode(unicodeBase + keyIndex) +tableHeaders[i].innerText;
                                if(getVar(keyPrefix + keyIndex).indexOf("ASC")!=-1){
                                    tableHeaders[i].innerText = tableHeaders[i].innerText +"\u25B2";
                                }
                                else{
                                    tableHeaders[i].innerText = tableHeaders[i].innerText +"\u25BC";
                                }
                            }
                        }
                        keyIndex++;
                    }

                }
                              
                
                //select any items that were selected before refresh
                let selItems = getVar("selectedItem");
                if (getVar("selectedItem") !== undefined) {
                    selItems = selItems.split(",");
                    for( let i = 0; i < selItems.length; i++) {
                        document.getElementById(selItems[i]).classList.add("selected");
                    }
                    setQtyBoxState();
                }

                //add all the event listeners
                for(let i = 0; i < tableHeaders.length; i++){
                    tableHeaders[i].addEventListener('pointerdown', tableHeaderClicked);
                    }
                let buttons = document.getElementsByTagName("button")
                for(let i = 0; i < buttons.length; i++){
                    buttons[i].addEventListener('pointerdown', buttonClicked);
                }
                let elements = document.getElementsByClassName('menuItem');
                if (elements != null) {
                    for (let i = 0; i < elements.length; i++) {
                        elements[i].addEventListener('pointerdown',pointerDown);
                        elements[i].addEventListener('pointerup', pointerUp);
                    }
                }

                with (document.getElementById("chkQtyTracked")) {
                    addEventListener('change', function() {
                        var numQty = document.getElementById("numQty");
                        var qtyBtn = document.getElementById("btnUpdateQty");
                        if (this.checked) {
                            numQty.classList.remove("disabled");
                            numQty.removeAttribute("disabled");   
                            if(document.getElementById("numQty").value == ''){
                                qtyBtn.classList.add("disabled");
                                qtyBtn.setAttribute("disabled", true);
                            }        
                        } else {
                            numQty.classList.add("disabled");
                            numQty.setAttribute("disabled", true);
                            qtyBtn.classList.remove("disabled");
                            qtyBtn.removeAttribute("disabled");  
                        }
                    });
                }
                with (document.getElementById("numQty")){
                    addEventListener('input', function(){
                        var qtyBtn = document.getElementById("btnUpdateQty");
                        if(this.value == '' &&document.getElementById("chkQtyTracked").checked){
                            qtyBtn.classList.add("disabled");
                            qtyBtn.setAttribute("disabled", true);
                        }
                        else{
                            qtyBtn.classList.remove("disabled");
                            qtyBtn.removeAttribute("disabled");  
                        }
                    })

                }
                

                // code that allows retention of scrollbar location between refreshes
                with (document.getElementsByTagName("form")[0]) {
                    let x = getVar("scrollLeft");
                    let y = getVar("scrollTop");
                    if (x !== undefined) {
                        scrollLeft = x;
                        scrollTop = y;
                    }

                    window.addEventListener('scroll', function(event) {
                        event.stopPropagation();
                        if (getVar("scrollLeft") != null) {
                            setVar("scrollLeft", scrollLeft);
                            setVar("scrollTop", scrollTop);
                        }
                    }, true);   
                }

                setTimeout(updateDisplay, 30000);
            }

            
            
        </script>
    </head>
    <body onload="allElementsLoaded()">
        <!-- this form submits to itself -->
        <form id="sessionContainer" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php require_once "../Resources/php/sessionHeader.php"; ?>
            <div id="sessionBody">
            <!-- PLACE YOUR PHP LAYOUT LOGIC CODE HERE -->
            <?php            
                if (isset($_POST['command'])) {
                    $inStr = isset($_POST['selectedItem']) ?
                            "('"  .str_replace(",","','", $_POST['selectedItem']). "')" : "('')";
                    
                    switch ($_POST['command']) {
                        //reset all sort keys
                        case 'resetAllSortKeys':
                            unset($_POST['tblInventory_SortKey1'], 
                                  $_POST['tblInventory_SortKey2'],
                                  $_POST['tblInventory_SortKey3'],
                                  $_POST['tblInventory_SortKey4'],
                                  $_POST['ResetSortKeys']);
                            break;

                        //if the button to reset all quantities was clicked, set all tracked quantities to 0
                        case 'resetAllQty':
                            $sql = "UPDATE menuitems
                                    SET Quantity = 0
                                    WHERE Quantity IS NOT NULL;";
                            break;
                            
                        //resets all requests to 0
                        case 'resetAllReq':
                            $sql = "UPDATE menuItems
                                    SET requests = 0;";
                            break;

                        //updates the quantity of all selected items if the respective button was clicked.
                        //contains fail-safe for if untracked items are present in the selection
                        case 'updateQty':
                            if (!empty($_POST['qtyTracked']) && $_POST['qtyTracked'] == true&&!empty($_POST['qty'])) {
                                $sql = "UPDATE menuitems
                                        SET Quantity = ".$_POST['qty']."
                                        WHERE quickCode IN $inStr;";
                            }
                            elseif(empty($_POST['qtyTracked'])||$_POST['qtyTracked'] == false) {
                                $sql = "UPDATE menuitems
                                        SET Quantity = NULL
                                        WHERE quickCode IN $inStr;";
                            }
                            break;

                        //resets requests of selected menu items to 0
                        case 'resetReq':
                            $sql = "UPDATE menuitems
                                    SET requests = 0
                                    WHERE quickCode IN $inStr;";
                            break;
                    }
                    if (isset($sql)) {
                        connection()->query($sql);
                    }
                }
            ?>
            <!-- Spans to arrange the buttons with. The first is the three 'reset all' butons.
            The second contains the selected menu items functionality-->
            <div id= "tabHeader">Inventory / Popularity Window</div>
            <fieldset id="fstMasterResets">
                <legend>Master&nbsp;Resets</legend>
                <button type="button" id="btnResetAllSortKeys" command="resetAllSortKeys">Reset&nbsp;All&nbsp;Sort&nbsp;Keys</button>
                <button type="button" id="btnResetAllQty" command="resetAllQty">Reset&nbsp;All&nbsp;Quantities</button>
                <button type="button" id="btnResetAllReq" command="resetAllReq">Reset&nbsp;All&nbsp;Requests</button>
            </fieldset>
            <fieldset id="fstSelMenuItmOptions">
                <legend>Selected&nbsp;Menu&nbsp;Items</legend>
                <label id="lblQuantityTracked" for="numQty">Quantity</label>
                <label id="lblQtyTracked" for="chkQtyTraked" style="display: none;">Quantity</label>
                <input id="chkQtyTracked" type="checkbox" name="qtyTracked">
                <input id="numQty" type="number" min="0" name="qty">
                <button id="btnUpdateQty" type="button" command="updateQty">Update&nbsp;Quantity</button>
                <button id="btnResetReq" type="button" command="resetReq">Reset&nbsp;Requests</button>
            </fieldset>
            <table id="tblInventory">
                <tr>
                    <th id="Item" sqlColumnId='menuitems.title'>Menu&nbsp;Item</th>
                    <th id="Category" sqlColumnId='Category'>Menu&nbsp;Category</th>
                    <th id="Quantity" sqlColumnId='Quantity'>Quantity</th>
                    <th id="Requests" sqlColumnId='Requests'>Requests</th>
                </tr>
                <?php
                    $orderKey = "";
                    //checks if any sort keys are set, appending each to the ORDER BY CLAUSE if so
                    if(isset($_POST['tblInventory_SortKey1'])){
                        $orderKey = "ORDER BY ".$_POST['tblInventory_SortKey1'];
                        $sortKeyIndex = 2;
                        while(isset($_POST['tblInventory_SortKey'.$sortKeyIndex])){
                            $orderKey = $orderKey.", ".$_POST['tblInventory_SortKey'.$sortKeyIndex];
                            $sortKeyIndex++;
                        }
                    }

                    
                    
                    $sql = "SELECT menuitems.quickCode AS id, menuitems.title AS Item, IFNULL(menucategories.title, 'None') AS Category, IFNULL(menuitems.quantity, '') AS Quantity, menuitems.requests AS Requests
                    FROM ((menuitems LEFT JOIN menuassociations ON menuitems.quickCode = menuassociations.childQuickCode)
                        LEFT JOIN menucategories ON menuassociations.parentQuickCode = menucategories.quickCode)".
                    $orderKey.";";
                    $results = connection()->query($sql);
                    //it's printing time
                    while($menuItem = $results->fetch_assoc() ){
                        echo("<tr class='menuItem' id =".$menuItem['id'].">
                        <td name='Item' class='itm'>".$menuItem['Item']."</td>
                        <td name='Category' class='cat'>".$menuItem['Category']."</td>
                        <td name='Quantity' class='qty'>".$menuItem['Quantity']."</td>
                        <td name='Requests' class='req'>".$menuItem['Requests']."</td>
                        </tr>");
                    }
                ?>
            </table>


            <!-- retain any POST vars. When updateDisplay() is called or the form is submitted,
            these variables will be carried over -->
            <?php
                unset($_POST['command'], $_POST['qtyTracked'], $_POST['qty']); 
                require_once '../Resources/php/display.php'; 
            ?>
           </div>
        </form>
    </body>
</html>

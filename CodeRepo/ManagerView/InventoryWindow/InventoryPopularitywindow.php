<?php require_once '../../Resources/php/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']); ?>

<!DOCTYPE html>
<?php require_once '../../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
    <link rel="stylesheet" href="../../Resources/CSS/baseStyle.css">
    <link rel="stylesheet" href="../../Resources/CSS/ticketStyle.css">
    <style>
        form{
            background-color:black;
            color:white; 
        }
        .sessionBody {
				display: grid;
				grid-template-areas: "tabHeader tabHeader"
                                     "resetButtons updateSelectedItems"
                                     "inventoryTable inventoryTable";
				grid-template-columns: max-content 1fr max-content;
				grid-template-rows: min-content 1fr min-content;
		
            }
        .conflict{
            background-color:yellow;
        }
        #updateSelectedItems{
            border:3px solid white;
        }
    </style>
        <!-- gives you access to setVar, getVar, removeVar, 
        clearVars, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        

        <script>
            function tableHeaderClicked() {
                let columnName = this.id;
                let tableId = document.getElementsByTagName("table")[0].id;
                toggleSortKey(tableId, columnName);
                updateDisplay();
            }
            function buttonClicked(){
                setVar(this.id, "yes");
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
                let updateInput = document.getElementById("updateQty")
                if(itemsToCheck == null){
                    document.getElementById("isQuantityChecked").checked = false;
                    updateInput.disabled = true;
                    updateInput.placeholder = "None";
                    return;
                }
                itemsToCheck = itemsToCheck.split(',');
                for(let i= 0; i < itemsToCheck.length; i++){
                    let theItem = document.getElementById(itemsToCheck[i]);
                    if(theItem.getElementsByName("Quantity")[0].innerText==""){
                        anyUntracked = true;
                    }
                    else{
                        anyTracked = true;
                        if(quantityToCompare == -1){
                            quantityTocompare = theItem.getElementsByName("Quantity")[0].innerText.parseInt();
                        }
                        else if(quantityToCompare != theItem.getElementsByName("Quantity")[0].innerText.parseInt()) {sameQuantity = false;}
                    }
                }
                document.getElementById("isQuantityTracked").checked = anyTracked;
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
                if(getVar('inventoryTable.SortKey1')!=null){
                        let keyIndex = 1;
                        let unicodeBase = 9311;
                        let keyPrefix = 'inventoryTable.SortKey';
                        while(getVar(keyPrefix + keyIndex)!= null){
                            for(let i = 0; i < tableHeaders.length; i++){
                                if(getVar(keyPrefix + keyIndex).indexof(tableHeaders[i].id!= -1)){
                                    tableHeaders[i].text = "&#" + (unicodeBase + keyIndex)+";" +tableHeaders[i].text;
                                    if(getVar(keyPrefix + keyIndex).indexof("ASC")!=-1){
                                        tableHeaders[i].text = tableHeaders[i].text +"&#9650;";
                                    }
                                    else{
                                        tableHeaders[i].text = tableHeaders[i].text +"&#9660;";
                                    }
                                }
                            }
                        }
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
                rememberScrollPosition();
                setTimeout(updateDisplay, 30000);
            }
            
        </script>
    </head>
    <body onload="allElementsLoaded()">
        <!-- this form submits to itself -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <!-- PLACE YOUR PHP LAYOUT LOGIC CODE HERE -->
            <?php
            if(isset($_POST['ResetSortKeys'])){
                unset($_POST['inventoryTable.SortKey1'], $_POST['inventoryTable.SortKey2'],
                      $_POST['inventoryTable.SortKey3'], $_POST['inventoryTable.SortKey4'], $_POST['ResetSortKeys'])
            }
            //updates the quantity of all selected items if the respective button was clicked.
            //contains fail-safe for if untracked items are present in the selection
            if(isset($_POST['updateQty'])&&!empty($_POST['selectedItem'])){

                $sql = "UPDATE menuitems
                        SET Quantity = ".$_POST['newQty']."
                        WHERE quickCode IN (".$_POST['selectedItem'].")
                        AND Quantity IS NOT NULL;";
                connection()->qurey($sql);
            }
            
            //if the button to reset all quantities was clicked, set all tracked quantities to 0
            if(isset($_POST['resetAllQty'])){
                $sql = "UPDATE menuitems
                        SET Quantity = 0
                        WHERE Quantity IS NOT NULL;";
                connection()->query($sql);
                unset($_POST['resetAllQty'])
            }
            //resets all requests to 0
            if(isset($_POST['resetAllReq'])){
                $sql = "UPDATE menuItems
                        SET requests = 0;"
            }
            //resets requests of selected menu items to 0
            if(isset($_POST['resetSomeReq'])){
                $sql = "UPDATE menuitems
                        SET requests = 0;
                        WHERE quickCode IN (".$_POST['selectedItem'].");"
                connection()->query($sql);
                unset($_POST['resetSomeReq'])
            }
            ?>
            <!-- Spans to arrange the buttons with. The first is the three 'reset all' butons.
            The second contains the selected menu items functionality-->
            <h id= "tabHeader">Inventory / Popularity Window</h>
            <span id="resetButtons">
                <button type="button" id="ResetSortKeys" value="Reset Sort Keys"><br/>
                <button type="button" id="resetAllQty" value="Reset All Quantities"><br/>
                <button type="button" id="resetAllReq" value="Reset All Requests">
            </span>
            <span id="updateSelectedItems">
                Selected Menu Items<br/>
                <label for="isQuantityTracked">Quantity</label>
                <input type="checkbox" id="isQuantityTracked" name="isQuantityTracked">
                <input type="number" min="0" name="newQty">
                <button type="button" id="updateQty" value="Update Quantity">
                <button type="button" id="resetSomeReq" value="Reset Requests">
            </span>
            <table id="inventoryTable">
                <tr>
                    <th id="Item">Menu Item</th>
                    <th id="Category">Menu Category</th>
                    <th id="Quantity">Quantity</th>
                    <th id="Requests">Requests</th>
                </tr>
                <?php
                $orderKey = "";
                //checks if any sort keys are set, appending each to the ORDER BY CLAUSE if so
                if(isset($_POST['inventoryTable.SortKey1'])){
                    $orderKey = "ORDER BY ".$_POST['inventoryTable.SortKey1'];
                    $sortKeyIndex = 2;
                    while(isset($_POST['inventoryTable.SortKey'.$sortKeyIndex])){
                        $orderKey = $orderKey.", ".$_POST['inventoryTable.SortKey'.$sortKeyIndex];
                        $sortKeyIndex++;
                    }
                }

                
                
                $sql = "SELECT menuitems.quickCode AS id, menuitems.title AS Item, IFNULL(menucategories.title, 'None') AS Category, IFNULL(menuitems.quantity, '') AS Quantity, menuitems.requests AS Requests
                FROM ((menuitems LEFT JOIN menuassociations ON menuitems.quickCode = menuassociations.childQuickCode)
                    LEFT JOIN menucategories ON menuassociations.parentQuickCode = menucategories.quickCode)".
                $orderKey.";"
                $results = connection()->query($sql);
                //it's printing time
                while($menuItem = $results->fetch_assoc() ){
                    echo("<tr class='menuItem' id =".$menuItem['id'].">
                    <td name='Item'>".$menuItem['Item']."</td>
                    <td name='Category'>".$menuItem['Category']."</td>
                    <td name='Quantity'>".$menuItem['Quantity']."</td>
                    <td name='Requests'>".$menuItem['Requests']."</td>
                    </tr>");
                }
                ?>
            </table>


            <!-- retain any POST vars. When updateDisplay() is called or the form is submitted,
            these variables will be carried over -->
            <?php require_once '../Resources/php/display.php'; ?>
           
        </form>
    </body>
</html>
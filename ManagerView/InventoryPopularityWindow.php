<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']); ?>

<!DOCTYPE html>
<?php require_once '../Resources/PHP/dbConnection.php'; ?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/ticketStyle.css">
        <style>
            * {
                color: white;
            }
            table {
                border-collapse: collapse;
            }
            th {
                background: rgb(68,68,68);
                background: linear-gradient(0deg, rgba(68,68,68,1) 0%, rgba(102,102,102,1) 100%);
                padding-top: 1rem;
                padding-bottom: 1rem;
            }
            tr + tr {
                border-top: .125rem solid #333;
            }
            #sessionForm {
                height: 100%;
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
                font-size: 2rem;
                font-weight: bold;
                margin: 1rem auto 3rem auto;
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
                background-color: #bf1e2e;
            }
            
            #numQty {
                max-width: 7rem;
            }

            fieldset {
                background: rgb(119,119,119);
                background: linear-gradient(0deg, rgba(40,40,40,1) 0%, rgba(0,0,0,1) 100%);
                color: white;
                border-radius: .5rem;
            }

            
        </style>
        
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script>
        

        <script>

            /////////////////////////////////////////////////////////////////////
            // TASKS WHEN PAGE IS COMPLETELY LOADED
            /////////////////////////////////////////////////////////////////////

            function allElementsLoaded(){
                //add the unicode characters to table headers with sort keys.
                let tableHeaders = document.getElementsByTagName("th");
                if(varGet('tblInventory_SortKey1')!=null){
                    let keyIndex = 1;
                    let unicodeBase = 9311;
                    let keyPrefix = 'tblInventory_SortKey';
                    while(varGet(keyPrefix + keyIndex)!= null){
                        let keyToScan = varGet(keyPrefix + keyIndex);
                        for(let i = 0; i < tableHeaders.length; i++){
                            if(keyToScan.indexOf(tableHeaders[i].getAttribute("sqlColumnId"))!= -1){
                                tableHeaders[i].innerText = keyIndex + "\xa0" +tableHeaders[i].innerText;
                                if(varGet(keyPrefix + keyIndex).indexOf("ASC")!=-1){
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
                let selItems = varGet("selectedItem");
                if (varGet("selectedItem") !== undefined) {
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
                        elements[i].addEventListener('pointerenter', pointerEnter);
                    }
                }
                
                document.getElementsByTagName("body")[0].addEventListener('pointerup', disengageMultiselect);

                document.querySelector("#keyCatcher").addEventListener("keydown", keydown);
                document.querySelector("#keyCatcher").addEventListener("pointerdown", deselectAllMenuItems);
                document.querySelector("#keyCatcher").addEventListener("keyup", keyup);

                document.querySelector("#btnUpdateQty").addEventListener("click", updateQty);

                document.querySelector("#chkQtyTracked").addEventListener("pointerdown", ignorePointerDown);
                document.querySelector("#numQty").addEventListener("pointerdown", ignorePointerDown);

                with (document.getElementById("chkQtyTracked")) {
                    addEventListener('change', function() {
                        var numQty = document.getElementById("numQty");
                        var qtyBtn = document.getElementById("btnUpdateQty");
                        if (this.checked) {
                            //numQty.classList.remove("disabled");
                            numQty.removeAttribute("disabled");   
                            if(document.getElementById("numQty").value == ''){
                                //qtyBtn.classList.add("disabled");
                                qtyBtn.setAttribute("disabled", true);
                            }        
                        } else {
                            //numQty.classList.add("disabled");
                            numQty.setAttribute("disabled", true);
                            //qtyBtn.classList.remove("disabled");
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
                    });

                }
                

                // code that allows retention of scrollbar location between refreshes
                with (document.getElementById("sessionForm")) {
                    let x = varGet(id + "_scrollLeft");
                    let y = varGet(id + "_scrollTop");
                    if (x !== undefined) {
                        scrollLeft = x;
                        scrollTop = y;
                    }

                    window.addEventListener('scroll', function(event) {
                        varSet(id + "_scrollLeft", scrollLeft);
                        varSet(id + "_scrollTop", scrollTop);
                    }, true);   
                }

                setTitle("CentRes RMS: Management Tools - Inventory / Popularity Window", "Management Tools");

                //setTimeout(updateDisplay, 30000);
            }

            /////////////////////////////////////////////////////////////////////
            // All other functions
            /////////////////////////////////////////////////////////////////////

            function tableHeaderClicked() {
                let tableId = document.getElementsByTagName("table")[0].id;
                toggleSortKey(tableId, this.getAttribute("sqlColumnId"));
            }
            function buttonClicked(){
                varSet("command", this.getAttribute("command"));
                updateDisplay();
            }

            //=========================FUNCTIONS INVOLVING MENU ITEM SELECTION============================
            var multiselectEngaged = false;
	        function pointerDown() {
                event.stopPropagation();
                multiselectEngaged = true;
                if (ctrlDown) {
                    toggleSelection(this);
                }
                else {
                    var oldSelectedItems = document.getElementsByClassName("menuItem");
                    for(let i = 0; i < oldSelectedItems.length; i++){
        	            oldSelectedItems[i].classList.remove("selected");
    	            }
                    varSet("selectedItem", this.id);
                    this.classList.add("selected");

                }
                setQtyBoxState();
	        }

            function pointerEnter() {
                if (multiselectEngaged) {
                    toggleSelection(this);
                }
                setQtyBoxState();               
            }

            function toggleSelection(target) {
                if (ctrlDown) {
                    if (shiftDown) {
                        target.classList.toggle("selected");
                    }
                    else {
                        target.classList.add("selected");
                    }
                }
                else {
                    target.classList.toggle("selected");
                }
                let selMenuItems = document.querySelectorAll(".selected");
                let selStr = "";
                for (let i = 0; i < selMenuItems.length; i++) {
                    selStr += "," + selMenuItems[i].id;
                }
                if (selStr.length > 0) {
                    selStr = selStr.substring(1);
                }
                varSet("selectedItem", selStr);
            }

            function disengageMultiselect() {
                multiselectEngaged = false;
            }

            function deselectAllMenuItems() {
                let selItems = document.querySelectorAll(".selected");
                for (let i = 0; i < selItems.length; i++) {
                    selItems[i].classList.remove("selected");
                }
                varRem("selectedItem");
            }

            function ignorePointerDown() {
                event.stopPropagation();
            }

            var shiftDown = false;
            var ctrlDown = false;
            function keydown(event) {
                //alert(event.keyCode);
                switch (event.keyCode) {
                    case 16:
                        shiftDown = true;
                        break;
                    case 17:
                        ctrlDown = true;
                        break;
                    case 13:
                        event.preventDefault();   
                        if (document.activeElement != null && document.activeElement.id == "numQty") {
                            document.querySelector("#btnUpdateQty").click();
                        }
                            
                }       
            }

            function keyup(event) {
                switch (event.keyCode) {
                    case 16:
                        shiftDown = false;
                        break;
                    case 17:
                        ctrlDown = false;
                        break;
                }
            }

            function updateQty() {
                varSet("command", "updateQty");
                updateDisplay();
            }

            function setQtyBoxState(){
                let sameQuantity = true;
                let anyUntracked = false;
                let numQtyVal = undefined;
                let selMenuItems = document.getElementsByClassName("selected");
                let numQty = document.getElementById("numQty");
                let chkQtyTracked = document.getElementById("chkQtyTracked");
                
                if(selMenuItems.length == 0){
                    document.getElementById("chkQtyTracked").checked = false;
                    numQty.disabled = true;
                    numQty.placeholder = "";
                    return;
                }
                for(let i= 0; i < selMenuItems.length; i++){
                    with (selMenuItems[i]) {
                        if(getElementsByClassName("qty")[0].innerText==""){
                            anyUntracked = true;
                        }
                        else{
                            var thisVal = parseInt(getElementsByClassName("qty")[0].innerText);
                            if(numQtyVal === undefined){
                                numQtyVal = thisVal;
                            }
                            else if(numQtyVal != thisVal) {
                                numQtyVal = null;
                            }
                        }
                    }
                }
                
                numQty.classList.remove("conflict");
                if (numQtyVal === undefined) {
                    // only untracked exists
                    chkQtyTracked.checked = false;
                    numQty.value = null;
                    numQty.disabled = true;
                    numQty.placeholder = "";  
                }
                else if (numQtyVal !== undefined && anyUntracked) {
                    // conflict
                    chkQtyTracked.checked = true;
                    numQty.classList.add("conflict");
                    numQty.value = null;
                    numQty.disabled = false;
                    numQty.placeholder = "Tracking Conflict";
                }
                else if (numQtyVal == null) {
                    // all tracked, multiple quantities
                    chkQtyTracked.checked = true;
                    numQty.value = null;
                    numQty.disabled = false;
                    numQty.placeholder = "Multiple Qtys";
                } 
                else {
                    // all tracked with same quantities
                    chkQtyTracked.checked = true;
                    numQty.value = numQtyVal;
                    numQty.disabled = false;
                    numQty.placeholder = "";
                }

            }
            

            
            
        </script>
    </head>
    <body onload="allElementsLoaded()" id="keyCatcher">
        
        <form id="sessionForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php require_once "../Resources/PHP/sessionHeader.php"; ?>
            <div id="sessionBody">
            
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

                    
                    
                    $sql = "SELECT menuitems.quickCode AS id, menuitems.title AS Item, menucategories.title AS Category, menuitems.quantity AS Quantity, menuitems.requests AS Requests
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


            
            <?php
                unset($_POST['command'], $_POST['qtyTracked'], $_POST['qty']); 
                require_once '../Resources/PHP/display.php'; 
            ?>
           </div>
        </form>
    </body>
</html>

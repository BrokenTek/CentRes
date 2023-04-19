


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
            #sessionForm {
            height: 95%;
            width: 100%;
		    }
            #sessionBody {
            
                display: grid;
                grid-template-areas: ". tabHeader    tabHeader       ."
                                 ". rosterTable  rosterTable     ."
                                 ". .            modifyButtons   .";
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
            #employeeRoster {
                grid-area:rosterTable;
            }
            #modifyButtons{
                grid-area:modifyButtons;
                border:none;
            }
            .hidden {
                display: none;
            }

            .recordCell{
                border: none solid none solid;
            }


           #btnRemove, #btnCancel, #btnCommit {
                width: 1.5rem;
                max-width: 1.5rem;
                height: 1.5rem;
                margin: 0;
                padding: 0;
           }

            
        </style>

        
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script> 
        
        <script>
            function buttonClicked(){
                if(this.value == "Delete"){
                    //TODO: prompt if the user is sure they want to delete that employee. Important UX considering the button's proximity to the edit button
                    varSet("command", "Delete");
                    updateDisplay();
                }
                else{
                    varSet("mode", this.value);
                    //clears some extraneous $_POST variables
                    varRem('rosterTable_SortKey1');
                    varRem('rosterTable_SortKey2');
                    varRem('rosterTable_SortKey3');
                    varRem('rosterTable_SortKey4');
                    //submit selected items and submission mode to NewEditEmployee.php instead of back to this site.
                    document.getElementById("sessionForm").setAttribute("action", "NewEditEmployee.php");
                    document.getElementById("btnSubmit").click();
                }
            }
            function tableHeaderClicked() {
                let tableId = document.getElementsByTagName("table")[0].id;
                toggleSortKey(tableId, this.getAttribute("sqlColumnId"));
            }
            //=========================FUNCTIONS INVOLVING SELECTION============================
            const LONG_TIME_TOUCH_LENGTH = 250;
            var targetEmployee = null;
            var longTouchEnabled = false;
            var longTouchTimer = null;
	        function pointerDown() {
                if (this === undefined || this.classList.contains('disabled')) { return; }
                this.classList.toggle("selected");
                if (!this.classList.contains("selected")) {
                    // deselect action performed.
                    this.classList.remove("multiselect");
                    let sel = document.getElementsByClassName("selected");
                    if (sel.length == 1) {
                        sel[0].classList.remove("multiselect");
                    }
                    varSet("selectedEmp", varGet("selectedEmp").replace(this.id,"").replace(",,",",").replace(/^,+|,+$/g, ''));
                    setNewEditBtnState();
                    return;
                }
                targetEmployee = this;
                if (varGet("selectedEmp") != null && varGet("selectedEmp") != this.id) {
                    longTouchTimer = setTimeout(longTouch, LONG_TIME_TOUCH_LENGTH);
                }
	        }
            
            // if you pressed on a menu item, you already have another one selected, and the minimum required time
            // for multiselect has elapsed, change the selected item to "multiselect" 
            function longTouch() {
                longTouchEnabled = true;
                targetEmployee.classList.add("multiselect");

                // if there is exactly 1 other item selected, make it multi-select as well.
                var alreadySelected = varGet("selectedEmp");
                if (alreadySelected != null && alreadySelected.indexOf(",") == -1) {
                    document.getElementById(alreadySelected).classList.add("multiselect");
                }
            }

            function pointerUp() {
                if (targetEmployee == null) { return; }
                
                if (longTouchTimer != null) {
                    clearTimeout(longTouchTimer);
                }

                let oldselectedEmps = document.getElementsByClassName("employee");
                // if you only have 1 item selected, adjust the state of applicable menu items to reflect that.
                if (!longTouchEnabled) {
    	            /*this iterates through the list returned, if there is no case where multiple items are selected concurrently,
    	            you can just use oldselectedEmps[0].classList.remove("selected"); instead*/
	                for(let i = 0; i < oldselectedEmps.length; i++){
    	                oldselectedEmps[i].classList.remove("selected");
                        oldselectedEmps[i].classList.remove("multiselect");
    	            }
    	            targetEmployee.classList.add("selected");
                    targetEmployee.classList.remove("multiselect");
                    varSet("selectedEmp", targetEmployee.id);
                }
                // or you have multiple items selected
                else {
                    varSet("selectedEmp", varGet("selectedEmp") + "," + targetEmployee.id); 
                }

                targetEmployee = null;
                longTouchEnabled = false;
                setNewEditBtnState();
            }
            function deselect(){
                if(document.getElementById("rosterTable").matches(":hover")){
                    return;
                }
                let selectedEmps = document.getElementsByClassName("employee");
                for(let i = 0; i < selectedEmps.length; i++){
    	                selectedEmps[i].classList.remove("selected");
                        selectedEmps[i].classList.remove("multiselect");
                varRem("selectedEmp");
                setNewEditBtnState();
                }
            }
            //the button value determines the mode in which the button functions
            function setNewEditBtnState(){
                let newEditBtn = document.getElementById("btnEditRoster")
                let deleteBtn = document.getElementById("btnDelete")
                let selectedEmps = varGet("selectedEmp");
                if(selectedEmps){
                    deleteBtn.removeAttribute("disabled");
                    if(selectedEmps.indexOf(",")!=-1){
                        newEditBtn.setAttribute("disabled", true);
                        newEditBtn.classList.add("disabled")
                    }
                    else{
                        newEditBtn.value = "Edit"
                        newEditBtn.innerText = "Edit"
                        newEditBtn.removeAttribute("disabled");
                        newEditBtn.classList.remove("disabled")
                    }

                }
                else{
                    deleteBtn.setAttribute("disabled", true);
                    newEditBtn.value = "New"
                    newEditBtn.innerText = "New"
                    newEditBtn.removeAttribute("disabled");
                    newEditBtn.classList.remove("disabled")
                }
            }
            function allElementsLoaded() {
                // ADD EVENT LISTENERS HERE
                //add the unicode characters to table headers with sort keys.
                let tableHeaders = document.getElementsByTagName("th");
                if(varGet('rosterTable_SortKey1')!=null){
                    let keyIndex = 1;
                    let unicodeBase = 9311;
                    let keyPrefix = 'rosterTable_SortKey';
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
                
                let selItems = varGet("selectedEmp");
                if (varGet("selectedEmp") !== undefined) {
                    selItems = selItems.split(",");
                    for( let i = 0; i < selItems.length; i++) {
                        document.getElementById(selItems[i]).classList.add("selected");
                    }
                    setNewEditBtnState();
                }

                //add all the event listeners
                with(document.getElementById("sessionBody")){
                    addEventListener('pointerup',deselect);
                }
                for(let i = 0; i < tableHeaders.length; i++){
                    tableHeaders[i].addEventListener('pointerdown', tableHeaderClicked);
                    }
                let buttons = document.getElementsByTagName("button")
                for(let i = 0; i < buttons.length; i++){
                    buttons[i].addEventListener('pointerdown', buttonClicked);
                }
                let elements = document.getElementsByClassName('employee');
                if (elements != null) {
                    for (let i = 0; i < elements.length; i++) {
                        elements[i].addEventListener('pointerdown',pointerDown);
                        elements[i].addEventListener('pointerup', pointerUp);
                    }
                }

                setTitle("CentRes POS: Management Tools - Employee Roster", "Management Tools");
            }

            //Place your other JavaScript Code here
        </script>
    </head>
    <body onload="allElementsLoaded()">
        
        <form  id="sessionForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php require_once "../Resources/PHP/sessionHeader.php"; ?>
            <div id="sessionBody">
                <div id="tabHeader">Employee Roster</div>
                <br>
                <div id="employeeRoster">

                    <table id="rosterTable">
                        <tr>
                            <th id="lastName" sqlColumnId='employees.lastname'>Last&nbsp;Name</th>
                            <th id="firstName" sqlColumnId='employees.firstName'>First&nbsp;Name</th>
                            <th id="username" sqlColumnId='employees.username'>Username</th>
                            <th id="Role" sqlColumnId='employees.roleLevel'>Role</th>
                        </tr>
                        <?php
                        if (isset($_POST['command'])) {
                            $inStr = isset($_POST['selectedEmp']) ?
                                    "('"  .str_replace(",","','", $_POST['selectedEmp']). "')" : "('')";

                            try{
                                if($_POST['command']=="Delete"){
                                    $sql = "DELETE FROM employees 
                                    WHERE id IN ".$inStr.";";
                                    connection()->query($sql);

                                }
                            }
                            catch (Exception $e) {
                                $errorMessage = "Could not remove that employee!\nMake sure they are logged out.";
                            }
                            finally{
                                unset($_POST['selectedEmp']);
                                unset($_POST['command']);
                            }
                        }
                        // INSERT THE CODE TO GRAB EMPLOYEES FROM DATABASE HERE
                        $orderKey = "";
                        //checks if any sort keys are set, appending each to the ORDER BY CLAUSE if so
                        if(isset($_POST['rosterTable_SortKey1'])){
                            $orderKey = "ORDER BY ".$_POST['rosterTable_SortKey1'];
                            $sortKeyIndex = 2;
                            while(isset($_POST['rosterTable_SortKey'.$sortKeyIndex])){
                                $orderKey = $orderKey.", ".$_POST['rosterTable_SortKey'.$sortKeyIndex];
                                $sortKeyIndex++;
                            }
                        }
                        $sql = "SELECT employees.id AS empId, lastname, firstname, username, title 
                        FROM employees 
                        LEFT JOIN loginroutetable ON employees.roleLevel = loginroutetable.id "
                        .$orderKey.";";
                        $results = connection()->query($sql);
                        // POPULATE THE EMPLOYEES HERE
                        while($employee = $results->fetch_assoc() ){
                            echo("<tr class='employee' id =".$employee['empId'].">
                                <td name='lastName' class='recordCell'>".$employee['lastname']."</td>
                                <td name='firstName' class='recordCell'>".$employee['firstname']."</td>
                                <td name='username' class='recordCell'>".$employee['username']."</td>
                                <td name='role' class='recordCell'>".$employee['title']."</td>
                                </tr>");
                        }
                        if(isset($errorMessage)){
                            echo("<p style='color:red'>".$errorMessage."</p>");
                        }
                        ?>
                    </table>
                </div>
                <div id="modifyButtons">
                <button id="btnDelete" type="button" value="Delete" disabled>Delete</button>
                <button id="btnEditRoster" type="button" value="New">New</button>
                <input id="btnSubmit" type="submit" style="display:none">
                </div>
            </div>
            
            <?php unset($_POST['thisVariableIWantToForget'], $_POST['thisOtherVariableIDontNeed']) ?>

            
            <?php require_once '../Resources/PHP/display.php'; ?>
        </form>
    </body>
</html>
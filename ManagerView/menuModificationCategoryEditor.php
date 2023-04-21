<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']); ?>
<?php require_once '../Resources/PHP/dbConnection.php'; ?>
<?php

    /////////////////////////////////////////////////////////
    //
    // NOTE: This page will be called from MenuEditor.php
    // $_POST['quickCode] will be passed in if you are editing
    // an existing menu object. Check if $_POST['menuTitle']
    // is set.
    // 
    // if it isn't, you're going to have to grab your
    // data from the db and set them into the appropriate
    // $_POST[] variables.
    // 
    // See the bottom of the file
    // for a list of needed vars referenced
    // in the unset() call.
    //
    /////////////////////////////////////////////////////////
    //
    // process $_POST['commit'] or $_POST['delete'] here.
    // if any errors occur, set a message in $errorMessage
    // to display at the bottom of the page.
    // 
    // If no error messages occur, set a success $message.
    //
    // if you create a new item, make sure you set
    // $_POST['quickCode'] for what you just created.
    // look up the quickCode >>>> hint: titles
    // are not unique but duplicates will have a different
    // creationDate
    //
    /////////////////////////////////////////////////////////
    foreach($_POST as $key => $value){
        if ($value == ""){
            unset($_POST[$key]);  
        }
    }
    try {
        $_POST['ignoreRedirect'] = "yes";
        if(isset($_POST['quickCode'])&&!isset($_POST['menuTitle'])){
            $sql = "SELECT * FROM MenuModificationCategories WHERE quickCode = '" .$_POST['quickCode']. "';";
            $fieldData = connection()->query($sql)->fetch_assoc();
            //just in case the quickCode persists after a deletion, these statements are wrapped in a condition
            //to prevent unwanted warnings from showing up. Note to self: unwrap this if you account for this.
            if(isset($fieldData)){
                $_POST['menuTitle'] = $fieldData['title'];
                $_POST['categoryType'] = $fieldData['categoryType'];
            }
        }

        if(isset($_POST['commit'])){
            if($_POST['commit'] == 'Update'){
                //attempt to get the original title. This will be displayed in the message to user.
                $sql = "SELECT title FROM MenuModificationCategories WHERE quickCode = '" .$_POST['quickCode']. "';";
                $result = connection()->query($sql);
                $title = $result->fetch_assoc()['title'];


                //change the category's name
                $sql = "UPDATE MenuModificationCategories SET title = ?, categoryType = ? WHERE quickCode = ?;";      
                $sql = connection()->prepare($sql);
                $sql->bind_param('sss', $_POST['menuTitle'], $_POST['categoryType'], $_POST['quickCode']);
                $sql->execute();

                // clear any associations with menu items and mod items
                $sql = "DELETE FROM MenuAssociations WHERE parentQuickCode = ? OR childQuickCode = ?;";
                $sql = connection()->prepare($sql);
                $sql->bind_param('ss', $_POST['quickCode'], $_POST['quickCode']);
                $sql->execute();

                // associate with any menu items
                if (isset($_POST['parentMenuItems'])) {
                    for ($i = 0; $i < sizeof($_POST['parentMenuItems']); $i++) {
                        if (!empty($_POST['parentMenuItems'][$i])) {
                            $sql = "INSERT INTO MenuAssociations (parentQuickCode, childQuickCode) VALUES (?, ?)";
                            $sql = connection()->prepare($sql);
                            $sql->bind_param('ss', $_POST['parentMenuItems'][$i], $_POST['quickCode']);
                            $sql->execute();
                        }
                    }
                }

                // associate mod items
                if (isset($_POST['childMenuModItems'])) {
                    for ($i = 0; $i < sizeof($_POST['childMenuModItems']); $i++) {
                        if (!empty($_POST['childMenuModItems'][$i])) {
                            $sql = "INSERT INTO MenuAssociations (parentQuickCode, childQuickCode) VALUES (?, ?)";
                            $sql = connection()->prepare($sql);
                            $sql->bind_param('ss', $_POST['quickCode'], $_POST['childMenuModItems'][$i]);
                            $sql->execute();
                        }
                    }
                }

                $message = "<b>$title</b> updated.";
            }
            else{
                //attempt to create the new modification item
                $sql = "INSERT INTO MenuModificationCategories (title, categoryType) VALUES (?, ?);";
                $sql = connection()->prepare($sql);
                $sql->bind_param('ss', $_POST['menuTitle'], $_POST['categoryType'],);
                $sql->execute();

                //get its new quick code and bind it to the $_POST variable.
                $sql2 = "SELECT quickCode FROM MenuModificationCategories WHERE title = ? ORDER BY counter DESC LIMIT 1;";
                $sql2 = connection()->prepare($sql2);
                $sql2->bind_param('s', $_POST['menuTitle']);
                $sql2->execute();
                $_POST['quickCode'] = $sql2->get_result()->fetch_assoc()['quickCode'];

                // associate with any menu items
                if (isset($_POST['parentMenuItems'])) {
                    for ($i = 0; $i < sizeof($_POST['parentMenuItems']); $i++) {
                        if (!empty($_POST['parentMenuItems'][$i])) {
                            $sql = "INSERT INTO MenuAssociations (parentQuickCode, childQuickCode) VALUES (?, ?)";
                            $sql = connection()->prepare($sql);
                            $sql->bind_param('ss', $_POST['parentMenuItems'][$i], $_POST['quickCode']);
                            $sql->execute();
                        }
                    }
                }

                // associate mod items
                if (isset($_POST['childMenuModItems'])) {
                    for ($i = 0; $i < sizeof($_POST['childMenuModItems']); $i++) {
                        if (!empty($_POST['childMenuModItems'][$i])) {
                            $sql = "INSERT INTO MenuAssociations (parentQuickCode, childQuickCode) VALUES (?, ?)";
                            $sql = connection()->prepare($sql);
                            $sql->bind_param('ss', $_POST['quickCode'], $_POST['childMenuModItems'][$i]);
                            $sql->execute();
                        }
                    }
                }

                unset($_POST['quickCode']);

                $message = "<b>" .$_POST['menuTitle']. "</b> created.";
            }
        }
        if(isset($_POST['delete'])){
            echo("DELETE");
            $sql = "DELETE FROM MenuModificationCategories WHERE quickCode = '".$_POST['quickCode']."';";
            connection()->query($sql);

            $sql = "DELETE FROM MenuAssociations WHERE childQuickCode = '".$_POST['quickCode']."' OR parentQuickCode = '" .$_POST['quickCode']."';";
            connection()->query($sql);
            $message = "<b>" .$_POST['menuTitle']. "</b> deleted.";
            unset($_POST['quickCode'], $_POST['menuTitle']);
        }
    }
    catch (Exception $e) {
        if (strpos(" " . $e->getMessage(), "Duplicate entry") > 0) {
            $errorMessage = "<b>" .$_POST['menuTitle']. "</b> already exists in the menu. Choose another name.";   
        }
        else {
            $errorMessage = "An unexpected error occurred, please contact your system administrator or developer(s). ".$e->getMessage();
        }
    }
    if (!isset($errorMessage) && isset($_POST['errorMessage'])) {
        $errorMessage = $_POST['errorMessage'];
        unset($_POST['errorMessage']);
    }
    unset($_POST['childMenuModItems'], $_POST['parentMenuItems']);
?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/menuEditorStyle.css">
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script> 
        <script>
            function allElementsLoaded() {
                window.addEventListener("message", window.processJSONeventCall);
                // any startup tasks go here after page has fully loaded.

                document.querySelector("#selMenuTitle").addEventListener("change", selChanged);

                document.querySelector("#btnReset").addEventListener("pointerdown", btnResetPressed);

                document.querySelector("#selMenuTitle").addEventListener("change", selChanged);

                document.querySelector("#sessionForm").addEventListener("keydown", keydown);
                document.querySelector("#sessionForm").addEventListener("keyup", keyup);

                document.querySelector("#btnMenuCategoryEditor").addEventListener("pointerdown", 
                function() {redirect("menuCategoryEditor.php");});

                document.querySelector("#btnMenuItemEditor").addEventListener("pointerdown", 
                function() { redirect("menuItemEditor.php"); });

                document.querySelector("#" + "txtModItemFilter").addEventListener("input", filterModItems);

                document.querySelector("#btnMenuModificationEditor").addEventListener("pointerdown", 
                function() {redirect("menuModificationItemEditor.php");});

                with (document.querySelector("#txtMenuTitle")) {
                    focus();
                    setSelectionRange(0, value.length);
                } 

                filterModItems();

                setTimeout(function() { 
                    let msgs = document.getElementsByClassName("message");
                    if (msgs.length == 1) {
                        msgs[0].classList.add("disappear");
                    } 
                }, 1500);

                setTimeout(function() { 
                    let errs = document.getElementsByClassName("errorMessage");
                    if (errs.length == 1) {
                        errs[0].classList.add("disappear");
                    } 
                }, 5000);
            }

            function btnResetPressed(event) {
                varRem("quickCode");
                document.getElementById("selMenuTitle").selectedIndex = 0;
                document.getElementById("selCategoryType").selectedIndex = 0;
                document.getElementById("txtMenuTitle").value = "";
                document.getElementById("btnSubmit").setAttribute("value", "Create");
                if (document.getElementById("btnDelete") != null) {    
                    document.getElementById("btnDelete").remove();
                }
                let checks = document.querySelectorAll("input[type='checkbox']");
                for (let i = 0; i < checks.length; i++) {
                    checks[i].checked = false;
                }
                document.getElementById("selMenuTitle").focus();
            }

            function selChanged(event) {
                varRem("title");
                varSet("quickCode", this.options[this.selectedIndex].value);
                document.getElementById("txtMenuTitle").removeAttribute("value");
                updateDisplay(null, true);
            }

            document.selectMenuItemCheck = function(event) {
                with (document.getElementById(this.menuItemId)) {
                    scrollIntoView();
                    toggleAttribute("checked");
                }

            }

            function filterModItems() {
                let ops = document.querySelector("#" + "modItemList").getElementsByTagName("label");
                if (this.value == undefined || this.value == "") {
                    for (let i = 0; i < ops.length; i++) {
                        ops[i].classList.remove("hidden");
                        ops[i].nextSibling.classList.remove("hidden");
                        document.querySelector("#" + ops[i].getAttribute("for")).classList.remove("hidden");
                    }
                }
                else {
                    filters = this.value.toUpperCase().split("`");
                    for (let i = 0; i < ops.length; i++) {
                        let hide = true;
                        for (let j = 0; j < filters.length; j ++) {
                            if (ops[i].innerHTML.toUpperCase().startsWith(filters[j])) {
                               hide = false;
                            }
                        }
                        if (hide) {
                                ops[i].classList.add("hidden");
                                ops[i].nextSibling.classList.add("hidden");
                                document.querySelector("#" + ops[i].getAttribute("for")).classList.add("hidden");
                        }
                        else {
                            ops[i].classList.remove("hidden");
                            ops[i].nextSibling.classList.remove("hidden");
                            document.querySelector("#" + ops[i].getAttribute("for")).classList.remove("hidden");
                        }
                            
                    }
                }
            }
            

            ///////////////////////////////////////////////////
            //           RAPID ENTRY KEYBOARD EVENTS
            ///////////////////////////////////////////////////
            
            let ctrlDown = false;
            let shiftDown = false;

            function keydown(event) {
                //alert(event.keyCode2);
                switch (event.keyCode) {
                    case 16:
                        shiftDown = true;
                        break;
                    case 17:
                        ctrlDown = true;
                        break;
                    default:
                        let parentRecall = varGet("recallParentCategory");
                        if (ctrlDown) {
                            if (event.keyCode == 13) { 
                                event.preventDefault();
                                redirect("menuModificationItemEditor.php");
                            }
                            else if (event.keyCode == 46) { // CTRL + DELETE >>>>> Delete current record if one selected
                                event.preventDefault();
                                let btnDelete = document.querySelector("#btnDelete");
                                if (btnDelete != null) {
                                    btnDelete.click(); 
                                }
                                else { // Nothing to delete. Generate error message.
                                    varSet("errorMessage", "Oops! Nothing to delete.", null, true, true);
                                }
                            }
                            else if (event.keyCode == 8) { // CTRL + BACKSPACE >>>>> Reset form
                                event.preventDefault();
                                document.querySelector("#btnReset").click();
                            }
                            else if (event.keyCode == 77) { // CTRL + M >>>>> Go to mod editor window.
                                event.preventDefault();
                                if (shiftDown) {
                                    redirect("menuItemEditor.php");  
                                }
                                else {
                                    redirect("menuCategoryEditor.php");
                                }
                            }
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

            ///////////////////////////////////////////////////
            //             MENU REDIRECT FUNCTION
            ///////////////////////////////////////////////////

            function redirect(loc, parentCategory) {
                with (document.querySelector("#frmRedirect")) {
                    setAttribute("action", loc);
                    submit();
                }
                
            }
        </script>
        <style>
            #modCatAssocDiv {
                margin-top: 1.5rem;
                grid-column: 1 / span 2;
                grid-template-columns: 1fr 1fr;
            }
            #menuItemList, #modItemList {
                max-height: 8rem;
                min-height: 8rem;
                overflow: auto;
                background-color: black;
            }
            .listHeader {
                font-size: 1.5rem;
                font-weight: bold;
                margin-top: .5rem;
            }
            #txtModItemFilter {
                width: 100%;
            }
            .hidden {
                display: none;
                height: 0;
                max-height: 0;
            }
        </style>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()" class="fadeIntro">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="sessionBody">
                <div id="menuEditorNavBar">
                    <button id="btnMenuCategoryEditor" type="button" class="button menuNavButton">New Category</button>
                    <button id="btnMenuItemEditor" type="button" class="button menuNavButton">New Item</button>
                    <button id="btnMenuModificationEditor" type="button" class="button menuNavButton">Edit Mod Items</button>
                </div>
                <fieldset>
                    <legend>Menu&nbsp;Modification<br>Category&nbsp;Editor</legend>
                    <label for="selMenuTitle"></label>
                    <select id="selMenuTitle">
                        <option value="">New Mod Category</option>
                        <?php
                            $sql = "SELECT * FROM MenuModificationCategories WHERE visible = 1 ORDER BY title";
                            $result = connection()->query($sql);
                            /////////////////////////////////////////////////////////
                            // populate all MenuModificationCategories from database.
                            // the values should be the quick quick code
                            /////////////////////////////////////////////////////////
                            while ($row = $result->fetch_assoc()) {
                                echo('<option value="' .$row['quickCode']. '")');
                                if (isset($_POST['quickCode']) && $_POST['quickCode'] == $row['quickCode']) {
                                    echo(" selected");
                                }
                                echo('>' .$row['title']. '</option>');
                            }
                        ?>
                    </select>
                    <label for="txtMenuTitle">Mod Category Name</label>
                    <input id="txtMenuTitle" name="menuTitle" required maxlength="75" <?php if(isset($_POST['menuTitle'])) { echo(' value="' . $_POST['menuTitle'] . '"'); } ?>>
                    <label for="selCategoryType">Mod Category Type</label>
                    <select id="selCategoryType" name="categoryType" required>
                        <?php if (!isset($_POST['categoryType'])): ?>
                            <option value='MandatoryOne'>MandatoryOne</option>
                            <option value='MandatoryAny'>MandatoryAny</option>
                            <option value='OptionalOne'>OptionalOne</option>
                            <option value='OptionalAny'>OptionalAny</option>
                        <?php elseif ($_POST['categoryType'] == 'MandatoryOne'): ?>
                            <option value='MandatoryOne' selected>MandatoryOne</option>
                            <option value='MandatoryAny'>MandatoryAny</option>
                            <option value='OptionalOne'>OptionalOne</option>
                            <option value='OptionalAny'>OptionalAny</option>
                        <?php elseif ($_POST['categoryType'] == 'MandatoryAny'): ?>
                            <option value='MandatoryOne'>MandatoryOne</option>
                            <option value='MandatoryAny' selected>MandatoryAny</option>
                            <option value='OptionalOne'>OptionalOne</option>
                            <option value='OptionalAny'>OptionalAny</option>
                        <?php elseif ($_POST['categoryType'] == 'OptionalOne'): ?>
                            <option value='MandatoryOne'>MandatoryOne</option>
                            <option value='MandatoryAny'>MandatoryAny</option>
                            <option value='OptionalOne' selected>OptionalOne</option>
                            <option value='OptionalAny'>OptionalAny</option>
                        <?php elseif ($_POST['categoryType'] == 'OptionalAny'): ?>
                            <option value='MandatoryOne'>MandatoryOne</option>
                            <option value='MandatoryAny'>MandatoryAny</option>
                            <option value='OptionalOne'>OptionalOne</option>
                            <option value='OptionalAny' selected>OptionalAny</option>
                        <?php endif; ?>
                    </select>
                    <div id="modCatAssocDiv">
                        <?php
                            // Get all of the existing associations for this mod category.

                             // if existing mod category, get all the menu items that have this mod category
                             $associatedMenuItems = "";
                             if (isset($_POST['quickCode'])) {
                                 $sql = "SELECT parentQuickCode as quickCode FROM MenuAssociations WHERE childQuickCode = '" .$_POST['quickCode']. "';";
                                 $result = connection()->query($sql);
                                 while ($row = $result->fetch_assoc()) {
                                     $associatedMenuItems .= "," . $row['quickCode'];
                                 }
                             }

                             // if existing mod category, get all the mod items that are contained in this mod category
                             $associatedModItems = "";
                             if (isset($_POST['quickCode'])) {
                                 $sql = "SELECT childQuickCode as quickCode FROM MenuAssociations WHERE parentQuickCode = '" .$_POST['quickCode']. "';";
                                 $result = connection()->query($sql);
                                 while ($row = $result->fetch_assoc()) {
                                     $associatedModItems .= "," . $row['quickCode'];
                                 }
                             }
                        ?>
                        <div id="modMenuItemAssoc" class="listWithHeader">
                            <div class="listHeader">Associated Menu Items</div>
                            <div id="menuItemList">
                                <?php
                                    // populate the menu items list, and make items checked if menu item has this mod category
                                    $sql = "SELECT title, quickCode FROM MenuItems WHERE visible = 1 ORDER BY title";
                                    $result = connection()->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        if (strpos($associatedMenuItems,$row['quickCode']) > 0) {
                                            echo("<input type='checkbox' id='" .$row['quickCode'].  "' name='parentMenuItems[]' value='" .$row['quickCode'].  "' checked>");
                                        }
                                        else {
                                            echo("<input type='checkbox' id='" .$row['quickCode']. "' name='parentMenuItems[]' value='" .$row['quickCode'].  "'>");
                                        }
                                        echo("<label for='" .$row['quickCode']. "'>" .$row['title']. "</label><br>");
                                    }
                                ?>
                            </div>
                            <div class="listHeader">Associated Mod Items</div>
                            <input type="text" id="txtModItemFilter" placeholder="Filter: delimeter `" name="filterString" <?php if (isset($_POST['filterString'])) {echo(" value='" .$_POST['filterString']. "'");} ?>>
                            <div id="modItemList">
                                <?php
                                    // populate the menu items list, and make items checked if menu item has this mod category
                                    $sql = "SELECT title, quickCode FROM MenuModificationItems WHERE visible = 1 ORDER BY title";
                                    $result = connection()->query($sql);
                                    while ($row = $result->fetch_assoc()) {
                                        if (strpos($associatedModItems,$row['quickCode']) > 0) {
                                            echo("<input type='checkbox' id='" .$row['quickCode'].  "' name='childMenuModItems[]' value='" .$row['quickCode'].  "' checked>");
                                        }
                                        else {
                                            echo("<input type='checkbox' id='" .$row['quickCode']. "' name='childMenuModItems[]' value='" .$row['quickCode'].  "'>");
                                        }
                                        echo("<label for='" .$row['quickCode']. "'>" .$row['title']. "</label><br>");
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php if (isset($_POST['quickCode']) && 
                            (!isset($_POST['delete']) || isset($errorMessage))): ?>
                        <div class="buttonGroup3">
                            <input id="btnSubmit" type="submit" name="commit" value="Update" class="button">
                            <button id="btnReset" type="button" class="button">Clear</button>
                            <input id="btnDelete" type="submit" name="delete" value="Delete" class="button">
                        </div>
                    <?php else: ?>
                        <div class="buttonGroup2">
                            <input id="btnSubmit" type="submit" name="commit" value="Create" class="button">
                            <button id="btnReset" type="button" class="button">Clear</button>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($errorMessage)): ?>
                        <div class="errorMessage">
                            <?php echo $errorMessage; ?>
                        </div>
                    <?php elseif (isset($message)): ?>
                        <div class="message">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>
                </fieldset>
            </div>
            <?php unset($_POST['delete'], 
                        $_POST['commit'],
                        $_POST['menuTitle'],
                        $_POST['categoryType'],
                        $_POST['filterString']);  
                     // $_POST['quickCode'] stays ?>

            <?php require_once '../Resources/PHP/display.php'; ?>
           
        </form>
        <form id="frmRedirect" style="display: none;" method="POST">
            <input type="text" id="txtParentCategory" name="parentCategory">
            <input type="text" id="txtRecallParentCategory" name="recallParentCategory">
            <input type="text" id="txtLookAt" name="lookAt">
            <input type="text" id="txtQC" name="quickCode">
        </form>
    </body>
</html>
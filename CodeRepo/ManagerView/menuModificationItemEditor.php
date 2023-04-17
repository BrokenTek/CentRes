<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']); ?>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
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
        if(isset($_POST['quickCode'])&&!isset($_POST['menuTitle'])){
            $sql = "SELECT * FROM MenuModificationItems WHERE quickCode = '" .$_POST['quickCode']. "';";
            $fieldData = connection()->query($sql)->fetch_assoc();
            
            //just in case the quickCode persists after a deletion, these statements are wrapped in a condition
            //to prevent unwanted warnings from showing up. Note to self: unwrap this if you account for this.
            if(isset($fieldData)){
                $_POST['menuTitle'] = $fieldData['title'];
                $_POST['quantifierString'] = $fieldData['quantifierString'];
            }
        }

        if(isset($_POST['commit'])){
            if($_POST['commit'] == 'Update'){
                //attempt to update the category to reflect the changes made in the form
                $sql = "UPDATE MenuModificationItems SET title = ?, quantifierString = ? WHERE quickCode = ?;";      
                $sql = connection()->prepare($sql);
                $sql->bind_param('sss', $_POST['menuTitle'], $_POST['quantifierString'], $_POST['quickCode']);
                $sql->execute();

                $message = "Menu Modification Item updated.";
            }
            else{
                //attempt to create the new modification item
                $sql = "INSERT INTO MenuModificationItems (title, quantifierString) VALUES (?, ?);";
                $sql = connection()->prepare($sql);
                $sql->bind_param('ss', $_POST['menuTitle'], $_POST['quantifierString']);
                $sql->execute();

                //get its new quick code and bind it to the $_POST variable.
                $sql2 = "SELECT quickCode FROM MenuModificationItems WHERE title = ? ORDER BY counter DESC LIMIT 1;";
                $sql2 = connection()->prepare($sql2);
                $sql2->bind_param('s', $_POST['menuTitle']);
                $sql2->execute();
                $_POST['quickCode'] = $sql2->get_result()->fetch_assoc()['quickCode'];
                $message = "<b>" .$_POST['menuTitle']. "</b> created.";
            }
        }
        if(isset($_POST['delete'])){
            $sql = "UPDATE MenuModificationItems SET visible = FALSE WHERE quickCode = '".$_POST['quickCode']."';";
            connection()->query($sql);
            $message = "<b>" .$_POST['menuTitle']. "</b> deleted.";
            unset($_POST['quickCode'], $_POST['menuTitle']);
        }
    }
    catch (Exception $e) {
        $errorMessage = "An unexpected error occurred, please contact your system administrator or developer(s). ".$e->getMessage();
    }
    if (!isset($errorMessage) && isset($_POST['errorMessage'])) {
        $errorMessage = $_POST['errorMessage'];
        unset($_POST['errorMessage']);
    }
?>

<!DOCTYPE html>

<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/menuEditorStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/modOptionStyle.css">
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script>
        <script src="../Resources/JavaScript/modOptionUtility.js" type="text/javascript"></script>
        <script>
            function allElementsLoaded() {
                window.addEventListener("message", window.processJSONeventCall);
                // any startup tasks go here after page has fully loaded.

                document.querySelector("#selMenuTitle").addEventListener("change", selChanged);

                document.querySelector("#txtQuantifierString").addEventListener("input", generateModOption);

                document.querySelector("#selModPreviewCatType").addEventListener("change", generateModOption);

                document.querySelector("#txtMenuTitle").addEventListener("input", generateModOption);

                document.querySelector("#btnReset").addEventListener("pointerdown", btnResetPressed);

                document.querySelector("#sessionForm").addEventListener("keydown", keydown);
                document.querySelector("#sessionForm").addEventListener("keyup", keyup);

                document.querySelector("#btnMenuCategoryEditor").addEventListener("pointerdown", 
                function() {redirect("menuCategoryEditor.php");});

                document.querySelector("#btnMenuItemEditor").addEventListener("pointerdown", 
                function() { redirect("menuItemEditor.php"); });

                document.querySelector("#btnMenuModificationEditor").addEventListener("pointerdown", 
                function() {redirect("menuModificationCategoryEditor.php");});

                with (document.querySelector("#txtMenuTitle")) {
                    focus();
                    setSelectionRange(0, value.length);
                }

                generateModOption();
            }

            function btnResetPressed(event) {
                varRem("quickCode");
                document.getElementById("selMenuTitle").selectedIndex = 0;
                document.getElementById("txtMenuTitle").value = "";
                document.getElementById("txtQuantifierString").value = "";
                document.getElementById("btnSubmit").setAttribute("value", "Create");
                if (document.getElementById("btnDelete") != null) {    
                    document.getElementById("btnDelete").remove();
                }
                generateModOption();
                document.getElementById("selMenuTitle").focus();
            }

            function selChanged(event) {
                varRem("title");
                varSet("quickCode", this.options[this.selectedIndex].value);
                document.getElementById("txtMenuTitle").removeAttribute("value");
                document.getElementById("txtQuantifierString").removeAttribute("value");
                updateDisplay(null, true);
            }

            function generateModOption() {
                let quickCode = varGet("quickCode");
                if (quickCode === undefined) {
                    quickCode = "X----";
                }
                let text = document.getElementById("txtMenuTitle").value;
                let quantifierString = document.getElementById("txtQuantifierString").value;
                let categoryType = document.getElementById("selModPreviewCatType").value;
                document.getElementById("modPreview").innerHTML = generateModOptionDiv(quickCode, text, quantifierString, true, categoryType);
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
                                redirect("menuModificationCategoryEditor.php");
                            }
                            else if (event.keyCode == 46) { // CTRL + DELETE >>>>> Delete current record if one selected
                                let btnDelete = document.querySelector("#btnDelete");
                                if (btnDelete != null) {
                                    btnDelete.click(); 
                                }
                                else { // Nothing to delete. Generate error message.
                                    varSet("errorMessage", "Oops! Nothing to delete.", null, true, true);
                                }
                            }
                            else if (event.keyCode == 8) { // CTRL + BACKSPACE >>>>> Reset form
                                document.querySelector("#btnReset").click();
                            }
                            else if (event.keyCode == 77) { // CTRL + M >>>>> Go to mod editor window.
                                redirect("menuCategoryEditor.php");
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
            #previewWrapper {
                grid-column: 1 / span 2;
                display: grid;
                grid template-columns; max-content;
                border: .125rem solid white;
                margin-top: 1rem;
                margin-bottom: 1.5rem;
                min-height: 3rem;
                font-weight: bold;
            }
            #previewWrapper > * {
                margin-inline: auto;
            }
           
        </style>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()" class="fadeIntro">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="sessionBody">
                <div id="menuEditorNavBar">
                    <button id="btnMenuCategoryEditor" type="button" class="button menuNavButton">New Category</button>
                    <button id="btnMenuItemEditor" type="button" class="button menuNavButton">New Item</button>
                    <button id="btnMenuModificationEditor" type="button" class="button menuNavButton">Edit Mod Cats</button>
                </div>
                <fieldset>
                    <legend>Menu&nbsp;Modification<br>Item&nbsp;Editor</legend>
                    <label for="selMenuTitle"></label>
                    <select id="selMenuTitle">
                        <option value="">New Mod Item</option>
                        <?php
                            $sql = "SELECT * FROM MenuModificationItems WHERE visible = 1 ORDER BY title";
                            $result = connection()->query($sql);
                            /////////////////////////////////////////////////////////
                            // populate all MenuModificationItems from database.
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
                    <label for="txtMenuTitle">Mod Item Name</label>
                    <input id="txtMenuTitle" name="menuTitle" required maxlength="75" <?php if(isset($_POST['menuTitle'])) { echo(' value="' . $_POST['menuTitle'] . '"'); } ?>>
                    <label for="txtQuantifierString">Quantifier&nbsp;String</label>
                    <input id="txtQuantifierString" name="quantifierString" maxlength="1000" <?php if(isset($_POST['quantifierString'])) { echo(' value="' . $_POST['quantifierString'] . '"'); } ?>>
                    <div id="previewWrapper">
                        <div id="modPreviewHeader">Mod Control Preview</div>
                        <div>
                            <label id="lblModPreviewCatType" for="selModPreviewCatType">Category Type</label>
                        </div>
                        <select id="selModPreviewCatType">
                            <option value="MandatoryOne">Mandatory One</option>
                            <option value="MandatoryAny">Mandatory Any</option>
                            <option value="OptionalOne">Optional One</option>
                            <option value="OptionalAny">Optional Any</option>
                        </select>
                        <div id="modPreview">
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
            </div>
            

            <?php unset($_POST['delete'], 
                        $_POST['commit'],
                        $_POST['menuTitle'],
                        $_POST['quantifierString']);  
                     // $_POST['quickCode'] stays ?>

            <?php require_once '../Resources/php/display.php'; ?>
           
        </form>
        <form id="frmRedirect" style="display: none;" method="POST">
            <input type="text" id="txtParentCategory" name="parentCategory">
            <input type="text" id="txtRecallParentCategory" name="recallParentCategory">
            <input type="text" id="txtLookAt" name="lookAt">
            <input type="text" id="txtQC" name="quickCode">
        </form>
    </body>
</html>
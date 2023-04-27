<?php require_once '../Resources/PHP/dbConnection.php'; ?>
<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']); ?>
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
        if (isset($_POST['menuTitle'])) {
            $_POST['menuTitle'] = str_replace("`", "", $_POST['menuTitle']);
        }
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
        if (!isset($_POST['quantifierString'])) {
            $_POST['quantifierString'] = "";
        }
        if(isset($_POST['commit'])){
            if($_POST['commit'] == 'Update'){
                //attempt to get the original title. This will be displayed in the message to user.
                $sql = "SELECT title FROM MenuModificationItems WHERE quickCode = '" .$_POST['quickCode']. "';";
                $result = connection()->query($sql);
                $title = $result->fetch_assoc()['title'];

                //attempt to update the category to reflect the changes made in the form
                
                $sql = "UPDATE MenuModificationItems SET title = ?, quantifierString = ? WHERE quickCode = ?;";      
                $sql = connection()->prepare($sql);
                $sql->bind_param('sss', $_POST['menuTitle'], $_POST['quantifierString'], $_POST['quickCode']);
                $sql->execute();

                $message = "<b>$title</b> updated.";
            }
            else{
                //attempt to create the new modification item
                $sql = "INSERT INTO MenuModificationItems (title, quantifierString) VALUES (?, ?);";
                $sql = connection()->prepare($sql);
                $sql->bind_param('ss', $_POST['menuTitle'], $_POST['quantifierString']);
                $sql->execute();
                $message = "<b>" .$_POST['menuTitle']. "</b> created.";

                unset($_POST['menuTitle']);
            }
        }
        if(isset($_POST['delete'])){
            $sql = "DELETE FROM MenuModificationItems WHERE quickCode = '".$_POST['quickCode']."';";
            connection()->query($sql);

            $sql = "DELETE FROM MenuAssociations WHERE childQuickCode = '".$_POST['quickCode']."';";
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
?>

<!DOCTYPE html>

<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/menuEditorStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/modOptionStyle.css">
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script>
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

                setTimeout(function() { 
                    let msgs = document.getElementsByClassName("message");
                    if (msgs.length == 1) {
                        msgs[0].classList.add("disappear");
                    } 
                }, 3000);

                setTimeout(function() { 
                    let errs = document.getElementsByClassName("errorMessage");
                    if (errs.length == 1) {
                        errs[0].classList.add("disappear");
                    } 
                }, 3000);
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
                    quickCode = "N----";
                }
                let text = document.getElementById("txtMenuTitle").value;
                with (document.getElementById("txtQuantifierString")) {
                    if (value.length > 0) {
                        value = value.replaceAll("  ", "|");
                    }
                }
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
                                event.preventDefault();
                                redirect("menuModificationCategoryEditor.php");
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
                                    redirect("menuCategoryEditor.php");
                                }
                                else {
                                    redirect("menuItemEditor.php");
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
            #previewWrapper {
                grid-column: 1 / span 2;
                display: grid;
                grid-template-columns: max-content;
                border: .125rem solid white;
                margin-top: 1rem;
                margin-bottom: 1.5rem;
                min-height: 3rem;
                font-weight: bold;
                margin-inline: auto;
            }
            #previewWrapper > * {
                margin-inline: auto;
                padding-inline: 2rem;
                margin-block: .125rem;
            }
            html, #sessionForm {
                background-color: transparent !important;
                background-image: none;
            }
            form {
                background: rgb(119,119,119);
                background: radial-gradient(circle, rgba(119,119,119,1) 0%, rgba(68,68,68,1) 100%);
                border-radius: 1rem;
                border: .25rem solid white;
                margin: auto;
            }
            .sessionBody {
                margin-inline: auto !important;
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
                            $sql = "SELECT * FROM MenuModificationItems ORDER BY title";
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
                    <input id="txtMenuTitle" name="menuTitle" required maxlength="75" spellcheck="false" <?php if(isset($_POST['menuTitle'])) { echo(' value="' . $_POST['menuTitle'] . '"'); } ?>>
                    <label for="txtQuantifierString">Quantifier&nbsp;String</label>
                    <input id="txtQuantifierString" name="quantifierString" maxlength="1000" spellcheck="false" <?php if(isset($_POST['quantifierString'])) { echo(' value="' . $_POST['quantifierString'] . '"'); } ?>>
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
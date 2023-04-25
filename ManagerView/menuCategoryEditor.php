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
        if(isset($_POST['quickCode'])&&!isset($_POST['menuTitle'])){
            if ($_POST['quickCode'] != 'dtchd') {
                $sql = "SELECT MenuCategories.title AS 'categoryTitle', MenuAssociations.parentQuickCode AS 'parent'
                    FROM (MenuCategories LEFT JOIN MenuAssociations ON MenuCategories.quickCode = MenuAssociations.childQuickCode)
                    WHERE quickCode = '".$_POST['quickCode']."' ORDER BY quickCode DESC LIMIT 1;";
                $fieldData = connection()->query($sql)->fetch_assoc();
                //just in case the quickCode persists after a deletion, these statements are wrapped in a condition
                //to prevent unwanted warnings from showing up. Note to self: unwrap this if you account for this.
                if(isset($fieldData)){
                    $_POST['menuTitle'] = $fieldData['categoryTitle'];
                    $_POST['parentCategory'] = $fieldData['parent'];
                }
            }
            else {
                unset($_POST['quickCode']);
            }
        }

        if(isset($_POST['commit'])){
            if($_POST['commit'] == 'Update'){
                //attempt to get the original title. This will be displayed in the message to user.
                $sql = "SELECT title FROM MenuCategories WHERE quickCode = '" .$_POST['quickCode']. "';";
                $result = connection()->query($sql);
                $title = $result->fetch_assoc()['title'];
                

                //attempt to update the category to reflect the changes made in the form
                $sql = "UPDATE MenuCategories SET title = ? WHERE quickCode = ?;";      
                $sql = connection()->prepare($sql);
                $sql->bind_param('ss', $_POST['menuTitle'], $_POST['quickCode']);
                $sql->execute();

                $sql = "UPDATE MenuAssociations SET parentQuickCode = ? WHERE childQuickCode = ?;";  
                $sql = connection()->prepare($sql);
                $sql->bind_param('ss', $_POST['parentCategory'], $_POST['quickCode']);
                $sql->execute();

                $message = "<b>$title</b> updated.";
                $_POST['lookAt'] = $_POST['quickCode'];
            }
            else{
                //attempt to create the new category
                $sql = "INSERT INTO MenuCategories (title) VALUES (?);";
                $sql = connection()->prepare($sql);
                $sql->bind_param('s', $_POST['menuTitle']);
                $sql->execute();

                //get its new quick code and bind it to the $_POST variable.
                $sql2 = "SELECT quickCode FROM MenuCategories WHERE title = ? ORDER BY quickCode DESC LIMIT 1;";
                $sql2 = connection()->prepare($sql2);
                $sql2->bind_param('s', $_POST['menuTitle']);
                $sql2->execute();
                $_POST['quickCode'] = $sql2->get_result()->fetch_assoc()['quickCode'];

                //make the new association
                $sql3 = "INSERT INTO MenuAssociations (parentQuickCode, childQuickCode)
                        VALUES ('".$_POST['parentCategory']."', '".$_POST['quickCode']."');";
                connection()->query($sql3);
                $message = "<b>" .$_POST['menuTitle']. "</b> created.";

                $_POST['lookAt'] = $_POST['quickCode'];
                unset($_POST['quickCode'], $_POST['title']);
            }

            // menu editor has an event listener for this window to onload.
            // It checks varGet('updated', [ifrActiveWindow]) to see if menu needs to be reloaded. 
            $_POST['updated'] = "true";
        }
        else if(isset($_POST['delete'])){
            // get parent to change lookAt value
            $sql = "SELECT parentQuickCode AS 'parent' FROM MenuAssociations WHERE childQuickCode = '".$_POST['quickCode']."';";
            $result = connection()->query($sql);

            if (mysqli_num_rows($result) == 0) {
                unset($_POST['lookAt']);
            }
            else {
                $_POST['lookAt'] = $result->fetch_assoc()['parent'];
            }

            $sql = "UPDATE MenuAssociations SET parentQuickCode = 'dtchd' WHERE parentQuickCode = '".$_POST['quickCode']."';";
            connection()->query($sql);

            $sql = "DELETE FROM MenuCategories WHERE quickCode = '".$_POST['quickCode']."';";
            connection()->query($sql);

           

            $message = "<b>" .$_POST['menuTitle']. "</b> deleted.";
            unset($_POST['quickCode'], $_POST['menuTitle'], $_POST['parentCategory']);

            // menu editor has an event listener for this window to onload.
            // It checks varGet('updated', [ifrActiveWindow]) to see if menu needs to be reloaded. 
            $_POST['updated'] = "true";
        }
        else if (isset($_POST['inactivate'])){
            // get parent to change lookAt value
            $_POST['lookAt'] = $_POST['quickCode'];

            $sql = "UPDATE MenuAssociations SET parentQuickCode = 'dtchd' WHERE childQuickCode = '".$_POST['quickCode']."';";
            connection()->query($sql);           

            $message = "<b>" .$_POST['menuTitle']. "</b> inactivated.";
            unset($_POST['quickCode'], $_POST['menuTitle'], $_POST['parentCategory']);

            // menu editor has an event listener for this window to onload.
            // It checks varGet('updated', [ifrActiveWindow]) to see if menu needs to be reloaded. 
            $_POST['updated'] = "true";
        }
        else if (isset($_POST['parentCategory']) && !isset($_POST['quickCode']) && !isset($_POST['lookAt'])) {
            $_POST['lookAt'] = $_POST['parentCategory'];
        }
        else {
            $_POST['lookAt'] = "root";
        }

        if (isset($_POST['parentCategory']) && strpos(" " . $_POST['parentCategory'], "!") == 1) {
            
            if ($_POST['parentCategory'] != '!root') {
                $sql = "SELECT parentQuickCode as qc FROM MenuAssociations WHERE childQuickCode = '".substr($_POST['parentCategory'],1). "';";
                $_POST['parentCategory'] = connection()->query($sql)->fetch_assoc()['qc'];
                $_POST['lookAt'] = $_POST['parentCategory'];
            }
            else {
                $errorMessage = "Cannot navigate above root menu item.";
                $_POST['parentCategory'] = 'root';
            }
        }
        if (isset($_POST['lookAt']) && strpos(" " . $_POST['lookAt'], "!") == 1) {
            if ($_POST['lookAt'] != '!root') {
                $sql = "SELECT parentQuickCode as qc FROM MenuAssociations WHERE childQuickCode = '".substr($_POST['lookAt'],1). "';";
                $_POST['lookAt'] = connection()->query($sql)->fetch_assoc()['qc'];
            }
            else {
                $_POST['lookAt'] = 'root';
            }
        }
        if (!isset($_POST['lookAt'])) {
            if (isset($_POST['quickCode'])) {
                $_POST['lookAt'] = $_POST['quickCode'];
            }
            elseif (isset($_POST['parentCategory'])) {
                $_POST['lookAt'] = $_POST['parentCategory'];
            }
        }
        if (isset($_POST['parentCategory']) && isset($_POST['lookAt']) && $_POST['lookAt'] == 'root' && $_POST['parentCategory'] != 'root' ) {
            $_POST['lookAt'] = $_POST['parentCategory'];
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
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script> 
        <script>
            function allElementsLoaded() {
                // any startup tasks go here after page has fully loaded.

                document.querySelector("#selParentCategory").addEventListener("change", selChanged);

                document.querySelector("#btnReset").addEventListener("pointerdown", btnResetPressed);

                let btnDelete = document.querySelector("#btnDelete");
                if (btnDelete != null) {
                    btnDelete.addEventListener("pointerdown", btnDeletePressed);
                }

                document.querySelector("#sessionForm").addEventListener("keydown", keydown);
                document.querySelector("#sessionForm").addEventListener("keyup", keyup);

                document.querySelector("#btnMenuCategoryEditor").addEventListener("pointerdown", 
                function() {redirect("menuCategoryEditor.php", (shiftDown ? "!" : "") + varGet("lookAt"));});

                document.querySelector("#btnMenuItemEditor").addEventListener("pointerdown", 
                function() {
                    redirect("menuItemEditor.php",
                    varExists("lookAt") ? varGet("lookAt") : document.querySelector("#selParentCategory").value); });

                document.querySelector("#btnMenuModificationEditor").addEventListener("pointerdown", 
                function() {redirect("menuModificationCategoryEditor.php");});

                with (document.querySelector("#txtMenuTitle")) {
                    focus();
                    setSelectionRange(0, value.length);
                }

                with (document.querySelector("#selParentCategory")) {
                    addEventListener("change", function() { 
                        varSet("lookAt", varGet("quickCode"));
                        with (document.querySelector("#txtMenuTitle")) {
                            focus();
                            setSelectionRange(0, value.length);
                        }
                     });
                }

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
                varRem("parentCategory");
                document.getElementById("selParentCategory").selectedIndex = 0;
                document.getElementById("txtMenuTitle").value = "";
                updateDisplay(null, true);
            }

            function btnDeletePressed (event) {
                varSet("delete", "yes");
                updateDisplay(null, true);
            }

            function selChanged(event) {
                if (!varExists("quickCode")) {
                    dispatchJSONeventCall("selectMenuObject", {"menuObjectId": this.options[this.selectedIndex].value}, ["ifrMenu"]);
                }
            }

            ///////////////////////////////////////////////////
            //           RAPID ENTRY KEYBOARD EVENTS
            ///////////////////////////////////////////////////

            let ctrlDown = false;
            let shiftDown = false;

            function keydown(event) {
                //alert(event.keyCode);
                switch (event.keyCode) {
                    case 16:
                        shiftDown = true;
                        break;
                    case 17:
                        ctrlDown = true;
                        break;
                    default:
                        if (ctrlDown) {
                            if (event.keyCode == 13 && varExists("lookAt")) { 
                                event.preventDefault();
                                if (shiftDown) { //  CTRL + SHIFT + ENTER >>>>> Navigate to MenuCategory 1 Level Up
                                    redirect("menuCategoryEditor.php", "!" + document.querySelector("#selParentCategory").value);
                                }
                                else { // CTRL ENTER >>>>> Navigate to MenuCategory at Current Level
                                    let target = (varExists("quickCode") ? varGet("quickCode") : document.querySelector("#selParentCategory").value);  
                                    redirect("menuItemEditor.php", target);
                                }
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
                            else if (event.keyCode >= 48 && event.keyCode <= 57 && shiftDown) {
                                event.preventDefault();
                                // CTRL + SHIFT + [0-9] Quick Access to Sub Menu Category. Hopefully Sub Menu Categories won't exceed 10
                                // per parent category.
                                selStr = str_pad(event.keyCode - 48,2,"0", STR_PAD_LEFT) + document.querySelector("#selParentCategory").value;
                                redirect("menuCategoryEditor.php", "!" + document.querySelector("#selParentCategory").value);
                            }
                            else if (event.keyCode == 77) { // CTRL + M >>>>> Go to mod editor window.
                                event.preventDefault();
                                if (shiftDown) {
                                    redirect("menuModificationItemEditor.php");  
                                }
                                else {
                                    redirect("menuModificationCategoryEditor.php");
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
                if (parentCategory != undefined && parentCategory != null) {
                    document.querySelector("#txtParentCategory").setAttribute("value", parentCategory);
                    document.querySelector("#txtRecallParentCategory").setAttribute("value", parentCategory);
                }
                with (document.querySelector("#frmRedirect")) {
                    setAttribute("action", loc);
                    submit();
                }
                
            }

        </script>
        <style>
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
                label {
                    margin-left: auto;
                }
                .errorMessage {
                    margin-inline: auto;
                    width: 100%;
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
                    <legend>Menu&nbsp;Category&nbsp;Editor</legend>
                
                    <label for="selParentCategory">Parent Category</label>
                    <select id="selParentCategory" name="parentCategory" required>
                        <option value="root">Main Menu</option>
                        <option value="dtchd">Inactive</option>
                        <?php
                            $sql = "SELECT * FROM MenuCategories ORDER BY title";
                            $result = connection()->query($sql);
                            if (mysqli_num_rows($result) == 0) {
                                $errorMessage = "Create a Menu Category Please!";
                            }
                            else {
                                /////////////////////////////////////////////////////////
                                // populate all MenuCategory from database.
                                // the values should be the quick quick code
                                /////////////////////////////////////////////////////////
                                while ($row = $result->fetch_assoc()) {
                                    echo('<option value="' .$row['quickCode']. '")');
                                    if (isset($_POST['parentCategory']) && $_POST['parentCategory'] == $row['quickCode']) {
                                        echo(" selected");
                                    }
                                    echo('>' .$row['title']. '</option>');
                                }
                            }
                        ?>
                    </select>
                    <label for="txtMenuTitle">Category Title</label>
                    <input id="txtMenuTitle" name="menuTitle" maxlength=75 spellcheck="false"<?php if(isset($_POST['menuTitle'])) { echo(' value="' . $_POST['menuTitle'] . '"'); } ?>>
                    <?php if (isset($_POST['quickCode']) && 
                            (!isset($_POST['delete']) || isset($errorMessage))): ?>
                        <?php if (isset($_POST['parentCategory']) && $_POST['parentCategory'] == 'dtchd'): ?> 
                            <div class="buttonGroup3">
                                <input id="btnSubmit" type="submit" name="commit" value="Update" class="button">
                                <button id="btnReset" type="button" class="button">Clear</button>
                                <input id="btnDelete" type="submit" name="delete" value="Delete" class="button">
                            </div>
                        <?php else: ?>
                            <div class="buttonGroup4">
                                <input id="btnSubmit" type="submit" name="commit" value="Update" class="button">
                                <button id="btnReset" type="button" class="button">Clear</button>
                                <input id="btnInactivate" type="submit" name="inactivate" value="Inactivate" class="button">
                                <input id="btnDelete" type="submit" name="delete" value="Delete" class="button">
                            </div>
                        <?php endif; ?>
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
                        $_POST['parentCategory']);  
                     // $_POST['quickCode'] stays ?>

            <?php require_once '../Resources/PHP/display.php'; ?>
           
        </form>
        <form id="frmRedirect" style="display: none;" method="POST">
            <input type="text" id="txtParentCategory" name="parentCategory">
            <input type="text" id="txtRecallParentCategory" name="recallParentCategory">
            <input type="text" id="txtQC" name="quickCode">
            <input type="text" id="txtLookAt" name="lookAt">
        </form>
        </form>
    </body>
</html>
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
            $sql = "SELECT MenuItems.title AS 'title',
                            MenuItems.price AS 'price',
                            MenuItems.route AS 'route',
                            MenuAssociations.parentQuickCode AS 'parent'
                    FROM (MenuItems LEFT JOIN MenuAssociations ON MenuItems.quickCode = MenuAssociations.childQuickCode)
                    WHERE quickCode = '".$_POST['quickCode']."' ORDER BY quickCode DESC LIMIT 1;";
            $fieldData = connection()->query($sql)->fetch_assoc();
            
            //just in case the quickCode persists after a deletion, these statements are wrapped in a condition
            //to prevent unwanted warnings from showing up. Note to self: unwrap this if you account for this.
            if(isset($fieldData)){
                $_POST['menuTitle'] = $fieldData['title'];
                $_POST['price'] = $fieldData['price'];
                $_POST['route'] = $fieldData['route'];
                $_POST['parentCategory'] = $fieldData['parent'];
            }
        }

        if(isset($_POST['commit'])){
            if($_POST['commit'] == 'Update'){
                //attempt to get the original title. This will be displayed in the message to user.
                $sql = "SELECT title FROM MenuItems WHERE quickCode = '" .$_POST['quickCode']. "';";
                $result = connection()->query($sql);
                $title = $result->fetch_assoc()['title'];
                

                //attempt to update the category to reflect the changes made in the form
                $sql = "UPDATE MenuItems SET title = ?, route = ?, price = ? WHERE quickCode = ?;";      
                $sql = connection()->prepare($sql);
                $sql->bind_param('ssds', $_POST['menuTitle'], $_POST['route'], $_POST['price'], $_POST['quickCode']);
                $sql->execute();

                try {
                    $sql = "UPDATE MenuAssociations SET parentQuickCode = ? WHERE childQuickCode = ?;";  
                    $sql = connection()->prepare($sql);
                    $sql->bind_param('ss', $_POST['parentCategory'], $_POST['quickCode']);
                    $sql->execute();
                }
                catch (Exception $ex) {
                    
                }

                $message = "<b>$title</b> updated.";
                $_POST['lookAt'] = $_POST['quickCode'];
            }
            else{
                //attempt to create the new item
                $sql = "INSERT INTO MenuItems (title, route, price) VALUES (?, ?, ?);";
                $sql = connection()->prepare($sql);
                $sql->bind_param('ssd', $_POST['menuTitle'], $_POST['route'], $_POST['price']);
                $sql->execute();

                //get its new quick code and bind it to the $_POST variable.
                $sql2 = "SELECT quickCode FROM MenuItems WHERE title = ? ORDER BY quickCode DESC LIMIT 1;";
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
                unset($_POST['quickCode'], $_POST['menuTitle']);
            }

            // menu editor has an event listener for this window to onload.
            // It checks varGet('updated', [ifrActiveWindow]) to see if menu needs to be reloaded. 
            $_POST['updated'] = "true";
        }
        if(isset($_POST['inactivate'])){
            $sql = "UPDATE MenuAssociations SET parentQuickCode = 'dtchd' WHERE childQuickCode = '".$_POST['quickCode']."';";
            connection()->query($sql);
            $message = "<b>" .$_POST['menuTitle']. "</b> inactivated.";
            $_POST['lookAt'] = $_POST['parentCategory'];  
            unset($_POST['quickCode'], $_POST['menuTitle']);

            // menu editor has an event listener for this window to onload.
            // It checks varGet('updated', [ifrActiveWindow]) to see if menu needs to be reloaded. 
            $_POST['updated'] = "true";
        }
        else if(isset($_POST['delete'])){
            $sql = "SELECT COUNT(*) AS cnt FROM TicketItems WHERE menuItemQuickCode = '".$_POST['quickCode']."';";
            $count = connection()->query($sql)->fetch_assoc()['cnt'];
            if ($count > 0) {
                $errorMessage = "<b>" .$_POST['menuTitle']. "</b> has already been ordered at least once. Make sure this menu item is inactive and archived before deleting.";
            }
            else {
                // permenantly delete the menu item
                $sql = "DELETE FROM MenuAssociations WHERE childQuickCode = '".$_POST['quickCode']."' OR parentQuickCode = '" .$_POST['quickCode']. "';";
                connection()->query($sql);

                $sql = "DELETE FROM MenuItems WHERE quickCode = '".$_POST['quickCode']."';";
                connection()->query($sql);

                $message = "<b>" .$_POST['menuTitle']. "</b> deleted.";
                $_POST['lookAt'] = $_POST['parentCategory'];  
                unset($_POST['quickCode'], $_POST['menuTitle']);

                // menu editor has an event listener for this window to onload.
                // It checks varGet('updated', [ifrActiveWindow]) to see if menu needs to be reloaded. 
                $_POST['updated'] = "true";
            }
        }
        else if (isset($_POST['parentCategory']) && !isset($_POST['quickCode']) && !isset($_POST['lookAt'])) {
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
                window.addEventListener("message", window.processJSONeventCall);
                // any startup tasks go here after page has fully loaded.

                document.querySelector("#btnReset").addEventListener("pointerdown", btnResetPressed);

                document.querySelector("#sessionForm").addEventListener("keydown", keydown);
                document.querySelector("#sessionForm").addEventListener("keyup", keyup);

                document.querySelector("#btnMenuCategoryEditor").addEventListener("pointerdown", 
                function() {redirect("menuCategoryEditor.php", (shiftDown ? "!" : "") + document.querySelector("#selParentCategory").value);});

                document.querySelector("#btnMenuItemEditor").addEventListener("pointerdown", 
                function() { redirect("menuItemEditor.php", document.querySelector("#selParentCategory").value); });

                document.querySelector("#btnMenuModificationEditor").addEventListener("pointerdown", 
                function() {redirect("menuModificationCategoryEditor.php");});

                with (document.querySelector("#txtMenuTitle")) {
                    focus();
                    setSelectionRange(0, value.length);
                } 

                with (document.querySelector("#selParentCategory")) {
                    addEventListener("change", function() {  
                        varSet("lookAt", value);
                        varSet("recallParentCategory", value);
                        with (document.querySelector("#txtMenuTitle")) {
                            focus();
                            setSelectionRange(0, value.length);
                        }
                     });
                }

                if (varExists("lookAt")) {
                    setTimeout(() => {
                        dispatchJSONeventCall("selectMenuObject", {"menuObjectId": varGet("lookAt")}, ["ifrMenu"]);
                    }, 1000);    
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
                varSet("parentCategory", "root");
                varSet("recallParentCategory", "root");
                //varRem("lookAt");
                varSet("parentCategory", "root");
                document.getElementById("selParentCategory").selectedIndex = 0;
                document.getElementById("txtMenuTitle").value = "";
                document.getElementById("txtMenuTitle").value = "";
                document.getElementById("txtPrice").value = "";
                document.getElementById("txtRoute").value = "";
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
                            if (event.keyCode == 13 && parentRecall !== undefined && parentRecall != null) { 
                                event.preventDefault();
                                if (shiftDown) { //  CTRL + SHIFT + ENTER >>>>> Navigate to MenuCategory 1 Level Up
                                    redirect("menuCategoryEditor.php", "!" + parentRecall.replace("!", ""));
                                }
                                else { // CTRL ENTER >>>>> Navigate to MenuCategory at Current Level
                                    redirect("menuCategoryEditor.php", parentRecall);
                                }
                            }
                            else if (event.keyCode == 13) {
                                redirect("menuCategoryEditor.php", "root");
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
                                    redirect("menuModificationCategoryEditor.php");
                                }
                                else {
                                    redirect("menuModificationItemEditor.php");
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
                    document.querySelector("#txtLookAt").setAttribute("value", parentCategory);
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
                    <legend>Menu&nbsp;Item&nbsp;Editor</legend>
                
                    <label for="selParentCategory">Parent Category</label>
                    <select id="selParentCategory" name="parentCategory" required>
                        <option value="dtchd">Inactive</option>
                        <?php
                            $sql = "SELECT * FROM MenuCategories ORDER BY title;";
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
                    <label for="txtMenuTitle">Menu Item Name</label>
                    <input id="txtMenuTitle" name="menuTitle" required maxlength=75 spellcheck="false" <?php if(isset($_POST['menuTitle'])) { echo(' value="' . $_POST['menuTitle'] . '"'); } ?>>
                    <label for="txtPrice">Price</label>
                    <input id="txtPrice" name="price" pattern="^(0|[1-9]\d*)?(\.\d{1,2})?$" required <?php if(isset($_POST['price'])) { echo(' value="' . $_POST['price'] . '"'); } ?>>
                    <label for="txtRoute">Route</label>
                    <input id="txtRoute" name="route" maxlength="1" <?php if(isset($_POST['route'])) { echo(' value="' . $_POST['route'] . '"'); } ?>>
                    <?php if (isset($_POST['quickCode']) && 
                            (!isset($_POST['delete']) || isset($errorMessage))): ?>
                        <div class="buttonGroup4">
                            <input id="btnSubmit" type="submit" name="commit" value="Update" class="button">
                            <button id="btnReset" type="button" class="button">Clear</button>
                            <input id="btnInactivate" type="submit" name="inactivate" value="Inactivate" class="button">
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
                        $_POST['price'],
                        $_POST['route'],
                        $_POST['parentCategory']);  
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
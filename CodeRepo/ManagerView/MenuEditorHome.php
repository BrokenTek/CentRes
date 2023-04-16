
<!DOCTYPE html>

<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/menuEditorStyle.css">
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        <script>
            function allElementsLoaded() {
                // any startup tasks go here after page has fully loaded.
                
                document.querySelector("#btnMenuCategoryEditor").addEventListener("pointerdown", 
                function() { location.replace("menuCategoryEditor.php") });

                document.querySelector("#btnMenuItemEditor").addEventListener("pointerdown", 
                function() { location.replace("menuItemEditor.php") });

                document.querySelector("#btnMenuModificationEditor").addEventListener("pointerdown", 
                function() { location.replace("MenuModificationEditor.php") });
            }



        </script>
        
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        <form id="frmMenuEditorHome" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="sessionBody">
                <div id="menuEditorNavBar">
                    <button id="btnMenuCategoryEditor" type="button" class="button menuNavButton">New Category</button>
                    <button id="btnMenuItemEditor" type="button" class="button menuNavButton">New Item</button>
                    <button id="btnMenuModificationEditor" type="button" class="button menuNavButton"  style="display: none;">Mods Editor</button>
                </div>
                <fieldset>
                    <legend>Menu&nbsp;Editor&nbsp;Home</legend>
                    <div>Press a red menu category<br>or one of the orange buttons above<br>to get started.</div> 
                </fieldset>
            </div>  
            
        </form>
        <form id="frmRedirect" style="display: none;" method="POST">
            <input type="text" id="txtParentCategory" name="parentCategory">
            <input type="text" id="txtRecallParentCategory" name="recallParentCategory">
            <input type="text" id="txtLookAt" name="lookAt">
            <input type="text" id="txtQC" name="quickCode">
        </form>
    </body>
</html>
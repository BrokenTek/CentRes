
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
                
                document.querySelector("#btnMenuCategoryEditor").addEventListener("pointerdown", 
                function() { location.replace("menuCategoryEditor.php") });

                document.querySelector("#btnMenuItemEditor").addEventListener("pointerdown", 
                function() { location.replace("menuItemEditor.php") });

                document.querySelector("#btnMenuModificationEditor").addEventListener("pointerdown", 
                function() { location.replace("menuModificationCategoryEditor.php") });

                document.querySelector("#sessionForm").addEventListener("keydown", keydown);
                document.querySelector("#sessionForm").addEventListener("keyup", keyup);
            }

             ///////////////////////////////////////////////////
            //           RAPID ENTRY KEYBOARD EVENTS
            ///////////////////////////////////////////////////
            
            let ctrlDown = false;
            let shiftDown = false;

            function keydown(event) {
                switch (event.keyCode) {
                    case 16:
                        shiftDown = true;
                        break;
                    case 17:
                        ctrlDown = true;
                        break;
                    default:
                        if (ctrlDown) {
                            if (event.keyCode == 13) { 
                                event.preventDefault();
                                if (shiftDown) {
                                    redirect("menuItemEditor.php");  
                                }
                                else {
                                    redirect("menuCategoryEditor.php");
                                }
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
            #frmMenuEditorHome {
                background: rgb(119,119,119);
                background: radial-gradient(circle, rgba(119,119,119,1) 0%, rgba(68,68,68,1) 100%);
                border-radius: 1rem;
                border: .25rem solid white;
                margin: auto;
            }
        </style>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        <form id="frmMenuEditorHome" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="sessionBody">
                <div id="menuEditorNavBar">
                    <button id="btnMenuCategoryEditor" type="button" class="button menuNavButton">New Category</button>
                    <button id="btnMenuItemEditor" type="button" class="button menuNavButton">New Item</button>
                    <button id="btnMenuModificationEditor" type="button" class="button menuNavButton">Edit Mod Cats</button>
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
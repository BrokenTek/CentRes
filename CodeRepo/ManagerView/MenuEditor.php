
<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']); ?>

<!DOCTYPE html>
<?php require_once '../Resources/PHP/dbConnection.php'; ?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <style>
            form {
                width: 100vw;
                height: 100vh;
                display: grid;
                grid-template-columns: 1fr 1fr;
                grid-auto-rows: min-content 1fr;
                margin-inline: auto;
                margin-block: auto;
                opacity: 90%;
            }
            iframe {
                width: 95%;
                height: 95%;
                margin: auto auto auto auto;
                grid-row: 2;
                border: none;

            }
            #sessionHeader {
                grid-column: 1 / span 2;
            }
            #frmMenuEditor {
                background-color: transparent;
            }
        </style>
        
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script> 

        <script>
            var mnu;
            var mnuEditor;
            function allElementsLoaded() {
                varSet("showDetachedMenuObjects","yes", "ifrMenu", true);
                
                mnu = document.getElementById("ifrMenu");
                mnuEditor = document.getElementById("ifrMenuEditor");

                mnuEditor.addEventListener("load", editorRefreshed);
                setTitle("CentRes POS: Management Tools - Menu Editor", "Management Tools");
                //setup path for iframes to call this window's functions
                document.getElementById("ifrMenuEditor").focus();
            }

            function editorRefreshed(event) {
                if (varGet("updated", "ifrMenuEditor") != null) {
                    varCpyRen("lookAt", "ifrMenuEditor", "updated", "ifrMenu");
                    updateDisplay("ifrMenu");
                    
                }                
            }

            document.menuItemSelected = function() {
                if (!varExists("ignoreRedirect","ifrMenuEditor")) {
                    with (document.getElementById("ifrMenuEditor").contentDocument) {
                        getElementById("txtQC").setAttribute("value", this.menuItemId);
                        getElementById("txtLookAt").setAttribute("value", this.menuItemId);
                        if (this.parentCategoryId != null) {
                            getElementById("txtParentCategory").setAttribute("value", this.parentCategoryId);
                            getElementById("txtRecallParentCategory").setAttribute("value", this.parentCategoryId);
                        }
                        with (getElementById("frmRedirect")) {
                            setAttribute("action", "menuItemEditor.php");
                            submit();
                        }

                    }
                }
                else {
                    dispatchJSONeventCall("selectMenuItemCheck", {"menuItemId": this.menuItemId}, ["ifrMenuEditor"]);
                }
            }

            document.menuCategorySelected = function() {
                if (!varExists("ignoreRedirect","ifrMenuEditor")) {
                    with (document.getElementById("ifrMenuEditor").contentDocument) {
                        getElementById("txtQC").setAttribute("value", this.menuCategoryId);
                        getElementById("txtLookAt").setAttribute("value", this.menuCategoryId);
                        if (this.parentCategoryId != null) {
                            getElementById("txtParentCategory").setAttribute("value", this.menuCategoryId);
                            getElementById("txtRecallParentCategory").setAttribute("value", this.menuCategoryId);
                        }
                        with (getElementById("frmRedirect")) {
                            setAttribute("action", "menuCategoryEditor.php");
                            submit();
                        }

                    }
                }
            }

            //Place your JavaScript Code here
        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        
        <form id='frmMenuEditor' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php require_once '../Resources/PHP/sessionHeader.php'; ?>
            <iframe id="ifrMenuEditor" src="menuEditorHome.php"></iframe>
            <iframe id="ifrMenu" src="../ServerView/menu.php"></iframe>
            
            <?php unset($_POST['thisVariableIWantToForget'], $_POST['thisOtherVariableIDontNeed']) ?>

            
            <?php require_once '../Resources/PHP/display.php'; ?>
           
        </form>
    </body>
</html>
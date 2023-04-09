<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->


<!-- ensures you are logged in before rendering page, and are logged in under the correct role.
If you aren't logged in, it will reroute to the login page.
If you are logged in but don't have the correct role to view this page,
you'll be routed to whatever the home page is for your specified role level -->
<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(8, $GLOBALS['role']); ?>

<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
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
        <!-- gives you access to varSet, varGet, varRem, 
        varClr, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
        <!-- demonstration on how to use varGet, varSet, updateDisplay for just this page -->
        <!-- remove this script tag -->
        <script>
            var mnu;
            var mnuEditor;
            function allElementsLoaded() {
                mnu = document.getElementById("ifrMenu");
                mnuEditor = document.getElementById("ifrMenuEditor");

                mnu.contentDocument.addEventListener("click", menuUpdated);
                mnuEditor.addEventListener("load", editorRefreshed);
                
            }

            function editorRefreshed(event) {
                ignoreUpdate = true;
                if (varGet("updated", "ifrMenuEditor") != null) {
                    varCpyRen("lookAt", "ifrMenu", "focusedMenuObject", "ifrMenu");
                    updateDisplay("ifrMenu");
                    setTimeout(() => {
                        mnu.contentDocument.addEventListener("click", menuUpdated);
                    }, 1000);
                }
                ignoreUpdate = false;
                
            }

            let ignoreUpdate = false
            function menuUpdated(event) {
                if (ignoreUpdate) {
                    ignoreUpdate = false;
                    return;
                }
                var itm = varGet("selectedMenuItem", "ifrMenu");
                var cat = varGet("selectedMenuCategory", "ifrMenu");
                if (itm != undefined && itm.length > 0) {
                    let parentId = mnu.contentDocument.getElementById(itm).parentElement.parentElement.id; //.parentElement.id;
                    mnuEditor.contentDocument.getElementById("txtParentCategory").setAttribute("value", parentId);
                    mnuEditor.contentDocument.getElementById("txtRecallParentCategory").setAttribute("value",parentId);
                    mnuEditor.contentDocument.getElementById("txtQC").setAttribute("value", itm);
                    mnuEditor.contentDocument.getElementById("txtLookAt").setAttribute("value", itm);
                    with (mnuEditor.contentDocument.getElementById("frmRedirect")) {
                        action = "MenuItemEditor.php";
                        submit();
                    }
                    
                }
                else if (cat != undefined) {
                    mnuEditor.contentDocument.getElementById("txtParentCategory").setAttribute("value", cat);
                    with (mnuEditor.contentDocument.getElementById("frmRedirect")) {
                        action = "MenuCategoryEditor.php";
                        submit();
                    }

                }
            }
          

            //Place your JavaScript Code here
        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        <!-- this form submits to itself -->
        <form id='frmMenuEditor' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php require_once '../Resources/php/sessionHeader.php'; ?>
            <iframe id="ifrMenuEditor" src="MenuEditorHome.php"></iframe>
            <iframe id="ifrMenu" src="../ServerView/menu.php"></iframe>
            <div id="flickControl"></div>
            
            <?php unset($_POST['thisVariableIWantToForget'], $_POST['thisOtherVariableIDontNeed']) ?>

            <!-- retain any POST vars. When updateDisplay() is called or the form is submitted,
            these variables will be carried over -->
            <?php require_once '../Resources/php/display.php'; ?>
           
        </form>
    </body>
</html>
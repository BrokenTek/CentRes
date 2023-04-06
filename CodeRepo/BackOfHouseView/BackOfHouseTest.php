<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->


<!-- ensures you are logged in before rendering page, and are logged in under the correct role.
If you aren't logged in, it will reroute to the login page.
If you are logged in but don't have the correct role to view this page,
you'll be routed to whatever the home page is for your specified role level -->
<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(1, $GLOBALS['role']); ?>
<!-- CHANGE 255 TO THE ALLOWED ROLE LEVEL FOR THE PAGE -->

<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <style>
            #sessionForm {
                position: relative;
                background-image: none;
            }
            #frmBOH {
                postion: absolute;
                inset: 0;
                display-grid:
                grid-auto-columns: min-content;
                grid-template-rows: min-content;
                grid-gap: 4rem;
                background-color: transparent;
                height: 100%;
            }
            iframe {
                height: 90vh;
                width: 15rem;
            }
            .activeTicketGroup {
                grid-row: 1;
            }
        </style>
        <!-- gives you access to varSet, varGet, varRem, 
        varClr, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
        <!-- demonstration on how to use varGet, varSet, updateDisplay for just this page -->
        <!-- remove this script tag -->
        <script>
            function allElementsLoaded() {
                varCpy("route", null, "ifrTGC", true);
                eventLoop();
            }
            
            var groupIdBuffer = [];

            function eventLoop(){
                var addedGroups = varGetOnce("addedGroups", "ifrTGC");
                    var removedGroups = varGetOnce("removedGroups", "ifrTGC");
                    var updatedGroups = varGetOnce("updatedGroups", "ifrTGC");
                                        
                    // create ifrTicketGroup. Pass the windowHash via get.
                    if(addedGroups !== undefined){
                        addedGroups = addedGroups.split(',');
                        
                        for(let i = 0; i < addedGroups.length; i++){
                            var newGrpIfr = document.getElementById("ifr" + addedGroups[i].replace(".","_"));
                            if (newGrpIfr != null) {
                                varRem("closeMe", "ifr" + addedGroups[i].replace(".","_"));
                                varRem("completeAndCloseable", "ifr" + addedGroups[i].replace(".","_"));
                                updateDisplay("ifr" + addedGroups[i].replace(".","_"));
                            }
                            else {
                                newGrpIfr = document.createElement("iframe");
                                newGrpIfr.setAttribute("src", "ifrTicketGroup.php");
                                newGrpIfr.setAttribute("frameborder", "0");
                                newGrpIfr.setAttribute("class", "activeTicketGroup");
                                newGrpIfr.addEventListener("load", ifrATGloaded);
                                document.getElementById("frmBOH").append(newGrpIfr);
                                groupIdBuffer.push(addedGroups[i]);
                            }
                        }
                    }
                    var atg = document.getElementsByClassName("activeTicketGroup");
                    if (atg.length > 0) {
                        for (let i = 0; i < atg.length; i++) {
                            if (atg[i].id != null) {
                                closeCheck(atg[i]);
                            }
                        }
                    }
                    if(removedGroups !== undefined){
                        removedGroups = removedGroups.split(',');
                        for(let i = 0; i < removedGroups.length; i++){
                            let removeIfr = document.getElementById("ifr" + removedGroups[i].replace(".","_"));
                            if (removeIfr  != null){
                                varSet("completeAndCloseable","true", "ifr" + removedGroups[i].replace(".","_"), true);
                            }
                        }
                    }
                    if(updatedGroups !== undefined){
                        updatedGroups = updatedGroups.split(',');
                        for(let i = 0; i < updatedGroups.length; i++){
                            let updatedIfr = document.getElementById('ifr'+ updatedGroups[i].replace(".","_"));
                            if (updatedIfr  != null){                                
                                updateDisplay('ifr'+ updatedGroups[i].replace(".","_"));
                            }
                        }
                    }
                try {


                
                }
                catch(err) {
                    //alert(err);
                }
                updateDisplay("ifrTGC");
                setTimeout(eventLoop, 1000);
                

            }

            function closeCheck(ATGifr) {
                try {
                    if (varGet("closeMe", ATGifr.id) !== undefined) {
                        ATGifr.remove();
                    }
                }
                catch (err) {

                }
                
            }

            function ifrATGloaded(event) {
                try {
                    var grpId = groupIdBuffer.shift();
                    this.id = "ifr" + grpId.replace(".","_");
                    varSet("route", varGet("route"), "ifr" + grpId.replace(".","_"));
                    varSet("groupId", grpId, "ifr" + grpId.replace(".","_"), true);
                }
                catch (err) {

                }
            }
        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        <!-- this form submits to itself -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="frmBOH" method="POST">
            <?php require_once "../Resources/php/sessionHeader.php"; ?>
           
            <?php require_once '../Resources/php/display.php'; ?>
        
        </form>
        <iframe src="ticketGroupConnector.php" id="ifrTGC" frameborder="0" style="display: none;"></iframe>
    </body>
</html>
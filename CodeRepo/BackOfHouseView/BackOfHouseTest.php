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
            form {
                display-grid:
                grid-auto-columns: 20rem;
                grid-auto-rows: 60rem;
                grid-gap: 4rem;
                background-color: transparent;
                height: 100%;
            }
            iframe {
                width: 20rem;
                height: 30rem;
                padding: 1rem;
                background-color: #444;
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
                try {
                    var addedGroups = varGetOnce("addedGroups", "ifrTGC");
                    var removedGroups = varGetOnce("removedGroups", "ifrTGC");
                    var updatedGroups = varGetOnce("updatedGroups", "ifrTGC");
                                        
                    // create ifrTicketGroup. Pass the windowHash via get.
                    if(addedGroups !== undefined){
                        addedGroups = addedGroups.split(',');
                        
                        for(let i = 0; i < addedGroups.length; i++){
                            var newGrpIfr = document.getElementById("ifr" + addedGroups[i]);
                            if (newGrpIfr != null) {
                                varRem("closeMe");
                                varRem("completeAndCloseable", "ifr" + addedGroups[i], true);
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
                    document.getElementsByClassName("activeTicketGroup").foreach(closeCheck);
                    if(removedGroups !== undefined){
                        removedGroups = removedGroups.split(',');
                        for(let i = 0; i < removedGroups.length; i++){
                            let removeIfr = document.getElementById("ifr" + removedGroups[i]);
                            if (removeIfr  != null){
                                varSet("completeAndClosable","true", "ifr" + removedGroups[i], true);
                            }
                        }
                    }
                    if(updatedGroups !== undefined){
                        updatedGroups = updatedGroups.split(',');
                        for(let i = 0; i < updatedGroups.length; i++){
                            let updatedIfr = document.getElementById('ifrm'+ updatedGroups[i]);
                            if (updateIfr  != null){
                                varRem()
                                
                                updateDisplay('ifrm'+ updatedGroups[i]);
                            }
                        }
                    }

                
                }
                catch(err) {
                    
                }
                updateDisplay("ifrTGC");
                setTimeout(eventLoop, 1000);
                

            }

            function closeCheck(ATGifr) {
                if (getVar("closeMe", ATGifr.id) !== undefined) {
                    alert("Close");
                    ATGifr.remove();
                }
            }

            function ifrATGloaded(event) {
                this.removeEventListener("load", ifrATGloaded);
                
                var grpId = groupIdBuffer.shift();
                this.id = "ifr" + grpId;
                varSet("route", varGet("route"), "ifr" + grpId);
                varSet("groupId", grpId, "ifr" + grpId, true);
            }
        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        <!-- this form submits to itself -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="frmBOH" method="POST">
            <?php //echo("<h1>Looking at Traffic for Route " .$_POST['route']. "</h1>"); ?>
            <?php require_once "../Resources/php/sessionHeader.php"; ?>
           
            <?php require_once '../Resources/php/display.php'; ?>
        
        </form>
        <iframe src="ticketGroupConnector.php" id="ifrTGC" frameborder="0" ></iframe>
    </body>
</html>
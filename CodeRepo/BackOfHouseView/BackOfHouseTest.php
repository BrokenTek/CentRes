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
        <!-- gives you access to varSet, varGet, varRem, 
        varClr, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
        <!-- demonstration on how to use varGet, varSet, updateDisplay for just this page -->
        <!-- remove this script tag -->
        <script>
            function allElementsLoaded() {

                setTimeout(eventLoop, 1000);
            }
            var newNames = [];
            function eventLoop(){
                var addedGroups = varGetOnce("addedGroups", "ifrTGC");
                var removedGroups = varGetOnce("removedGroups", "ifrTGC");
                var updatedGroups = varGetOnce("updatedGroups", "ifrTGC");
                
                // create Donovan window
                if(addedGroups !== undefined){
                    addedGroups = addedGroups.split(',');
                    for(let i = 0; i < addedGroups.length; i++){

                        var newName = "ifr" + addedGroups[i];
                        newNames.push(newName);

                        var template = document.getElementById("template")
                        template.setAttribute("id", newName);
                        var newIfr = template.cloneNode(true);
                        template.setAttribute("id", "template");
                        
                        
                        newIfr.removeAttribute("style");
                        //newIfr.removeAttribute('id');

                        // compose the string that contains the groupId, route, and, hash here
                        //
                        
                        document.getElementById('frmBOH').appendChild(newIfr);
                

                        newIfr.addEventListener("load", function() {
                            
                            var lookAt = newNames.shift();
                            //document.getElementById(lookAt).contentWindow.document.getElementById("consternation").innerHTML += "<h1 id='getMe' getMeVal='1234' >" + lookAt +"</h1>";
                            //this.setAttribute("id", lookAt);
                            //this.id = "ifr" + lookAt;
                            this.contentWindow.document.getElementsByTagName("form")[0].submit();
                            //document.getElementById(lookAt).contentWindow.document.getElementById("consternation").innerHTML += "<h1 id='getMe' getMeVal='1234' >" + lookAt +"</h1>";
                            //document.getElementById(lookAt).contentWindow.document.getElementsByTagName("form")[0].submit();
                            //varSet('activeGroupId', addedGroups[i], newName);
                            //varCpy('route', null, newName);
                            //connector will get the hash for David to complete
                            //updateDisplay(newName);
                        });

                        

                    }


                }
                if(removedGroups !== undefined){
                    removedGroups = removedGroups.split(',');
                    for(let i = 0; i < removedGroups.length; i++){
                        let removeIfr = document.getElementById('ifrm'+ removedGroups[i]);
                        if (removeIfr  != null){
                            removeIfr.remove();
                        }
                    }
                    

                }
                if(updatedGroups !== undefined){
                    updatedGroups = updatedGroups.split(',');
                    for(let i = 0; i < removedGroups.length; i++){
                        
                        let updatedIfr = document.getElementById('ifrm'+ updatedGroups[i]);
                        if (updateIfr  != null){
                            updateDisplay('ifrm'+ updatedGroups[i]);
                        }
                    }
                }
                updateDisplay("ifrTGC");
                setTimeout(eventLoop, 1000);

            }

           
            function wowness() {
                document.getElementsByTagName("iframe").contentWindow.document.getElementsByTagName('form')[0].submit();
            }

            //Place your JavaScript Code here
        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        <!-- this form submits to itself -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="frmBOH" method="POST">
            <?php require_once "../Resources/php/sessionHeader.php"; ?>
            <!-- PLACE YOUR PHP LAYOUT LOGIC CODE HERE -->
            
            <!-- If you want to forget/not carry over variables, use PHP unset function
            to remove these variables -->
            <?php unset($_POST['thisVariableIWantToForget'], $_POST['thisOtherVariableIDontNeed']) ?>

            <!-- retain any POST vars. When updateDisplay() is called or the form is submitted,
            these variables will be carried over -->
            <?php require_once '../Resources/php/display.php'; ?>
            <?php echo("<h1>Looking at Traffic for Route " .$_POST['route']. "</h1>"); ?>
        
        </form>
        <iframe src="ticketGroupConnector.php" id="ifrTGC" frameborder="0" style = "display: none;"></iframe>
        <iframe src="activeGroupAutoForm.html" id="template" style="display: none;"></iframe>
    </body>
</html>
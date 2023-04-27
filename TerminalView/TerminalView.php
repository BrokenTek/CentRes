
<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(1, $GLOBALS['role']); ?>


<!DOCTYPE html>
<?php require_once '../Resources/PHP/dbConnection.php'; ?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <style>
            #sessionForm {
                position: relative;
                background-image: none;
            }
            #frmBOH {
                position: absolute;
                inset: 0;
                background-color: transparent;
                height: 100%;
            }
            .activeTicketGroup {
                height: 45vh;
                width: 15rem;
                background-color: transparent;
            }
        </style>
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script> 
        
        <script>
            function allElementsLoaded() {
                varCpy("route", null, "ifrTGC", true);
                setTitle("CentRes RMS: Terminal Window", "Terminal Window: Viewing Route " + varGet("route"));
            }
            
            var groupIdBuffer = [];

            function ifrATGloaded(event) {
                try {
                    this.removeEventListener("load", ifrATGloaded);
                    var grpId = groupIdBuffer.shift();
                    
                    this.id = "ifr" + grpId.replace(".","_");
                    varSet("route", varGet("route"), this.id);
                    varSet("ticketGroupId", grpId, this.id, true);
                } catch (err) { }
            }

            document.closeActiveTicketGroupIframe = function() {
                document.getElementById("ifr" + this.ticketGroupId.toString().replace(".","_")).remove();
            }

            document.activateTicketGroups = function() {
                for(let i = 0; i < this.ticketGroupIds.length; i++){
                    var newGrpIfr = document.getElementById("ifr" + this.ticketGroupIds[i].replace(".","_"));
                    if (newGrpIfr == null) {
                        newGrpIfr = document.createElement("iframe");
                        newGrpIfr.setAttribute("src", "ticketGroup.php");
                        newGrpIfr.setAttribute("frameborder", "0");
                        newGrpIfr.setAttribute("class", "activeTicketGroup");
                        newGrpIfr.addEventListener("load", ifrATGloaded);
                        document.getElementById("frmBOH").append(newGrpIfr);
                        groupIdBuffer.push(this.ticketGroupIds[i]);
                    }
                }
            }

        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="frmBOH" method="POST">
            <?php require_once "../Resources/PHP/sessionHeader.php"; ?>
            <?php require_once '../Resources/PHP/display.php'; ?>
        </form>
        <iframe src="ticketGroupConnector.php" id="ifrTGC" frameborder="0" style="display: none;"></iframe>
    </body>
</html>
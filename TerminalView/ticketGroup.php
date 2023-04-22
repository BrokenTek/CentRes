<!DOCTYPE html>
<?php require_once '../Resources/PHP/dbConnection.php'; ?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" href="../Resources/CSS/atgStyle.css">
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script>
        <script>
            function allElementsLoaded(){
                let ticketItemElements = document.getElementsByName("ticketItem");
                for(let i = 0; i < ticketItemElements.length; i++){
                    let element = ticketItemElements[i];
                    with(element.classList) {
                        if(!(contains("removed") || contains("delivered"))){
                            element.addEventListener('pointerdown',ticketItemPressed);
                        }
                    }
                }
                
                let x = varGet("scrollX");
                let y = varGet("scrollY");
                if (x !== undefined) {
                    window.scroll({
                        top: y,
                        left: x,
                        // behavior: "smooth"
                    });
                }

                window.addEventListener('scroll', function(event) {
                    varSet("scrollX", window.scrollX);
                    varSet("scrollY", window.scrollY);
                }, true);
            }

            document.configure = function(event) { 
                if (varExists("ticketGroupId")) { return; }
                varSet("route", this.route);
                varSet("ticketGroupId", this.ticketGroupId);
                updateDisplay();
            }

            document.activateTicketGroups = function(event) {
                for(let i = 0; i < this.ticketGroupIds.length; i++){
                    if (this.ticketGroupIds[i] == varGet("ticketGroupId")) {
                        updateDisplay();
                    }
                }
            }
            document.updateTicketGroups = document.activateTicketGroups;
            document.inactivateTicketGroups = document.activateTicketGroups;

            function ticketItemPressed(){
                if (this.classList.contains("removed") || this.classList.contains("hidden")) {
                    return;
                }
                varSet("ticketItemNumber", this.id, "activeTicketGroupConnector", true)   
            }

            function closeButtonPressed(event) {
                dispatchJSONeventCall("closeActiveTicketGroupIframe", {"ticketGroupId": varGet("ticketGroupId")});
            }

            

        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="frmATG">
            <?php if(isset($_POST['ticketGroupId']) && isset($_POST['route'])): ?>
                <?php
                    $sql = "SELECT tableid FROM tablelog WHERE ticketid = ".floor($_POST['ticketGroupId'])."
                    ORDER BY timeStamp DESC;";
                    $currentTable = connection()->query($sql)->fetch_assoc()['tableid'];

                    $sql = "SELECT menuItems.title AS 'itemName', id, flag, modificationNotes AS 'mods' 
                        FROM (ticketItems LEFT JOIN menuItems ON menuItemQuickCode=quickCode) 
                        WHERE groupid = ".$_POST['ticketGroupId']."
                        AND route='".$_POST['route']."';";
                    $itemList = connection()->query($sql);
                    $sql = "SELECT timeCreated FROM ActiveTicketGroups WHERE id = ".$_POST['ticketGroupId'].";";
                    $submitTimeResult = connection()->query($sql);
                    if (mysqli_num_rows($submitTimeResult) == 1) {
                        $timeSubmitted = $submitTimeResult->fetch_assoc()['timeCreated'];                         
                    }
                ?>
                <div id='descriptors'>
                    <div id="lblSubmitted">Submitted:</div><div id="valSubmitted"><?php echo substr($timeSubmitted,11,5)?></div>
                    <button id="btnClose" type="button" onpointerdown="closeButtonPressed()" style="display: none;">Close</button> 
                    <div id="lblGroupId">Ticket-Group:</div><div id="valGroupId"><?php echo $_POST['ticketGroupId']; ?></div>
                    <div id="lblRoute">Route:</div><div id="valRoute"><?php echo $_POST['route']; ?></div>
                    <div id="lblTableId">Table:</div><div id=valTableId><?php echo $currentTable; ?></div>
                </div>
                <div class='ticketItems'>
                <?php
                    $closeable = true;
                    while($ticketItem = $itemList->fetch_assoc()){
                        $itemState = connection()->query("SELECT ticketItemStatus(".$ticketItem['id'].") AS status;")->fetch_assoc()['status'];
                        $itemClass = 'ticketItem';
                        $readyChar = ' ';
                        switch($itemState){
                            case "Delivered":
                                $itemClass.=" delivered";
                                $readyChar = '✔';
                                break;
                            case "Ready":
                                $itemClass.=" ready";
                                $readyChar = '✔';
                                break;
                            case "Updated":
                                $itemClass.=" updated";
                            case "Preparing":
                                $closeable = false;
                                break;
                            case "Removed":
                                $itemClass.=" removed";
                                break;
                            case "Hidden":
                                $itemClass.=" hidden";
                                break;
                        }
                        
                        echo("<div name='ticketItem' id='".$ticketItem['id']."' class ='".$itemClass."'><p>".$readyChar.$ticketItem['itemName']."</p>");

                        if($ticketItem['mods']!=""){
                            $modList = explode(",", $ticketItem['mods']);
                            $modLength = count($modList);
                            //iterate through each pair of modification values.
                            for($i=2; $i<$modLength; $i+=3){
                                echo("<ul>");
                                $sql = "SELECT title FROM MenuModificationItems WHERE quickCode = '".$modList[$i-2]."';";
                                
                                $result = connection()->query($sql);
                                $mod = $result->fetch_assoc();

                                if (strpos(' ' . $mod['title'], '.') == 1) {
                                    echo(substr($mod['title'],  strpos($mod['title'], ' ') + 1));
                                }
                                else {
                                    echo($mod['title']);
                                }

                                // TEMP
                                // echo("DEBUG TITS " . );

                                if (isset($modList[$i-1]) && strlen($modList[$i-1]) != 0) {
                                    echo(": ");
                                    echo($modList[$i-1]);
                                }
                                echo("</ul>");
                            }
                            //append the last odd note, which should be a custom one for instructions or whatever, to the list of modifications
                            if($modLength % 3 == 1){
                                echo("<ul>".htmlspecialchars($modList[$modLength - 1])."</ul>");
                            }
                        }
                        echo("</div>");
                    } 
                    if ($closeable) {
                        echo("
                            <script>
                                document.querySelector('#btnClose').removeAttribute('style');
                                document.querySelector('#lblSubmitted').setAttribute('style', 'display: none;');
                                document.querySelector('#valSubmitted').setAttribute('style', 'display: none;');
                            </script>
                        ");
                    }
                ?>
                </div>
            <?php endif; ?>
            
            
            <?php require_once '../Resources/PHP/display.php'; ?>
        </form>
        <iframe src="activeTicketGroupConnector.php" id="activeTicketGroupConnector" style="display: none;"></iframe>
    </body>
</html>
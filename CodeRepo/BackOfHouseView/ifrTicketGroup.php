<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/atgStyle.css">
        <style>
            
            


        </style>
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script>
        <script>
            
            function toggleReadyListener(){
                varSet("ticketItemNumber", this.id, "activeTicketGroupConnector", true);
                event.stopPropagation();
                
            }
           
            function allElementsLoaded(){
                if(varGet('groupId') !== undefined){
                    varCpyRen("groupId", null,"activeGroupId","activeTicketGroupConnector");
                    
                    let ticketItemElements = document.getElementsByName("ticketItem");
                    for(let i = 0; i < ticketItemElements.length; i++){
                        let element = ticketItemElements[i];
                        with(element.classList) {
                            if(!(contains("removed") || contains("delivered"))){
                                element.addEventListener('pointerdown',toggleReadyListener);
                            }
                        }
                    }
                    //document.getElementById("btnClose").addEventListener("pointerdown", setClosed);
                }
            }

            function setClosed(event) {
                //varSet("closeMe", "yes");
                varRen('completeAndCloseable', 'closeMe');
            }
        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="frmATG">
            <?php if(isset($_POST['groupId']) && isset($_POST['route'])): ?>
                <?php
                    $sql = "SELECT tableid FROM tablelog WHERE ticketid = ".floor($_POST['groupId'])."
                    ORDER BY timeStamp DESC;";
                    $currentTable = connection()->query($sql)->fetch_assoc();

                    $sql = "SELECT menuItems.title AS 'itemName', id, flag, modificationNotes AS 'mods' 
                        FROM (ticketItems LEFT JOIN menuItems ON menuItemQuickCode=quickCode) 
                        WHERE groupid = ".$_POST['groupId']."
                        AND route='".$_POST['route']."';";
                    $itemList = connection()->query($sql);
                ?>
                <div id='descriptors'>
                    <?php if (isset($_POST['completeAndCloseable']) || isset($_POST['closeMe'])): ?>
                        <button id="btnClose" type="button" onpointerdown="setClosed()">Close</button>
                    <?php else: ?>
                        <?php
                            if (!(isset($_POST['completeAndCloseable']) || isset($_POST['closeMe']))) {
                                $sql = "SELECT timecreated FROM activeticketgroups WHERE id = ".$_POST['groupId'].";";
                                $timeSubmitted = connection()->query($sql)->fetch_assoc()['timecreated'];
                            } 
                        ?>
                        <div id="lblSubmitted">Submitted:</div><div id="valSubmitted"><?php echo substr($timeSubmitted,11,5)?></div>                                  
                    <?php endif; ?>
                    <div id="lblGroupId">Ticket-Group:</div><div id="valGroupId"><?php echo $_POST['groupId']; ?></div>
                    <div id="lblRoute">Route:</div><div id="valRoute"><?php echo $_POST['route']; ?></div>
                </div>
                <div class='ticketItems'>
                <?php
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

                            case "Removed":
                                $itemClass.=" removed";
                                break;
                            case "Updated":
                                $itemClass.=" updated";
                                break;
                            case "Hidden":
                                $itemClass.=" hidden";
                                break;
                        }
                        if($ticketItem['flag'] == "updated"){
                            $itemClass.=" updated";
                        }
                        echo("<div name='ticketItem' id='".$ticketItem['id']."' class ='".$itemClass."'><p>".$readyChar.$ticketItem['itemName']."</p>");


                        if($ticketItem['mods']!=""){
                            $modList = explode(",", $ticketItem['mods']);
                            $modLength = count($modList);
                            //iterate through each pair of modification values.
                            for($i=0; ($i + 1)<$modLength; $i+=2){
                                echo("<ul>");
                                $sql = "SELECT title, selfDescriptive FROM MenuModificationCategories WHERE quickCode = ".$modList[$i].";";
                                $mod = connection()->query($sql)->fetch_assoc();
                                if(!$mod['selfDescriptive']){
                                    echo($mod['title']);
                                }
                                echo($modList[i + 1]);
                                echo("</ul>");
                            }
                            //append the last odd note, which should be a custom one for instructions or whatever, to the list of modifications
                            if($modLength % 2 == 1){
                                echo("<ul>".htmlspecialchars($modList[$modLength - 1])."</ul>");
                            }
                        }
                        echo("</div>");
                    } 
                ?>
                </div>
            <?php endif; ?>
            
            
            <?php require_once '../Resources/php/display.php'; ?>
        </form>
        <iframe src="activeTicketGroupConnector.php" id="activeTicketGroupConnector" style="display: none;"></iframe>
    </body>
</html>
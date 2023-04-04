<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/ticketStyle.css">
        <style>
            #sessionForm{
                display:grid;
                grid-template-areas: "btnClose    descriptors"
                                     "ticketItems ticketItems";
                grid-template-columns: min-content max-content;
            }
            #btnClose{
                grid-area:btnClose;
            }
            .descriptors{
                grid-area:descriptors;
                display:grid;
                grid-template-columns: max-content max-content;
                grid-template-rows: 1fr 1fr 1fr;
            }
            .ticketItems{
                grid-area:ticketItems;
            }
            .updated{
                background-color: #F6941D;
            }
        </style>
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script>
        <script>
            
            function toggleReadyListener(){
                varCpyRen("groupId", null,"activeGroupId","activeTicketGroupConnector");
                varSet("ticketItemNumber", this.id, "activeTicketGroupConnector");
                varCpy("atgHash", null,"activeTicketGroupConnector",true);
                updateDisplay();
            }
            
            function closeMe(){
                varSet("closeMe", "yes");
            }

            function allElementsLoaded(){
                if(!varGet('groupId')==undefined){
                    let ticketItemElements = document.getElementsByName("ticketItem");
                    let clearable = true;
                        for(let i = 0; i < ticketItemElements.length; i++){
                            let element = ticketItemElements[i];
                            if(!(element.classList.contains("disabled"))){
                                element.addEventListener('pointerdown',toggleReadyListener);
                            }
                            if(!(element.classList.contains("ready")||element.classList.contains("disabled")||element.classList.contains("hidden"))){
                                clearable = false;
                            }
                        }
                    if(clearable){
                        document.getElementById("btnClose").addEventListener('pointerdown',closeMe);
                    }
                }
            }

        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php if(isset($_POST['groupId']) && isset($_POST['route'])): ?>
                <?php
                    $sql = "SELECT timecreated FROM activeticketgroups WHERE id = ".$_POST['groupId'].";";
                    $timeSubmitted = connection()->query($sql)->fetch_assoc();

                    $sql = "SELECT tableid FROM tablelog WHERE ticketid = ".floor($_POST['groupId'])."
                    ORDER BY timeStamp DESC;";
                    $currentTable = connection()->query($sql)->fetch_assoc();
                    
                    $sql = "SELECT menuItems.title AS 'itemName', id, flag, modificationNotes AS 'mods' 
                        FROM (ticketItems LEFT JOIN menuItems ON menuItemQuickCode=quickCode) 
                        WHERE groupid = ".$_POST['groupId']."
                        AND route='".$_POST['route']."';";
                    $itemList = connection()->query($sql);
                ?>
                <button id= "btnClose">Close</button>
                <div class='descriptors'>
                    <p><?php echo $_POST['groupId']; ?></p>
                    <p><?php echo $_POST['route']; ?></p>
                </div>
                <div class='ticketItems'>
                <?php
                    while($ticketItem = $itemList->fetch_assoc()){
                        $itemState = connection()->query("SELECT ticketItemStatus(".$ticketItem['id'].") AS status;")->fetch_assoc()['status'];
                        $itemClass = 'ticketItem';
                        $readyChar = ' ';
                        switch($itemState){
                            case "delivered":

                            case "ready":
                                $itemClass.=" ready";
                                $readyChar = '✔';
                                break;

                            case "removed":
                                $itemClass.=" disabled";
                                break;
                            case "hidden":
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
                    } 
                ?>
                </div>
            <?php endif; ?>
            
            
            <?php require_once '../Resources/php/display.php'; ?>
        </form>
    </body>
</html>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../CSS/baseStyle.css">
        <script src="../JavaScript/displayInterface.js"></script>
        <script>
            function signalStatus(status) {
                setVar("status", status);
            }
        </script>
    </head>
    <body>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php
                include 'connect_disconnect.php';

                if(isset($_POST['selectedItem'])) {
                    $selectedItem = $_POST['selectedItem'];
                }

                if(!isset($_POST['selectedItem'])) {
                    echo("<script>signalStatus('await');</script>");
                    //echo("<H1>Waiting for <b>selectedItem</b> to be injected</H1>");
                }
                else {
                    if (isset($_POST['newModValue'])) {
                        connection();
                        $sql = "CALL modifyTicketItem(" .$_POST['selectedItem']. ", '" .$_POST['newModValue']. "');";
                        connection()->query($sql);
                        echo("<script>signalStatus('await');</script>");
                        //echo("<H1>Waiting for Server Window to redirect back to ticket item</H1>");
                    }
                    // **** LIKELY NEED TO MAKE THIS ELSE STATEMENT INTO AN IF STATEMENT SO THAT BOTH PREDEFINED MODS AND
                    //      CUSTOM NOTE MODS CAN BE SENT TOGETHER ****
                    else {
                        
                        //===================================================================================================
                        echo("<script>signalStatus('pending');</script>");
                        echo("<h1>You mod window content goes here : ". $_POST['selectedItem'] ."</h1>");

                        $ticketItemModQCs = array();
                        $selectedItemMods = array();
                        $sql = "SELECT menuItemQuickCode FROM ticketitems WHERE id = '" . $selectedItem . "';";
                        $result = connection()->query($sql);
                        if($result->num_rows > 0) {
                            $menuItemQuickCode = $result->fetch_assoc()['menuItemQuickCode'];
                        }
                        else {
                            // Pretty Much Just A Debug Statement
                            echo "<h3 class='debug_statement'>No Results</h3>";
                        }
                        
                        $sql = "SELECT childQuickCode FROM menuassociations WHERE parentQuickCode = '$menuItemQuickCode';";
                        $result = connection()->query($sql);
                        if($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                array_push($ticketItemModQCs, $row['childQuickCode']);
                            }
                        }
                        for ($i = 0; $i<sizeof($ticketItemModQCs); $i++) {
                            $sql = "SELECT title, categoryType FROM menumodificationcategories WHERE quickCode = '$ticketItemModQCs[$i]'";
                            $result = connection()->query($sql);
                            if($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    array_push($selectedItemMods, $row['title'] . ', ' . $row['categoryType']);
                                }
                            }
                        }

                        if (substr($selectedItemMods[1], -3) == 'One') {
                            echo "<select id='modOneChoice'>";
                        
                        for ($i=0; $i<sizeof($selectedItemMods); $i++) {
                                echo "<option value='" . $selectedItemMods[$i] . "'>" . $selectedItemMods[$i] . "</option>";    
                            }
                        echo "</select>";
                        }

                        else {
                            // THIS IS WHERE RADIO ECHOS WILL GO
                        }



                        

                        echo("<label for='txtModString'>Mod String w/ commas</label>
                        <input type='text' id='txtModString' name='newModValue'>
                        <input type='submit' value='Update Mods'>
                        <button type='button' onclick='signalStatus(" .'"await"'. ")'>Cancel Update</button>");
                        //===================================================================================================
                    }
                                 
                }
                include 'display.php';
            ?>
        </form>
    </body>
</html>
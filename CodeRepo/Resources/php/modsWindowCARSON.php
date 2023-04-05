<!-- ensures you are logged in before rendering page.
Otherwise will reroute to logon page -->
<?php require_once './sessionLogic.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Modification Selection</title>
        <link rel="stylesheet" href="../CSS/baseStyle.css">
        <script src="../JavaScript/displayInterface.js"></script>
        <script>
            function signalStatus(status) {
                varSet("status", status);
            }
        </script>

        <script>
            function addPrefixs() {          
                var form = document.getElementById("modWindowId");

                // use querySelector with starts with function to find the existence
                //  of the prefixIs select element

                // Add an event listener to the form for the "change" event
                form.addEventListener("change", function(event) {
                    if (event.target.type === "radio") {
                        var selectElement = document.createElement("select");
                        selectElement.setAttribute("id", "prefixIs");

                        var optionOne = document.createElement("option");
                        optionOne.value = "Add";
                        optionOne.textContent = "Add";
                        selectElement.appendChild(optionOne);

                        var optionTwo = document.createElement("option");
                        optionTwo.value = "None";
                        optionTwo.textContent = "None";
                        selectElement.appendChild(optionTwo);

                        var optionThree = document.createElement("option");
                        optionThree.value = "Xtra";
                        optionThree.textContent = "Xtra";
                        selectElement.appendChild(optionThree);

                        var optionFour = document.createElement("option");
                        optionFour.value = "Lite";
                        optionFour.textContent = "Lite";
                        selectElement.appendChild(optionFour);

                        // Append the select element as a child of the element with id "choosePrefix"
                        document.getElementById("choosePrefix").appendChild(selectElement);
                        }
                    });
                };
              
        </script>
    </head>
    <body onload="addPrefixs()">
        <form id="modWindowId" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php
                require_once 'connect_disconnect.php';

                if(isset($_POST['selectedItem'])) {
                    $selectedItem = $_POST['selectedItem'];
                }

                if(!isset($_POST['selectedItem'])) {
                    echo("<script>signalStatus('await');</script>");
                    //echo("<H1>Waiting for <b>selectedItem</b> to be injected</H1>");
                }
                else {
                    if (isset($_POST['newModValue'])) {
                        
                        $sql = "CALL modifyTicketItem('" .$_POST['selectedItem']. "', '" .$_POST['newModValue']. "');";
                        echo($sql);
                        connection()->query($sql);
                        echo("<script>signalStatus('await');</script>");
                        //echo("<H1>Waiting for Server Window to redirect back to ticket item</H1>");
                    }
                    else {
                        
                        //===================================================================================================
                        echo("<script>signalStatus('pending');</script>");

                        // Initialize the return string    // "M002,Xtra,M054,None,Make sure to actually use extra, Jack"	*** Remove COMMAS from explicit mod***
                        $newModValue = "fefe";

                        // Get the menu item's quick code
                        $sql = "SELECT menuItemQuickCode FROM ticketitems WHERE id = '" . $selectedItem . "';";
                        $result = connection()->query($sql);
                        if($result->num_rows > 0) {
                            $menuItemQuickCode = $result->fetch_assoc()['menuItemQuickCode'];
                        }
                        else {
                            echo "<h3 class='debug_statement'>No Results1</h3>";
                        }

                        // Get the menu item's title
                        $sql = "SELECT title FROM menuitems WHERE quickCode = '$menuItemQuickCode';";
                        $result = connection()->query($sql);
                        if($result->num_rows > 0) {
                            $menuItemTitle = $result->fetch_assoc()['title'];
                            echo("<h3>Available Modifications For <i> $menuItemTitle </i></h3>");
                            echo("<hr>");
                        }
                        else {
                            echo "<h3 class='debug_statement'>No Results2</h3>";
                        }

                        // Get the menu item's price
                        $sql = "SELECT price FROM menuitems WHERE quickCode = '$menuItemQuickCode';";
                        $result = connection()->query($sql);

                        if($result->num_rows > 0) {
                            $menuItemPrice = $result->fetch_assoc()['price'];
                        }
                        else {
                            echo "<h3 class='debug_statement'>No Results3</h3>";
                        }

                        // Initialize the mod quick code array
                        $modQuickCodeArray = array();
                        // Get the menumodificationitems data based on the mod quick code
                        $sql = "SELECT childQuickCode FROM menuassociations WHERE parentQuickCode = '$menuItemQuickCode';";
                        $result = connection()->query($sql);
                        if($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $modQuickCode = $row['childQuickCode'];
                                array_push($modQuickCodeArray, $modQuickCode);
                            }
                        }
                        else {
                            echo "<h3 class='debug_statement'>No Results4</h3>";
                        }
                        
                        // Initialize the mod title, price, and category type array
                        $modAvailableArray = array();
                        // Get the menumodificationitems data based on the array of mod quick codes
                        for ($i = 0; $i<sizeof($modQuickCodeArray); $i++) {
                            $sql = "SELECT title, priceOrModificationValue, categoryType FROM menumodificationitems WHERE quickCode = '$modQuickCodeArray[$i]';";
                            $result = connection()->query($sql);
                            if($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $modTitle = $row['title'];
                                    $modPriceOrModValue = $row['priceOrModificationValue'];
                                    $modCategoryType = $row['categoryType'];
                                    array_push($modAvailableArray, $modTitle .",". $modPriceOrModValue .",". $modCategoryType);
                                    // echo("<input type='radio' name='newModValue' value='$modQuickCode,$modTitle,$modPriceOrModValue,$modCategoryType'>");
                                    // echo($modTitle . " - $" . $modPriceOrModValue);
                                    // echo("<br>");
                                }
                            }
                            else {
                                echo "<h3 class='debug_statement'>No Results5</h3>";
                            }
                        }
                        
                        // Initialize the mod quickcodechosen array
                        $modTupleChosenArray = array();
                        // Display choices for mods
                        for ($i = 0; $i<sizeof($modAvailableArray); $i++) {
                            $modAvailableArray[$i] = explode(",", $modAvailableArray[$i]);

                            if ($modAvailableArray[$i] == "mandatoryAny") {
                                
                                echo("<label for='newModChosen'>");
                                echo("<input type='radio' name='newModChosen' value='$modQuickCodeArray[$i]' style='color: orange;>");
                                echo($modAvailableArray[$i][0] . " - $" . $modAvailableArray[$i][1]);
                                 
                            }

                            elseif ($modAvailableArray[$i] == "mandatoryOne") {
                                
                                echo("<label for='newModChosen'>");
                                echo("<input type='radio' name='newModChosen' value='$modQuickCodeArray[$i]' style='color: red;'>");
                                echo($modAvailableArray[$i][0] . " - $" . $modAvailableArray[$i][1]);
                                 
                            }

                            elseif ($modAvailableArray[$i] == "optionalOne") {
                                
                                echo("<label for='newModChosen'>");
                                echo("<input type='radio' name='newModChosen' value='$modQuickCodeArray[$i]' style='color: powderblue;'>");
                                echo($modAvailableArray[$i][0] . " - $" . $modAvailableArray[$i][1]);
                                 

                            }

                            else {
                                
                                echo("<label for='newModChosen'>");
                                echo("<input type='radio' name='newModChosen' value='$modQuickCodeArray[$i]'>");
                                echo($modAvailableArray[$i][0] . " - $" . $modAvailableArray[$i][1]);
                                                               
                            }

                            echo("<fieldSet id='choosePrefix'>");
                            echo("<legend style='font-size: 75%; color: grey;'>Choose Prefix</legend>");

                            echo("</fieldSet>");

                            // echo("<select name='prefixMod' id='prefixMod'>");
                            // echo("<option value='Add'>Add</option>");
                            // echo("<option value='None'>None</option>");
                            // echo("<option value='Xtra'>Xtra</option>");
                            // echo("<option value='Lite'>Lite</option>");
                            // echo("</select>");
                        }


                        
                        echo("<script>");
                        echo("varSet('. $newModValue .', ' . $newModValue . ');");
                        echo("</script>");

                        echo("<label for='txtModString'>Mod String w/ commas</label>
                        <input type='text' id='txtModString' name='newModValue'>
                        <input type='submit' value='Update Mods'>
                        <button type='button' onpointerdown='signalStatus(" .'"await"'. ")'>Cancel Update</button>");
                        //===================================================================================================
                    }
                                 
                }
                require_once 'display.php';
            ?>
        </form>
    </body>
</html>
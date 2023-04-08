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


        <!-- Keep checkboxes for mods in a line -->
        <style>
            .modOptionDiv {
                display: inline-block;
            }
        </style>



        <script>
            function signalStatus(status) {
                varSet("status", status);
            }
        </script>


        <script>

            // function listenBtn() {
            //     var form = document.getElementById('modWindowId');
            // }

            // Possible circumvention for input data discovery before Submit. Not Used.
            function varSetMod(varName, varValue) {
                var form = document.getElementById("modWindowId");
                var input = document.createElement("input");
                input.setAttribute("type", "hidden");
                input.setAttribute("name", varName);
                input.setAttribute("value", varValue);
                form.appendChild(input);
            }

            function addSuffixs() {    
                
                // Get the form element
                var form = document.getElementById("modWindowId");

                // Listen for the selection of checkboxes. Append select element to parent 
                //  container if checked. Remove select element if unchecked.
                form.addEventListener('change', function(event) {
                    if (event.target.type === 'checkbox') {
                        const parentDiv = event.target.parentNode;

                        if (event.target.checked) {
                          const select = document.createElement('select');
                          select.name = 'newModSuffix[]';
                        
                          // add options to select element
                          const option1 = document.createElement('option');
                          option1.value = 'Add';
                          option1.text = 'Add';
                          select.add(option1);
                        
                          const option2 = document.createElement('option');
                          option2.value = 'None';
                          option2.text = 'None';
                          select.add(option2);

                          const option3 = document.createElement('option');
                          option3.value = 'Xtra';
                          option3.text = 'Xtra';
                          select.add(option3);

                          const option4 = document.createElement('option');
                          option4.value = 'Lite';
                          option4.text = 'Lite';
                          select.add(option4);

                          parentDiv.appendChild(select);
                        }                    
                        else {
                          const select = parentDiv.querySelector('select');
                          parentDiv.removeChild(select);
                        }  
                    }
                });
            }
              
        </script>
    </head>
    <body onload="addSuffixs()">
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

                        //===================================================================================================   (Setting the newModValue)
                        if (isset($_POST['newModChosen[]']) && isset($_POST['newModSuffix[]'])) {
                            $newModChosenArray = $_POST['newModChosen[]'];
                            $newModSuffixArray = $_POST['newModSuffix[]'];
                            $newModValue = "";

                            for ($i = 0; $i < count($newModChosenArray); $i++) {
                                $newModValue .= strval($newModChosenArray[$i]) . "," . strval($newModSuffixArray[$i]) . ",";
                            }
                            if (isset($_POST['newModNote'])) {
                                // $newModValue .= str_replace(',', '_', $_POST['newModNote']);
                                $newModValue .= $_POST['newModNote'];
                            } 
                            
                            echo("<script>");
                            echo("varSet('. $newModValue .', ' . $newModValue . ');");
                            echo("</script>");
                        }

                        // FOR IF ITEM IS MANDATORY OR DOES NOT NEED A SUFFIX
                        // else if (isset($_POST['newModChosen[]']) && !isset($_POST['newModSuffix[]'])) {
                        //     $newModChosenArray = $_POST['newModChosen[]'];
                        //     $newModValue = "";

                        //     for ($i = 0; $i < count($newModChosenArray); $i++) {
                        //         $newModValue .= strval($newModChosenArray[$i]) . ",";
                        //     }
                        //     if (isset($_POST['newModNote'])) {
                        //         // $newModValue .= str_replace(',', '_', $_POST['newModNote']);
                        //         $newModValue .= $_POST['newModNote'];
                        //     }
                        // }

                        // TEMPORARY DEBUG
                        else {
                            $newModValue = "None";
                            echo("<h1>ERRRRROR DUBUIG</h1>");
                        }



                         //===================================================================================================
                        
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
                        $newModValue = "";

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
                                
                                echo("<div class='modOptionDiv'>");
                                echo("<label for='newModChosen'>");
                                echo($modAvailableArray[$i][0] . " - $" . $modAvailableArray[$i][1]);
                                echo("</label>");
                                echo("<input type='checkbox' name='newModChosen' value='$modQuickCodeArray[$i]' style='color: orange;>");
                                echo("</div>");
                                 
                            }

                            elseif ($modAvailableArray[$i] == "mandatoryOne") {
                                
                                echo("<div class='modOptionDiv'>");
                                echo("<label for='newModChosen'>");
                                echo($modAvailableArray[$i][0] . " - $" . $modAvailableArray[$i][1]);
                                echo("</label>");
                                echo("<input type='radio' name='newModChosen' value='$modQuickCodeArray[$i]' style='color: red;'>");
                                echo("</div>");
                                 
                            }

                            elseif ($modAvailableArray[$i] == "optionalOne") {
                                
                                echo("<div class='modOptionDiv'>");
                                echo("<label for='newModChosen'>");
                                echo($modAvailableArray[$i][0] . " - $" . $modAvailableArray[$i][1]);
                                echo("</label>");
                                echo("<input type='radio' name='newModChosen' value='$modQuickCodeArray[$i]' style='color: powderblue;'>");
                                echo("</div>");
                                 

                            }

                            else {
                                
                                echo("<div class='modOptionDiv'>");
                                echo("<label for='newModChosen[]'>");
                                echo($modAvailableArray[$i][0] . " - $" . $modAvailableArray[$i][1]);
                                echo("</label>");
                                echo("<input class='modChoice' type='checkbox' name='newModChosen[]' value='$modQuickCodeArray[$i]'>");
                                // echo("<fieldSet class='chooseSuffix'>");

                                // echo("<legend id='prefixBtn' style='font-size: 75%; color: grey;'>Choose Suffix</legend>");
    
                                // echo("</fieldSet>");

                                echo("</div>");
                                                               
                            }

                            // echo("<fieldSet id='chooseSuffix'>");
                            // echo("<legend style='font-size: 75%; color: grey;'>Choose Suffix</legend>");
                            // echo("</fieldSet>");
                            // echo("<select name='prefixMod' id='prefixMod'>");
                            // echo("<option value='Add'>Add</option>");
                            // echo("<option value='None'>None</option>");
                            // echo("<option value='Xtra'>Xtra</option>");
                            // echo("<option value='Lite'>Lite</option>");
                            // echo("</select>");
                        }

                            
                        echo("<hr>");
                        
                        // echo("<script>");
                        // echo("varSet('. $newModValue .', ' . $newModValue . ');");
                        // echo("</script>");

                        // Custom mod note name changed from newModValue -> newModNote
                        echo("<label for='txtModString'>Mod String w/ commas</label>
                        <input type='text' id='txtModString' name='newModNote'>    
                        <input class='submitBtn' type='submit' value='Update Mods'>
                        <button type='button' onpointerdown='signalStatus(" .'"await"'. ")'>Cancel Update</button>");
                        //===================================================================================================
                    }
                                 
                }
                require_once 'display.php';
            ?>
        </form>
    </body>
</html>
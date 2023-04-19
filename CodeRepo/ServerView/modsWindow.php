<!-- ensures you are logged in before rendering page.
Otherwise will reroute to logon page -->
<?php require_once '../Resources/PHP/sessionLogic.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Modification Selection</title>
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/modOptionStyle.css">
        <script src="../Resources/JavaScript/display.js"></script>
        <script src="../Resources/JavaScript/modOptionUtility.js"></script>


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

            function createCategoryFieldset(categoryTitle, categoryId, categoryType) {
                let newFst = document.createElement("fieldset");
                with (newFst) {
                    setAttribute("id", categoryId);
                    classList.add("menuCategory");
                    classList.add("modOptionFieldset");
                    classList.add(categoryType);
                    let suffix = "";
                    let logoType = "";
                    if (categoryType == "MandatoryOne") {
                        suffix = "&nbsp;1&nbsp;";
                        logoType = "one";
                    }
                    else if (categoryType == "MandatoryAny") {
                        suffix = "1-∞";
                        logoType = "moreThanZero";
                    }
                    else if (categoryType == "OptionalOne") {
                        suffix = "0-1";
                        logoType = "zeroOrOne";
                    }
                    else if (categoryType == "OptionalAny") {
                        suffix = "0-∞";
                        logoType = "noBounds";
                    }
                    innerHTML = "<legend class='menuCategoryTitle'><div class='quantityLogo " + logoType + "'>" + suffix + "</div>" + categoryTitle + "</legend>";
                    document.getElementById('frmModWindow').appendChild(newFst);
                }
                
            }

            function commitMods() {
                let modStr = generateModString();
                varSet("newModValue", modStr);
                varSet("priceWithMods", calculateModsPrice(modStr));
                updateDisplay();
            }


            function allElementsLoaded() {
                document.getElementById("btnCommit").addEventListener("pointerdown", commitMods);
                <?php
                    require_once '../Resources/PHP/dbConnection.php';

                    if(!isset($_POST['selectedTicketItemId'])) {
                        echo("signalStatus('await');");
                    }
                    else {
                        $selectedTicketItemId = $_POST['selectedTicketItemId'];
                        if (isset($_POST['newModValue'])) {
                            $sql = "CALL modifyTicketItem('" .$_POST['selectedTicketItemId']. "', '" .$_POST['newModValue']. "');";
                            connection()->query($sql);

                            $sql = "SELECT calculatedPrice FROM ticketItems WHERE id = " .$_POST['selectedTicketItemId']. ";";
                            $basePrice = connection()->query($sql)->fetch_assoc()['calculatedPrice'];

                            $priceWithMods = $_POST['priceWithMods'];
                            $sql = "UPDATE ticketItems SET calculatedPriceWithMods = " .($basePrice +  $priceWithMods)." WHERE id = " .$_POST['selectedTicketItemId'].";";
                            connection()->query($sql);
                            echo("signalStatus('await');");
                           
                        }
                        else {
                            echo("signalStatus('pending');");
                            //echo("signalStatus('pending');");
                          
                            /////////////////////////////////////////////////////////////////////////////////////////
                            // GET THE MENU ITEM AND IT'S PRICE
                            /////////////////////////////////////////////////////////////////////////////////////////

                            // Get the menu item's quick code
                            $sql = "SELECT menuItemQuickCode FROM ticketitems WHERE id = '" . $selectedTicketItemId . "';";
                            $result = connection()->query($sql);
                            if($result->num_rows > 0) {
                                $menuItemQuickCode = $result->fetch_assoc()['menuItemQuickCode'];
                            }

                            // Get the menu item's title and price
                            $sql = "SELECT title, price FROM menuitems WHERE quickCode = '$menuItemQuickCode';";
                            $result = connection()->query($sql);
                            if($result->num_rows > 0) {
                                $record = $result->fetch_assoc();
                                $menuItemTitle = $record['title'];
                                $menuItemPrice = $record['price'];
                            }

                            /////////////////////////////////////////////////////////////////////////////////////////
                            // GET AND ITERATE THROUGH THE MENU CATEGORIES ASSOCIATED WITH THIS MENU ITEM
                            /////////////////////////////////////////////////////////////////////////////////////////
                            
                            $sql = "SELECT childQuickCode FROM menuassociations WHERE parentQuickCode = '$menuItemQuickCode';";
                            $modCategories = connection()->query($sql);
                            $defered = false;
                            $deferedStr = "";
                            while($modCategory = $modCategories->fetch_assoc()) {
                                $modCategoryQuickCode = $modCategory['childQuickCode'];
                                $sql = "SELECT * FROM MenuModificationCategories WHERE quickCode = '$modCategoryQuickCode';";
                                $modCategoryDetails = connection()->query($sql)->fetch_assoc();
                                                              
                                /////////////////////////////////////////////////////////////////////////////////////
                                // CREATE EACH OF THE MENU CATEGORY FIELDSETS
                                /////////////////////////////////////////////////////////////////////////////////////
                                
                                if (str_starts_with($modCategoryDetails['categoryType'] , "Optional")) {
                                    $defered = true;
                                    $deferedStr .= "createCategoryFieldset('" .$modCategoryDetails['title']. "', '$modCategoryQuickCode', '" .$modCategoryDetails['categoryType']. "');\n";
                                }
                                else {
                                    $defered = false;
                                    echo("createCategoryFieldset('" .$modCategoryDetails['title']. "', '$modCategoryQuickCode', '" .$modCategoryDetails['categoryType']. "');\n");
                                }



                                /////////////////////////////////////////////////////////////////////////////////////
                                // GET AND ITERATE THROUGH ALL OF THE MOD ITEMS ASSOCIATED WITH THIS MOD CATEGORY.
                                // DEFER GENERATING OPTIONAL CATEGORIES UNTIL LATER
                                /////////////////////////////////////////////////////////////////////////////////////

                                $sql = "SELECT childQuickCode FROM menuassociations WHERE parentQuickCode = '$modCategoryQuickCode';";
                                $modItems = connection()->query($sql);
                                while($modItem = $modItems->fetch_assoc()) {
                                    $modItemQuickCode = $modItem['childQuickCode'];
                                    $sql = "SELECT * FROM MenuModificationItems WHERE quickCode = '$modItemQuickCode';";
                                    $modItemDetails = connection()->query($sql)->fetch_assoc();
                                    $title = str_replace("'", "\\'", $modItemDetails['title']);
                                    $quantifierString = str_replace("'", "\\'",$modItemDetails['quantifierString']);
                                    $categoryType = $modCategoryDetails['categoryType'];
                                    if ($defered) {
                                        echo("//defered\n");
                                        $deferedStr .= "document.getElementById('$modCategoryQuickCode').appendChild(" .
                                        "generateModOptionDiv('$modItemQuickCode','$title','$quantifierString', false, '$categoryType'));\n";
                                    }
                                    else {
                                        echo ("document.getElementById('$modCategoryQuickCode').appendChild(");
                                        echo("generateModOptionDiv('$modItemQuickCode','$title','$quantifierString', false, '$categoryType')"); 
                                        echo(");\n");
                                    }
                                }
                            }

                            /////////////////////////////////////////////////////////////////////////////////////////
                            // ECHO ANY OPTIONAL MOD CATEGORES THAT WERE DEFERED
                            /////////////////////////////////////////////////////////////////////////////////////////
                            
                            echo($deferedStr);

                            /////////////////////////////////////////////////////////////////////////////////////////
                            // GET THE EXISTING MOD STRING FOR THE TICKET ITEM
                            /////////////////////////////////////////////////////////////////////////////////////////

                            $sql = "SELECT modificationNotes FROM TicketItems WHERE id = '" . $selectedTicketItemId . "';";
                            $recordedModNotes = connection()->query($sql)->fetch_assoc()['modificationNotes'];

                            /////////////////////////////////////////////////////////////////////////////////////////
                            // CONFIGURE THE MODS TO MATCH WHAT WAS ALREADY RECORDED
                            /////////////////////////////////////////////////////////////////////////////////////////

                            if (isset($recordedModNotes)) {
                                echo("configureInputs('$recordedModNotes');");
                            }
                        }
                        unset($_POST['newModValue'], $_POST['priceWithMods']);
                    }
                ?>  
            }
        </script>
    </head>
    <body onload="allElementsLoaded()">
        <form id="frmModWindow" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <button id='btnCommit' type='button'>Accept</button> 
            <button type='button' onpointerdown='signalStatus("await")'>Cancel</button>
            <fieldset class='modOptionFieldset'>
            <label for='txtCustomModNote'>Custom Mod Note</label>
            <input type='text' id='txtCustomModNote'>
            </fieldset>    
            <?php require_once '../Resources/PHP/display.php'; ?>
        </form>
    </body>
</html>

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
    </head>
    <body onload="addSuffixs()">
        <form id="modWindowId" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php
                require_once 'connect_disconnect.php';

                if(!isset($_POST['selectedItem'])) {
                    //<script>signalStatus('await');</script>
                    $_POST['status'] = 'await';
                    //echo("<H1>Waiting for <b>selectedItem</b> to be injected</H1>");
                    
                }
                else {
                    $selectedItem = $_POST['selectedItem'];
                    if (isset($_POST['newModValue'])) {                        
                        $sql = "CALL modifyTicketItem('" .$_POST['selectedItem']. "', '" .$_POST['newModValue']. "');";
                        connection()->query($sql);

                        //<script>signalStatus('await');
                        $_POST['status'] = 'await';
                        //echo("<H1>Waiting for Server Window to redirect back to ticket item</H1>");
                    }
                    else {
                        
                        //===================================================================================================
                        //<script>signalStatus('pending');</script>
                        $_POST['status'] = 'pending';

                        $newModValue = "";

                        // Get the menu item's quick code
                        $sql = "SELECT menuItemQuickCode FROM ticketitems WHERE id = '" . $selectedItem . "';";
                        $result = connection()->query($sql);
                        if($result->num_rows == 1) {
                            $menuItemQuickCode = $result->fetch_assoc()['menuItemQuickCode'];

                            // Get the menu item's title
                            $sql = "SELECT title FROM menuitems WHERE quickCode = '$menuItemQuickCode';";
                            $result = connection()->query($sql);
                            
                            if($result->num_rows == 1) {
                                $menuItemTitle = $result->fetch_assoc()['title'];
                                echo("<div id='lblDefinedMods'>Modifications For <i> $menuItemTitle </i></div>");

                                // Initialize the mod quick code array
                                $modQuickCodeArray = array();
                                // Get the menumodificationitems data based on the mod quick code
                                $sql = "SELECT childQuickCode FROM menuassociations INNER JOIN MenuModificationItems ON MenuAssociations.childQuickCode = MenuModificationItems.quickCode WHERE parentQuickCode = '$menuItemQuickCode' AND visible = 1 ORDER BY categoryType, modGroup, displayIndex;";
                                $result = connection()->query($sql);
                                if($result->num_rows > 0) {
                                    $mandatoryOneStr = "";
                                    $mandatoryAnyStr = "";
                                    $optionalOneStr = "";
                                    $optionalAnyStr = "";

                                    // Get the menumodificationitems data based on the array of mod quick codes
                                    for ($i = 0; $i<sizeof($modQuickCodeArray); $i++) {
                                        $sql = "SELECT title, categoryType, modActionCategory, modGroup, quickCode FROM menumodificationitems WHERE quickCode = '$modQuickCodeArray[$i]';";
                                        $applicableMods = connection()->query($sql);

                                        $lastRecordedCatType = '';

                                        while($row = $applicableMods->fetch_assoc()) {
                                            $quickCode = $row['quickCode']; 
                                            $modTitle = $row['title'];
                                            $modCategoryType = $row['categoryType'];
                                            $modCategory = $row['modActionCategory'];
                                            $modGroup = $row['modActionCategory'];
                                            if (!isset($modGroup)) {
                                                $modGroup = '';
                                            }

                                            // get the options and prices associated with each mod item.
                                            $sql = "SELECT title FROM ModActions ORDER BY title WHERE modActionCategory = '$modCategory';";
                                            $modOptions = connection()->query($sql);
                                            $modOptionCount = sqli_num_rows($modOptions);
                                            
                                            if ($modCategoryType == "MandatoryOne") {
                                                if ($lastRecordedCatType != 'MandatoryOne') {
                                                    $lastRecordedCatType = 'MandatoryOne';
                                                    unset($lastRecordedGroup);
                                                }
                                                if (!isset($lastRecordedGroup) || $modGroup != $lastRecordedGroup) {
                                                    if (isset($lastRecordedGroup)) {
                                                        $mandatoryOneStr .= "</fieldset>";
                                                    }
                                                    $lastRecordedGroup = $modGroup;

                                                    $mandatoryOneStr .= "<fieldset class='modGroup'>";
                                                    if ($modGroup != '') {
                                                        $mandatoryOneStr .= "<legend>$modGroup</legend>";
                                                    }
                                                }
                                                $mandatoryOneStr .= "\n<label id='lbl$quickCode' for='sel$quickCode'>$modTitle</label>".
                                                                    "\n\t<select id='sel$quickCode' name='$quickCode'>";
                                                    for($j = 0; $j < sizeOf($modOptionCount); $j++ ) {
                                                        $modOption = $modOptions->fetch_assoc();
                                                        $modOptionTitle = $modOption['title'];
                                                        $modOptionprice = $modOption['price']; 
                                                        $mandatoryOneStr .= "\n\t\t<option value='$quickCode,$modOptionTitle,$modOptionprice'>
                                                                                        <div class='modItemprice'>" .currencyPrint($modOptionprice). "</div>
                                                                                        <div class='modItemTitle'>$modOptionTitle</div>
                                                                                    </option>";
                                                    }
                                                $mandatoryOneStr .= "</select>";
                                            }
                                            else if ($modCategoryType == 'MandatoryAny') {
                                                if ($lastRecordedCatType != 'MandatoryAny') {
                                                    $lastRecordedCatType = 'MandatoryAny';
                                                    unset($lastRecordedGroup);
                                                }
                                                if (!isset($lastRecordedGroup) || $modGroup != $lastRecordedGroup) {
                                                    if (isset($lastRecordedGroup)) {
                                                        $mandatoryAnyStr .= "</fieldset>";
                                                    }
                                                    $lastRecordedGroup = $modGroup;

                                                    $mandatoryAnyStr .= "<fieldset class='modGroup'>";
                                                    if ($modGroup != '') {
                                                        $mandatoryAnyStr .= "<legend>$modGroup</legend>";
                                                    }
                                                }
                                                $mandatoryAnyStr .= "<fieldset id='lbl$quickCode'><legend>$modTitle</legend>";
                                                    for($j = 0; $j < sizeOf($modOptionCount); $j++ ) {
                                                        $modOption = $modOptions->fetch_assoc();
                                                        $modOptionQuickCode = $modOption['quickCode'];
                                                        $modOptionTitle = $modOption['title'];
                                                        $modOptionprice = $modOption['price']; 
                                                        $optionalAnyStr .= "<div class='checkAndLabel'>
                                                                                <input type='checkbox' id='$modOptionQuickCode' name='$modOptionQuickCode' value='$modOptionQuickCode,$modOptionTitle,$modOptionprice'>
                                                                                <label for='$modOptionQuickCode'>
                                                                                    <div class='modItemprice'>" .currencyPrint($modOptionprice). "</div>
                                                                                    <div class='modItemTitle'>$modOptionTitle</div>
                                                                                </label>
                                                                            </div>";
                                                    }
                                                $mandatoryAnyStr .= "</fieldset>";
                                            }
                                            else if ($modCategoryType == 'OptionalOne') {
                                                if ($lastRecordedCatType != 'OptionalOne') {
                                                    $lastRecordedCatType = 'OptionalOne';
                                                    unset($lastRecordedGroup);
                                                }
                                                if (!isset($lastRecordedGroup) || $modGroup != $lastRecordedGroup) {
                                                    if (isset($lastRecordedGroup)) {
                                                        $optionalOneStr .= "</fieldset>";
                                                    }
                                                    $lastRecordedGroup = $modGroup;

                                                    $optionalOneStr .= "<fieldset class='modGroup'>";
                                                    if ($modGroup != '') {
                                                        $optionalOneStr .= "<legend>$modGroup</legend>";
                                                    }
                                                }
                                                $optionalOneStr .= "<label id='lbl$quickCode' for='sel$quickCode'>$modTitle</label>".
                                                                    "<select id='sel$quickCode' name='$quickCode'>".
                                                                    "<option valvue=''>Select One</option>";
                                                    for($j = 0; $j < sizeOf($modOptionCount); $j++ ) {
                                                        $modOption = $modOptions->fetch_assoc();
                                                        $modOptionTitle = $modOption['title'];
                                                        $modOptionprice = $modOption['price']; 
                                                        $optionalOneStr .= "<option value='$quickCode,$modOptionTitle,$modOptionprice'>
                                                                                        <div class='modItemprice'>" .currencyPrint($modOptionprice). "</div>
                                                                                        <div class='modItemTitle'>$modOptionTitle</div>
                                                                                    </option>";
                                                    }
                                                $optionalOneStr .= "</select>";
                                            }
                                            else if ($modCategoryType == 'OptionalAny') {
                                                //OUT OF THE BOX HTML CANNOT ENFORCE THIS.
                                                //CODE HAS TO BE CREATED TO MAKE SURE AT LEAST 1 ITEM IS CHECKED.
                                                if ($lastRecordedCatType != 'OptionalAny') {
                                                    $lastRecordedCatType = 'OptionalAny';
                                                    unset($lastRecordedGroup);
                                                }
                                                if (!isset($lastRecordedGroup) || $modGroup != $lastRecordedGroup) {
                                                    if (isset($lastRecordedGroup)) {
                                                        $optionalAnyStr .= "</fieldset>";
                                                    }
                                                    $lastRecordedGroup = $modGroup;

                                                    $optionalAnyStr .= "<fieldset class='modGroup'>";
                                                    if ($modGroup != '') {
                                                        $optionalAnyStr .= "<legend>$modGroup</legend>";
                                                    }
                                                }
                                                $optionalAnyStr .= "<fieldset id='lbl$quickCode'><legend>$modTitle</legend>";
                                                    for($j = 0; $j < sizeOf($modOptionCount); $j++ ) {
                                                        $modOption = $modOptions->fetch_assoc();
                                                        $modOptionQuickCode = $modOption['quickCode'];
                                                        $modOptionTitle = $modOption['title'];
                                                        $modOptionprice = $modOption['price']; 
                                                        $optionalAnyStr .= "<div class='checkAndLabel'>
                                                                                <input type='checkbox' id='$modOptionQuickCode' name='$modOptionQuickCode' value='$modOptionQuickCode,$modOptionTitle,$modOptionprice'>
                                                                                <label for='$modOptionQuickCode'>
                                                                                    <div class='modItemprice'>" .currencyPrint($modOptionprice). "</div>
                                                                                    <div class='modItemTitle'>$modOptionTitle</div>
                                                                                </label>
                                                                            </div>";
                                                    }
                                                $optionalAnyStr .= "</fieldset>";
                                            }   
                                        }
                                    }
                                    if ($mandatoryOneStr != "") {
                                        echo("<fieldset id='fstMandatoryOne' class='predefinedMods'><legend>Must Select 1 for Each of the Following Categories</legend>
                                            $mandatoryOneStr
                                            </fieldset></fieldset>");
                                    }
                                    if ($mandatoryAnyStr != "") {
                                        echo("<fieldset id='fstMandatoryAny' class='predefinedMods'><legend>Must Select Any for Each of the Following Categories</legend>
                                            $mandatoryAnyStr
                                            </fieldset></fieldset>");
                                    }
                                    if ($optionalOneStr != "") {
                                        echo("<fieldset id='fstOptionalOne' class='predefinedMods'><legend>Select 1 for Each of the Following Categories</legend>
                                            $optionalOneStr
                                            </fieldset></fieldset>");
                                    }
                                    if ($optionalAnyStr != "") {
                                        echo("<fieldset id='fstOptionalAny' class='predefinedMods'><legend>Select Any for Each of the Following Categories</legend>
                                            $optionalAnyStr
                                            </fieldset></fieldset>");
                                    }
                                }
                                else {
                                    //echo "<h3 class='debug_statement'>No Predefined Mods Found for this item</h3>";
                                }
                                echo("<fieldset id='fstCustomMod'>
                                        <label for='txtModString'>Custom Mod Note</label>
                                        <input type='text' id='txtModString' name='newModNote'>
                                      </fieldset>");
                            }
                            else {
                                // debug, should be unrechable code
                                echo("<div id='lblDefinedMods' class='debug_statement'>Menu Item Not Found</div>");
                            }
                        }
                        else {
                            echo "<h3 class='debug_statement'>Ticket Item Not Found</h3>";
                        }
                                                    
                        echo("<button id='submitBtn' type='button'>Update Mods</button> 
                        <input id='postSubmitBtn' type='submit' value='Update Mods' style='display: none;' >
                        <button type='button' onpointerdown='signalStatus(" .'"await"'. ")'>Cancel Update</button>");
                        //===================================================================================================
                    }
                                 
                }
                require_once 'display.php';
            ?>
        </form>
    </body>
</html>
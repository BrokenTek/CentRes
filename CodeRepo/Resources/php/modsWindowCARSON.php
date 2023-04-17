
<!-- TEMPORARY COMMENT!!!!!!!!!!!
    THIS HAS A SYNTAX ERROR BUT SHOULD BE WORKING AT THIS POINT. THERE IS NOT MUCH DATA TO WORK WITH BUT THAT SCRIPT WILL COME TOMORROW LIKELY
      THE CURRENT IMPLMEMENTATION DOES NOT WORK. THERE IS A SYNTAX ERROR. AROUND LINE 97 AS PER THE ERROR MSG IN CHROME. FIX SHOULD BE EASY TO
      APPLY. FEATURE MISSING CURRENTLY: MANDATORY VS OPTIONAL DISTINCTION. EASY ADDITION WITH IF STATEMENT IF CURRENT VERSION WOULD WORK.
-->

<?php require_once './sessionLogic.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Modification Selection</title>
        <link rel="stylesheet" href="../CSS/baseStyle.css">
        <script src="../JavaScript/displayInterface.js"></script>
        <script src="../JavaScript/modOptionUtility.js"></script>


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

            function listenerFormCreator() {    
                
                // Get the form element
                var form = document.getElementById("modWindowId");

                // New form input builder
                ///////////////////////////////////////////////////////////////////////////////////////////////////////



                ///////////////////////////////////////////////////////////////////////////////////////////////////////






                // Create newModValue string
                form.addEventListener("pointerdown", function(event) {
                    if (event.target.id === 'submitBtn') {
                        // var newModValue = "";

                        // // Get all the checked checkboxes
                        // var checkedBoxes = document.querySelectorAll('input[type="checkbox"]:checked');

                        // // Loop through the checked checkboxes
                        // for (var i = 0; i < checkedBoxes.length; i++) {
                        //     // Get the parent div of the checkbox
                        //     var parentDiv = checkedBoxes[i].parentNode;

                        //     // Get the select element
                        //     var select = parentDiv.querySelector('select');

                        //     // Get the value of the select element
                        //     var selectValue = select.value;

                        //     // Get the value of the checkbox
                        //     var checkboxValue = checkedBoxes[i].value;

                        //     // Append the checkbox value and select value to the newModValue string
                        //     newModValue += checkboxValue + "," + selectValue + ",";
                        // }

                        // if (document.getElementById('txtModString').value != "") {
                        //     var customModNote = document.getElementById('txtModString');
                        //     customModNote = customModNote.value.replaceAll(',', '.');
                        //     newModValue += customModNote;
                        // }

                        // else {
                        //     newModValue = newModValue.slice(0, -1);
                        // }

                        var newModValue = generateModString();

                        varSet("newModValue", newModValue);

                        // Click the submit button (end of function)
                        document.getElementById('postSubmitBtn').click();
                    }
                });

            }
        </script>
    </head>
    <body onload="listenerFormCreator()">
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
                        try {
                            connection()->query($sql);
                        }
                        catch (Exception $e) {
                            echo "<(h3 class='debug_statement'>)Error: " . $e->getMessage() . "</h3>";
                        }
                        echo("<script>signalStatus('await');</script>");
                        //echo("<H1>Waiting for Server Window to redirect back to ticket item</H1>");
                    }
                    else {
                        
                        //===================================================================================================
                        echo("<script>signalStatus('pending');</script>");
// ERROR LIKELY         // Open script for populating form inputs
                        echo("<script>");

                        // POPULATE FORM INPUTS /////////////////////////////////////////////////////////////////////////////////////////////////////

                        // Get the menu item's quick code
                        $sql = "SELECT menuItemQuickCode FROM ticketitems WHERE id = '" . $selectedItem . "';";
                        $result = connection()->query($sql);
                        if($result->num_rows > 0) {
                            $menuItemQuickCode = $result->fetch_assoc()['menuItemQuickCode'];
                        }
                        else {
                            echo ("<h3 class='debug_statement'>Not Able To Get </h3>");
                        }

                        // Initialize the mod quick code array
                        $modCatQuickCodeArray = array();
                        // ^Get the menumodificationitems data based on the mod quick code
                        $sql = "SELECT childQuickCode FROM menuassociations WHERE parentQuickCode = '$menuItemQuickCode';";
                        $result = connection()->query($sql);
                        if($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $modCatQuickCode = $row['childQuickCode'];
                                array_push($modCatQuickCodeArray, $modCatQuickCode);
                            }
                        }
                        else {
                            echo("<h3 class='debug_statement'>No Results4</h3>");
                        }
                        
                        // Initialize array of arrays that provice the arguments for generateModOptionDiv()
                        $modTitleQuickCodeQuantifierArray = array();

                        // Get the modCategory 'types' and title(s) and quickCode(s)
                        for ($i = 0; $i < count($modCatQuickCodeArray); $i++) {
                            // get the categoryType (mandatoryAny, mandatoryAll, optionalAny, optionalAll) and mod cat title
                            $sql = "SELECT title, quickCode, categoryType FROM menumodificationcategories WHERE quickCode = '$modCatQuickCodeArray[$i]';";
                            $result = connection()->query($sql);
                            if($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $categoryTitle = $row['title'];
                                    $categoryQuickCode = $row['quickCode'];
                                    $categoryType = $row['categoryType'];

                                    // Get modItem('s) quickCode(s), title(s) and quantifierString(s)
                                    // for ($i = 0; $i < count($modCatQuickCodeArray); $i++) {
                                    $sql = "SELECT quickCode, title, quantifierString FROM menumodificationitems WHERE quickCode = '$categoryQuickCode';";
                                    $result = connection()->query($sql);
                                    if($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            $modItemTitle = $row['title'];
                                            $modItemQuickCode = $row['quickCode'];
                                            $modItemQuantifierString = $row['quantifierString'];

                                            // Possible change here where the loop ends and items are appended to an array for each result that is appended to the array initialized above
                                            array_push($modTitleQuickCodeQuantifierArray, array($modItemTitle, $modItemQuickCode, $modItemQuantifierString));

                                        //     // Create the mod category div
                                        //     echo("var newModValueGenerated = generateModOptionDiv('$modItemQuickCode', '$modItemTitle', '$modItemQuantifierString');");
                                        //     echo("var form = document.getElementById('modWindowId');");
                                        //     echo("form.appendChild(newModValueGenerated);");
                                        // }
                                        }
                                    }
                                    else {
                                        echo("<h3 class='debug_statement'>Cannot Get modItem('s) quickCode(s), title(s) and quantifierString(s) - Mod Category: $categoryTitle</h3>");
                                    }
                                }
                            }
                            else {
                                echo("<h3 class='debug_statement'>Cannot Get CategoryType from menumoificationcategories</h3>");
                            }
                        }

                        for ($i = 0; $i < count($modTitleQuickCodeQuantifierArray); $i++) {
                            $modItemTitle = $modTitleQuickCodeQuantifierArray[$i][0];
                            $modItemQuickCode = $modTitleQuickCodeQuantifierArray[$i][1];
                            $modItemQuantifierString = $modTitleQuickCodeQuantifierArray[$i][2];

                            // Create the mod category div
                            echo("var newModValueGenerated = generateModOptionDiv('$modItemQuickCode', '$modItemTitle', '$modItemQuantifierString');");
                            echo("var form = document.getElementById('modWindowId');");
                            echo("form.appendChild(newModValueGenerated);");
                        }
                        

// Error Likely 2       // Close script tag that populates form input elements
                        echo("<br></script>");

                        // POPULATE FORM INPUTS - END /////////////////////////////////////////////////////////////////////////////////////////////////////

                        // // Get modItem('s) quickCode(s), title(s) and quantifierString(s)
                        // for ($i = 0; $i < count($modCatQuickCodeArray); $i++) {
                        //     $sql = "SELECT quickCode, title, quantifierString FROM menumodificationitems WHERE quickCode = '$modCatQuickCodeArray[$i]';";
                        // }






                        // $sql = "SELECT quantifierString FROM menumodificationitems WHERE quickCode = 'Y0060';";
                        // $result = connection()->query($sql);

                        // if($result->num_rows > 0) {
                        //     while($row = $result->fetch_assoc()) {
                        //         $quantifierString = $row['quantifierString'];
                        //     }
                        // }
                        // else {
                        //     echo("<h3 class='debug_statement'>No Results</h3>");
                        // }


                        // Start Of Script To Create Form!
                        // echo("<script>");

                        // $sql = "SELECT quickCode FROM menumodificationcategories WHERE quickCode = 'X0001';";

                        // Add the input elements to the form
                        // echo("var newModValueGenerated = generateModOptionDiv('Y0060', 'Gruyere', '$quantifierString');");
                        // echo("var form = document.getElementById('modWindowId');");
                        // echo("form.appendChild(newModValueGenerated);");

                        // echo("</script>");


                        /////////////////////////////////////////////////////////////////////////////////////////////////////
                            
                        echo("<hr>");
                        
                        // Custom mod note name changed from newModValue -> newModNote
                        echo("<label for='txtModString'>Mod String w/ commas</label>
                        <input type='text' id='txtModString' name='newModNote'>    
                        <button id='submitBtn' type='button'>Update Mods</button> 
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
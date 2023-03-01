<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html lang='en'>
<head>

<!-- <IfModule mod_mime.c>
    AddType application/manifest+json   webmanifest
</IfModule> -->
    <meta charset='utf-8' />
    <meta name="viewport" content="width=device-width" />
    <link rel="stylesheet" href="../Resources/CSS/tableStyles.css" />
    <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script>
    <script src="../Resources/JavaScript/SvgManipulation.js"></script>
    <!-- Will Need To Change CSS File Path Later -->

    <script>
        function allElementsLoaded() {
            // TODO
            // add pointer down and pointer up event listeners
            // to select/multi-select


            updateDisplay("tableStatusListener");
            updateTableStatuses();
            startListenerLoop();
        }

        function updateTableStatuses() {
            var newTableData = getVar("updatedTables", "tableStatusListener").split(",");
            for (var i = 0; i < newTableData.length; i += 2 ) {
                with (document.getElementById(newTableData[i]).classList) {
                    remove("disabled","unassigned", "open", "seated", "bussing");
                    add(newTableData[i+1]);
                }
            }
        }

        var listenerLoopTimer;
        function listenerLoop() {
            updateDisplay("tableStatusListneer");
            if (getVar("updatedTables", "tableStatusListener") !== undefined) {
                updateTableStatuses();
            }
            startListenerLoop();
        }
        
        function startListenerLoop() {
            listenerLoop = setTimeout(listenerLoop, 1000);
        }

        function stopListenerLoop() {
            clearTimeout(listenerLoopTimer);
        }

        function onClickTable(tableId) {
            var oldSelectedTables = document.getElementsByClassName("selected");
            for(var i = 0 ; i < oldSelectedTables.length; i++) {
                oldSelectedTables[i].classList.remove("selected");
            }
            document.getElementById(tableId).classList.add("selected");
            setVar("selectedTable", tableId);
        }


    </script>

</head>

<body onload="allElementsLoaded()">
    <form>
        
    <svg id='parentSvg' xmlns="http://www.w3.org/2000/svg" height="100vh" width="100vw">
        
        <script type="application/ecmascript">
            <![CDATA[
                function transformMe(evt) {
                    // svg root element to access the createSVGTransform() function
                    const svgroot = evt.target.parentNode;
                    // SVGTransformList of the element that has been clicked on
                    const tfmList = evt.target.transform.baseVal;

                    // Create a separate transform object for each transform
                    const translate = svgroot.createSVGTransform();
                    translate.setTranslate(50,5);
                    const rotate = svgroot.createSVGTransform();
                    rotate.setRotate(10,0,0);
                    const scale = svgroot.createSVGTransform();
                    scale.setScale(0.8,0.8);

                    // apply the transformations by appending the SVGTransform objects to the SVGTransformList associated with the element
                    tfmList.appendItem(translate);
                    tfmList.appendItem(rotate);
                    tfmList.appendItem(scale);
            }
            ]]>
        </script>

        <!--   HARD CODED TABLES FOR DAVID TO TEST WITH IN SelectedTable.php -->
        <path id="T01" onpointerdown="onClickTable('T01')" width="5vmin" height="10vmin" class="table booth" d="M1 16V1H14.9535V16M1 16V31H14.9535V16M1 16H14.9535" fill="#808080" stroke="black" stroke-opacity="0.75" transform="translate(0 0)" />
        <circle id="T02" onpointerdown="onClickTable('T02')" class="table hightop" width="10vmin" height="10vmin" cx="5vmin" cy="5vmin" r="5vmin" fill="grey" stroke="black" stroke-opacity="0.75" transform="translate(0 100)" />
        <rect id="T03" onpointerdown="onClickTable('T03')" class="table longtable" width="10vmin" height="5vmin" fill="grey" stroke="black" stroke-opacity="0.75" transform="translate(0 200)" />
        <rect id="T04" onpointerdown="onClickTable('T04')" class="table square" width="10vmin" height="10vmin" fill="grey" stroke="black" stroke-opacity="0.75" transform="translate(0 300)" />

        <path id="T05" onpointerdown="onClickTable('T05')" width="5vmin" height="10vmin" class="table booth" d="M1 16V1H14.9535V16M1 16V31H14.9535V16M1 16H14.9535" fill="#808080" stroke="black" stroke-opacity="0.75" transform="translate(200 0)" />
        <circle id="T06" onpointerdown="onClickTable('T06')" class="table hightop" width="10vmin" height="10vmin" cx="5vmin" cy="5vmin" r="5vmin" fill="grey" stroke="black" stroke-opacity="0.75" transform="translate(200 100)" />
        <rect id="T07" onpointerdown="onClickTable('T07')" class="table longtable" width="10vmin" height="5vmin" fill="grey" stroke="black" stroke-opacity="0.75" transform="translate(200 200)" />
        <rect id="T08" onpointerdown="onClickTable('T08')" class="table square" width="10vmin" height="10vmin" fill="grey" stroke="black" stroke-opacity="0.75" transform="translate(200 300)" />

        <?php
            // Load all of the tables here
            
                $sql = "SELECT id, transformData, shapeName, svgPathData FROM Tables JOIN TableShapes ON Tables.shape = TableShapes.shapeName;";
                $tables = connection()->query($sql);
                while ($row = $tables->fetch_assoc()) {
                    try {
                        $tableStr = str_replace("TABLEID", $row['id'], $row['svgPathData']);                
                        $tableStr = str_replace("TRANSFORMDATA", $row['transformData'], $tableStr);
                        echo("<$tableStr/>");
                    }
                        catch (Exception $e) {
                        echo($e);
                    }
                }      
                // Load all of the structure/non table elements here
        ?>

    </svg>

    <script>
        
        // window.addEventListener('DOMContentLoaded', setDimensions);
    </script>
    </form>
    <iframe id="tableStatusListener" src="TableStatusListener.php" style="display: none;"></iframe>
</body>
</html>


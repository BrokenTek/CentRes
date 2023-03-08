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
    <style>
        .multiselect {
            animation: goDark .25s 0s ease forwards;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #111;
        }

    @keyframes goDark {
        0% {background-color: initial;}
        100% { background-color: #333;}
    }
    </style>
    <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script>
    <script src="../Resources/JavaScript/SvgManipulation.js"></script>
    <!-- Will Need To Change CSS File Path Later -->

    <script>
        function allElementsLoaded() {
             //create table select listeners
             var elements = document.getElementsByClassName('table');
            if (elements != null) {
                for (var i = 0; i < elements.length; i++) {
                    elements[i].addEventListener('pointerdown',pointerDown);
                    elements[i].addEventListener('pointerup', pointerUp);
                    elements[i].addEventListener('pointerenter', pointerEnter);
                    elements[i].addEventListener('pointerleave', pointerLeave);
                }
            }
            document.getElementsByTagName("form")[0].addEventListener('pointerup', disengageMultiselect);
            updateDisplay("tableStatusListener");
            updateTableStatuses();
            startListenerLoop();

           
        }

        function updateTableStatuses() {
            try {
                var newTableData = getVar("updatedTables", "tableStatusListener");
                if (newTableData != null) {
                    newTableData = newTableData.split(",");
                    for (var i = 0; i < newTableData.length; i += 2 ) {
                        with (document.getElementById(newTableData[i]).classList) {
                            remove("disabled","unassigned", "open", "seated", "bussing");
                            add(newTableData[i+1]);
                        }
                    }
                    let selectedTable = getVar("selectedTable");
                    if (selectedTable !== undefined && ("," + newTableData + ",").indexOf("," + selectedTable + ",") > -1) {
                        setVar("flag", "updateSelectedTable");
                    }
                }
            }
            catch (err) {
                setTimeout(updateTableStatuses, 250);
            }
        }

        var listenerLoopTimer;
        var update = true;
        function listenerLoop() {
            if (update) {
                updateDisplay("tableStatusListener");
            }
            try {
                if (getVar("updatedTables", "tableStatusListener") !== undefined) {
                    updateTableStatuses();
                }
            }

            catch (err) {
                update = false;
                setTimeout(listenerLoop, 250);
                return;
            }
            
            if (getVarOnce("highlightedTablesChanged")) {
                let allTables = document.getElementsByClassName("table");
                for (let i = 0; i < allTables.length; i++) {
                    allTables[i].classList.remove("highlighted");
                }

                let tables = getVar("highlightedTables");
                if (tables !== undefined) {
                    tables = tables.split(",");
                    for (let i = 0; i < tables.length; i++) {
                        document.getElementById(tables[i]).classList.add("highlighted");
                    }
                }
            }

            update = true;
            startListenerLoop();
           
        }
        
        function startListenerLoop() {
            listenerLoopTimer = setTimeout(listenerLoop, 1000);
        }

        function stopListenerLoop() {
            clearTimeout(listenerLoopTimer);
        }

         // ========================= TABLE SELECT FUNCTIONS ==============================
         const DOUBLE_TOUCH_LENGTH = 500;
        var targetTable = null;
        var doubleTouchTimer = null;
        var multiselectEnabled = false;
        var pointerIsDown = false;
        function pointerDown() {
            if (this === undefined) { return; } else { event.stopPropagation(); }
            pointerIsDown = true;
            if (targetTable != null && this == targetTable) {
                setVar("goToTable", targetTable.id);
                clearInterval(doubleTouchTimer);
                clearSelectedTables(this.id);
                return;
            }
            else if (getVar("authorizationId") !== undefined && this.classList.contains("seated")) {
                targetTable = this;
                doubleTouchTimer = setTimeout(() => {
                    targetTable = null;
                }, DOUBLE_TOUCH_LENGTH);
            }
            if (!multiselectEnabled) {
                let selTables = getVar("selectedTable");
                if (selTables !== undefined && selTables.indexOf(",") > -1 && selTables.indexOf(this.id) > -1) {
                    clearSelectedTables(this.id);
                }
                else {
                    let id = this.classList.contains("selected") ? null : this.id;
                    clearSelectedTables(id);
                }
            }
            else {
                this.classList.toggle("selected");
                updateSelectedTables();
            }       
        }

        function pointerEnter() {
            if (pointerIsDown) {
                if (multiselectEnabled) {
                    this.classList.toggle("selected");
                    updateSelectedTables();
                }
                else {
                    let lookAt = getVar("selectedTable");
                    if (lookAt != null && lookAt.indexOf(",") == -1) {
                        multiselectEnabled = true;
                        document.getElementsByTagName("form")[0].classList.add("multiselect");
                        this.classList.toggle("selected");
                        updateSelectedTables();
                    }
                }
                
            }
        }

        function pointerLeave() {
            targetTable = null;
        }
        function pointerDownOnNothing() {
            if (multiselectEnabled) {
                multiselectEnabled = false;
                document.getElementsByTagName("form")[0].classList.remove("multiselect");
            }
            else {
                var oldSelectedItems = document.getElementsByClassName("table");
                for(let i = 0; i < oldSelectedItems.length; i++){
                    oldSelectedItems[i].classList.remove("selected");
                }
                setVar("selectedTable", "clear");
            }
            
        }

        // when you've made your current selection
        function pointerUp(disableMultiselect = false) {
            
            pointerIsDown = false;
            if (this === undefined) { return; } else { event.stopPropagation(); }
        }

        function disengageMultiselect() {
            pointerIsDown = false;
            multiselectEnabled = false;
            document.getElementsByTagName("form")[0].classList.remove("multiselect");
        }

        function updateSelectedTables() {
            let selectedTables = document.getElementsByClassName("selected");
            if (selectedTables.length == 0) {
                removeVar("selectedTable");
            }
            else {
                let selTableStr = selectedTables[0].id;
                for(let i = 1; i < selectedTables.length; i++) {
                    selTableStr += "," + selectedTables[i].id;
                }
                setVar("selectedTable", selTableStr);
            }
        }

        function clearSelectedTables(id) {
            setVar("selectedTable", id == null ? "clear" : id);
            var selTables = document.getElementsByClassName("table") 
            for (let i = 0; i < selTables.length; i++) {
                selTables[i].classList.remove("selected");
            }
            if (id != null) { 
                document.getElementById(id).classList.add("selected");
            }    
        }


    </script>

</head>

<body onload="allElementsLoaded()">
    <form>
        
    <svg id='parentSvg' xmlns="http://www.w3.org/2000/svg" height="100vh" width="100vw" onpointerdown="pointerDownOnNothing()">
        
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
        <path id="T01" width="5vmin" height="10vmin" class="table booth unassigned" d="M1 16V1H14.9535V16M1 16V31H14.9535V16M1 16H14.9535" fill="#808080" stroke="black" stroke-opacity="0.75" transform="translate(0 0)" />
        <circle id="T02" class="table hightop unassigned" width="10vmin" height="10vmin" cx="5vmin" cy="5vmin" r="5vmin" fill="grey" stroke="black" stroke-opacity="0.75" transform="translate(0 100)" />
        <rect id="T03" class="table longtable unassigned" width="10vmin" height="5vmin" fill="grey" stroke="black" stroke-opacity="0.75" transform="translate(0 200)" />
        <rect id="T04" class="table square unassigned" width="10vmin" height="10vmin" fill="grey" stroke="black" stroke-opacity="0.75" transform="translate(0 300)" />

        <path id="T05" width="5vmin" height="10vmin" class="table booth unassigned" d="M1 16V1H14.9535V16M1 16V31H14.9535V16M1 16H14.9535" fill="#808080" stroke="black" stroke-opacity="0.75" transform="translate(200 0)" />
        <circle id="T06" class="table hightop unassigned" width="10vmin" height="10vmin" cx="5vmin" cy="5vmin" r="5vmin" fill="grey" stroke="black" stroke-opacity="0.75" transform="translate(200 100)" />
        <rect id="T07" class="table longtable unassigned" width="10vmin" height="5vmin" fill="grey" stroke="black" stroke-opacity="0.75" transform="translate(200 200)" />
        <rect id="T08" class="table square unassigned" width="10vmin" height="10vmin" fill="grey" stroke="black" stroke-opacity="0.75" transform="translate(200 300)" />

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


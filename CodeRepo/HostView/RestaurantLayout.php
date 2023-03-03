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
             //create table select listeners
             var elements = document.getElementsByClassName('table');
            if (elements != null) {
                for (var i = 0; i < elements.length; i++) {
                    elements[i].addEventListener('pointerdown',pointerDown);
                    elements[i].addEventListener('pointerup', pointerUp);
                }
            }
            updateDisplay("tableStatusListener");
            updateTableStatuses();
            startListenerLoop();

           
        }

        function updateTableStatuses() {
            try {
                var newTableData = getVar("updatedTables", "tableStatusListener").split(",");
                for (var i = 0; i < newTableData.length; i += 2 ) {
                    with (document.getElementById(newTableData[i]).classList) {
                        remove("disabled","unassigned", "open", "seated", "bussing");
                        add(newTableData[i+1]);
                    }
                }
            }
            catch (err) {
                setTimeout(updateTableStatuses, 250);
            }
        }

        var listenerLoopTimer;
        var update = true;
        function listenerLoop(update = true) {
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
   
    const LONG_TIME_TOUCH_LENGTH = 500;
    const DOUBLE_TOUCH_LENGTH = 500;
    var targetTable = null;
    var longTouchEnabled = false;
    var longTouchTimer = null;
    var doubleTouchTimer = null;
	function pointerDown() {
        if (this === undefined) { return; }
        if (targetTable != null && this == targetTable) {
            if (this.classList.contains("seated")) {
                setVar("goToTable", targetTable.id);
            }
            clearInterval(doubleTouchTimer);
        }
        targetTable = this;
        targetTable.classList.add("selected");
        if (getVar("selectedTable") != null && getVar("selectedTable") != this.id) {
            longTouchTimer = setTimeout(longTouch, LONG_TIME_TOUCH_LENGTH);
        }
        var classList = Array.from(targetTable.classList)
        if (getVar("authorizationId") !== undefined && Array.from(targetTable.classList).indexOf("seated") > -1) {
                doubleTouchTimer = setTimeout(doubleTouchDisable, DOUBLE_TOUCH_LENGTH);
        }
	}

    // if oyu pressed on a ticket item, you already have another one selected, and the minimum required time
    // for multiselect has elapsed, change the selected item to "multiselect" 
    function longTouch() {
         longTouchEnabled = true;
         targetTable.classList.add("multiselect");

        // if there is exactly 1 other item selected, make it multi-select as well.
        var alreadySelected = getVar("selectedTable");
        if (alreadySelected != null && alreadySelected.indexOf(",") == -1) {
            document.getElementById(alreadySelected).classList.add("multiselect");
        }
    }

    function doubleTouchDisable() {
        clearTimeout(doubleTouchTimer);
        targetTable = null;
    }

    // when you've made your current selection
    function pointerUp() {
        if (targetTable == null) { return; }
        
        if (longTouchTimer != null) {
            clearTimeout(longTouchTimer);
        }

        var oldSelectedItems = document.getElementsByClassName("table");
        // if you only have 1 item selected, adjust the state of applicable ticket items to reflect that.
        if (!longTouchEnabled) {
    	    /*this iterates through the list returned, if there is no case where multiple items are selected concurrently,
    	    you can just use oldSelectedItems[0].classList.remove("selected"); instead*/
    	    for(let i = 0; i < oldSelectedItems.length; i++){
                if (oldSelectedItems[i] != targetTable) {
                    oldSelectedItems[i].classList.remove("selected");
                }
                try {
                    oldSelectedItems[i].classList.remove("multiselect");
                }
                catch (err) {
                    alert(oldSelectedItems[i]);
                }
    	    }
            setVar("selectedTable", targetTable.id);
        }
        // or you have multiple tables selected
        else {
            for(let i = 0; i < oldSelectedItems.length; i++){
        	    oldSelectedItems[i].classList.add("multiselect");
    	    }

            setVar("selectedTable", getVar("selectedTable") + "," + targetTable.id); 
        }
        
        if (doubleTouchTimer == null) {
            targetTable = null;
        }
        longTouchEnabled = false;
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


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
            width: 1300px;
            height:200px;
        }

        .tabletest {
            opacity: 20%;
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
        <defs>
            <linearGradient id="GradientStructure">
                <stop class="stop1" offset="0%" />
                <stop class="stop2" offset="50%" />
                <stop class="stop3" offset="100%" />
            </linearGradient>    
            <linearGradient id="Gradient2" x1="0" x2="0" y1="0" y2="1">
                <stop offset="0%" stop-color="#D2D2D2" />
                <stop offset="50%" stop-color="#ADADAD" stop-opacity="0.5" />
                <stop offset="100%" stop-color="#E7E7E7" />
            </linearGradient>

            <style>
            <![CDATA[
                .structure { fill: url(#GradientStructure); fill-opacity: 0.20; }
                .stop1 { stop-color: #D2D2D2; }
                .stop2 { stop-color: #ADADAD; stop-opacity: 0; }
                .stop3 { stop-color: #E7E7E7; }
            ]]>
            </style>
        </defs>    

        <svg> 
            <svg id="structureLayout">
                
                <line class="structure" id="Line 1" x1="428.5" y1="247" x2="428.5" y2="467" stroke="black" stroke-width="3"/>
                <line class="structure" id="Line 12" x1="1294.5" y1="14" x2="1294.5" y2="169" stroke="black" stroke-width="3"/>
                <line class="structure" id="Line 3" x1="246.5" y1="8" x2="246.5" y2="119" stroke="black" stroke-width="3"/>
                <line class="structure" id="Line 2" x1="246.991" y1="173.004" x2="246" y2="633.004" stroke="black" stroke-width="4"/>
                <line class="structure" id="Line 6" x1="951" y1="195" x2="951" y2="506" stroke="black" stroke-width="4"/>
                <line class="structure" id="Line 8" x1="1287" y1="195" x2="1287" y2="506" stroke="black" stroke-width="4"/>
                <rect class="structure" id="backline" x="53.5" y="45.5" width="65" height="658" stroke="black" stroke-width="5"/>
                <path class="structure" id="stairs" d="M250 126V167H251V126H250ZM254 131V161H255V131H254ZM255 129.5H257.5V162.5H255H254.5V163V168.5H251H250.5V169V172.5H245.5V119.5L250.5 119.5V124.048V124.548H251H254.451L254.5 129.006L254.505 129.5H255Z" stroke="black"/>
                <path class="structure" id="stairs_2" d="M902 196H943V195H902V196ZM907 192H937V191H907V192ZM905.5 191V188.5H938.5V191V191.5H939H944.5V195V195.5H945H948.5V200.5H895.5L895.5 195.5H900.048H900.548V195V191.549L905.006 191.5L905.5 191.495V191Z" stroke="black"/>
                <path class="structure" id="stairs_3" d="M902 503H943V502H902V503ZM907 499H937V498H907V499ZM905.5 498V495.5H938.5V498V498.5H939H944.5V502V502.5H945H948.5V507.5H895.5L895.5 502.5H900.048H900.548V502V498.549L905.006 498.5L905.5 498.495V498Z" stroke="black"/>
                <path class="structure" id="stairs_4" d="M249 640V681H250V640H249ZM253 645V675H254V645H253ZM254 643.5H256.5V676.5H254H253.5V677V682.5H250H249.5V683V686.5H244.5V633.5L249.5 633.5V638.048V638.548H250H253.451L253.5 643.006L253.505 643.5H254Z" stroke="black"/>
                <path class="structure" id="Rectangle 2" d="M694 33V34V55V57H250V10H694V12V31V32V33Z" stroke="black" stroke-width="4"/>
                <line class="structure" id="Line 4" x1="426.995" y1="199.5" x2="894.995" y2="198.5" stroke="black" stroke-width="5"/>
                <line class="structure" id="Line 5" x1="426.995" y1="504.5" x2="894.995" y2="503.5" stroke="black" stroke-width="5"/>
                <line class="structure" id="Line 14" x1="51" y1="739.5" x2="1293" y2="739.5" stroke="black" stroke-width="5"/>
                <line class="structure" id="Line 7" x1="999" y1="197.5" x2="1285" y2="197.5" stroke="black" stroke-width="5"/>
                <line class="structure" id="Line 11" x1="1027" y1="166.5" x2="1293" y2="166.5" stroke="black" stroke-width="5"/>
                <line class="structure" id="Line 10" x1="1027" y1="16.5" x2="1293" y2="16.5" stroke="black" stroke-width="5"/>
                <path class="structure" id="Line 13" d="M701 10L1027 10L1027 19" stroke="black" stroke-width="4"/>
                <line class="structure" id="Line 9" x1="999" y1="503.5" x2="1285" y2="503.5" stroke="black" stroke-width="5"/>
                <path class="structure" id="Polygon 5" d="M1049.02 653.526V653.78L1048.9 654.003L1041.11 668.357C1040.69 669.131 1041.41 670.031 1042.26 669.798L1072 661.658V605.682L1042.52 596.461C1041.65 596.191 1040.9 597.117 1041.35 597.906L1048.89 611.299L1049.02 611.527V611.789V653.526Z" stroke="black" stroke-width="2"/>
                <path class="structure" id="Polygon 6" d="M1295.8 582.584C1297.11 582.054 1298.22 581.654 1299 581.383V632H1261.99C1261.99 631.999 1261.99 631.998 1261.98 631.996C1261.97 631.983 1261.94 631.948 1261.91 631.886C1261.89 631.825 1261.88 631.766 1261.89 631.724C1261.89 631.689 1261.9 631.676 1261.91 631.669C1265.41 628.023 1271.71 619.629 1271.71 606.5C1271.71 598.245 1278.58 591.879 1285.87 587.481C1289.47 585.309 1293.08 583.675 1295.8 582.584Z" stroke="black" stroke-width="2"/>
                <path class="structure" id="Polygon 7" d="M1295.8 683.416C1297.11 683.946 1298.22 684.346 1299 684.617V634H1261.99C1261.99 634.001 1261.99 634.002 1261.98 634.004C1261.97 634.017 1261.94 634.052 1261.91 634.114C1261.89 634.175 1261.88 634.234 1261.89 634.276C1261.89 634.311 1261.9 634.324 1261.91 634.331C1265.41 637.977 1271.71 646.371 1271.71 659.5C1271.71 667.755 1278.58 674.121 1285.87 678.519C1289.47 680.691 1293.08 682.325 1295.8 683.416Z" stroke="black" stroke-width="2"/>
                <path class="structure" id="Arrow 1" d="M1188.96 610.924C1188.36 611.501 1188.35 612.451 1188.92 613.045L1198.33 622.73C1198.91 623.325 1199.86 623.338 1200.45 622.761C1201.04 622.184 1201.06 621.235 1200.48 620.64L1192.12 612.031L1200.73 603.672C1201.32 603.094 1201.34 602.145 1200.76 601.55C1200.18 600.956 1199.23 600.942 1198.64 601.519L1188.96 610.924ZM1259.02 611.515L1190.02 610.5L1189.98 613.5L1258.98 614.514L1259.02 611.515Z" fill="black"/>
                <path class="structure" id="Arrow 2" d="M1188.96 651.938C1188.36 652.516 1188.35 653.465 1188.92 654.06L1198.33 663.745C1198.91 664.339 1199.86 664.353 1200.45 663.776C1201.04 663.199 1201.06 662.249 1200.48 661.655L1192.12 653.046L1200.73 644.686C1201.32 644.109 1201.34 643.159 1200.76 642.565C1200.18 641.971 1199.23 641.957 1198.64 642.534L1188.96 651.938ZM1259.02 652.529L1190.02 651.515L1189.98 654.514L1258.98 655.529L1259.02 652.529Z" fill="black"/>
                <line class="structure" id="Line 15" x1="246" y1="687" x2="246" y2="737" stroke="black" stroke-width="4"/>
                
            </svg>
        </svg>

        <?php
            // Load all of the tables here
                $sql = "SELECT id, transformData, shapeName, svgPathData FROM Tables JOIN TableShapes ON Tables.shape = TableShapes.shapeName;";
                $tables = connection()->query($sql);
                while ($row = $tables->fetch_assoc()) {
                    try {
                        $tableStr = str_replace("TABLEID", $row['id'], $row['svgPathData']);
                        $tableStr = str_replace("TRANSFORMDATA", $row['transformData'], $tableStr);
                        if (strpos($row['svgPathData'], "<g") != "") {
                            echo("$tableStr");
                        }
                        else {
                            echo("<$tableStr/>");
                        }
                        
                    }
                        catch (Exception $e) {
                        echo($e);
                    }
                }
                
        ?>

    </svg>

    <script>
        
        // window.addEventListener('DOMContentLoaded', setDimensions);
    </script>
    </form>
    <iframe id="tableStatusListener" src="TableStatusListener.php" style="display: none;"></iframe>
</body>
</html>


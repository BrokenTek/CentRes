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
        <svg>
            <path class="structure" d="M268 191H255V615H268V191Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M1290 191H1277V516H1290V191Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M967 191H954V516H967V191Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M430 239H425V481H430V239Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M246 113V118L488 118V113L246 113Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M99 9H86V93H99V9Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M99 717H86V744H99V717Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M527 191V204L896 204V191L527 191Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M527 503V516H896V503H527Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M1046 178V191H1208V178H1046Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M1122 673.667H1147M1122 692H1147M1146.41 666.667H1138.03V699.333H1146.41V666.667ZM1147 666H1137.88V700H1147V666ZM1129.35 696.333V695.667H1122.59V670.333H1129.35V669.667H1122V696.333H1129.35ZM1137.88 698.667V698H1129.5V668.333H1137.88V667.667H1129.35V698.667H1137.88Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M280 644.373H246M280 669.176H246M246.8 634.902H258.2V679.098H246.8V634.902ZM246 634H258.4V680H246V634ZM270 675.039V674.137H279.2V639.863H270V638.961H280V675.039H270ZM258.4 678.196V677.294H269.8V637.157H258.4V636.255H270V678.196H258.4Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M953 561.964V571.459H897V561.964M953 561.964H908.565M953 561.964V503L940.373 561.964V588M897 561.964V503L908.565 561.964M897 561.964H908.565M910.176 588V561.964M951.902 562.577V571.306H898.098V562.577H951.902ZM903.039 580.342H904.137V587.387H945.863V580.342H946.961V588H903.039V580.342ZM899.196 571.
            459H9 "00.294V580.189H949.157V571.459H950.255V580.342H899.196V571.459Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M953 249.964V259.459H897V249.964M953 249.964H908.565M953 249.964V191L940.373 249.964V276M897 249.964V191L908.565 249.964M897 249.964H908.565M910.176 276V249.964M951.902 250.577V259.306H898.098V250.577H951.902ZM903.039 268.342H904.137V275.387H945.863V268.342H946.961V276H903.039V268.342ZM899.196 
            259.4"59H900.294V268.189H949.157V259.459H950.255V268.342H899.196V259.459Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M280 136.373H246M280 161.176H246M246.8 126.902H258.2V171.098H246.8V126.902ZM246 126H258.4V172H246V126ZM270 167.039V166.137H279.2V131.863H270V130.961H280V167.039H270ZM258.4 170.196V169.294H269.8V129.157H258.4V128.255H270V170.196H258.4Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M1148 680.974L1158.5 667.417M1159.93 665.582L1171 651.291M1171 651.309L1159.99 665.728M1158.57 667.589L1148 681.425M1171 651V651.291L1159.93 665.582C1159.04 663.485 1157.96 661.724 1156.71 660.467C1155.44 659.19 1153.94 658.381 1152.27 658.381C1150.54 658.381 1149.26 659.282 1148.37 660.693C1148.24 
            660.904 1148.12 661.126 1148 661.361V651H1171ZM1148 714V703.105C1148.12 703.336 1148.25 703.555 1148.39 703.762C1149.28 705.119 1150.57 705.948 1152.27 705.948C1153.94 705.948 1155.44 705.14 1156.71 703.863C1157.97 702.589 1159.07 700.797 1159.96 698.663C1161.76 694.39 1162.84 688.556 1162.84 682.165C1162.84 675.804 
            1161.77 669.996 1159.99 665.728L1171 651.309V714H1148ZM1158.57 667.589L1148 681.425C1148 681.735 1148 682.046 1148 682.358V682.634C1148 688.904 1148 694.37 1148.64 698.282C1148.96 700.257 1149.43 701.715 1150.05 702.659C1150.64 703.545 1151.34 703.948 1152.27 703.948C1153.25 703.948 1154.27 703.482 1155.29 
            702.453C1156.31 701.421 1157.28 699.877 1158.12 697.888C1159.79 693.915 1160.84 688.357 1160.84 682.165C1160.84 676.548 1159.97 671.453 1158.57 667.589ZM1158.5 667.417L1148 680.974C1148 675.264 1148.05 670.155 1148.64 666.377C1148.96 664.315 1149.43 662.766 1150.07 661.755C1150.67 660.798 1151.37 660.381 1152.27 
            660.381C1153.25 660.381 1154.27 660.848 1155.29 661.877C1156.31 662.909 1157.28 664.453 1158.12 666.442C1158.25 666.757 1158.38 667.083 1158.5 667.417Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M1284 674V639C1259.99 643.181 1253.73 651.265 1253 674M1284 674H1253M1284 674V709C1259.99 704.819 1253.73 696.735 1253 674" stroke="black" stroke-width="3"/>
            <path class="structure" d="M99 403.964V156.214L90.3002 147.703C82.403 139.978 69.7378 140.119 62.0142 148.018L54 156.214V651.714L62.6649 659.788C70.2213 666.829 81.8964 666.958 89.6068 660.086L99 651.714V594.714M99 403.964C99 403.964 82 388.714 82 403.964V594.714C82 610.214 99 594.714 99 594.714M99 403.964V594.714M86 113H99V156.5L86
             142.5V113ZM86 693H99V650L86 663.839V693ZM66.0149 200.306C66.0149 168.753 66.0149 156.132 97.0149 200.306V222.306C64.3233 235.657 66.0149 222.306 66.0149 222.306V200.306ZM66.0149 276.306C66.0149 244.753 66.0149 232.132 97.0149 276.306V298.306C64.3233 311.657 66.0149 298.306 66.0149 298.306V276.306ZM67.0149 352.306C67.0149 
             320.753 67.0149 308.132 98.0149 352.306V374.306C65.3233 387.657 67.0149 374.306 67.0149 374.306V352.306Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M245.024 748L245.024 64.5M246 65H270L246 74V65ZM246 747H270L246 738V747Z" stroke="black" stroke-width="2"/>
            <path class="structure" d="M1037 34H1289M1037 34L1037.52 65.5029M1037 34H1116.5H1289M1289 34L1296.5 61.5V91.5V117L1289 143M1289 143H1037M1289 143H1106H1037M1037 143L1037.52 111.497" stroke="black" stroke-width="3"/>
            <path class="structure" d="M245.597 9V59H655.434M245 9H652.451C666.967 11.5 696 22.3 696 45.5V57.5" stroke="black" stroke-width="2"/>
            <path class="table unassigned" d="M657 59L691 59" stroke="black" stroke-width="2" stroke-dasharray="1 1"/>
            <path class="table unassigned longtable" d="M644.378 382V331.96M716 387.5V331.5C716 309.685 698.315 292 676.5 292V292C654.685 292 637 309.685 637 331.5V387.5C637 409.315 654.685 427 676.5 427V427C698.315 427 716 409.315 716 387.5Z" stroke="black" stroke-width="1.68103"/>
            <path class="table unassigned longtable" d="M783.378 382V331.96M855 387.5V331.5C855 309.685 837.315 292 815.5 292V292C793.685 292 776 309.685 776 331.5V387.5C776 409.315 793.685 427 815.5 427V427C837.315 427 855 409.315 855 387.5Z" stroke="black" stroke-width="1.68103"/>
            <path class="table unassigned longtable" d="M1037.62 241.203H1066.2M1004 237H1104.86V282H1004V237Z" stroke="black" stroke-width="1.68103"/>
            <path class="table unassigned longtable" d="M1037.62 334.203H1066.2M1004 330H1104.86V375H1004V330Z" stroke="black" stroke-width="1.68103"/>
            <path class="table unassigned longtable" d="M1037.62 424.203H1066.2M1004 420H1104.86V465H1004V420Z" stroke="black" stroke-width="1.68103"/>
            <path class="table unassigned longtable" d="M1193.62 241.203H1222.2M1160 237H1260.86V282H1160V237Z" stroke="black" stroke-width="1.68103"/>
            <path class="table unassigned longtable" d="M1193.62 334.203H1222.2M1160 330H1260.86V375H1160V330Z" stroke="black" stroke-width="1.68103"/>
            <path class="table unassigned longtable" d="M1193.62 424.203H1222.2M1160 420H1260.86V465H1160V420Z" stroke="black" stroke-width="1.68103"/>
            <path class="table unassigned booth" d="M536 516H563.294M536 516V559.671H563.294V516M536 516H542.005M563.294 516H557.29M542.005 516V559.671M542.005 516H557.29M557.29 516V559.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M680 516H707.294M680 516V559.671H707.294V516M680 516H686.005M707.294 516H701.29M686.005 516V559.671M686.005 516H701.29M701.29 516V559.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M824 516H851.294M824 516V559.671H851.294V516M824 516H830.005M851.294 516H845.29M830.005 516V559.671M830.005 516H845.29M845.29 516V559.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M776 516H803.294M776 516V559.671H803.294V516M776 516H782.005M803.294 516H797.29M782.005 516V559.671M782.005 516H797.29M797.29 516V559.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M728 516H755.294M728 516V559.671H755.294V516M728 516H734.005M755.294 516H749.29M734.005 516V559.671M734.005 516H749.29M749.29 516V559.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M632 516H659.294M632 516V559.671H659.294V516M632 516H638.005M659.294 516H653.29M638.005 516V559.671M638.005 516H653.29M653.29 516V559.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M584 516H611.294M584 516V559.671H611.294V516M584 516H590.005M611.294 516H605.29M590.005 516V559.671M590.005 516H605.29M605.29 516V559.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M536 147H563.294M536 147V190.671H563.294V147M536 147H542.005M563.294 147H557.29M542.005 147V190.671M542.005 147H557.29M557.29 147V190.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M680 147H707.294M680 147V190.671H707.294V147M680 147H686.005M707.294 147H701.29M686.005 147V190.671M686.005 147H701.29M701.29 147V190.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M824 147H851.294M824 147V190.671H851.294V147M824 147H830.005M851.294 147H845.29M830.005 147V190.671M830.005 147H845.29M845.29 147V190.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M776 147H803.294M776 147V190.671H803.294V147M776 147H782.005M803.294 147H797.29M782.005 147V190.671M782.005 147H797.29M797.29 147V190.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M728 147H755.294M728 147V190.671H755.294V147M728 147H734.005M755.294 147H749.29M734.005 147V190.671M734.005 147H749.29M749.29 147V190.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M632 147H659.294M632 147V190.671H659.294V147M632 147H638.005M659.294 147H653.29M638.005 147V190.671M638.005 147H653.29M653.29 147V190.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned booth" d="M584 147H611.294M584 147V190.671H611.294V147M584 147H590.005M611.294 147H605.29M590.005 147V190.671M590.005 147H605.29M605.29 147V190.671" stroke="black" stroke-width="1.09177"/>
            <path class="table unassigned twotop" d="M554.5 224H536V209H554.5M554.5 224H573V209H554.5M554.5 224V209" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M608.5 722H590V707H608.5M608.5 722H627V707H608.5M608.5 722V707" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M554.5 722H536V707H554.5M554.5 722H573V707H554.5M554.5 722V707" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M824.5 224H806V209H824.5M824.5 224H843V209H824.5M824.5 224V209" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M770.5 224H752V209H770.5M770.5 224H789V209H770.5M770.5 224V209" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M716.5 224H698V209H716.5M716.5 224H735V209H716.5M716.5 224V209" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M662.5 224H644V209H662.5M662.5 224H681V209H662.5M662.5 224V209" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M956.5 41H938V26H956.5M956.5 41H975V26H956.5M956.5 41V26" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M824.5 722H806V707H824.5M824.5 722H843V707H824.5M824.5 722V707" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M902.5 41H884V26H902.5M902.5 41H921V26H902.5M902.5 41V26" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M770.5 722H752V707H770.5M770.5 722H789V707H770.5M770.5 722V707" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M848.5 41H830V26H848.5M848.5 41H867V26H848.5M848.5 41V26" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M716.5 722H698V707H716.5M716.5 722H735V707H716.5M716.5 722V707" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M794.5 41H776V26H794.5M794.5 41H813V26H794.5M794.5 41V26" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M503.5 80H485V65H503.5M503.5 80H522V65H503.5M503.5 80V65" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M341.5 80H323V65H341.5M341.5 80H360V65H341.5M341.5 80V65" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M279.5 100H261V85H279.5M279.5 100H298V85H279.5M279.5 100V85" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M449.5 80H431V65H449.5M449.5 80H468V65H449.5M449.5 80V65" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M611.5 80H593V65H611.5M611.5 80H630V65H611.5M611.5 80V65" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M557.5 80H539V65H557.5M557.5 80H576V65H557.5M557.5 80V65" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M395.5 80H377V65H395.5M395.5 80H414V65H395.5M395.5 80V65" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M662.5 722H644V707H662.5M662.5 722H681V707H662.5M662.5 722V707" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M608.5 224H590V209H608.5M608.5 224H627V209H608.5M608.5 224V209" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned round" d="M1208.68 76.2167H1218.47M1208.68 42.9365H1218.47M1233.15 59.5766C1233.15 70.3885 1224.39 79.1532 1213.58 79.1532C1202.76 79.1532 1194 70.3885 1194 59.5766C1194 48.7647 1202.76 40 1213.58 40C1224.39 40 1233.15 48.7647 1233.15 59.5766Z" stroke="black" stroke-width="0.97883"/>
            <path class="table unassigned round" d="M1247.68 129.217H1257.47M1247.68 95.9365H1257.47M1272.15 112.577C1272.15 123.388 1263.39 132.153 1252.58 132.153C1241.76 132.153 1233 123.388 1233 112.577C1233 101.765 1241.76 93 1252.58 93C1263.39 93 1272.15 101.765 1272.15 112.577Z" stroke="black" stroke-width="0.97883"/>
            <path class="table unassigned round" d="M1176.68 129.217H1186.47M1176.68 95.9365H1186.47M1201.15 112.577C1201.15 123.388 1192.39 132.153 1181.58 132.153C1170.76 132.153 1162 123.388 1162 112.577C1162 101.765 1170.76 93 1181.58 93C1192.39 93 1201.15 101.765 1201.15 112.577Z" stroke="black" stroke-width="0.97883"/>
            <path class="table unassigned round" d="M1106.68 129.217H1116.47M1106.68 95.9365H1116.47M1131.15 112.577C1131.15 123.388 1122.39 132.153 1111.58 132.153C1100.76 132.153 1092 123.388 1092 112.577C1092 101.765 1100.76 93 1111.58 93C1122.39 93 1131.15 101.765 1131.15 112.577Z" stroke="black" stroke-width="0.97883"/>
            <path class="table unassigned round" d="M1067.68 75.2167H1077.47M1067.68 41.9365H1077.47M1092.15 58.5766C1092.15 69.3885 1083.39 78.1532 1072.58 78.1532C1061.76 78.1532 1053 69.3885 1053 58.5766C1053 47.7647 1061.76 39 1072.58 39C1083.39 39 1092.15 47.7647 1092.15 58.5766Z" stroke="black" stroke-width="0.97883"/>
            <path class="table unassigned round" d="M1137.68 76.2167H1147.47M1137.68 42.9365H1147.47M1162.15 59.5766C1162.15 70.3885 1153.39 79.1532 1142.58 79.1532C1131.76 79.1532 1123 70.3885 1123 59.5766C1123 48.7647 1131.76 40 1142.58 40C1153.39 40 1162.15 48.7647 1162.15 59.5766Z" stroke="black" stroke-width="0.97883"/>
            <path class="table unassigned square" d="M479.583 254.896H491.167M468 252H502.75V269.375V286.75H468V252Z" stroke="black" stroke-width="1.15834"/>
            <path class="table unassigned square" d="M479.583 342.896H491.167M468 340H502.75V357.375V374.75H468V340Z" stroke="black" stroke-width="1.15834"/>
            <path class="table unassigned square" d="M479.333 430.896H490.917M467.75 428H502.5V445.375V462.75H467.75V428Z" stroke="black" stroke-width="1.15834"/>
            <path class="table unassigned square" d="M438.333 692.896H449.917M426.75 690H461.5V707.375V724.75H426.75V690Z" stroke="black" stroke-width="1.15834"/>
            <path class="table unassigned square" d="M473.333 587.896H484.917M461.75 585H496.5V602.375V619.75H461.75V585Z" stroke="black" stroke-width="1.15834"/>
            <path class="table unassigned square" d="M362.583 254.896H374.167M351 252H385.75V269.375V286.75H351V252Z" stroke="black" stroke-width="1.15834"/>
            <path class="table unassigned square" d="M362.583 342.896H374.167M351 340H385.75V357.375V374.75H351V340Z" stroke="black" stroke-width="1.15834"/>
            <path class="table unassigned square" d="M362.583 430.896H374.167M351 428H385.75V445.375V462.75H351V428Z" stroke="black" stroke-width="1.15834"/>
            <path class="table unassigned square" d="M321.583 692.896H333.167M310 690H344.75V707.375V724.75H310V690Z" stroke="black" stroke-width="1.15834"/>
            <path class="table unassigned square" d="M356.583 587.896H368.167M345 585H379.75V602.375V619.75H345V585Z" stroke="black" stroke-width="1.15834"/>
            <path class="table unassigned hightop" d="M548.564 640.582H553.449M537.957 619H565.033C566.537 619 567.477 620.845 566.725 622.32L553.187 648.883C552.435 650.359 550.555 650.359 549.803 648.883L536.265 622.32C535.513 620.845 536.453 619 537.957 619Z" stroke="black" stroke-width="0.774634"/>
            <path class="table unassigned hightop" d="M613.562 628.406H618.446M602.957 649.985H630.029C631.533 649.985 632.473 648.141 631.721 646.666L618.185 620.107C617.433 618.631 615.553 618.631 614.801 620.107L601.265 646.666C600.513 648.141 601.453 649.985 602.957 649.985Z" stroke="black" stroke-width="0.774634"/>
            <path class="table unassigned hightop" d="M679.983 640.582H685.031M669.022 619H697.001C698.555 619 699.527 620.845 698.749 622.32L684.76 648.883C683.983 650.359 682.04 650.359 681.263 648.883L667.274 622.32C666.496 620.845 667.468 619 669.022 619Z" stroke="black" stroke-width="0.774634"/>
            <path class="table unassigned hightop" d="M745.562 627.406H750.446M734.957 648.985H762.029C763.533 648.985 764.473 647.141 763.721 645.666L750.185 619.107C749.433 617.631 747.553 617.631 746.801 619.107L733.265 645.666C732.513 647.141 733.453 648.985 734.957 648.985Z" stroke="black" stroke-width="0.774634"/>
            <path class="table unassigned hightop" d="M805.564 640.582H810.449M794.957 619H822.033C823.537 619 824.477 620.845 823.725 622.32L810.187 648.883C809.435 650.359 807.555 650.359 806.803 648.883L793.265 622.32C792.513 620.845 793.453 619 794.957 619Z" stroke="black" stroke-width="0.774634"/>
            <path class="table unassigned twotop" d="M280 255.5V237H295V255.5M280 255.5V274H295V255.5M280 255.5H295" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M280 328.5V310H295V328.5M280 328.5V347H295V328.5M280 328.5H295" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M280 401.5V383H295V401.5M280 401.5V420H295V401.5M280 401.5H295" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M280 474.5V456H295V474.5M280 474.5V493H295V474.5M280 474.5H295" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="table unassigned twotop" d="M280 547.5V529H295V547.5M280 547.5V566H295V547.5M280 547.5H295" stroke="black" stroke-opacity="0.75" stroke-width="1.11207"/>
            <path class="structure" d="M955 518V531H1290V518H955Z" stroke="black" stroke-width="2"/>
            <path class="structure d="M955 547V560H1290V547H955Z" stroke="black" stroke-width="2"/>
        </svg>

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


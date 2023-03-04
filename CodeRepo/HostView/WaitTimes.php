<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->


<!-- ensures you are logged in before rendering page, and are logged in under the correct role.
If you aren't logged in, it will reroute to the login page.
If you are logged in but don't have the correct role to view this page,
you'll be routed to whatever the home page is for your specified role level -->
<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(255, $GLOBALS['role']); ?>
<!-- CHANGE 255 TO THE ALLOWED ROLE LEVEL FOR THE PAGE -->

<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<?php
    $sql = "SELECT IFNULL(MAX(partySize), 1) AS maxPartySize FROM Tickets;";
    $maxPartySize = connection()->query($sql)->fetch_assoc()['maxPartySize'];
    if (!isset($_POST['lowerPartySize'])) {
        $_POST['lowerPartySize'] = 1;
    }
    if (!isset($_POST['upperPartySize'])) {
        $_POST['upperPartySize'] = $maxPartySize;
    }
    if (!isset($_POST['timeSpan'])) {
        $_POST['timeSpan'] = 5;
    }

    $sql = "SELECT IFNULL(AVG(TIMESTAMPDIFF(MINUTE, timeRequested, timeSeated)),-1) AS avgTime FROM Tickets
            WHERE timeSeated IS NOT NULL
            AND TIMESTAMPDIFF(MINUTE,timeRequested, NOW()) BETWEEN 0 AND " .$_POST['timeSpan'].
          " AND partySize BETWEEN " .$_POST['lowerPartySize']. " AND " .$_POST['upperPartySize']. ";";
    $waitTime = connection()->query($sql)->fetch_assoc()['avgTime'];
    if ($waitTime == -1) {
        $waitTime = "&nbsp;No&nbsp;Data";
    }
    elseif ($waitTime < 1) {
        $waitTime = "&nbsp;None";
    }
    else {
        $waitTime = round($waitTime) . "&nbsp;Minutes";
    }
?>
<html>
    <head>
        <style>
            fieldset {
                display: grid;
                grid-template-columns: min-content 1fr;
            }
        </style>
        <!-- gives you access to setVar, getVar, removeVar, 
        clearVars, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/waitListStructure.css">
        
        <!-- demonstration on how to use getVar, setVar, updateDisplay for just this page -->
        <!-- remove this script tag -->
        <script>
            
            function allElementsLoaded() {
                valTimeToRefresh = document.getElementById("valTimeToRefresh");
                valTimeToRefresh.innerHTML = "1&nbsp;Minute";
                rngMin = document.getElementById("rngLowerPartySize");
                rngMax = document.getElementById("rngUpperPartySize");
                rngTimeSpan = document.getElementById("rngTimeSpan");
                legend = document.getElementsByTagName("legend")[0];
                minValueChanged(true);
                maxValueChanged(true);
                timeRangeChanged(true);
                setInterval(refreshCountdown, 1000);
            }

             //Place your JavaScript Code here
            var rngMin;
            var rngMax;
            var rngTimeSpan;
            var valTimeToRefresh;
            var legend;
            var secsToRefresh = 60;

            function refreshWaitTimeCalculation() {
                document.getElementsByTagName("form")[0].submit();
            }

            function refreshCountdown() {
                secsToRefresh--;
                if (secsToRefresh == 0) {
                    refreshWaitTimeCalculation()
                }
                valTimeToRefresh.innerHTML = secsToRefresh + "&nbsp;Seconds";
            }

            function minValueChanged(initialLoad = false) {
                if (rngMin.value > rngMax.value) {
                    rngMax.value = rngMin.value;
                    rngMax.previousElementSibling.innerHTML = rngMax.value + '&nbsp;Max';
                }
                rngMin.previousElementSibling.innerHTML = rngMin.value + '&nbsp;Min';
                if (initialLoad) { return; }
                legend.innerHTML = "Press&nbsp;to&nbsp;Update&nbsp;Wait&nbsp;Time";

            }

            function maxValueChanged(initialLoad = false) {
                if (rngMax.value < rngMin.value) {
                    rngMin.value = rngMax.value;
                    rngMin.previousElementSibling.innerHTML = rngMin.value + '&nbsp;Min';
                }
                rngMax.previousElementSibling.innerHTML = rngMax.value + '&nbsp;Max';
                if (initialLoad) { return; }
                legend.innerHTML = "Press&nbsp;to&nbsp;Update&nbsp;Wait&nbsp;Time";
            }

            function timeRangeChanged(initialLoad = false) {
                if (rngTimeSpan.value == 60) {
                    rngTimeSpan.previousElementSibling.innerHTML = "1&nbsp;Hour"; 
                }
                else {
                    rngTimeSpan.previousElementSibling.innerHTML = rngTimeSpan.value + "&nbsp;Minutes";
                }
                if (initialLoad) { return; }
                legend.innerHTML = "Press&nbsp;to&nbsp;Update&nbsp;Wait&nbsp;Time";
            }

        </script>
    </head>
    <body onload="allElementsLoaded()"  class="intro">
        <!-- this form submits to itself -->
        <form action="WaitTimes.php" method="POST">
            <!-- PLACE YOUR PHP LAYOUT LOGIC CODE HERE -->
            <fieldset>
           
                <legend onclick="refreshWaitTimeCalculation()">Wait&nbsp;Time:&nbsp;<?php echo($waitTime); ?></legend>
                <label id="lblLowerPartySize" for="rngLowerPartySize"></label>
                <input id="rngLowerPartySize" type="number" name="lowerPartySize" min="1" max="<?php echo($_POST['timeSpan']); ?>" required oninput="minValueChanged()" value="<?php echo($_POST['lowerPartySize']); ?>">
                
                <label id="lblUpperPartySize" for="rngUpperPartySize"></label>
                <input id="rngUpperPartySize" type="number" name="upperPartySize" min="1" max="<?php echo($_POST['timeSpan']); ?>" required oninput="maxValueChanged()" value="<?php echo($_POST['upperPartySize']); ?>">
                
                <label id="lblTimeSpan" for="rngTimeSpan"></label>
                <input id="rngTimeSpan" type="number" name="timeSpan" min="5" max="60" step="5" required oninput="timeRangeChanged()" value="<?php echo($_POST['timeSpan']); ?>">
                
                <div id="lblTimeToRefresh">Refresh&nbsp;In&nbsp;</div><div id="valTimeToRefresh"></div>
            </fieldset>
            <!-- If you want to forget/not carry over variables, use PHP unset function
            to remove these variables -->
            <?php //unset($thisVariableIWantToForget) ?>

            <!-- retain any POST vars. When updateDisplay() is called or the form is submitted,
            these variables will be carried over -->
            <?php //require_once '../Resources/php/display.php'; ?>
           
        </form>
    </body>
</html>
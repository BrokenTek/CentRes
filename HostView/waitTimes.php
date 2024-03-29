
<?php require_once '../Resources/PHP/sessionLogic.php'; restrictAccess(255, $GLOBALS['role']); ?>


<!DOCTYPE html>
<?php require_once '../Resources/PHP/dbConnection.php'; ?>
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
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <style>
            fieldset {
                display: grid;
                grid-template-columns: min-content 1fr;
            }
        </style>
        
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script> 
        <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
        <link rel="stylesheet" href="../Resources/CSS/waitListStructure.css">

        <script>
            let initialLoad = true;
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
                initialLoad = false;
            }

             //Place your JavaScript Code here
            var rngMin;
            var rngMax;
            var rngTimeSpan;
            var valTimeToRefresh;
            var legend;
            var secsToRefresh = 60;

            function refreshCountdown() {
                secsToRefresh--;
                if (secsToRefresh == 0) {
                    updateDisplay();
                    secsToRefresh = 60;
                    valTimeToRefresh.innerHTML = "1&nbsp;Minute";
                    return;
                }
                valTimeToRefresh.innerHTML = secsToRefresh + "&nbsp;Seconds";
            }

            function minValueChanged(e) {
                let maxVal = parseInt(rngMax.getAttribute("max"));
                if (rngMin.value < 1) {
                    rngMin.value = 1;
                }
                else if (rngMin.value > maxVal) {
                    rngMin.value = maxVal;
                   
                }
                if (rngMin.value > rngMax.value) {
                    rngMax.value = rngMin.value;
                    rngMax.previousElementSibling.innerHTML = rngMax.value + '&nbsp;Max';
                }
                rngMin.previousElementSibling.innerHTML = rngMin.value + '&nbsp;Min';
                if (initialLoad) { return; }
                legend.innerHTML = "Press&nbsp;to&nbsp;Update&nbsp;Wait&nbsp;Time";
            }

            function maxValueChanged(e) {
                let maxVal = parseInt(rngMax.getAttribute("max"));
                if (rngMax.value < 1) {
                    rngMax.value = 1;
                }
                else if (rngMax.value > maxVal) {
                    rngMax.value = maxVal;
                   
                }
                if (rngMax.value < rngMin.value) {
                    rngMin.value = rngMax.value;
                    rngMin.previousElementSibling.innerHTML = rngMin.value + '&nbsp;Min';
                }
                rngMax.previousElementSibling.innerHTML = rngMax.value + '&nbsp;Max';
                if (initialLoad) { return; }
                legend.innerHTML = "Press&nbsp;to&nbsp;Update&nbsp;Wait&nbsp;Time";
            }

            function timeRangeChanged(initialLoad = false) {
                if (rngTimeSpan.value < 1) {
                    rngTimeSpan.value = 1;
                }
                else if (rngTimeSpan.value > 60) {
                    rngTimeSpan.value = 60;
                }
                if (rngTimeSpan.value == 60) {
                    rngTimeSpan.previousElementSibling.innerHTML = "1&nbsp;Hour"; 
                }
                else {
                    rngTimeSpan.previousElementSibling.innerHTML = (rngTimeSpan.value + "&nbsp;Minutes").replace("1&nbsp;Minutes", "1&nbsp;Minute");
                }
                if (initialLoad) { return; }
                legend.innerHTML = "Press&nbsp;to&nbsp;Update&nbsp;Wait&nbsp;Time";
            }

        </script>
    </head>
    <body onload="allElementsLoaded()"  class="intro">
        
        <form action="waitTimes.php" method="POST">
            
            <fieldset>
           
                <legend onpointerdown="updateDisplay()">Wait&nbsp;Time:&nbsp;<?php echo($waitTime); ?></legend>
                <label id="lblLowerPartySize" for="rngLowerPartySize"></label>
                <input id="rngLowerPartySize" type="number" name="lowerPartySize" min="1" max="<?php echo($maxPartySize); ?>" required oninput="minValueChanged()" value="<?php echo($_POST['lowerPartySize']); ?>">
                
                <label id="lblUpperPartySize" for="rngUpperPartySize"></label>
                <input id="rngUpperPartySize" type="number" name="upperPartySize" min="1" max="<?php echo($maxPartySize); ?>" required oninput="maxValueChanged()" value="<?php echo($_POST['upperPartySize']); ?>">
                
                <label id="lblTimeSpan" for="rngTimeSpan"></label>
                <input id="rngTimeSpan" type="number" name="timeSpan" min="5" max="60" step="5" required oninput="timeRangeChanged()" value="<?php echo($_POST['timeSpan']); ?>">
                
                <input id="btnSubmit" type="submit" style="display:none">
                
                <div id="lblTimeToRefresh">Refresh&nbsp;In&nbsp;</div><div id="valTimeToRefresh"></div>
            </fieldset>
            
            <?php unset($_POST['lowerPartySize'], $_POST['upperPartySize'], $_POST['timeSpan']); ?>

            
            <?php require_once '../Resources/PHP/display.php'; ?>
           
        </form>
    </body>
</html>
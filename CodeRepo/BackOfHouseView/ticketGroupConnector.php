<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<?php
    $sql = "SELECT id, timeModified FROM ActiveTicketGroups";
    $result = connection()->query($sql);
    $result2 = connection()->query($sql);
    if (isset($_POST['addedGroups']) || isset($_POST['removedGroups']) || isset($_POST['updatedGroups'])) {
        $errorMessage = "Please Process the Highlighted Vars before continuing";
    }
    elseif (!isset($_POST['recordedGroups'])) {
        if (mysqli_num_rows($result) > 0) {
            $_POST['addedGroups'] = "";
            while ($row = $result->fetch_assoc()) {
                $_POST['addedGroups'] .= "," . $row['id'];
            }
            $_POST['addedGroups'] = substr($_POST['addedGroups'], 1);
            $_POST['recordedGroups'] = $_POST['addedGroups'];
        }
    }
    else {
        if (mysqli_num_rows($result) > 0) {
            $groupsIn = "";
            $addedGroups = "";
            $updatedGroups = "";
            while ($row = $result->fetch_assoc()) {
                $groupsIn .= "," . $row['id'];
                if (!strpos("," .$_POST['recordedGroups']. ",", $row['id'])) {
                    $addedGroups .= "," . $row['id'];
                }
                else {
                    if (!isset($_POST['lastUpdated']) || $row['timeModified'] > $_POST['lastUpdated']) {
                        $updatedGroups .= "," . $row['id'];
                    }
                }
            }
            if (strlen($addedGroups) > 0) {
                $_POST['addedGroups'] = substr($addedGroups, 1);
            }
            if (strlen($updatedGroups) > 0) {
                $_POST['updatedGroups'] = substr($updatedGroups, 1);
            }
            $groupsIn = substr($groupsIn, 1);

            $removedGroups = "";
            $recGrpLst = explode(",", $_POST['recordedGroups']);
            foreach ($recGrpLst as $recGrp) {
                if (!strpos("," .$groupsIn. ",", $recGrp)) {
                    $removedGroups .= "," . $recGrp;
                }
            }
            if (strlen($removedGroups) > 0) {
                $_POST['removedGroups'] = substr($removedGroups, 1);
            }

            $_POST['recordedGroups'] = $groupsIn;
        }
        else {
            $_POST['removedGroups'] = $_POST['recordedGroups'];
            unset($_POST['recordedGroups']);
        }
    }

    if (!isset($errorMessage)) {
        $sql = "SELECT NOW()";
        $_POST['lastUpdated'] = connection()->query("SELECT NOW() AS lastUpdated")->fetch_assoc()['lastUpdated'];
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <!-- gives you access to varSet, varGet, varRem, 
        varClr, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
        <!-- demonstration on how to use varGet, varSet, updateDisplay for just this page -->
        <!-- remove this script tag -->
        <script>
            function allElementsLoaded() {
                document.getElementById("recGrp").innerText = varGet("recordedGroups");
                document.getElementById("addGrp").innerText = varGet("addedGroups");
                document.getElementById("remGrp").innerText = varGet("removedGroups");
                document.getElementById("updGrp").innerText = varGet("updatedGroups");
                document.getElementById("updateTime").innerText = varGet("lastUpdated");
            }

            function processVars() {
                varRem("addedGroups");
                varRem("removedGroups");
                varRem("updatedGroups");
                with (document.getElementById("addGrp")) {
                    innerText = "undefined";
                    classList.remove("highlighted");
                }
                with (document.getElementById("remGrp")) {
                    innerText = "undefined";
                    classList.remove("highlighted");
                }
                with (document.getElementById("updGrp")) {
                    innerText = "undefined";
                    classList.remove("highlighted");
                }
                var errMsg = document.getElementById("errorMessage");
                if (errMsg != null) {
                    errMsg.remove();
                }
                //updateDisplay();
            }

        </script>
        <style>
            body {
                font-family: Helvetica;
            }
            table * {
                border: 1px solid black;
            }
            .highlighted {
                background-color: yellow;
            }
        </style>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        <h1><code>ticketGroupConnector.php</code></h1>

        <!-- this form submits to itself -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <?php require_once '../Resources/php/display.php'; ?>
            <input type="submit" value="Refresh">
            <button type="button" onclick="processVars()">Process Highlighted Vars</button>
        </form>
        <table>
            <tr><th>var</th><th>value</th></tr>
            <tr><td>recordedGroups</td><td id="recGrp"></td></tr>
            <tr><td>addedGroups</td><td id="addGrp" <?php if (isset($_POST['addedGroups'])) { echo " class='highlighted'";} ?>></td></tr>
            <tr><td>removedGroups</td><td id="remGrp" <?php if (isset($_POST['removedGroups'])) { echo " class='highlighted'";} ?>></td></tr>
            <tr><td>updatedGroups</td><td id="updGrp" <?php if (isset($_POST['updatedGroups'])) { echo " class='highlighted'";} ?>></td></tr>
            <tr><td>lastUpdated</td><td id="updateTime"></td></tr>
        </table>
        <?php
            if (isset($errorMessage)) {
                echo("<h1 class='highlighted' id='errorMessage'>$errorMessage</h1>");
            }
        ?>
    </body>
</html>
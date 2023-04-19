<?php require_once '../Resources/PHP/dbConnection.php'; ?>
<?php
    if (isset($_POST['route'])) {
        $sql = "SELECT * FROM ActiveTicketGroups WHERE route = '" .$_POST['route']. "' ORDER BY TimeCreated";
    }
    else {
        $sql = "SELECT * FROM ActiveTicketGroups where 0 = 1";
    }
    $result = connection()->query($sql);
    if (!isset($_POST['recordedGroups'])) {
        if (mysqli_num_rows($result) > 0) {
            $_POST['addedGroups'] = "";
            while ($row = $result->fetch_assoc()) {
                $_POST['addedGroups'] .= "," . $row['id'];
                $_POST[str_replace(".","_",'grp'. $row['id'])] = $row['updateCounter'];
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
                if (strpos(',' . $_POST['recordedGroups'] . ',', $row['id']) == 0) {
                    $val =  $row['id']; 
                    $addedGroups .= "," . $row['id'];
                }
                elseif (isset($_POST[str_replace(".","_",'grp'. $row['id'])]) && $_POST[str_replace(".","_",'grp'. $row['id'])] != $row['updateCounter'] )  {
                    $updatedGroups .= "," . $row['id'];    
                }
                $_POST[str_replace(".","_",'grp'. $row['id'])] = $row['updateCounter'];
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
                    unset($_POST[('grp'. $_POST[str_replace(".","_",$recGrp)])]);
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
    
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <script src="../Resources/JavaScript/display.js" type="text/javascript"></script> 
        
        <script>
            function allElementsLoaded() {
                let lookAt = (varGetOnce("addedGroups"));
                if (lookAt !== undefined) {
                    dispatchJSONeventCall("activateTicketGroups", {"ticketGroupIds": lookAt.split(",")});
                }

                lookAt = (varGetOnce("removedGroups"));
                if (lookAt !== undefined) {
                    dispatchJSONeventCall("inactivateTicketGroups", {"ticketGroupIds": lookAt.split(",")});
                }

                lookAt = (varGetOnce("updatedGroups"));
                if (lookAt !== undefined) {
                    dispatchJSONeventCall("updateTicketGroups", {"ticketGroupIds": lookAt.split(",")});
                }
                
                setTimeout(() => {
                    document.querySelector("#frmTGC").submit();
                }, 1000);
            }
        </script>
    </head>
    <body id="sessionForm" onload="allElementsLoaded()">
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" id="frmTGC">
            <?php require_once '../Resources/PHP/display.php'; ?>
        </form>
    </body>
</html>
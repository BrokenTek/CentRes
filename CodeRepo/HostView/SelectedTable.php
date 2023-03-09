<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->


<!-- ensures you are logged in before rendering page, and are logged in under the correct role.
If you aren't logged in, it will reroute to the login page.
If you are logged in but don't have the correct role to view this page,
you'll be routed to whatever the home page is for your specified role level -->
<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(4, $GLOBALS['role']);
      require_once '../Resources/php/connect_disconnect.php';
     //foreach($_POST as $key => $value) { echo "$key: $value<br>"; }

  $authorizationId = @$_POST['authorizationId'];
  $employeeId = @$_POST['employeeId'];
  $ticketId = @$_POST['ticketId'];
  $tableId = @$_POST['tableId'];

  $addEmployeeId = @$_POST['addEmployeeId'];
  $addTicketId = @$_POST['addTicketId'];

  if (isset($_POST['verboseAction'])) {
    $sqlCols = "authorizationId, tableId";
    $sqlVals = "$authorizationId, '$tableId'";
    switch($_POST['verboseAction']) {
      case "addTicket":
        $sqlCols .= ", action, ticketId";
        $sqlVals .= ", 'Add', '$addTicketId'";
        $update = true;
        break;
      case "addServer":
        $sqlCols .= ", action, employeeId";
        $sqlVals .= ", 'Add', '$employeeId'";
        $update = true;
        $removeEmployeeId = true;
        break;
      case "removeTicket":
        $sqlCols .= ", action, ticketId";
        $sqlVals .= ", 'Remove', '$ticketId'";
        $update = true;
        break;
      case "removeServer":
        $sqlCols .= ", action, employeeId";
        $sqlVals .= ", 'Remove', '$employeeId'";
        $update = true;
        $removeEmployeeId = true;
        break;
      case "setBused":
        $sqlCols .= ", action";
        $sqlVals .= ", 'SetBused'";
        break;
      case "disableTable":
        $sqlCols .= ", action";
        $sqlVals .= ", 'Disable'";
        break;
      case "enableTable":
        $sqlCols .= ", action";
        $sqlVals .= ", 'Enable'";
        break;
      case "setZone":
        echo("Set Zone Code Here");
        // any error messages you get, put them in $errorMessages, separated by \n
        $tableIds = explode(",", $tableId);
        try {
    // remove employee from all their currently assigned tables
          $sql = "DELETE FROM tableassignments WHERE employeeId = '$addEmployeeId';";
          connection()->query($sql);
          foreach ($tableIds as $tableId) {
            // add employee to the selected table and log the action
            $sql = "INSERT INTO tableassignments (employeeId, tableId) VALUES ('$addEmployeeId', '$tableId');";
            $result = connection()->query($sql);
            $values = "($authorizationId, '$tableId', 'SetZone', $addEmployeeId)";
            $values = rtrim($values, ",");
            $sql = "INSERT INTO TableLog (authorizationId, tableId, action, employeeId) VALUES $values;";
            $result = connection()->query($sql);
          }
      } catch (Error $e) {
          echo $e->getMessage();
          $errorMessage = "An error occurred while setting the zone.";
        }
        break;
      case "addToZone":
        echo("Add To Zone Code Here");
        $tableIds = explode(",", $tableId);
        foreach ($tableIds as $tableId){
          try {
            $values = "($authorizationId, '$tableId', 'Add', $addEmployeeId)";
            $values = rtrim($values, ",");
            $sql = "INSERT INTO TableLog (authorizationId, tableId, action, employeeId) VALUES $values;";
            $result = connection()->query($sql);

          } catch (Error $e) {
            echo $e->getMessage();
          }
          
        }
       
        break;
      case "removeFromZone":
        echo("Remove From Zone Code Here");
        $tableIds = explode(",", $tableId);
        foreach ($tableIds as $tableId){
          try {
            $sql = "DELETE FROM tableassignments WHERE tableId = '$tableId' AND employeeId = '$addEmployeeId';";
            $result = connection()->query($sql);
      
            $values = "($authorizationId, '$tableId', 'Remove', $addEmployeeId)";
            $values = rtrim($values, ",");
            $sql = "INSERT INTO TableLog (authorizationId, tableId, action, employeeId) VALUES $values;";
            $result = connection()->query($sql);

        } catch (Error $e) {
            echo $e->getMessage();
            }
        }     
        // any error messages you get, put them in $errorMessages, separated by \n
        $errorMessage = "ErrorMsg1 is really long and should wrap in the container\nErrorMsg2";
        break;
    }

    $sql = "INSERT INTO TableLog ($sqlCols) VALUES ($sqlVals);";
    //echo("<h1>$sql</h1>");
    connection()->query($sql);
    unset($_POST['verboseAction'], $_POST['ticketId'], $ticket);
  }
?>
<!DOCTYPE html>

<html>
    <head>
      <link rel="stylesheet" href="../Resources/CSS/tableStyles.css">
      <link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
      <style>
        *, form{
          background-color: black;
          color: white;
        }
        form {
          font-family: helvetica;
          font-weight: bold;
          display: grid;
          grid-template-columns: 1fr min-content min-content 1fr;
          grid-auto-rows: min-content;
        }

        #lblTableId {
          font-weight: bold;
          font-size: 1.25rem;
          grid-column: 2 / span 2;
          margin-inline: auto;
          margin-bottom: .25rem;
        }

        .lblServer, #lblTicket {
          grid-column: 3;
          padding-left: .25rem;
          margin-block: auto;
        }

        .removeButton {
          grid-column: 2;
          max-width: 2rem;
          margin-inline: auto;
          min-height: 1.5rem;
          min-width: 1.5rem;
          background-color: #bf1e2e;
          color: white;
          font-weight: bold;
        }

        .removeButton:hover {
          background-color: #F6941D;
        }

        #btnAddServer, #btnAddTicket, #btnEnable, #btnDisable, #btnBussingComplete {
          grid-column: 2 / span 2;
          margin-top: .25rem;
          min-height: 2rem;
          padding-inline: 1rem;
          font-weight: bold;
          background-color: #bf1e2e;
          color: white;
        }
        #btnAddServer:hover, 
        #btnAddTicket:hover, 
        #btnEnable:hover, 
        #btnDisable:hover, 
        #btnBussingComplete:hover {
          background-color: #F6941D;
        }

        .bannerLabel {
          grid-column: 1 / span 4;
          font-size: 2rem;
          margin-inline: auto;
        }
        .bannerLabel2 {
          grid-column: 1 / span 4;
          font-size: 1.5rem;
          margin-inline: auto;
        }
        .errorMessage {
          grid-column: 1 / span 4;
          border: .125rem solid white;
          margin-bottom: .25rem;
          padding-left: .5rem;
          margin-inline: .25rem;
        }
      </style>
        <!-- gives you access to setVar, getVar, removeVar, 
        clearVars, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
        <script>
            function allElementsLoaded() {
              <?php if (isset($update)) { echo("setVar('update', true);"); } ?>
            }
            
            function executeAction(verboseAction, employeeId = null) {
              setVar("verboseAction",verboseAction);
              if (employeeId != null) {
                setVar("employeeId", employeeId);
              }
              updateDisplay();
            }
        </script>
    </head>
        
    <body onload="allElementsLoaded()" class="intro">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <!-- PLACE YOUR PHP LAYOUT LOGIC CODE HERE -->
          <?php
            if (!isset($tableId) || $tableId == "clear") {
              echo("<div id='lblNoTicket' class='bannerLabel'>No Table Selected</div>");
            }
            elseif (strpos($tableId, ",") > 0) {
              if (isset($addEmployeeId)) {
                $sql = "SELECT usernameFromId($addEmployeeId) as username;";
                $username = connection()->query($sql)->fetch_assoc()['username'];
                echo("<div id='lblSectionAssignments' class='bannerLabel'>Section Assignment</div>");
                echo("<div class='bannerLabel2'>$username</div>");
                echo("<button type='button' class='button' onPointerDown='executeAction(\"addToZone\")'>Add To Zone</button>");
                echo("<button type='button' class='button' onPointerDown='executeAction(\"removeFromZone\")'>Remove From Zone</button>");
                echo("<button type='button' class='button' onPointerDown='executeAction(\"setZone\")'>Set Zone</button>");

                if (isset($errorMessage)) {
                  $errorMessages = explode("\n", $errorMessage);
                  for ($i = 0; $i < sizeof($errorMessages); $i++) {
                    echo("<div class='highlighted errorMessage'>$errorMessages[$i]</div>");
                  }
                }
              }
              else {
                echo("<div id='lblSectionAssignments' class='bannerLabel'>Select Server to Set Their Zone.</div>");
              }
            }
            else {
              //get table status
              $sql = "SELECT status FROM Tables WHERE id='$tableId';";
              $status = connection()->query($sql)->fetch_assoc()['status'];

              echo("<div id='lblTableId' class='$status'>$tableId&nbsp;-&nbsp;$status</div>");
              
              $ignoreEmp = false;
              // Check if the server is already assigned to table. If so, ignore $employeeId var
              if(isset($addEmployeeId)) {
                $sql = "SELECT COUNT(*) AS result FROM TableAssignments WHERE employeeId = '$addEmployeeId' && tableId = '$tableId'";
                if (connection()->query($sql)->fetch_assoc()['result'] == 1) {
                  $ignoreEmp = true;
                  
                }
              }
              
              $ticketSet = false;
              
              // Get the ticket associated with the table, if there is one
              $sql = "SELECT id FROM Tickets WHERE tableId = '$tableId';";
              $result = connection()->query($sql);
              if (mysqli_num_rows($result) == 1) {
                $ticketSet = true;
                $ticketId = $result->fetch_assoc()['id'];
                $_POST['ticketId'] = $ticketId;
                // TODO, ONLY ALLOW THE REMOVE BUTTON WHEN PAYMENT IS PROCESSED OR TICKET IS CLOSED
                echo("<button type='button' class='removeButton' onPointerDown='executeAction(\"removeTicket\")'>X</button>");
                
                echo("<div id='lblTicket'>Ticket:&nbsp;$ticketId</div>");
              }

              // Get any servers that are assigned to the table
              $sql = "SELECT employeeId, usernameFromId(employeeId) as username FROM TableAssignments WHERE tableId = '$tableId';";
              $result = connection()->query($sql);
              if (mysqli_num_rows($result) > 0) {

                // if there's only 1 server assigned to this table, and a seated ticket,
                // don't allow the option to remove the server.
                $canRemove = (mysqli_num_rows($result) > 1 || !$ticketSet ? true : false);
                while ($server = $result->fetch_assoc()) {
                  $serverId = $server['employeeId'];
                  $serverUsername = $server['username'];
                  if ($canRemove) {
                    echo("<button type='button' class='removeButton' onPointerDown='executeAction(\"removeServer\", $serverId)'>X</button>");
                  }
                  echo("<div class='lblServer'>$serverUsername</div>");
                }
              }

              if (isset($addEmployeeId)) {
                $sql = "SELECT usernameFromId($addEmployeeId) as username;";
                $addEmployeeUsername = connection()->query($sql)->fetch_assoc()['username'];
              }

              switch ($status) {
                case "disabled":
                  echo("<button type='button' id='btnEnable' onPointerDown='executeAction(\"enableTable\")'>Enable&nbsp;Table</button>");
                  break;
                case "unassigned":
                  if (isset($addEmployeeId) && !$ignoreEmp) {
                    echo("<button type='button' id='btnAddServer' onPointerDown='executeAction(\"addServer\", $addEmployeeId)'>Assign&nbsp;$addEmployeeUsername</button>");
                  }
                  echo("<button type='button' id='btnDisable' onPointerDown='executeAction(\"disableTable\")'>Disable&nbsp;Table</button>");
                  break;
                case "open":
                  if (isset($addEmployeeId) && !$ignoreEmp) {
                    echo("<button type='button' id='btnAddServer' onPointerDown='executeAction(\"addServer\", $addEmployeeId)'>Assign&nbsp;$addEmployeeUsername</button>");
                  }
                  if (isset($addTicketId)) {
                    echo("<button type='button' id='btnAddTicket' onPointerDown='executeAction(\"addTicket\")'>Assign&nbsp;Ticket</button>");
                  }
                  break;
                case "seated";
                  if (isset($addEmployeeId) && !$ignoreEmp) {
                    echo("<button type='button' id='btnAddServer' onPointerDown='executeAction(\"addServer\", $addEmployeeId)'>Assign&nbsp;$addEmployeeUsername</button>");
                  }
                  break;
                case "bussing";
                  if (isset($addEmployeeId) && !$ignoreEmp) {
                    echo("<button type='button' id='btnAddServer' onPointerDown='executeAction(\"addServer\", $addEmployeeId)'>Assign&nbsp;$addEmployeeUsername</button>");
                  }
                  echo("<button type='button' id='btnBussingComplete' onPointerDown='executeAction(\"setBused\")'>Bussing&nbsp;Complete</button>");
                  break;
              }
            }
            //retain any POST vars. When updateDisplay() is called or the form is submitted,
            //these variables will be carried over -->
            require_once '../Resources/php/display.php'; 
          ?>
           
        </form>
    </body>
</html>
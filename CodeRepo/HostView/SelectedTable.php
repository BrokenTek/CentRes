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
  $removeEmployeeId = false;

  if (isset($_POST['verboseAction'])) {
    $sqlCols = "authorizationId, tableId";
    $sqlVals = "$authorizationId, '$tableId'";
    switch($_POST['verboseAction']) {
      case "addTicket":
        $sqlCols .= ", action, ticketId";
        $sqlVals .= ", 'Add', '$ticketId'";
        $flag = "refreshWaitList";
        break;
      case "addServer":
        $sqlCols .= ", action, employeeId";
        $sqlVals .= ", 'Add', '$employeeId'";
        $removeEmployeeId = true;
        break;
      case "removeTicket":
        $sqlCols .= ", action, ticketId";
        $sqlVals .= ", 'Remove', '$ticketId'";
        break;
      case "removeServer":
        $sqlCols .= ", action, employeeId";
        $sqlVals .= ", 'Remove', '$employeeId'";
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
    }

    $sql = "INSERT INTO TableLog ($sqlCols) VALUES ($sqlVals);";
    //echo("<h1>$sql</h1>");
    connection()->query($sql);
    unset($_POST['verboseAction'], $_POST['ticketId'], $ticket);
    if ($removeEmployeeId) {
      unset($_POST['employeeId'], $employeeId);
    }
  }
?>
<!DOCTYPE html>

<html>
    <head>
      <style>
        body {
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

        #lblNoTicket {
          grid-column: 1 / span 4;
          font-size: 2rem;
        }
      </style>
        <!-- gives you access to setVar, getVar, removeVar, 
        clearVars, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
        <script>
            function allElementsLoaded() {
             <?php if (isset($flag)) { echo("setVar('flag', '$flag'"); } ?>
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
        
    <body onload="allElementsLoaded()">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <!-- PLACE YOUR PHP LAYOUT LOGIC CODE HERE -->
          <?php
            if (isset($tableId)) {
              //get table status
              $sql = "SELECT status FROM Tables WHERE id='$tableId';";
              $status = connection()->query($sql)->fetch_assoc()['status'];

              echo("<div id='lblTableId'>$tableId&nbsp;-&nbsp;$status</div>");
              
              // Check if the server is already assigned to table. If so, ignore $employeeId var
              if(isset($employeeId)) {
                $sql = "SELECT COUNT(*) AS result FROM TableAssignments WHERE employeeId = '$employeeId' && tableId = '$tableId'";
                if (connection()->query($sql)->fetch_assoc()['result'] == 1) {
                  unset($employeeId);
                  
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

              if (isset($employeeId)) {
                $sql = "SELECT usernameFromId($employeeId) as username;";
                $employeeUsername = connection()->query($sql)->fetch_assoc()['username'];
              }

              switch ($status) {
                case "disabled":
                  echo("<button type='button' id='btnEnable' onPointerDown='executeAction(\"enableTable\")'>Enable&nbsp;Table</button>");
                  break;
                case "unassigned":
                  if (isset($employeeId)) {
                    echo("<button type='button' id='btnAddServer' onPointerDown='executeAction(\"addServer\", $employeeId)'>Assign&nbsp;$employeeUsername</button>");
                  }
                  echo("<button type='button' id='btnDisable' onPointerDown='executeAction(\"disableTable\")'>Disable&nbsp;Table</button>");
                  break;
                case "open":
                  if (isset($employeeId)) {
                    echo("<button type='button' id='btnAddServer' onPointerDown='executeAction(\"addServer\", $employeeId)'>Assign&nbsp;$employeeUsername</button>");
                  }
                  if (isset($ticketId)) {
                    echo("<button type='button' id='btnAddTicket' onPointerDown='executeAction(\"addTicket\")'>Assign&nbsp;Ticket</button>");
                  }
                  break;
                case "seated";
                  if (isset($employeeId)) {
                    echo("<button type='button' id='btnAddServer' onPointerDown='executeAction(\"addServer\", $employeeId)'>Assign&nbsp;$employeeUsername</button>");
                  }
                  break;
                case "bussing";
                  if (isset($employeeId)) {
                    echo("<button type='button' id='btnAddServer' onPointerDown='executeAction(\"addServer\", $employeeId)'>Assign&nbsp;$employeeUsername</button>");
                  }
                  echo("<button type='button' id='btnBussingComplete' onPointerDown='executeAction(\"setBused\")'>Bussing&nbsp;Complete</button>");
                  break;
              }
            }
            else {
              echo("<div id='lblNoTicket'>No Table Selected</div>");
            }
            //retain any POST vars. When updateDisplay() is called or the form is submitted,
            //these variables will be carried over -->
            require_once '../Resources/php/display.php'; 
          ?>
           
        </form>
    </body>
</html>
<!-- DISPLAY TEMPLATE
This template includes starter code that allows
you to use display.php and displayInterface.js -->


<!-- ensures you are logged in before rendering page, and are logged in under the correct role.
If you aren't logged in, it will reroute to the login page.
If you are logged in but don't have the correct role to view this page,
you'll be routed to whatever the home page is for your specified role level -->
<?php require_once '../Resources/php/sessionLogic.php'; restrictAccess(4, $GLOBALS['role']); ?>
<!-- CHANGE 255 TO THE ALLOWED ROLE LEVEL FOR THE PAGE -->
<?php foreach($_POST as $key => $value) {
  echo "$key: $value<br>";
}
?>
<!DOCTYPE html>
<?php require_once '../Resources/php/connect_disconnect.php'; ?>
<html>
    <head>
        <!-- gives you access to setVar, getVar, removeVar, 
        clearVars, updateDisplay, rememberScrollPosition, and forgetScrollPosition -->
        <script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script> 
        
        <!-- demonstration on how to use getVar, setVar, updateDisplay for just this page -->
        <!-- remove this script tag -->
        <script>
            function allElementsLoaded() {}

            var authorizationId = "<?php echo $GLOBALS['userId']; ?>";
            <?php if (isset($_POST['authorizationId'])): ?>
            authorizationId = "<?php echo $_POST['authorizationId']; ?>";
            <?php endif; ?>
            //Place your JavaScript Code here
        </script>
        <?php
        require_once '../Resources/php/connect_disconnect.php';
connection();
 
if (isset($_POST['verboseAction'])) {
    $authorizationId = isset($_POST['authorizationId']) ? $_POST['authorizationId'] : '';
    echo $authorizationId;
    $tableId = $_POST['tableId'];
    $columns = "authorizationId, tableId";
    $values = "'$authorizationId', '$tableId'";
    
    $verboseAction = $_POST['verboseAction'];

    
    if ($verboseAction == 'addServer') {

      $sql_query = "SELECT employees.username FROM employees 
        WHERE employees.id IN (
          SELECT employeeId FROM ActiveEmployees WHERE employeeRole & 2 = employeeRole)";
        $result = $conn->query($sql_query);
        
        if ($result->num_rows > 0) {
          $row = $result->fetch_assoc();
          $employeeId = $row["username"];
          } else {
            $employeeId = ""; // or whatever default value you want to set
          }
        $columns .= ", action, employeeId";
        $values .= ",'Add', '$employeeId'";

    } 
    elseif ($verboseAction == 'addTicket') {
        $ticketId = $_POST['ticketId'];
        $sql_query = "SELECT id FROM tickets WHERE tableId='$tableId'";
        $result = $conn->query($sql_query);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $ticketId = $row["id"];
          } else {
            $columns .= ", action, ticketId";
            $values .= ",'Add', '$ticketId'";
          }
    }
 

    $sql = "INSERT INTO TableLog ($columns) VALUES ($values)";
    
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } 
    else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} 
    ?>
    </head>
    <body onload="allElementsLoaded()">
            
        <!-- this form submits to itself -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <!-- PLACE YOUR PHP LAYOUT LOGIC CODE HERE -->
           
                
                <br>
                <button type="submit" name="tableSelected" value="true">Select Table</button>

                <?php

                if (isset($_POST['tableSelected'])) {
                echo '<input type="hidden" name="ticketId" value="your_ticket_id_value_here">';
                $tableId = $_POST['tableId'];
                $sql = "SELECT status FROM Tables WHERE id='$tableId'";
                $result = mysqli_query($conn, $sql);

                if ($result) {
                    $row = mysqli_fetch_array($result);
                    $status = $row['status'];

                    // Check the table status and display the appropriate buttons
                    if ($status == 'Unassigned') {
                    echo '<button type="submit" name="verboseAction" value="addServer" onclick="setVar(\'action\', \'add\'); setVar(\'verboseAction\', \'addServer\'); updateDisplay();">Assign Server</button>
                    <button type="submit" name="verboseAction" value="addTicket" onclick="setVar(\'action\', \'add\'); setVar(\'verboseAction\', \'addTicket\'); updateDisplay();">Assign Ticket</button>';
                    } elseif ($status == 'Open') {
                    echo '<button type="submit" name="verboseAction" value="addServer" onclick="setVar(\'action\', \'add\'); setVar(\'verboseAction\', \'addServer\'); updateDisplay();">Assign Server</button>
                    <button type="submit" name="verboseAction" value="addTicket" onclick="setVar(\'action\', \'add\'); setVar(\'verboseAction\', \'addTicket\'); updateDisplay();">Assign Ticket</button>';
                    } elseif ($status == 'Seated') {
                    echo '<button type="submit" name="verboseAction" value="addServer" onclick="setVar(\'action\', \'add\'); setVar(\'verboseAction\', \'addServer\'); updateDisplay();">Assign Server</button>';
                    } 
                }
                }
                ?>
            <!-- retain any POST vars. When updateDisplay() is called or the form is submitted,
            these variables will be carried over -->
            <?php require_once '../Resources/php/display.php'; ?>
           
        </form>
    </body>
</html>
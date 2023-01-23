<html>
<body>
<?php


// Change these variables to match your XAMPP and MySql config
$servername = "localhost";
$username = "root";
$password = "blInk309";
$dbname = "CentRes";

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

createUser("sshort0", "Short", "Susan", "blInk309", 15);
login("sshort0", "mah", 15);
login("sshort0", "blInk309", 15);
login("sshort0", "blInk309", 15);
logout("sshort0");
mysqli_close($conn);

function createUser(string $userName, string $lastName, string $firstName, string $password, int $allowedRoles) {
	$hash = password_hash($password, PASSWORD_BCRYPT);
	$sql = "INSERT INTO Employees (userName, lastName, firstName, passwordBCrypt, permissionLevel) VALUES (".
		"'".$userName. "', ".
		"'".$lastName. "', ".
		"'".$firstName. "', ".
		"'".$hash. "', ".
		$allowedRoles.");";
	$result = $GLOBALS['conn']->query($sql);
}
function login(string $userName, string $password, int $requestedRole) {
	$sql = "SELECT * FROM Employees WHERE userName = '" .$userName. "';";
	$result = $GLOBALS['conn']->query($sql);
	if (mysqli_num_rows($result) == 0) {
		return "Username Not Found";
	}
	
	$empRecord = mysqli_fetch_array($result);
	if (!password_verify($password, $empRecord['passwordBCrypt'])) {
		return "Invalid Password";
	}
	if ($empRecord['permissionLevel'] & $requestedRole == 0) {
		return "Not Authorized";
	}
	
	// check if you're already logged in
	$sql = "SELECT * FROM EmployeeLog WHERE employeeID = " .$empRecord['employeeID']. " AND endTime IS NULL;";
	$result2 = $GLOBALS['conn']->query($sql);
	if (mysqli_num_rows ($result2)  > 0) {
		// Log out of current session if you are already logged in.
		$sql = "UPDATE EmployeeLog SET endTime = NOW() WHERE employeeID = " .$empRecord['employeeID']. " AND endTime IS NULL;";
		$result2 = $GLOBALS['conn']->query($sql);
	}
	
	$sql = "INSERT INTO EmployeeLog (employeeID, employeeRole) VALUES (" .$empRecord['employeeID']. ", " .$requestedRole. ")";
		$result2 = $GLOBALS['conn']->query($sql);
		return;	
}

function logout($userName) {
	$sql = "SELECT * FROM Employees WHERE userName = '" .$userName. "';";
	$result = $GLOBALS['conn']->query($sql);
	if (mysqli_num_rows ($result) == 0) {
		return "Username Not Found";
	}
	
	$empRecord = mysqli_fetch_array($result);
	$sql = "SELECT * FROM EmployeeLog WHERE employeeID = " .$empRecord['employeeID']. " AND endTime IS NULL;";
	$result2 = $GLOBALS['conn']->query($sql);
	if (mysqli_num_rows ($result2) == 0) {
		return "Not Logged In";
	}
	else {
		// You are logged into 1 or more devices.
		$sql = "UPDATE EmployeeLog SET endTime = NOW() WHERE employeeID = " .$empRecord['employeeID']. " AND endTime IS NULL;";
		$result2 = $GLOBALS['conn']->query($sql);
		return;
	}	
}

function getTicketItemSplitCount(int $ticketItemNumber) (
	//return null if invalid ticket #, otherwise get the record
	$sql = "SELECT * FROM TicketItems WHERE id = " .$ticketItemNumber. ";";
	$result = $GLOBALS['conn']->query($sql);
	if (mysqli_num_rows ($result) == 0) {
		return;
	}
	$ticketItemRecord = mysqli_fetch_array($result);
	
	$sql = "SELECT COUNT(*) FROM TicketItems WHERE ticketID = " .$ticketItemRecord['ticketID']. " AND splitID = " .$ticketItemRecord['splitID']. ";";
	$result = $GLOBALS['conn']->query($sql);
	return mysqli_fetch_array($result)[0];
)
?>
</body>
</html>
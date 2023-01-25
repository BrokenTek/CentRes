<!--  The below 'include' statement is not necessary as if someone is
		is logging in or out, the database connection has already been
		made. Therefore, calling $GLOBALS['conn']->query(*query*) will
		provide access to that connection (creatUser, login, logout). -->

<!-- THIS FILE CONTAINS THE MAIN METHODS BEING USED -->

<html>
<body>
<?php
function createUser(string $userName, string $lastName, string $firstName, string $password, int $allowedRoles) {
	$hash = password_hash($password, PASSWORD_BCRYPT);
	$sql = "INSERT INTO Employees (userName, lastName, firstName, passwordBCrypt, roleLevel) VALUES (".
		"'".$userName. "', ".
		"'".$lastName. "', ".
		"'".$firstName. "', ".
		"'".$hash. "', ".
		$allowedRoles.");";
	$result = $GLOBALS['conn']->query($sql);
}

function login(string $userName, string $password, int $requestedRole) {
	try {
		$sql = "SELECT userPasswordHash('$userName');";
		$passResult = $GLOBALS['conn']->query($sql);
		$passFromUser = $passResult->fetch_row()[0];

		if (!password_verify($password, $passFromUser)) {
			throw new mysqli_sql_exception("Password Is Not Valid");
			}

		$sql2 = "CALL login('$userName', $requestedRole);";

		$GLOBALS['conn']->query($sql2);
			
	}
	catch(Exception $e) {
		echo "EXCEPTION: ", $e->getMessage();
	}
}

function logout($userName) {
	try {
		$sql = "CALL logout('$userName');";
		$GLOBALS['conn']->query($sql);
	}
	catch(Exception $e) {
		echo "EXCEPTION: ", $e->getMessage();
	}	
}

function getTicketItemSplitCount(int $ticketItemNumber) {
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
}
?>
</body>
</html>
<!-- This is where the create_user HTML form is sent to create a user in the database. -->

<html>
<body>

<?php
include '../Resources/php/connect_disconnect.php';
// connection();

function createUser(string $userName, string $lastName, string $firstName, string $password, int $allowedRoles) {
	$hash = password_hash($password, PASSWORD_BCRYPT);
	$sql = "INSERT INTO Employees (userName, lastName, firstName, passwordBCrypt, roleLevel) VALUES (".
		"'".$userName. "', ".
		"'".$lastName. "', ".
		"'".$firstName. "', ".
		"'".$hash. "', ".
		$allowedRoles.");";
	$result = connection()->query($sql);
}

$uname = $_POST['uname'];
$lname = $_POST['lname'];
$fname = $_POST['fname'];
$pword = $_POST['pword'];
$prole = $_POST['prole'];

createUser($uname, $lname, $fname, $pword, $prole);

disconnect();

?>

<h2>User Created</h2>

</body>	
</html>

<!-- This is where the create_user HTML form is sent to create a user in the database. -->


<?php
require_once '../Resources/php/connect_disconnect.php';
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

try {
	createUser($uname, $lname, $fname, $pword, $prole);
	header("Location: ../LoginView/login.php");
}
catch (Exception $e) {
	header("Location: create_user.html");
}

disconnect();

?>
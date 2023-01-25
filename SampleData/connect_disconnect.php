<!-- 
-File: connect_disconnect.php
-This file contains two functions to be called for database connection/disconnection.
-This allows for the connection code to be written once and used anywhere.
-If the connect() function is called in a PHP script, the GLOBAL $conn variable is 
	accessible elsewhere in the execution until the disconnect() function is called.
-->

<html>
<body>
<?php

global $conn;

function connection() {
	if ($GLOBALS['conn'] && !$GLOBALS['conn']->connect_error) {
		return $GLOBALS['conn'];
	}

	$servername = "p:localhost";
	$username = "scott";
	$password = "tiger";
	$dbname = "centres";

	$conn = mysqli_connect($servername, $username, $password, $dbname);
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
		} 	
	return $conn;
}

// Disconnect From The Persistent Database Connection. This Is Necessary
function disconnect() {
	// mysqli_close($GLOBALS['conn']);	
	mysqli_close(connection());
}
?>

</body>
</html>
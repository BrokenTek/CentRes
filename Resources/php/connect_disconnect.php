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
global $connection_level;
$connection_level = 0;

// testing
global $conn2;

function connection() {
	if ($GLOBALS['conn'] && !$GLOBALS['conn']->connect_error) {
		$GLOBALS['connection_level']++;
		echo "<h2>Connection Is Still Connected</h2>";
		return $GLOBALS['conn'];
	}

	echo "<h2>DEBUG: Not connected, connecting now</h2>";

	$servername = "p:localhost";
	$username = "scott";
	$password = "tiger";
	$dbname = "centres0";

	$conn = mysqli_connect($servername, $username, $password, $dbname);
	$conn2 = mysqli_connect($servername, $username, $password, $dbname);			// test multiple login on same user
	if (!$conn) {
		die("Connection failed: " . mysqli_connect_error());
		} 	
	$GLOBALS['connection_level']++;	

	return $conn;
}

// Disconnect From The Persistent Database Connection. This Is Necessary
function disconnect() {
	// mysqli_close($GLOBALS['conn']);
	$GLOBALS['connection_level']--;	
	if ($GLOBALS['connection_level'] == 0) {
		mysqli_close(connection());
	}
}
?>

</body>
</html>
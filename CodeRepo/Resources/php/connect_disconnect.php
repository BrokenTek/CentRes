<!-- 
 File: connect_disconnect.php
 This file contains two functions to be called for database connection/disconnection.
 This allows for the connection code to be written once and used anywhere.
 
 connection() can be used anywhere in you code after the statement that 
 "includes" or "requires" connect_disconnect.php.
 
 Always call disconnect() when you are done accessing the databse.
 disconnect() an be called any amount of times and will not generate an error.
-->

<?php

global $conn;
global $connection_level;

function connection() {
	if (!empty($GLOBALS['conn']) && !$GLOBALS['conn']->connect_error) {
		$GLOBALS['connection_level']++;
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

	$GLOBALS['conn'] = $conn;
	return $conn;
}

// Disconnect From The Persistent Database Connection. This Is Necessary
function disconnect() {
	try {
		if (isset($GLOBALS['conn']) && !is_null($GLOBALS['conn'])) {
			mysqli_close($GLOBALS['conn']);
		}
		
		// coneection gracefully closed
	}
	catch (Exception $e) {
	}
	unset($GLOBALS['conn']);	
}
?>
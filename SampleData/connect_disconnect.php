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
// Connect To Database, Connection Is Persistent - 'p:'localhost
function connect() {
$servername = "p:localhost";
$username = "scott";
$password = "tiger";
$dbname = "centres";
global $conn;

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
	} 	
	return $conn;
}


// Disconnect From The Persistent Database Connection. This Is Necessary
function disconnect() {
	mysqli_close($GLOBALS['conn']);	
}
?>

</body>
</html>
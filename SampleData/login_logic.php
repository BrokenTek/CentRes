<!-- This is where the login form is sent via POST -->

<html>
<body>

<h3> Sample View After Login (First Implementation Should Be Server) </h3>

<?php
include 'phpTest.php';
include 'connect_disconnect.php';

// connection();

$uname = $_POST['uname'];
$pword = $_POST['pword'];
$role = $_POST['role'];

// YOU WILL NEED TO USE THE 'create_user.html' FORM TO CREATE A USER SO YOU
//		CAN PRACTICE AND UNDERSTAND THE LOGIN FUNCTIONALITY. WITHOUT A USER,
// 		YOU CANNOT LOG IN!

// Calls login. If unsuccessful, an error is returned in a graceful and readable format.
login($uname, $pword, $role);	
?>

</body>	
</html>

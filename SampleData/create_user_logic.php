<!-- This is where the create_user HTML form is sent to create a user in the database. -->

<html>
<body>

<?php
include 'connect_disconnect.php';
include 'phpTest.php';

connect();

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

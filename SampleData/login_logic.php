<!-- This is where the login form is sent via POST -->

<html>
<body>

<h3> Sample View After Login (First Implementation Should Be Server) </h3>

<?php
include 'phpTest.php';
include 'connect_disconnect.php';

connect();

$uname = $_POST['uname'];
$pword = $_POST['pword'];
$role = $_POST['role'];

// YOU WILL NEED TO USE THE 'create_user.html' FORM TO CREATE A USER SO YOU
//		CAN PRACTICE AND UNDERSTAND THE LOGIN FUNCTIONALITY. WITHOUT A USER,
// 		YOU CANNOT LOG IN!

// THE BELOW TWO FUNCTION CALLS HAPPEN AT THE SAME TIME.
// THIS CAUSES A USER TO BE LOGGED IN AND THEN LOGGED OUT WTHIN THE SAME SECOND.
// THAT LOGIN WILL BE POPULATED AS A RECORD IN THE EMPLOYEELOG TABLE IN THE DB.
// I AM WORKING ON A WAY TO SEPARATE THE LOGOUT USING AJAX AND JQUERY. I WILL
//		PUSH THAT SOLUTION WHEN IT IS DONE. AS OF NOW, THE FORM JUST DISPLAYS
//		EITHER A SUCCESS MESSAGE OR ERROR MESSAGE AS A RESULT OF THE LOGIN.
// I WILL INCLUDE THE FILES FOR CREATING USERS AS WELL.

// *YOU CAN COMMENT OUT THE LOGOUT IN ORDER TO SEE ACTIVEEMPLOYEES. JUST TRY
//		TO REMEMBER TO RUN IT AGAIN WITH LOGOUT *NOT* COMMENTED OUT, AND LOGIN
//		COMMENTED OUT INSTEAD. HAVING BOTH ON, AND GETTING AN ERROR FOR USERNAME
//		WILL MAKE THE USERNAME EXCEPTION DISPLAY TWICE.
login($uname, $pword, $role);	

logout($uname);

echo "<h5> If there are no Exceptions above, login was successful. Finding a better verification method, don't worry. </h5>";

disconnect();
?>


</body>	
</html>

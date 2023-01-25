<html>
<head>
</head>
<body>
	<h4> Logout Successful. Redirecting </h4>
	<?php

		include 'phpTest.php';
		include 'connect_disconnect.php';

		$uname = $_POST['uname'];

		logout($uname);

		disconnect();
	?>

	<!-- Temporary solution, going to have logout button direct back to main login page implicitly. -->
	<form action="login.php" method="post">
		<input type="submit" value="Go To Login">
	</form>

</body>	
</html>
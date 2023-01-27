<html>
<head>
	<title> CentRes Login </title>
	<meta charset="utf-8">
</head>
<body>
	<center>
	<h2> Enter Login Credentials Below </h2><br>

	<div>
		<form action="login_logic.php" method="post" target="_parent" id="loginId">

			<label for="uname">Enter Your Username</label>
			<input type=text id='uname' name='uname'>
			<br><br>
			<label for="pword">Enter Your Password</label>
			<input type=password id='pword' name='pword'>
			<br><br>


			<?php
			
			include 'connect_disconnect.php';

			connect();

			$sql = "SELECT * FROM employeeroles";
			$result = $conn->query($sql);

			echo "<label> Select Your Role </label>";
			echo "<select name='role' id='role'>";

			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					// echo "<option value='" .$row[id]. "'>". $row[id] . " " . "$row[title] </option>";
					echo "<option value=$row[id]> $row[title] $row[id] </option>";
				}
			}
			echo "</select>";

			disconnect();

			?>
			<br><br>
			<input type="submit" value="Login">
		</form>	

	</div>	


	</center>

</body>
</html>

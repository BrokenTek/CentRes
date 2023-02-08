<?php
	include 'connect_disconnect.php';
	$cookie_name = "804288a34eb7a49b349be68fc6437621cbf25e10d82f4268bb795eca277adedb6a3367add5bfb7cbffb50df150e2e78d26b276f37d32d96cd76746065df58a30cde25c4d9803aa7214dc8f6a985bf8643c341f229b5834964b0f371915d5677e4b579fbab42844cd63ddc3148e4250591277cfc521906bc30cfedd765974c2009ae5fe451ab1890e5ebbfa120ad18934c972618dbe3e";
	
	//build a PHP Session class for use by calling/parent PHP files

	$session_valid = false;
	
	//Cookie exists
	if(isset($_COOKIE[$cookie_name])) {
		
		try{
			
			// check corresponding accessToken in database exists and is valid
			$sql = "SELECT sessionRole('" .$_COOKIE[$cookie_name].  "') AS sessionRole;";
			$role = connection()->query($sql)->fetch_assoc()['sessionRole'];
			
			// session validated and session role determined.... get the session username
			$sql = "SELECT sessionUsername('" .$_COOKIE[$cookie_name].  "') AS sessionUsername;";
			$uname = connection()->query($sql)->fetch_assoc()['sessionUsername'];
			
			//$get First and Last Name
			$sql = "SELECT firstName, lastName FROM Employees where userName = '" .$uname. "';";
			$row = connection()->query($sql)->fetch_assoc();
			$fname = $row['firstName'];
			$lname = $row['lastName'];
					
			//check validated. 
			$session_valid = true;
			
			
			$username = $uname;
			$firstName = $fname;
			$lastName = $lname;
			
			echo('
				<script>
					const USERNAME = "' .$uname. '";
					const FIRST_NAME = "' .$fname. '";
					const LAST_NAME = "' .$lname. '";
					const ROLE = ' .$role. ';
				</script>'
			);

			echo('<link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
			<script>
				function logout() {
					
					

					let div = document.createElement("input");
					div.setAttribute("type","hidden");
					div.setAttribute("name","logout");
					div.setAttribute("value","true");
					document.getElementsByClassName("sessionContainer")[0].append(div);
					document.getElementsByClassName("sessionContainer")[0].submit();

					
					return;
				}
			</script>
				<div class="sessionHeader">
					<img src="../Resources/Images/centresLogo.png" id="lgoSession" width=50 height=50>
					<div id="sessionDetails">' .$username. '</div>
					<button type="button" id="btnLogout" onclick="logout()">Logout</button>
				</div>');

			
		}
		catch (Exception $e) {	
			// cookie exists, but sessionID doesn't exist in the DB or it's expired.
			// remove the cookie
			$_COOKIE[$cookie_name] = NULL;
		}				
			
	}

	if (!$session_valid or isset($_POST['logout'])) {
		// redirect to the login page
		header("Location: ../LoginView/Login.php");

		if (isset($_POST['logout'])) {
			$sql = "CALL logout('" .$username. "');";
			$result = connection()->query($sql);
		}

	}

	unset($uname, $fname, $lname, $sql, $row);

	disconnect();
?>
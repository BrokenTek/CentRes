<?php
	require_once '../Resources/php/connect_disconnect.php';
	
	$sql = NULL;
	$allowedRoles = NULL;
	$errorMessage = NULL;
	
	$cookie_name = "804288a34eb7a49b349be68fc6437621cbf25e10d82f4268bb795eca277adedb6a3367add5bfb7cbffb50df150e2e78d26b276f37d32d96cd76746065df58a30cde25c4d9803aa7214dc8f6a985bf8643c341f229b5834964b0f371915d5677e4b579fbab42844cd63ddc3148e4250591277cfc521906bc30cfedd765974c2009ae5fe451ab1890e5ebbfa120ad18934c972618dbe3e";;
	
	if (isset($_POST['logoutUsername'])) {
		$sql = "CALL logout('" .$_POST['logoutUsername']. "');";
		connection()->query($sql);
		$_COOKIE[$cookie_name] = NULL;		
	}

	//attempt to get allowed roles if unverifed username has been submitted
	if (isset($_POST['uname'])) {
		//if username exists, get the allowed roles value
		$sql = "SELECT roleLevel FROM Employees WHERE userName = '" .$_POST['uname']. "';";
		$result = connection()->query($sql);
		if (mysqli_num_rows ($result) == 1) {
			$allowedRoles = $result->fetch_assoc()['roleLevel'];
		}	
	}
	
	try { 
		if (!empty($_POST['uname']) and isset($_POST['pword']) and !isset($_POST['role'])) {
			// unvalidated username and password entered
		
			// confirm valid username & get password hash, otherwise invalid username.
			
			$sql = "SELECT userPasswordHash('" .$_POST['uname']. "') AS userPasswordHash;";
			$passResult = connection()->query($sql)->fetch_assoc()['userPasswordHash'];

			// confirm entered password matches stored password, otherwise invalid password entered.
			if (!password_verify($_POST['pword'], $passResult)) {
				$errorMessage = "Password Is Not Valid";
			}
		}
		elseif (isset($_POST['role'])) {
			// username, password, and role have been validated

			// check if somebody is already logged in on this machine, if so, log them out.
			// Only 1 person is allowed to be logged in at a time on a device.
			if (isset($_COOKIE[$cookie_name])) {
				$sql = "SELECT * FROM Employees WHERE accessToken = '" .$_COOKIE[$cookie_name].
				       "' AND accessTokenExpiration > NOW()";
				$existingLocalSession = connection()->query($sql);
				if (mysqli_num_rows($existingLocalSession) == 1) {
					echo("<h1>Error</h1>");
					$username = $existingLocalSession->fetch_assoc()['userName'];
					$sql = "CALL logout('$username');";
					connection()->query($sql);
				}
				
			}
		
			// generate session token
			$sessionToken = password_hash($_POST['uname'] . $_POST['pword']. time(), PASSWORD_BCRYPT);
					
			// login to database and set session token, otherwise sessionToken is already in use or incorrect role selected.
			$sql = "CALL login('" .$_POST['uname']. "', " .$_POST['role']. ", '" .$sessionToken. "');";
			connection()->query($sql);	
			
			// LOGIN SUCCESSFUL..... redirect to the appropriate page.
			
			header("Location: " .$_POST['route']);
			
			$sql = "SELECT sessionTimeoutInMins FROM Config;";
			$timeoutLength = connection()->query($sql)->fetch_assoc()['sessionTimeoutInMins'];
			
			
			
			setcookie($cookie_name, $sessionToken, time() + ($timeoutLength * 60), "/"); // value is in seconds... 86,400 per day, 60 per minute, 3600 per hour
		}
	}
	catch (Exception $e) {
		$errorMessage = $e->getMessage();
	}
?>

<html>
<head>
	<title> CentRes Employee Portal </title>
	<meta charset="utf-8">
	<!-- <link rel="stylesheet" href="style.css"> -->
	<link rel="stylesheet" href="../Resources/CSS/baseStyle.css">
	<script src="../Resources/JavaScript/displayInterface.js" type="text/javascript"></script>
	<script>
		function redirectToCreateAccount() {
			window.location.href = "../CreateUserView/create_user.html";
		}

		function allElementsLoaded() {
			<?php
				if(isset($_POST['message'])) {
					echo("setTimeout(alert('" .$_POST['message']. "'),2000);");
				} 
			?>
		}
	</script>
</head>
<body id="loginBody" onload="allElementsLoaded()">

	<div id="loginContainer">
	<div id="loginHeader">
		<img src="../Resources/Images/centresLogo.png" id="lgoSession" width=50 height=50>
		<div id="loginTitle">CentRes Employee Portal</div>
			<button type="button" id="btnCreateAccount" onclick="redirectToCreateAccount()">I'm New</button>
		</div>
	<div>
		<form action="login.php" method="POST" id="loginForm">
			<?php $readonlyStr = isset($errorMessage) ? "" : " readonly"; ?>
			<label for="uname" id="lblLoginUsername">Enter Your Username</label>
			<input type=text id='txtLoginUsername' name='uname' <?php if (isset($_POST['uname'])) { echo('value="' .$_POST['uname']. '"' .$readonlyStr. '>');} ?>
			<br><br>
			<label for="pword" id="lblLoginPassword">Enter Your Password</label>
			<input type=password id='pwdLoginPassword' name='pword' <?php if (isset($_POST['pword'])) { echo('value="' .$_POST['pword']. '"' .$readonlyStr. '>');} ?>
			<br><br>

<?php		
			
			if (isset($_POST['uname'])) {		
				
				
		
				if (!isset($errorMessage)) {
					$sql = "SELECT roleLevel from Employees WHERE id = idFromUsername('" .$_POST['uname']. "');";
					$allowedRoles = connection()->query($sql)->fetch_assoc()['roleLevel'];

					$sql = "SELECT * FROM LoginRouteTable;";
					$definedRoles = connection()->query($sql);
			
					echo "<label> Select Your Role </label>";
					echo "<select name='role' id='cboLoginRole' onchange='autoLogin()'>";
					echo "<option>Select Your Role</option>";
					$allowedRoleCount = 0;
					$allowedRoute = "";
					while($row = $definedRoles->fetch_assoc()) {
						if((intval($row['id']) & intval($allowedRoles)) == intval($row['id'])) {
							echo ('<option route="' .$row['route']. '" value=' .$row['id']. '>' .$row['title']. '</option>');
							$allowedRoleCount += 1;
							$allowedRoute = $row['route'];
						}
					}
					echo "</select>";
				
					echo('<br><br>');
					if ($allowedRoleCount == 1) {
						echo("
							<script>
								function autoRoute() {
									let cboLoginRole = document.querySelector('#cboLoginRole');
									if (cboLoginRole.options.length == 2) {
										cboLoginRole.selectedIndex = 1;
										setVar('role', cboLoginRole.options[1].getAttribute('value'));
										setVar('route',cboLoginRole.options[1].getAttribute('route'));
										document.getElementById('loginForm').submit();
									}
									else {
										setTimeout(autoRoute(), 250);
									}
								}

								autoRoute();
							</script>
						");
					}
				}
				else {
					echo('<input type="submit" value="Select Role" id="btnSelectRole">');
				}
			}
			else {
				echo('<input type="submit" value="Select Role" id="btnSelectRole">');
			}
			disconnect();
			echo("<button id='btnClearLogin' onclick='clearLogin()' >Clear</button>
			<script>
				function autoLogin() {
					let cboLoginRole = document.querySelector('#cboLoginRole');
					setVar('route', cboLoginRole.options[cboLoginRole.selectedIndex].getAttribute('route'));
					document.getElementById('loginForm').submit();
				}
				function clearLogin() {
					document.getElementById('txtLoginUsername').remove();
					document.getElementById('pwdLoginPassword').remove();
					document.getElementById('cboLoginRole').remove();
					
					
					autoLogin();
					
				}
				
				</script>
			");
			if (isset($errorMessage)) {
				echo('<h1>' .$errorMessage. '</h1>');
			}
?>	

		</form>	
		<form action="login.php" method="POST" id="frmClearLogin">
		</form>

	</div>	


	</div>

</body>
</html>
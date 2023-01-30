<?php
	include '../Resources/php/connect_disconnect.php';
	// connection();
	$sql = NULL;
	$allowedRoles = NULL;
	$errorMessage = NULL;
	
	$cookie_name = "804288a34eb7a49b349be68fc6437621cbf25e10d82f4268bb795eca277adedb6a3367add5bfb7cbffb50df150e2e78d26b276f37d32d96cd76746065df58a30cde25c4d9803aa7214dc8f6a985bf8643c341f229b5834964b0f371915d5677e4b579fbab42844cd63ddc3148e4250591277cfc521906bc30cfedd765974c2009ae5fe451ab1890e5ebbfa120ad18934c972618dbe3e";;
	
	//attempt to get allowed roles if unverifed username has been submitted
	if (isset($_POST['uname'])) {
		//if username exists, get the allowed roles value
		$sql = "SELECT roleLevel FROM Employees WHERE userName = '" .$_POST['uname']. "';";
		$result = connection()->query($sql);
		if (mysqli_num_rows ($result) == 1) {
			$allowedRoles = connection()->query($sql)->fetch_assoc()['roleLevel'];
		}	
	}
	
	try { 
		if (!empty($_POST['uname']) and isset($_POST['pword']) and !isset($_POST['role'])) {
			// unvalidated username and password entered
		
			// confirm valid username & get password hash, otherwise invalid username.
			// connection();
			$sql = "SELECT userPasswordHash('" .$_POST['uname']. "') AS userPasswordHash;";
			$passResult = connection()->query($sql)->fetch_assoc()['userPasswordHash'];

			// confirm entered password matches stored password, otherwise invalid password entered.
			if (!password_verify($_POST['pword'], $passResult)) {
				$errorMessage = "Password Is Not Valid";
			}
		}
		elseif (isset($_POST['role'])) {
			// username, password, and role have been validated
		
			// generate session token
			$sessionToken = password_hash($_POST['uname'] . $_POST['pword'], PASSWORD_BCRYPT);
					
			// login to database and set session token, otherwise sessionToken is already in use or incorrect role selected.
			$sql = "CALL login('" .$_POST['uname']. "', " .$_POST['role']. ", '" .$sessionToken. "');";
			connection()->query($sql);	
			
			// LOGIN SUCCESSFUL..... redirect to the appropriate page.
			header("Location: ../ServerView/ServerView.php");
			
			
			
			
			
			setcookie($cookie_name, $sessionToken, time() + (86400 * 30), "/"); // 86400 is 1 day
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
</head>
<body>
	<div id="login-container">
	<h2> CentRES Employee Portal </h2><br>

	<div>
		<form action="login.php" method="POST" id="frmLogin">

			<label for="uname" id="lblLoginUsername">Enter Your Username</label>
			<input type=text id='txtLoginUsername' name='uname' <?php if (isset($_POST['uname'])) { echo('value="' . $_POST['uname'] . '"');} echo( '>'); ?>
			<br><br>
			<label for="pword" id="lblLoginPassword">Enter Your Password</label>
			<input type=text id='pwdLoginPassword' name='pword' <?php if (isset($_POST['pword'])) { echo('value="' . $_POST['pword'] . '"');} echo ('>'); ?>
			<br><br>

<?php		
			// user has entered a valid username and password.... Existing session not verified yet.
			if (isset($_POST['uname']) and !isset($errorMessage)) {				
				$sql = "SELECT * FROM employeeroles";
				$definedRoles = connection()->query($sql);
			
				echo "<label> Select Your Role </label>";
				echo "<select name='role' id='cboLoginRole' onchange='autoLogin()'>";
				while($row = $definedRoles->fetch_assoc()) {
					if( $row['id'] & $allowedRoles = $row['id']) {
						echo ('<option value=' .$row['id']. '>' .$row['title']. ' - ' .$row['id']. '</option>');
					}
				}
				echo "</select>";
				
				echo('<br><br>');
			}
			else {
				echo('<input type="submit" value="Select Role" id="btnSelectRole">');
			}
			disconnect();
			echo("<button id='btnClearLogin' onclick='clearLogin()' >Clear</button>
			<script>
				function autoLogin() {
					document.getElementById('frmLogin').submit();
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

	</div>	


	</div>

</body>
</html>
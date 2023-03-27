<?php
	require_once 'connect_disconnect.php';
	$cookie_name = "804288a34eb7a49b349be68fc6437621cbf25e10d82f4268bb795eca277adedb6a3367add5bfb7cbffb50df150e2e78d26b276f37d32d96cd76746065df58a30cde25c4d9803aa7214dc8f6a985bf8643c341f229b5834964b0f371915d5677e4b579fbab42844cd63ddc3148e4250591277cfc521906bc30cfedd765974c2009ae5fe451ab1890e5ebbfa120ad18934c972618dbe3e";
	
	//build a PHP Session class for use by calling/parent PHP files

	$session_valid = false;
	
	//Cookie exists
	if(isset($_COOKIE[$cookie_name])) {
		
		try{
			
			// check corresponding accessToken in database exists and is valid
			$sql = "SELECT sessionRole('" .$_COOKIE[$cookie_name].  "') AS sessionRole;";
			$GLOBALS['role'] = connection()->query($sql)->fetch_assoc()['sessionRole'];
			
			// session validated and session role determined.... get the session username
			$sql = "SELECT sessionUsername('" .$_COOKIE[$cookie_name].  "') AS sessionUsername;";
			$uname = connection()->query($sql)->fetch_assoc()['sessionUsername'];
			
			//$get First and Last Name
			$db = connection();
			$sql = $db->prepare("SELECT id, firstName, lastName FROM Employees where userName = ?;");
			$sql->bind_param("s", $uname);
			$sql->execute();
			$result = $sql->get_result();
			$row = $result->fetch_assoc();
					
			$fname = $row['firstName'];
			$lname = $row['lastName'];
			$uid = $row['id'];

			$session_valid = true;
			$GLOBALS['userId'] = $uid;
			$GLOBALS['username'] = $uname;
			$GLOBALS['firstName'] = $fname;
			$GLOBALS['lastName'] = $lname;
			$GLOBALS['loggedIn'] = true;
			
		}
		catch (Exception $e) {	
			// cookie exists, but sessionID doesn't exist in the DB or it's expired.
			// remove the cookie
			$_COOKIE[$cookie_name] = NULL;
			header("Location: ../LoginView/LoginView.php");
			$GLOBALS['loggedIn'] = false;
		}					
	}
	else {
		$_COOKIE[$cookie_name] = NULL;
		header("Location: ../LoginView/LoginView.php");
		$GLOBALS['loggedIn'] = false;
	}	

	unset($uname, $uid, $fname, $lname, $sql, $row);

	disconnect();

	function restrictAccess($allowedRole, $actualRole) {
		if ((intval($actualRole) & intval($allowedRole)) == intval(0)) {
			$sql = "SELECT * FROM LoginRouteTable WHERE id = $actualRole;";
			$route = connection()->query($sql)->fetch_assoc()['route'];
			
			header("Location: $route");
		}
	}
?>
<?php
	include 'connect_disconnect.php';
	connect();
	$cookie_name = "804288a34eb7a49b349be68fc6437621cbf25e10d82f4268bb795eca277adedb6a3367add5bfb7cbffb50df150e2e78d26b276f37d32d96cd76746065df58a30cde25c4d9803aa7214dc8f6a985bf8643c341f229b5834964b0f371915d5677e4b579fbab42844cd63ddc3148e4250591277cfc521906bc30cfedd765974c2009ae5fe451ab1890e5ebbfa120ad18934c972618dbe3e";
	
	//Cookie exists
	if(isset($_COOKIE[$cookie_name])) {
		$session_valid = false;
		try{
			
			// check corresponding accessToken in database exists and is valid
			$sql = "SELECT sessionRole('" .$_COOKIE[$cookie_name].  "') AS sessionRole;";
			$role = $GLOBALS['conn']->query($sql)->fetch_assoc()['sessionRole'];
			
			// session validated and session role determined.... get the session username
			$sql = "SELECT sessionUsername('" .$_COOKIE[$cookie_name].  "') AS sessionUsername;";
			$uname = $GLOBALS['conn']->query($sql)->fetch_assoc()['sessionUsername'];
			
			//$get First and Last Name
			$sql = "SELECT firstName, lastName FROM Employees where userName = '" .$uname. "';";
			$row = $GLOBALS['conn']->query($sql)->fetch_assoc();
			$fname = $row['firstName'];
			$lname = $row['lastName'];
					
			//check validated. 
			$session_valid = true;
			
			//build a PHP Session class for use by calling/parent PHP files
			class Session {
				protected string $u;
				protected string $f;
				protected string $l;
				protected int $r;
				
				public function __construct(string $username, string $last_name, string $first_name, int $role) {
					$this->u = $username;
					$this->l = $last_name;
					$this->f = $first_name;
					$this->r = $role;
				}
				
				function username() {
					return $this->u;
				}
				function last_name() {
					return $this->l;
				}
				function first_name() {
					return $this->f;
				}
				function role() {
					return $this->r;
				}
			}
			
			$session = new Session($uname, $lname, $fname, $role);
			
			echo('
				<script>
					const USERNAME = "' .$uname. '";
					const FIRST_NAME = "' .$fname. '";
					const LAST_NAME = "' .$lname. '";
					const ROLE = ' .$role. ';
				</script>
			');
		}
		catch (Exception $e) {	
			// cookie exists, but sessionID doesn't exist in the DB or it's expired.
			
			// remove the cookie
			$_COOKIE[$cookie_name] = NULL;
		}				
			
	}
	
	if (!$session_valid) {
		// redirect to the login page
		header("Location: ../LoginView/Login.php");
	}				
	disconnect();
?>
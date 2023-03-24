<?php
require_once '../CodeRepo/Resources/php/connect_disconnect.php';

echo("<h1>Reset Data</h1><ol>");
// Reset Everything
$sql="DELETE FROM TableLog; ";
connection()->query($sql);
$sql="DELETE FROM TableAssignments; ";
connection()->query($sql);
$sql="DELETE FROM TableLog; ";
connection()->query($sql);
$sql="DELETE FROM TableShapes; ";
connection()->query($sql);
$sql="DELETE FROM Tickets; ";
connection()->query($sql);
$sql="DELETE FROM TicketItems; ";
connection()->query($sql);
$sql="DELETE FROM EmployeeLog; ";
connection()->query($sql);
$sql="DELETE FROM Employees;";
connection()->query($sql);

echo("<li>Reset Tables</li>");
// Create Tables
	// Create a table shape
	echo("<li>Reset Table Shapes</li>");
	$sql="INSERT INTO `tableshapes` (`shapeName`, `svgPathData`) VALUES ('square', 'SVG PATH DATA HERE');";
	echo($sql);
	connection()->query($sql);
	
	
	// Create tables with that shape… In our later sprints SVG path data will have to be included
	// so we know how to draw the table on the Host View.
	$sql="INSERT INTO tables (id, shape) VALUES 
	('T01', 'square'), 
	('T02', 'square'), 
	('T03', 'square'), 
	('T04', 'square'), 
	('T05', 'square'), 
	('T06', 'square'), 
	('T07', 'square'), 
	('T08', 'square');";
	connection()->query($sql);

	echo("<li>Create Employees");
/* INSERT USERS (EMPLOYEES) FOR TESTING INTO THE DATABASE. THIS REMOVES THE NEED TO CREATE A NEW EMPLOYEE ON EVERY UPDATE TO THE STRUCTURE 
    - This is for testing purposes only. It includes one employee record for each of our current permission levels. For server testing, use shemp.
    - They all have the SAME PASSWORD. That password is: shmoe */
	echo("<h2>Create employees with same password of  'shmoe'</h2><ul>");
	echo("<li>larry_mgr is the manager (permission level 14)</li>");
	echo("<li>moe and shemp are servers (permission level 2)</li>");
	echo("<li>curly is a host (permission level 4)</li></ul></li>");
$sql="
INSERT INTO employees (userName, roleLevel, passwordBCrypt, lastName, firstName)
VALUES 
('moe',2,'$2y$10\$iuY4GXUh76y3BfFlbw/OV.3YVkySZt//BLTbsgKIWjDBqyntWkLeu','howard','moe'),
('shemp',2,'$2y$10\$gYxSiQOCWGPz8dmpdtDbWeVHW32CQtXJn3zW4iUYoo47q.09sU67C','howard','shemp'),
('curly',6,'$2y$10\$oE2O7N0HDOjfov10/O.IjOYhzhVDkns43Ve2MUENXvSQ38/OoqMRC','howard','curly'),
('larry_mgr',14,'$2y$10$4asMBSoeQrzuhatxDyt2/OE6zU5EeUPpiwL6hH1Z3g58EYyp0ULkq','fine','larry');
";
echo($sql);
connection()->query($sql);

// ============================== STATEMNTS BELOW SHOULD BE INCLUDED FOR THROW-AWAY INTERACTIVE MANAGER PAGE =============================================

// Manager/Host is required to be logged in to perform add/remove from the TableLog
// you should just actually log in as Larry on the LoginView… That will allow you to pass this step.
echo("<h1>Host Curly: Login and assign Servers to tables</h1><ol>");
$sql= "CALL LOGIN('curly', 4, 'ANYTHING GOES HERE');";
echo($sql);
connection()->query($sql);

// Pretending to be the host/hostess, assign employees to tables

	// assign Shemp to table T01
	echo("<li>Assiged Shemp to T1</li>");
	$sql="INSERT INTO tablelog (tableId, action, timeStamp, authorizationId, employeeId) VALUES ('T01', 'Add', current_timestamp(), idFromUsername('curly'), idFromUsername('shemp'));";
	echo($sql);
	connection()->query($sql);
	
	echo("<li>Assiged Shemp to T2</li>");
	// assign Shemp to table T02
	$sql="INSERT INTO tablelog (tableId, action, timeStamp, authorizationId, employeeId) VALUES ('T02', 'Add', current_timestamp(), idFromUsername('curly'), idFromUsername('shemp'));";
	echo($sql);
	connection()->query($sql);
	
	echo("<li>Assiged Moe to help Shemp at T2</li>");
	// assign Moe to help Shemp with table T02
	$sql="INSERT INTO tablelog (tableId, action, timeStamp, authorizationId, employeeId) VALUES ('T02', 'Add', current_timestamp(), idFromUsername('curly'), idFromUsername('moe'));";
	echo($sql);
	connection()->query($sql);
	
	echo("<li>Assiged Moe to T3</li>");
	// assign Moe to table T03
	$sql="INSERT INTO tablelog (tableId, action, timeStamp, authorizationId, employeeId) VALUES ('T03', 'Add', current_timestamp(), idFromUsername('curly'), idFromUsername('moe'));";
	echo($sql);
	connection()->query($sql);

	// table to illustrate bussing occupied
	echo("<li>Assiged Moe to T4</li>");
	// assign Moe to table T04
	$sql="INSERT INTO tablelog (tableId, action, timeStamp, authorizationId, employeeId) VALUES ('T04', 'Add', current_timestamp(), idFromUsername('curly'), idFromUsername('moe'));";
	echo($sql);
	connection()->query($sql);

	// table to illustrate open
	echo("<li>Assiged Moe to T5</li>");
	// assign Moe to table T05
	$sql="INSERT INTO tablelog (tableId, action, timeStamp, authorizationId, employeeId) VALUES ('T05', 'Add', current_timestamp(), idFromUsername('curly'), idFromUsername('moe'));";
	echo($sql);
	connection()->query($sql);
	
	//T6 illustrates bussing unoccupied

	//T7 illustrates unassigned

	//T8 illustrates disabled

// Pretending to be the host/hostess, create new tickets when a group enters the restaurant and assign them to a table
echo("<h1>Assigning new tickets to tables</h1><ol>");
	// Create a few tickets for us to test with… the arguments are…
	// 1.	ticket nickname (last name of group or lastname/last 4 on credit card (if bar tab)… NICKNAMES MUST BE UNIQUE
	// 2.	the party size
	// 3.	the SQL OUT variable to get the new ticket number.

//create ticket, get the new ticket number, assign the ticket to a table

echo("<li>Assiged short to T1</li>");
$sql = "CALL createTicket('short', 12, @newTicketNumber);";
echo("<BR>" .$sql);
connection()->query($sql);
$sql= "SELECT @newTicketNumber AS newTicketNum;";
echo("<BR>" .$sql);
$newTick = connection()->query($sql)->fetch_assoc()['newTicketNum'];
$sql = "INSERT INTO `tablelog` (tableId, action, timeStamp, authorizationId, ticketId) VALUES ('T01', 'Add', current_timestamp(), idFromUsername('curly'), " .$newTick. ");";
echo("<BR>" .$sql);
connection()->query($sql);

echo("<BR><li>Assiged peterson to T1</li>");
$sql = "CALL createTicket('peterson', 12, @newTicketNumber);";
echo("<BR>" .$sql);
connection()->query($sql);
$sql= "SELECT @newTicketNumber AS newTicketNum;";
echo("<BR>" .$sql);
$newTick = connection()->query($sql)->fetch_assoc()['newTicketNum'];
$sql = "INSERT INTO `tablelog` (tableId, action, timeStamp, authorizationId, ticketId) VALUES ('T02', 'Add', current_timestamp(), idFromUsername('curly'), " .$newTick. ");";
echo("<BR>" .$sql);
connection()->query($sql);

echo("<BR><li>Assiged pribbs to T1</li>");
$sql = "CALL createTicket('pribbs', 4, @newTicketNumber);";
echo("<BR>" .$sql);
connection()->query($sql);
$sql= "SELECT @newTicketNumber AS newTicketNum;";
echo("<BR>" .$sql);
$newTick = connection()->query($sql)->fetch_assoc()['newTicketNum'];
$sql = "INSERT INTO `tablelog` (tableId, action, timeStamp, authorizationId, ticketId) VALUES ('T03', 'Add', current_timestamp(), idFromUsername('curly'), " .$newTick. ");";
echo("<BR>" .$sql);
connection()->query($sql);

// set bussing with/withoug assigned servers
$sql = "UPDATE Tables SET status = 'bussing' WHERE id in ('T04','T06');";
connection()->query($sql);

// set T08 disabled
$sql = "UPDATE Tables SET status = 'disabled' WHERE id = 'T08';";
connection()->query($sql);



echo("<h1>Curly has logged out from Host role</h1><ol>");
$sql="CALL logout('curly');";
echo("<BR>" .$sql);
connection()->query($sql);

?>
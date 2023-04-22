DROP TABLE IF EXISTS TicketItems;
DROP TABLE IF EXISTS Splits;
DROP TABLE IF EXISTS Tickets;
DROP TABLE IF EXISTS ActiveTicketGroups;
DROP TABLE IF EXISTS TableLog;
DROP TABLE IF EXISTS TableAssignments;
DROP TABLE IF EXISTS Tables;
DROP TABLE IF EXISTS TableStatuses;
DROP TABLE IF EXISTS TableShapes;
DROP TABLE IF EXISTS StructureShapes;
DROP TABLE IF EXISTS MenuAssociations;
DROP TABLE IF EXISTS MenuModificationItems;
DROP TABLE IF EXISTS MenuModificationCategories;
DROP TABLE IF EXISTS MenuItems;
DROP TABLE IF EXISTS MenuCategories;
DROP TABLE IF EXISTS QuickCodes;
DROP TABLE IF EXISTS ActiveEmployees;
DROP TABLE IF EXISTS EmployeeLog;
DROP TABLE IF EXISTS Employees;
DROP TABLE IF EXISTS EmployeeRoles;
DROP TABLE IF EXISTS LoginRouteTable;
DROP TABLE IF EXISTS Config;

CREATE TABLE Config (
	sessionTimeoutInMins INT UNSIGNED NOT NULL DEFAULT 5,
	businessDay DATE NOT NULL DEFAULT NOW()
);

CREATE TABLE LoginRouteTable (
		id SMALLINT UNSIGNED PRIMARY KEY,
		title VARCHAR(25),
		route VARCHAR(200)
);

CREATE TABLE EmployeeRoles (
	id SMALLINT UNSIGNED PRIMARY KEY,
	title VARCHAR(25)
);

	
CREATE TABLE Employees(
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	userName VARCHAR(50) NOT NULL UNIQUE,
	roleLevel INT UNSIGNED NOT NULL,
	passwordBCrypt CHAR(60) NOT NULL,
	lastName VARCHAR(50) NOT NULL,
	firstName VARCHAR(50) NOT NULL,
	accessToken VARCHAR(60),
	accessTokenExpiration DATETIME
);

CREATE TABLE EmployeeLog (
	employeeId INT UNSIGNED NOT NULL,
	employeeRole SMALLINT UNSIGNED NOT NULL,
	startTime DATETIME NOT NULL DEFAULT NOW(),
	endTime DATETIME,
	FOREIGN KEY (employeeId) REFERENCES Employees(id)
	ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE ActiveEmployees (
	employeeId INT UNSIGNED NOT NULL,
	employeeRole SMALLINT UNSIGNED
);

CREATE TABLE QuickCodes (
	id VARCHAR(5) PRIMARY KEY
);


CREATE TABLE MenuCategories (
	description VARCHAR(1),

	quickCode VARCHAR(5) PRIMARY KEY,
	counter INTEGER UNSIGNED UNIQUE AUTO_INCREMENT,
	title VARCHAR(75) NOT NULL UNIQUE,
	route char(1),
	visible BOOLEAN NOT NULL DEFAULT TRUE,
	defaultPrice DECIMAL(6, 2) UNSIGNED,
	FOREIGN KEY (quickCode) REFERENCES QuickCodes(id)
	ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE MenuItems (
	description VARCHAR(1),

	quickCode VARCHAR(5) PRIMARY KEY,
	counter INTEGER UNSIGNED UNIQUE AUTO_INCREMENT,
	title VARCHAR(75) NOT NULL UNIQUE,
	price DECIMAL(6, 2) UNSIGNED,
	route char(1),
	quantity SMALLINT UNSIGNED,
	requests SMALLINT UNSIGNED NOT NULL DEFAULT 0,
	prepTimeInSecs SMALLINT UNSIGNED,
	visible BOOLEAN NOT NULL DEFAULT TRUE,
	FOREIGN KEY (quickCode) REFERENCES QuickCodes(id)
	ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE MenuModificationCategories (
	description VARCHAR(1),

	quickCode VARCHAR(5) PRIMARY KEY,
	counter INTEGER UNSIGNED UNIQUE AUTO_INCREMENT,
	title VARCHAR(75) NOT NULL UNIQUE,
	selfDescriptive BOOLEAN NOT NULL DEFAULT FALSE,
	categoryType ENUM('MandatoryOne','MandatoryAny','OptionalOne','OptionalAny'),
	visible BOOLEAN NOT NULL DEFAULT TRUE,
	FOREIGN KEY (quickCode) REFERENCES QuickCodes(id)
	ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE MenuModificationItems (
	categoryType ENUM('MandatoryOne','MandatoryAny','OptionalOne','OptionalAny'),
	selfDescriptive BOOLEAN NOT NULL DEFAULT FALSE,
	description VARCHAR(1),
	price DECIMAL(6, 2),
	
	quickCode VARCHAR(5) PRIMARY KEY,
	displayIndex SMALLINT UNSIGNED,
	counter INTEGER UNSIGNED UNIQUE AUTO_INCREMENT,
	quantifierString VARCHAR(1000) NOT NULL DEFAULT '',
	title VARCHAR(75) NOT NULL UNIQUE,
	visible BOOLEAN NOT NULL DEFAULT TRUE,
	FOREIGN KEY (quickCode) REFERENCES QuickCodes(id)
	ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE MenuAssociations (
	parentQuickCode VARCHAR(5),
	childQuickCode VARCHAR(5),
	displayIndex SMALLINT UNSIGNED,
	UNIQUE(parentQuickCode, childQuickCode)
);

CREATE TABLE StructureShapes (
	shapeName VARCHAR(50) PRIMARY KEY,
	svgPathData VARCHAR(5000)
);

CREATE TABLE TableShapes (
	shapeName VARCHAR(50) PRIMARY KEY,
	svgPathData VARCHAR(5000),
	capacity SMALLINT UNSIGNED NOT NULL DEFAULT 0
);

CREATE TABLE TableStatuses (
	id VARCHAR(30) PRIMARY KEY
);


CREATE TABLE Tables (
	id VARCHAR(3) PRIMARY KEY,
	shape VARCHAR(50),
	gridLocationX SMALLINT UNSIGNED,
	gridLocationY SMALLINT UNSIGNED,
	gridSpanX SMALLINT UNSIGNED,
	gridSpanY SMALLINT UNSIGNED,
	transformData VARCHAR(5000),
	status VARCHAR(30) NOT NULL DEFAULT 'unassigned',
	FOREIGN KEY (shape) REFERENCES TableShapes(shapeName)
	ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (status) REFERENCES TableStatuses(id)
	ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE TableAssignments (
	employeeId INT UNSIGNED NOT NULL,
	tableId VARCHAR(3) NOT NULL,
	FOREIGN KEY (employeeId) REFERENCES Employees(id)
	ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE TableLog (
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	tableId VARCHAR(3) NOT NULL,
	action ENUM('Add', 'Remove', 'SetBused', 'Disable', 'Enable') NOT NULL,
	timeStamp TIMESTAMP DEFAULT NOW(),
	authorizationId INT UNSIGNED,
	employeeId INT UNSIGNED,
	ticketId INT UNSIGNED,
	FOREIGN KEY(tableId) REFERENCES Tables(id)
	ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY(authorizationId) REFERENCES Employees(id)
	ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (employeeId) REFERENCES Employees(id)
	ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Tickets (
	id INT UNSIGNED AUTO_INCREMENT NOT NULL,
	businessDay DATE NOT NULL,
	PRIMARY KEY (id, businessDay),
	nickname VARCHAR(25) NOT NULL,
	partySize INTEGER UNSIGNED NOT NULL,
	tableId VARCHAR(3),
	timeRequested DATETIME NOT NULL DEFAULT NOW(),
	timeReserved DATETIME NOT NULL DEFAULT NOW(),
	timeSeated DATETIME,
	timeClosed DATETIME,
	timeModified DATETIME NOT NULL DEFAULT NOW()
);

CREATE TABLE Splits (
	ticketId INT UNSIGNED NOT NULL,
	splitId SMALLINT UNSIGNED NOT NULL,
	businessDay DATE NOT NULL,
	PRIMARY KEY (ticketId, splitId, businessDay),
	id INT UNSIGNED GENERATED ALWAYS AS (ticketId * 10000 + splitId * 1000),
	overrideValue DECIMAL(6, 2) UNSIGNED,
	overrideNote VARCHAR(500),
	overrideAuthorization INT UNSIGNED,
	overrideTimeStamp DATETIME,
	tip DECIMAL(6, 2) UNSIGNED,
	totalAmountPaid DECIMAL(6, 2) UNSIGNED,
	calculatedSubtotal DECIMAL(6, 2) UNSIGNED,
	calculatedTax DECIMAL(6, 2) UNSIGNED,
	timeModified DATETIME NOT NULL DEFAULT NOW(),
	FOREIGN KEY (overrideAuthorization) References Employees(id)
	ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE TicketItems (
	id INT UNSIGNED NOT NULL,
	businessDay DATE NOT NULL,
	PRIMARY KEY (id, businessDay),
	ticketId INT UNSIGNED GENERATED ALWAYS AS (id DIV 10000),
	itemId INT UNSIGNED GENERATED ALWAYS AS (MOD(id, 1000)),
	splitFlag SMALLINT UNSIGNED,
	menuItemQuickCode VARCHAR(10),
	modificationNotes VARCHAR(1500),
	seat SMALLINT UNSIGNED,
	flag ENUM('Updated','Removed', 'Hidden'),
	calculatedPrice DECIMAL(6, 2),
	calculatedPriceWithMods DECIMAL(6, 2),
	overridePrice DECIMAL(6, 2),
	overrideNote VARCHAR(500),
	overrideAuthorization INT UNSIGNED,
	overrideTimeStamp DATETIME,
	prepPriority SMALLINT UNSIGNED NOT NULL DEFAULT 1,
	groupIndex SMALLINT UNSIGNED,
	groupId DECIMAL(6,2) GENERATED ALWAYS AS (id DIV 10000 + COALESCE(groupIndex, 0) / 100),
	submitTime DATETIME,
	readyTime DATETIME,
	deliveredTime DATETIME,
	FOREIGN KEY (overrideAuthorization) REFERENCES Employees(id)
	ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (menuItemQuickCode) REFERENCES QuickCodes(id) 
	ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE ActiveTicketGroups (
	id DECIMAL(6, 2) PRIMARY KEY,
	timeCreated DATETIME NOT NULL DEFAULT NOW(),
	updateCounter SMALLINT NOT NULL DEFAULT 0,
	route char(1)
);

INSERT INTO LoginRouteTable VALUES
	(1, 'Terminal Access', '../TerminalView/TerminalView.php'),
	(2, 'Server', '../ServerView/ServerView.php'),
	(6, 'Host', '../HostView/HostView.php'),
	(9, 'Back of House Manager', '../ManagerView/InventoryPopularityWindow.php'),
	(14, 'Front of House Manager', '../HostView/HostView.php'),
	(15, 'General Manager', '../HostView/HostView.php'),
	(65535, 'Admin', '../ManagerView/EmployeeRoster.php');

INSERT INTO EmployeeRoles VALUES
	(1, 'Terminal Access'),
	(2, 'Server'),
	(6, 'Host'),
	(9, 'Back of House Manager'),
	(14, 'Front of House Manager'),
	(15, 'General Manager'),
	(65535, 'Admin');

INSERT INTO TableStatuses VALUES
	('disabled'),
	('unassigned'),
	('open'),
	('seated'),
	('bussing');

-- Data to prime the database to function correctly
INSERT INTO Config (sessionTimeoutInMins) VALUES (3600);
INSERT INTO QuickCodes VALUES('root');
INSERT INTO QuickCodes VALUES('dtchd');
	




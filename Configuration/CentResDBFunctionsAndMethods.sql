-- todo switch to using menuItemPrice
-- todo switch to using ticketItemPrice
DROP TRIGGER IF EXISTS beforeDeleteEmployee;
DROP TRIGGER IF EXISTS beforeInsertTableLog;
DROP TRIGGER IF EXISTS afterInsertTableLog;
DROP TRIGGER IF EXISTS afterDeleteTicketItem;

DROP PROCEDURE IF EXISTS createUser;
DROP PROCEDURE IF EXISTS login;
DROP PROCEDURE IF EXISTS transientLogin;
DROP PROCEDURE IF EXISTS logout;
DROP FUNCTION IF EXISTS loggedIn;
DROP FUNCTION IF EXISTS sessionUsername;
DROP FUNCTION IF EXISTS sessionRole;
DROP FUNCTION IF EXISTS userPasswordHash;
DROP FUNCTION IF EXISTS usernameFromId;
DROP FUNCTION IF EXISTS idFromUsername;

DROP FUNCTION IF EXISTS ticketItemPrice;
DROP FUNCTION IF EXISTS ticketItemPriceWithMods;
DROP FUNCTION IF EXISTS modItemPrice;
DROP FUNCTION IF EXISTS splitCount;
DROP FUNCTION IF EXISTS lowestSplitFlag;
DROP FUNCTION IF EXISTS ticketSubtotal;
DROP FUNCTION IF EXISTS menuItemModifications;
DROP FUNCTION IF EXISTS ticketItemStatus;
DROP FUNCTION IF EXISTS ticketSplitFlag;
DROP FUNCTION IF EXISTS ticketItemPayStatus;
DROP FUNCTION IF EXISTS splitString;

DROP TRIGGER IF EXISTS beforeAddMenuCategory;
DROP TRIGGER IF EXISTS beforeAddMenuItem;
DROP TRIGGER IF EXISTS beforeAddMenuModificationCategory;
DROP TRIGGER IF EXISTS beforeAddMenuModificationItem;

DROP TRIGGER IF EXISTS beforeUpdateMenuCategory;
DROP TRIGGER IF EXISTS beforeUpdateMenuItem;
DROP TRIGGER IF EXISTS beforeUpdateMenuModificationCategory;
DROP TRIGGER IF EXISTS beforeUpdateMenuModificationItem;

DROP TRIGGER IF EXISTS afterDeleteMenuCategory;
DROP TRIGGER IF EXISTS afterDeleteMenuItem;
DROP TRIGGER IF EXISTS afterDeleteMenuModificationCategory;
DROP TRIGGER IF EXISTS afterDeleteMenuModificationItem;

DROP PROCEDURE IF EXISTS createTicket;
DROP PROCEDURE IF EXISTS createReservation;
DROP PROCEDURE IF EXISTS removeTicket;
DROP PROCEDURE IF EXISTS addSplit;
DROP PROCEDURE IF EXISTS removeSplit;
DROP PROCEDURE IF EXISTS createTicketItem;
DROP PROCEDURE IF EXISTS closeTicket;
DROP PROCEDURE IF EXISTS removeTicketItem;
DROP PROCEDURE IF EXISTS modifyTicketItem;
DROP PROCEDURE IF EXISTS overrideTicketItemPrice;
DROP PROCEDURE IF EXISTS moveTicketItemToSplit;
DROP PROCEDURE IF EXISTS moveTicketItemToSeat;
DROP PROCEDURE IF EXISTS removeTicketItemFromSplit;
DROP PROCEDURE IF EXISTS addTicketItemToSplit;
DROP PROCEDURE IF EXISTS markTicketItemAsReady;
DROP PROCEDURE IF EXISTS rescindTicketItemReadyState;
DROP PROCEDURE IF EXISTS toggleTicketItemReadyState;
DROP PROCEDURE IF EXISTS markTicketItemAsDelivered;
DROP PROCEDURE IF EXISTS reprepareTicketItem;
DROP PROCEDURE IF EXISTS hideTicketItem;
DROP PROCEDURE IF EXISTS updateTicketSplitTimeStamp;
DROP PROCEDURE IF EXISTS updateTicketSplitsTimeStamp;
DROP PROCEDURE IF EXISTS submitPendingTicketItems;
DROP PROCEDURE IF EXISTS cancelPendingTicketItems;
DROP PROCEDURE IF EXISTS updateTicketGroup;
DROP PROCEDURE IF EXISTS closeTicketGroup;

DROP FUNCTION IF EXISTS menuObjectTitleUnique;
DROP FUNCTION IF EXISTS titleToQuickCode;
DROP PROCEDURE IF EXISTS associateByTitle;

DROP PROCEDURE IF EXISTS closeBusinessDay;

CREATE PROCEDURE closeBusinessDay(IN serverPath VARCHAR(1000))
BEGIN
	DECLARE bDay DATE;
	SELECT businessDay INTO bDay FROM Config;

	SET @sql_text = CONCAT('SELECT id, businessDay, QUOTE(nickname), partySize, timeRequested, timeReserved, timeSeated, timeClosed
	INTO OUTFILE "', serverPath , bDay, ' - Tickets.dat" FROM Tickets;');
	PREPARE s1 FROM @sql_text;
	EXECUTE s1;
	DROP PREPARE s1;
	
	SET @sql_text = CONCAT('SELECT ticketId, splitId, businessDay, overrideValue, overrideNote, overrideTimeStamp, tip, totalAmountPaid, calculatedSubtotal, calculatedTax
	INTO OUTFILE "', serverPath, bDay, ' - Splits.dat" FROM Splits;');
	PREPARE s1 FROM @sql_text;
	EXECUTE s1;
	DROP PREPARE s1;

	SET @sql_text = CONCAT('SELECT id, businessDay, ticketId, itemId, splitFlag, menuItemQuickCode, modificationNotes, flag, calculatedPrice, calculatedPriceWithMods, overridePrice, overrideNote, submitTime, readyTime, deliveredTime
	INTO OUTFILE "', serverPath, bDay, ' - TicketItems.dat" FROM TicketItems;');
	PREPARE s1 FROM @sql_text;
	EXECUTE s1;
	DROP PREPARE s1;

	TRUNCATE TABLE Tickets;
	TRUNCATE TABLE Splits;
	TRUNCATE TABLE TicketItems;
	TRUNCATE TABLE TableAssignments;
	TRUNCATE TABLE TableLog;

	UPDATE Config set businessDay = NOW();
END;

CREATE FUNCTION menuObjectTitleUnique(objTitle VARCHAR(75)) RETURNS BOOLEAN
BEGIN
	IF ((SELECT COUNT(*) FROM MenuCategories WHERE title = objTitle LIMIT 1) = 1) THEN
		RETURN 0;
	ELSEIF ((SELECT COUNT(*) FROM MenuItems WHERE title = objTitle LIMIT 1) = 1) THEN
		RETURN 0;
	ELSEIF ((SELECT COUNT(*) FROM MenuModificationCategories WHERE title = objTitle LIMIT 1) = 1) THEN
		RETURN 0;
	ELSEIF ((SELECT COUNT(*) FROM MenuModificationItems WHERE title = objTitle LIMIT 1) = 1) THEN
		RETURN 0;
	END IF;
	RETURN 1;
END;

CREATE FUNCTION titleToQuickCode(objTitle VARCHAR(75)) RETURNS VARCHAR(5)
BEGIN
	DECLARE foundQuickCode VARCHAR(5) DEFAULT NULL;
	IF (foundQuickCode IS NULL) THEN
		SELECT quickCode INTO foundQuickCode FROM MenuItems WHERE title = objTitle;
	END IF;
	IF (foundQuickCode IS NULL) THEN
		SELECT quickCode INTO foundQuickCode FROM MenuCategories WHERE title = objTitle;
	END IF;
	IF (foundQuickCode IS NULL) THEN
		SELECT quickCode INTO foundQuickCode FROM MenuModificationCategories WHERE title = objTitle;
	END IF;
	IF (foundQuickCode IS NULL) THEN
		SELECT quickCode INTO foundQuickCode FROM MenuModificationItems WHERE title = objTitle;
	END IF;
	RETURN foundQuickCode;
END;

CREATE PROCEDURE associateByTitle(IN parentTitle VARCHAR(75), IN childTitle VARCHAR(75))
BEGIN
	INSERT INTO MenuAssociations (parentQuickCode, childQuickCode) VALUES (titleToQuickCode(parentTitle), titleToQuickCode(childQuickCode));
END;

CREATE TRIGGER beforeDeleteEmployee
BEFORE DELETE ON Employees FOR EACH ROW
BEGIN
  IF ((SELECT COUNT(*) FROM TableAssignments WHERE employeeId = OLD.id LIMIT 1) = 1) THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Employees cannot be deleted if they are currently assigned to any tables.';
  END IF;
END;

CREATE TRIGGER beforeAddMenuCategory
BEFORE INSERT ON MenuCategories FOR EACH ROW
BEGIN
	DECLARE cnt INTEGER UNSIGNED;
	DECLARE isUnique BOOLEAN;
	SELECT menuObjectTitleUnique(NEW.title) INTO isUnique;
	IF (NOT isUnique) THEN
		SIGNAL SQLSTATE '45000'
    	SET MESSAGE_TEXT = 'Menu Object Titles must be unique.';
	ELSE 
		IF (NEW.quickCode IS NULL OR NEW.quickCode = '') THEN
			-- SELECT GREATEST(COALESCE(MAX(CAST(SUBSTRING(quickCode,2) AS UNSIGNED)), 0), counter) + 1 INTO cnt FROM MenuCategories;
			SELECT IFNULL(MAX(counter), 0) + 1 INTO cnt FROM MenuCategories;
			SET NEW.quickCode = CONCAT('C', LPAD(CONVERT(cnt, VARCHAR(4)),4,'0'));
		END IF;
		INSERT INTO QuickCodes (id) VALUES (NEW.quickCode);
	END IF;
END;

CREATE TRIGGER beforeAddMenuItem
BEFORE INSERT ON MenuItems FOR EACH ROW
BEGIN
	DECLARE cnt INTEGER UNSIGNED;
	DECLARE isUnique BOOLEAN;
	SELECT menuObjectTitleUnique(NEW.title) INTO isUnique;
	IF (NOT isUnique) THEN
		SIGNAL SQLSTATE '45000'
    	SET MESSAGE_TEXT = 'Menu Object Titles must be unique.';
	ELSE 
		IF (NEW.quickCode IS NULL OR NEW.quickCode = '') THEN
			-- SELECT GREATEST(COALESCE(MAX(CAST(SUBSTRING(quickCode,2) AS UNSIGNED)), 0), counter) + 1 INTO cnt FROM MenuItems;
			SELECT IFNULL(MAX(counter), 0) + 1 INTO cnt FROM MenuItems;
			SET NEW.quickCode = CONCAT('I', LPAD(CONVERT(cnt, VARCHAR(4)),4,'0'));
		END IF;
		INSERT INTO QuickCodes (id) VALUES (NEW.quickCode);
	END IF;
END;

CREATE TRIGGER beforeAddMenuModificationCategory
BEFORE INSERT ON MenuModificationCategories FOR EACH ROW
BEGIN
	DECLARE cnt INTEGER UNSIGNED;
	DECLARE isUnique BOOLEAN;
	SELECT menuObjectTitleUnique(NEW.title) INTO isUnique;
	IF (NOT isUnique) THEN
		SIGNAL SQLSTATE '45000'
    	SET MESSAGE_TEXT = 'Menu Object Titles must be unique.';
	ELSE 
		IF (NEW.quickCode IS NULL OR NEW.quickCode = '') THEN
			-- SELECT GREATEST(COALESCE(MAX(CAST(SUBSTRING(quickCode,2) AS UNSIGNED)), 0), counter) + 1 INTO cnt FROM MenuModificationCategories;
			SELECT IFNULL(MAX(counter), 0) + 1 INTO cnt FROM MenuModificationCategories;
			SET NEW.quickCode = CONCAT('X', LPAD(CONVERT(cnt, VARCHAR(4)),4,'0'));
		END IF;
		INSERT INTO QuickCodes (id) VALUES (NEW.quickCode);
	END IF;
END;

CREATE TRIGGER beforeAddMenuModificationItem
BEFORE INSERT ON MenuModificationItems FOR EACH ROW
BEGIN
	DECLARE cnt INTEGER UNSIGNED;
	DECLARE isUnique BOOLEAN;
	SELECT menuObjectTitleUnique(NEW.title) INTO isUnique;
	IF (NOT isUnique) THEN
		SIGNAL SQLSTATE '45000'
    	SET MESSAGE_TEXT = 'Menu Object Titles must be unique.';
	ELSE 
		IF (NEW.quickCode IS NULL OR NEW.quickCode = '') THEN
			-- SELECT GREATEST(COALESCE(MAX(CAST(SUBSTRING(quickCode,2) AS UNSIGNED)), 0), counter) + 1 INTO cnt FROM MenuModificationItems;
			SELECT IFNULL(MAX(counter), 0) + 1 INTO cnt FROM MenuModificationItems;
			SET NEW.quickCode = CONCAT('Y', LPAD(CONVERT(cnt, VARCHAR(4)),4,'0'));
		END IF;
		INSERT INTO QuickCodes (id) VALUES (NEW.quickCode);
	END IF;
END;

CREATE TRIGGER afterDeleteMenuCategory
AFTER DELETE ON MenuCategories FOR EACH ROW
BEGIN
	DELETE FROM QuickCodes WHERE id = OLD.quickCode;
	DELETE FROM MenuAssociations WHERE parentQuickCode = OLD.quickCode OR childQuickCode = OLD.quickCode;
END;

CREATE TRIGGER afterDeleteMenuItem
AFTER DELETE ON MenuItems FOR EACH ROW
BEGIN
	DELETE FROM QuickCodes WHERE id = OLD.quickCode;
	DELETE FROM MenuAssociations WHERE parentQuickCode = OLD.quickCode OR childQuickCode = OLD.quickCode;
END;

CREATE TRIGGER afterDeleteMenuModificationCategory
AFTER DELETE ON MenuModificationCategories FOR EACH ROW
BEGIN
	DELETE FROM QuickCodes WHERE id = OLD.quickCode;
	DELETE FROM MenuAssociations WHERE parentQuickCode = OLD.quickCode OR childQuickCode = OLD.quickCode;
END;

CREATE TRIGGER afterDeleteMenuModificationItem
AFTER DELETE ON MenuModificationItems FOR EACH ROW
BEGIN
	DELETE FROM QuickCodes WHERE id = OLD.quickCode;
	DELETE FROM MenuAssociations WHERE parentQuickCode = OLD.quickCode OR childQuickCode = OLD.quickCode;
END;

CREATE TRIGGER beforeUpdateMenuCategory
BEFORE UPDATE ON MenuCategories FOR EACH ROW
BEGIN
	IF (OLD.quickCode <> NEW.quickCode OR OLD.title <> NEW.title) THEN
		IF ((SELECT COUNT(*) FROM menuCategories WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		ELSEIF ((SELECT COUNT(*) FROM menuItems WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		ELSEIF ((SELECT COUNT(*) FROM menuModificationCategories WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		ELSEIF ((SELECT COUNT(*) FROM menuModificationItems WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		END IF; 
	END IF;
END;

CREATE TRIGGER beforeUpdateMenuItem
BEFORE UPDATE ON MenuItems FOR EACH ROW
BEGIN
	IF (OLD.quickCode <> NEW.quickCode OR OLD.title <> NEW.title) THEN
		IF ((SELECT COUNT(*) FROM menuCategories WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		ELSEIF ((SELECT COUNT(*) FROM menuItems WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		ELSEIF ((SELECT COUNT(*) FROM menuModificationCategories WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		ELSEIF ((SELECT COUNT(*) FROM menuModificationItems WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		END IF;  
	END IF;
END;

CREATE TRIGGER beforeUpdateMenuModificationCategory
BEFORE UPDATE ON MenuModificationCategories FOR EACH ROW
BEGIN
	IF (OLD.quickCode <> NEW.quickCode OR OLD.title <> NEW.title) THEN
		IF ((SELECT COUNT(*) FROM menuCategories WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		ELSEIF ((SELECT COUNT(*) FROM menuItems WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		ELSEIF ((SELECT COUNT(*) FROM menuModificationCategories WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		ELSEIF ((SELECT COUNT(*) FROM menuModificationItems WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		END IF; 
	END IF;
END;

CREATE TRIGGER beforeUpdateMenuModificationItem
BEFORE UPDATE ON MenuModificationItems FOR EACH ROW
BEGIN
	IF (OLD.quickCode <> NEW.quickCode OR OLD.title <> NEW.title) THEN
		IF ((SELECT COUNT(*) FROM menuCategories WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		ELSEIF ((SELECT COUNT(*) FROM menuItems WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		ELSEIF ((SELECT COUNT(*) FROM menuModificationCategories WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		ELSEIF ((SELECT COUNT(*) FROM menuModificationItems WHERE (OLD.quickCode <> NEW.quickCode AND quickCode = NEW.quickCode) OR (OLD.title <> NEW.title AND title = NEW.title) LIMIT 2) = 1 ) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Duplicate entry';
		END IF;   
	END IF;
END;

CREATE TRIGGER beforeInsertTableLog
BEFORE INSERT ON TableLog FOR EACH ROW
BEGIN
	DECLARE onlyEmp INT UNSIGNED;
	IF ((NEW.tableId IS NOT NULL) AND (SELECT COUNT(*) FROM Tables WHERE id = NEW.tableId LIMIT 1) = 0) THEN
		-- if table id is invalid
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Specified table does not exist!';
	ELSEIF ((NEW.authorizationId IS NOT NULL) AND (SELECT COUNT(*) FROM Employees WHERE id = NEW.authorizationId LIMIT 1) = 0) THEN
		-- if an authorization employee number is specified, but it's invalid
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Specifed authorization employee ID does not exist!';
	ELSEIF ((NEW.employeeId IS NOT NULL) AND ((SELECT COUNT(*) FROM Employees WHERE id = NEW.employeeId LIMIT 1) = 0)) THEN
		-- if an employee number is specified, but it's invalid
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Specifed employee ID does not exist!';
	ELSEIF ((NEW.ticketId IS NOT NULL) AND (SELECT COUNT(*) FROM Tickets WHERE id = NEW.ticketId LIMIT 1) = 0) THEN
		-- if a ticket number is specified, but it's invalid
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Specifed ticket number does not exist!';
	ELSEIF (NEW.action = 'Add' AND NEW.ticketId IS NOT NULL AND NEW.tableId IS NOT NULL AND (SELECT COUNT(*) FROM Tables WHERE id = NEW.tableId AND status IN ('seated', 'disabled') LIMIT 2) = 1) THEN
		-- if you're assigning a ticket to a table, but it's occupied or disabled	
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Cannot assign a Ticket to an Unassigned or Occupied Table!';
	ELSEIF (NEW.action = 'Remove' AND NEW.employeeId IS NOT NULL AND NEW.ticketId IS NULL) THEN
		-- if you're removing the only server, but the table is occupied and you're not removing the ticket
		IF ((SELECT COUNT(*) FROM TableAssignments WHERE tableId = NEW.tableId) = 1) THEN
			SELECT employeeId INTO onlyEmp FROM TableAssignments WHERE tableId = NEW.tableId;
			IF (onlyEmp = NEW.employeeId AND (SELECT status FROM Tables WHERE id = NEW.tableId) = 'seated') THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'All seated tables must have at least 1 server assigned to them!';
			END IF;
		END IF;
	ELSEIF (NEW.action = 'SetDisabled' AND (SELECT status FROM Tables WHERE id = NEW.tableId) <> 'unassigned') THEN
		-- if you're trying to set the table as disabled but the table status <> unassigned
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Cannot Disable Table! Status Must Be Unassigned!';
	ELSEIF (NEW.action = 'SetBused' AND (SELECT COUNT(*) FROM Tables WHERE id = NEW.tableId AND status = 'bussing' LIMIT 1) = 0) THEN	
		-- if you're setting the bused flag but the table isn't flagged as bussing
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Specified Table is not marked for bussing. Cannot set status to bused.';
	END IF;
	IF (NEW.employeeId IS NOT NULL AND NEW.action = "Add" AND (SELECT COUNT(*) FROM TableAssignments WHERE employeeId = NEW.employeeId AND tableId = NEW.tableId) = 1) THEN
		-- if the server is already assigned to the table
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Server is already assigned to this table.';
	END IF;
	SET NEW.authorizationId = NULL;
END;

CREATE TRIGGER afterInsertTableLog
AFTER INSERT ON TableLog FOR EACH ROW
BEGIN
	DECLARE timeStmp DATETIME;
	IF (NEW.action = 'Add' AND NEW.ticketId IS NOT NULL AND NEW.employeeId IS NOT NULL) THEN
		UPDATE Tickets SET tableId = NEW.tableId, timeSeated = NOW() WHERE id = NEW.ticketId;
		INSERT INTO TableAssignments VALUES(NEW.employeeId, NEW.tableId);
		UPDATE Tables SET status = 'seated' WHERE id = NEW.tableId;
	ELSEIF (NEW.action = 'Add' AND NEW.ticketId IS NOT NULL) THEN
		UPDATE Tickets SET tableId = NEW.tableId, timeSeated = NOW() WHERE id = NEW.ticketId;
		UPDATE Tables SET status = 'seated' WHERE id = NEW.tableId;
	ELSEIF (NEW.action = 'Add' AND NEW.employeeId IS NOT NULL) THEN
		INSERT INTO TableAssignments VALUES(NEW.employeeId, NEW.tableId);
		IF ((SELECT COUNT(*) FROM Tickets WHERE tableId = NEW.TableId LIMIT 1) > 0) THEN
			UPDATE Tables SET status = 'seated' WHERE id = NEW.tableId;
		ELSE
			IF ((SELECT COUNT(status) FROM Tables WHERE id = NEW.TableId AND status = 'bussing' LIMIT 1) = 0) THEN
				UPDATE Tables SET status = 'open' WHERE id = NEW.tableId;
			END IF;
		END IF;
	ELSEIF (NEW.action = 'Remove' AND NEW.ticketId IS NOT NULL AND NEW.employeeId IS NOT NULL) THEN
		DELETE FROM TableAssignments WHERE employeeId = NEW.employeeId AND tableId = NEW.tableId;
		UPDATE Tickets SET tableId = NULL, timeSeated = NULL WHERE id = NEW.ticketId;
		UPDATE Tables SET status = 'bussing' WHERE id = NEW.tableId;
	ELSEIF (NEW.action = 'Remove' AND NEW.ticketId IS NOT NULL) THEN
		UPDATE Tickets SET tableId = NULL, timeSeated = NULL WHERE id = NEW.ticketId;
		UPDATE Tables SET status = 'bussing' WHERE id = NEW.tableId;
	ELSEIF (NEW.action = 'Remove' AND NEW.employeeId IS NOT NULL) THEN
		DELETE FROM TableAssignments WHERE employeeId = NEW.employeeId and tableId = NEW.tableId;
		IF (((SELECT COUNT(*) FROM Tickets WHERE tableId = NEW.TableId LIMIT 1) = 0) AND 
		((SELECT COUNT(*) FROM Tables WHERE id = NEW.tableId and status = 'bussing' LIMIT 1)) = 0) THEN
			UPDATE Tables SET status = 'unassigned' WHERE id = NEW.tableId;
		END IF;
	ELSEIF (NEW.action = 'SetBused') THEN
		IF ((SELECT COUNT(*) FROM TableAssignments WHERE tableId = NEW.TableId LIMIT 1) = 0) THEN
			UPDATE Tables SET status = 'unassigned' WHERE id = NEW.tableId;
		ELSE
			UPDATE Tables SET status = 'open' WHERE id = NEW.tableId;
		END IF;
	ELSEIF (NEW.action = 'Disable') THEN
		UPDATE Tables SET status = 'disabled' WHERE id = NEW.tableId;
	ELSEIF (NEW.action = 'Enable') THEN
		IF ((SELECT COUNT(*) FROM TableAssignments WHERE tableId = NEW.TableId LIMIT 1) = 0) THEN
			UPDATE Tables SET status = 'unassigned' WHERE id = NEW.tableId;
		ELSE
			UPDATE Tables SET status = 'open' WHERE id = NEW.tableId;
		END IF;
	END IF;
END;

CREATE FUNCTION userPasswordHash(uname VARCHAR(25)) RETURNS CHAR(60)
BEGIN
	DECLARE empId INT UNSIGNED;
	DECLARE hashReturn CHAR(60);
	IF ((SELECT COUNT(*) FROM Employees WHERE userName = uname LIMIT 1) = 0) THEN
		-- Invalid Employee Id
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Username not found!';
	ELSE
		SELECT passwordBCrypt INTO hashReturn FROM Employees WHERE userName = uname;
		RETURN hashReturn;
	END IF;	
END;

CREATE FUNCTION usernameFromId(empId INT UNSIGNED) RETURNS VARCHAR(25)
BEGIN
	DECLARE uname VARCHAR(25);
	IF ((SELECT COUNT(*) FROM Employees WHERE id = empId) = 0) THEN
		-- Invalid Employee Id
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Invalid Employee Id!';
	ELSE
		SELECT userName INTO uname FROM Employees WHERE id = empId;
		RETURN uname;
	END IF;	
END;

CREATE FUNCTION idFromUsername(uname VARCHAR(25)) RETURNS INT UNSIGNED
BEGIN
	DECLARE empId INT UNSIGNED;
	IF ((SELECT COUNT(*) FROM Employees WHERE userName = uname LIMIT 1) = 0) THEN
		-- Invalid Username
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Username not found!';
	ELSE
		SELECT id INTO empId FROM Employees WHERE userName = uname;
		RETURN empId;
	END IF;
END;

-- calculates a mod item price, taking into account it's split.
-- If a split flag of 0 or 1023 is supplied, the associated mod price is returned as-is. 
-- mod prices are taken as-is. They are not percent off or discounts
CREATE FUNCTION modItemPrice(ticketItemId INT UNSIGNED, prcTot DECIMAL(6,2), splitFlg SMALLINT UNSIGNED) RETURNS DECIMAL(6, 2)
BEGIN
	DECLARE spltCount SMALLINT UNSIGNED;
	DECLARE spltLow SMALLINT UNSIGNED;
	DECLARE prcAdj DECIMAL(6, 2);
	DECLARE prcRem DECIMAL(6, 2);
	DECLARE prcDiv DECIMAL(6, 2);

	IF (prcTot IS NULL) THEN
		RETURN NULL;
	ELSEIF (prcTot = 0) THEN
		RETURN prcTot;
	END IF;
	SELECT overridePrice, splitCount(splitFlag), lowestSplitFlag(splitflag) INTO prcAdj, spltCount, spltLow FROM TicketItems WHERE id = ticketItemId;
	
	SELECT TRUNCATE(prcTot / spltCount, 2) INTO prcDiv;
	SELECT (prcTot - prcDiv * spltCount) INTO prcRem; 

	IF (splitFlg IN (0, 1023) OR spltCount = 1) THEN
		-- if you want to get the price irrespective of split, or there isn't a split
		RETURN prcTot;
	ELSEIF (splitFlg & spltLow <> 0) THEN
		-- if there is a split and you specified the lowest split.
		RETURN prcDiv + prcRem;
	ELSE
		-- if there is a split and the split you specifed is not the lowest split.
		RETURN prcDiv;
	END IF;

END;

-- calculates the ticket item price, taking into account it's split.
-- If a split flag of 0 or 1023 is supplied, the associated menu item price is returned as-is. 
CREATE FUNCTION ticketItemPrice(ticketItemId INT UNSIGNED, splitFlg SMALLINT UNSIGNED) RETURNS DECIMAL(6, 2)
BEGIN
	DECLARE spltCount SMALLINT UNSIGNED;
	DECLARE spltLow SMALLINT UNSIGNED;
	DECLARE prcTot DECIMAL(6, 2);
	DECLARE prcAdj DECIMAL(6, 2);
	DECLARE prcRem DECIMAL(6, 2);
	DECLARE prcDiv DECIMAL(6, 2);

	SELECT calculatedPrice, overridePrice, splitCount(splitFlag), lowestSplitFlag(splitflag) INTO prcTot, prcAdj, spltCount, spltLow FROM TicketItems WHERE id = ticketItemId;
	IF (prcAdj IS NOT NULL) THEN
		IF (prcAdj < 0) THEN
			SET prcTot = prcTot + prcAdj;
		ELSEIF (prcAdj = 0) THEN
			SET prcTot = 0;
		ELSEIF (prcAdj >= 1) THEN
			SET prcTot = prcAdj;
		ELSE
			SET prcTot = prcTot * (1 - prcAdj);
		END IF;
	END IF;

	
	SELECT TRUNCATE(prcTot / spltCount, 2) INTO prcDiv;
	SELECT (prcTot - prcDiv * spltCount) INTO prcRem; 

	IF (splitFlg IN (0, 1023) OR spltCount = 1) THEN
		-- if you want to get the price irrespective of split, or there isn't a split
		RETURN prcTot;
	ELSEIF (splitFlg & spltLow <> 0) THEN
		-- if there is a split and you specified the lowest split.
		RETURN prcDiv + prcRem;
	ELSE
		-- if there is a split and the split you specifed is not the lowest split.
		RETURN prcDiv;
	END IF;

END;

-- calculates the ticket item price with mods, taking into account it's split.
-- If a split flag of 0 or 1023 is supplied, the associated menu item price is returned as-is. 
CREATE FUNCTION ticketItemPriceWithMods(ticketItemId INT UNSIGNED, splitFlg SMALLINT UNSIGNED) RETURNS DECIMAL(6, 2)
BEGIN
	DECLARE spltCount SMALLINT UNSIGNED;
	DECLARE spltLow SMALLINT UNSIGNED;
	DECLARE prcTot DECIMAL(6, 2);
	DECLARE prcAdj DECIMAL(6, 2);
	DECLARE prcRem DECIMAL(6, 2);
	DECLARE prcDiv DECIMAL(6, 2);

	SELECT calculatedPriceWithMods, overridePrice, splitCount(splitFlag), lowestSplitFlag(splitflag) INTO prcTot, prcAdj, spltCount, spltLow FROM TicketItems WHERE id = ticketItemId;
	IF (prcAdj IS NOT NULL) THEN
		IF (prcAdj < 0) THEN
			SET prcTot = prcTot + prcAdj;
		ELSEIF (prcAdj = 0) THEN
			SET prcTot = 0;
		ELSEIF (prcAdj >= 1) THEN
			SET prcTot = prcAdj;
		ELSE
			SET prcTot = prcTot * (1 - prcAdj);
		END IF;
	END IF;

	
	SELECT TRUNCATE(prcTot / spltCount, 2) INTO prcDiv;
	SELECT (prcTot - prcDiv * spltCount) INTO prcRem; 

	IF (splitFlg IN (0, 1023) OR spltCount = 1) THEN
		-- if you want to get the price irrespective of split, or there isn't a split
		RETURN prcTot;
	ELSEIF (splitFlg & spltLow <> 0) THEN
		-- if there is a split and you specified the lowest split.
		RETURN prcDiv + prcRem;
	ELSE
		-- if there is a split and the split you specifed is not the lowest split.
		RETURN prcDiv;
	END IF;

END;

-- provided a split flag, return the lowest digit turned on. Returns 0 if the lowest 10 digits are 0
CREATE FUNCTION lowestSplitFlag(splitFlg SMALLINT UNSIGNED) RETURNS SMALLINT UNSIGNED
BEGIN
	DECLARE bitMask SMALLINT UNSIGNED;
	SET bitMask = 1;
	bitMaskLoop: LOOP

		IF (bitMask & splitFlg <> 0) THEN
			LEAVE bitMaskLoop;
		END IF;

		SET bitMask = bitMask * 2;
		IF (bitMask = 1024) THEN
			SET bitMask = 0;
			LEAVE bitMaskLoop;
		END IF;

 	END LOOP bitMaskLoop;
	RETURN bitMask;
END;

-- provided a split flag, return the count of digits turned on in the lowest 10 bits
CREATE FUNCTION splitCount(splitFlg SMALLINT UNSIGNED) RETURNS SMALLINT UNSIGNED
BEGIN
	DECLARE bitMask SMALLINT UNSIGNED;
	DECLARE cnt SMALLINT UNSIGNED DEFAULT 0; 
	SET bitMask = 1;
	bitMaskLoop: LOOP

		IF (bitMask & splitFlg <> 0) THEN
			SET cnt = cnt + 1;
		END IF;
		
		SET bitMask = bitMask * 2;
		IF (bitMask = 1024) THEN
			LEAVE bitMaskLoop;
		END IF;
		
 	END LOOP bitMaskLoop;
	RETURN cnt;
END;

-- calculate a subtotal for split. If splitFlg is 0 OR 1023 (bin 1111111111) the subtotal for the entire ticket is returned.
CREATE FUNCTION ticketSubtotal(ticketNumber INT UNSIGNED, splitFlg SMALLINT UNSIGNED) RETURNS DECIMAL(6,2)
BEGIN
	DECLARE splitSubtotal DECIMAL(6,2);
	IF (splitFlg = 0) THEN
		SET splitFlg = 1023;
	END IF;
	SELECT SUM(ticketItemPrice(id, splitFlg)) INTO splitSubtotal FROM TicketItems WHERE ticketId = ticketNumber AND splitFlg & splitFlag <> 0;
	RETURN splitSubtotal;
END;

CREATE FUNCTION sessionRole(token VARCHAR(60)) RETURNS INT UNSIGNED
BEGIN
	DECLARE empId INT UNSIGNED;
	DECLARE role INT UNSIGNED;
	SELECT id INTO empId FROM Employees WHERE accessToken = token AND accessTokenExpiration > NOW();
	IF (empId IS NULL) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Access Token Expired Or Doesn''t Exist!';
	ELSE
		SELECT employeeRole INTO role FROM ActiveEmployees WHERE employeeId = empId;
		RETURN role;
	END IF;
END;

CREATE FUNCTION sessionUsername(token VARCHAR(60)) RETURNS VARCHAR(25)
BEGIN
	 DECLARE uname VARCHAR(25);
	 SELECT userName INTO uname FROM Employees WHERE accessToken = token AND accessTokenExpiration > NOW();
	IF (uname IS NULL) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Access Token Expired Or Doesn''t Exist!';
	ELSE
		RETURN uname;
	END IF;
END;

CREATE FUNCTION loggedIn(uname VARCHAR(25)) RETURNS BOOLEAN
BEGIN
	DECLARE endTime DATETIME;
	DECLARE empId INT UNSIGNED;
	SELECT id INTO empId FROM Employees WHERE userName = uname;
	SELECT accessTokenExpiration INTO endTime FROM Employees WHERE userName = uname;
	IF (empId IS NULL) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Username Not Found!';
	ELSEIF (endTime IS NULL) THEN
		RETURN FALSE;
	ELSEIF (endTime < NOW()) THEN
		CALL logout( uname );
	ELSEIF (endTime IS NULL OR endTime < NOW()) THEN
		RETURN FALSE;
	ELSE
		RETURN TRUE;
	END IF;
END;

CREATE PROCEDURE createUser(IN lName VARCHAR(50), fName VARCHAR(60), IN uName VARCHAR(25), IN pHash VARCHAR(60), IN roles SMALLINT UNSIGNED)
BEGIN
	INSERT INTO Employees (lastName, firstName, userName, passwordBCrypt, roleLevel) VALUES (lName, fName, uname, pHash, roles);
END;

CREATE PROCEDURE transientLogin(IN requestedUsername VARCHAR(25), IN requestedRoles SMALLINT UNSIGNED, IN newAccessToken varchar(60))
BEGIN
	DECLARE timeoutMins INT UNSIGNED;
	SELECT (sessionTimeoutInMins * 100) INTO timeoutMins FROM Config;
	INSERT INTO Employees (lastName, firstName, userName, passwordBCrypt, roleLevel)
	VALUES ('Doe', 'John', requestedUsername, newAccessToken, requestedRoles);
	CALL login(requestedUsername, requestedRoles, newAccessToken);
END;

CREATE PROCEDURE login(IN requestedUsername VARCHAR(25), IN requestedRoles SMALLINT UNSIGNED, IN newAccessToken varchar(60))
BEGIN
	DECLARE allowedRoles SMALLINT UNSIGNED;
	DECLARE empId INT UNSIGNED;
	DECLARE timeoutMins INT UNSIGNED;
	IF ((SELECT COUNT(*) FROM Employees WHERE userName = requestedUsername) = 0) THEN
		-- Invalid Employee Id
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Username not found!';
	ELSE
		SELECT roleLevel INTO allowedRoles FROM Employees WHERE userName = requestedUsername;
		
		IF ((allowedRoles & requestedRoles) <> requestedRoles) THEN
			-- Invalid Requested Roles
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'You do not have the authorization to login with the role you specified!';
		ELSEIF ((SELECT COUNT(*) FROM Employees WHERE accessToken = newAccessToken AND userName <> requestedUsername LIMIT 2) = 1 ) THEN
			IF ((SELECT COUNT(*) FROM Employees WHERE userName = requestedUsername AND accessToken IS NOT NULL AND accessTokenExpiration > now() LIMIT 2) = 1) THEN
				CALL logout(requestedUsername);
			END IF;
			-- Another user is already logged in using your access key.
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Access Token Not Unique! Retry Login.';
		ELSE
					
			SELECT id INTO empId FROM Employees WHERE userName = requestedUsername;
			UPDATE EmployeeLog SET endTime = NOW() WHERE employeeId = empId and endTime IS NULL;
			INSERT INTO EmployeeLog (employeeId, employeeRole) VALUES(empId, requestedRoles);
			DELETE FROM ActiveEmployees WHERE employeeId = empId;
			INSERT INTO ActiveEmployees (employeeId, employeeRole) VALUES (empId, requestedRoles);
			SELECT (sessionTimeoutInMins * 100) INTO timeoutMins FROM Config;	
			UPDATE employees SET accessToken = newAccessToken, accessTokenExpiration = ADDTIME(NOW(), timeoutMins) WHERE userName = requestedUsername;
		END IF;
	END IF;	
END;

CREATE PROCEDURE logout(IN uname VARCHAR(25))
BEGIN
	DECLARE empId INT UNSIGNED;
	IF ((SELECT COUNT(*) FROM Employees WHERE userName = uname LIMIT 1) = 0) THEN
		-- Invalid Employee Id
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Username not found!';
	ELSE
		SELECT id INTO empId FROM Employees WHERE userName = uname;
		UPDATE EmployeeLog SET endTime = NOW() WHERE employeeId = empId and endTime IS NULL;
		UPDATE Employees SET accessToken = NULL, accessTokenExpiration = NULL WHERE userName = uname;
		DELETE FROM ActiveEmployees WHERE employeeId = empId;
	END IF;	
END;

CREATE FUNCTION ticketItemStatus(ticketNum INT UNSIGNED) RETURNS VARCHAR(20)
BEGIN
	DECLARE flg VARCHAR(20);
	DECLARE sub DATETIME;
	DECLARE red DATETIME;
	DECLARE del DATETIME;
	DECLARE menItm VARCHAR(10);
	DECLARE rte CHAR(1);
	IF ((SELECT COUNT(*) FROM TicketItems WHERE id = ticketNum LIMIT 1) = 0) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Ticket Item Number Not Found!';
	ELSE
		SELECT menuItemQuickCode, flag, submitTime, readyTime, deliveredTime INTO menItm, flg, sub, red, del FROM TicketItems WHERE id = ticketNum;
		SELECT route into rte FROM MenuItems WHERE quickCode = menItm;
		IF (rte IS NULL) THEN
			RETURN 'n/a';
		ELSEIF (flg = 'Removed') THEN
			RETURN 'Removed';
		ELSEIF (flg = 'Hidden') THEN
			RETURN 'Hidden';
		ELSEIF (del IS NOT NULL) THEN
			RETURN 'Delivered';
		ELSEIF (red IS NOT NULL) THEN
			RETURN 'Ready';
		ELSEIF (sub IS NULL) THEN 
			RETURN 'Pending';
		ELSEIF (flg IS NULL) THEN
			RETURN 'Preparing';
		ELSE
			RETURN flg;
		END IF;
	END IF;
END;

CREATE PROCEDURE createTicket(IN ticketNickname VARCHAR(25), IN peopleCount INT UNSIGNED, OUT newTicketNumber INT UNSIGNED)
BEGIN
	DECLARE bDay DATE;
	SELECT businessDay FROM Config INTO bDay;
	INSERT INTO Tickets (nickname, partySize, businessDay) VALUES (ticketNickname, peopleCount, bDay);
	SELECT MAX(id) INTO newTicketNumber FROM Tickets; 
END;

CREATE PROCEDURE createReservation(IN ticketNickname VARCHAR(25), IN peopleCount INT UNSIGNED, IN requestedTime DATETIME, OUT newTicketNumber INT UNSIGNED)
BEGIN
	DECLARE bDay DATE;
	SELECT businessDay FROM Config INTO bDay;
	INSERT INTO Tickets (nickname, partySize, timeRequested, businessDay) VALUES (ticketNickname, peopleCount, requestedTime, bDay);
	SELECT MAX(id) INTO newTicketNumber FROM Tickets; 
END;

CREATE PROCEDURE removeTicket(IN ticketNumber INT UNSIGNED)
BEGIN
	DELETE FROM TicketItems WHERE ticketId = ticketNumber;
	DELETE FROM Splits WHERE ticketId = ticketNumber;
	DELETE FROM Tickets WHERE id = ticketNumber;
END;

-- todo switch to using menuItemPrice
-- need to use real price calculation
CREATE PROCEDURE createTicketItem(IN ticketNumber INT UNSIGNED, IN seatNumber SMALLINT UNSIGNED, IN split SMALLINT UNSIGNED, IN menuItemQC VARCHAR(10))
BEGIN
	DECLARE bDay DATE;
	DECLARE tbl VARCHAR(3);
	DECLARE newItemIndex SMALLINT UNSIGNED;
	DECLARE calcprice DECIMAL(6, 2);
	DECLARE qty SMALLINT;
	DECLARE req SMALLINT;
	DECLARE nextAvailableItmNum SMALLINT;
	SELECT businessDay FROM Config INTO bDay;
	SET newItemIndex = 1;
	SELECT tableId INTO tbl FROM Tickets WHERE id = ticketNumber;
	IF ((SELECT COUNT(*) FROM Tickets WHERE id = ticketNumber LIMIT 1) = 0) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Ticket Item Number Doesn''t Exist!';
	ELSEIF (split > 9) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Split Number Must Not Exceed 9!';
	ELSEIF (tbl IS NULL) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Ticket Isn''t Assigned to a Table!';
	ELSEIF ((SELECT count(quickCode) FROM MenuItems WHERE quickCode = menuItemQC) = 0 ) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Menu Item Not Found!';
	ELSE
		-- todo switch to using menuItemPrice
		SELECT price, quantity, requests INTO calcprice, qty, req FROM MenuItems WHERE quickCode = menuItemQC;
		IF (calcprice IS NULL) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Menu Item price Cannot Be Determined!';
		ELSE
			-- record a menu item was requested.
			UPDATE MenuItems SET requests = req + 1 WHERE quickCode = menuItemQC;
			-- null qty is unchecked.... Thinks like drinks and stuff not routed to the
			-- kitchen or bar should not keep track of quantity.
			IF (IFNULL(qty, 1) = 0) THEN
				SIGNAL SQLSTATE '45000'
				SET MESSAGE_TEXT = 'Menu Item Is Out of Stock!';
			ELSE
				
				-- find the next available item number in ticket.
				SELECT (IFNULL(MAX(itemId),0) +1) INTO newItemIndex FROM TicketItems WHERE ticketId = ticketNumber;

				-- create the ticket item
				INSERT INTO TicketItems (id, splitFlag, seat, menuItemQuickCode, calculatedPrice, calculatedPriceWithMods, businessDay) VALUES (ticketNumber * 10000 + newItemIndex, POWER(2, split),  seatNumber, menuItemQC, calcprice, calcprice, bDay);
				-- SET newTicketItemID = ticketNumber * 10000 + newItemIndex;

				-- create the split if it doesn't exist
				CALL addSplit(ticketNumber, split);

				-- decrease the inventory by 1
				IF (qty IS NOT NULL) THEN
					UPDATE MenuItems SET quantity = qty - 1 WHERE quickCode = menuItemQC;
				END IF; 
				CALL updateTicketSplitTimeStamp(ticketNumber, split);
			END IF;
		END IF; 
	END IF;
END;


CREATE PROCEDURE modifyTicketItem(IN ticketItemNumber INT UNSIGNED, IN modNotes VARCHAR(500))
BEGIN
	DECLARE stat VARCHAR(20);
	DECLARE tickNum INT UNSIGNED;
	DECLARE splitFlg SMALLINT UNSIGNED;
	DECLARE tickGrp DECIMAL(6, 2);
	SELECT ticketId, splitFlag, groupId INTO tickNum, splitFlg, tickGrp FROM TicketItems WHERE id = ticketItemNumber;
	IF ((SELECT COUNT(*) FROM TicketItems WHERE id = ticketItemNumber LIMIT 1) = 0) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Ticket Item Number Doesn''t Exist!';
	ELSE
		SELECT ticketItemStatus(ticketItemNumber) INTO stat; 
		IF (stat in ('Delivered', 'Ready', 'Removed')) THEN
			SIGNAL SQLSTATE '45000'
			SET MESSAGE_TEXT = 'Removed/Ready/Delivered Ticket Items Cannot Be Modified!';
		ELSE
			UPDATE TicketItems SET modificationNotes = modNotes WHERE id = ticketItemNumber;
			-- DEPRECATED needs to be recoded
			UPDATE TicketItems SET calculatedPriceWithMods = ticketItemPrice(ticketItemNumber, 1023) WHERE id = ticketItemNumber;
			IF (stat = 'Preparing') THEN
				UPDATE TicketItems SET flag = 'Updated' WHERE id = ticketItemNumber;
			END IF;
			CALL updateTicketGroup(tickGrp, 0);
			CALL updateTicketSplitsTimeStamp(tickNum, splitFlg);
		END IF;
	END IF;
END;

CREATE PROCEDURE overrideTicketItemPrice(IN ticketItemNumber INT UNSIGNED, IN adjustemnt DECIMAL(6, 2), note VARCHAR(500), authorizationUsername VARCHAR(25))
BEGIN
	DECLARE empId INT UNSIGNED;
	DECLARE stat VARCHAR(30);
	DECLARE tickNum INT UNSIGNED;
	DECLARE splitFlg SMALLINT UNSIGNED;
	SELECT ticketItemStatus(ticketItemNumber) INTO stat;
	SELECT ticketId, splitFlag INTO tickNum, splitFlg FROM TicketItems WHERE id = ticketItemNumber;
	IF ((SELECT COUNT(*) FROM TicketItems WHERE id = ticketItemNumber LIMIT 1) = 0) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Ticket Item Number Doesn''t Exist!';
	ELSEIF (authorizationUsername IS NULL) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'price Overrides must include Employee# to Log Event!';
	ELSEIF (stat NOT IN ('Delivered', 'Hidden')) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'price Overrides Can Only Be Applied to Delivered Ticket Items!';
	ELSE
		SELECT idFromUsername(authorizationUsername) INTO empId; 
		UPDATE TicketItems SET overridePrice = adjustment, 
		                       overrideNote = note,
							   overrideAuthorization = empId,
							   overrideTimeStamp = NOW()
						   WHERE id = ticketItemNumber;
		CALL updateTicketSplitsTimeStamp(tickNum, splitFlg);
	END IF;
END;

CREATE PROCEDURE closeTicket(IN ticketNumber INT UNSIGNED)
BEGIN
	DECLARE targetTableID VARCHAR(3);
	SELECT tableId INTO targetTableID FROM Tickets WHERE id = ticketNumber;
	UPDATE Tickets SET timeClosed = NOW()  
					WHERE id = ticketNumber;
	CALL updateTicketSplitsTimeStamp(ticketNumber, 0);
	INSERT INTO tableLog(tableId, action, ticketId)
		VALUES (targetTableID, 'remove', ticketNumber);
END;

CREATE PROCEDURE removeTicketItem(IN ticketItemNumber INT UNSIGNED, IN skipRemovedState BOOLEAN)
BEGIN
	DECLARE stat VARCHAR(20);
	DECLARE qc VARCHAR(40);
	DECLARE qty SMALLINT UNSIGNED;
	DECLARE tickNum INT UNSIGNED;
	DECLARE splitFlg SMALLINT UNSIGNED;
	DECLARE ticketNumber INT UNSIGNED;
	DECLARE split SMALLINT UNSIGNED;
	DECLARE tickGrp DECIMAL(6, 2);
	SELECT ticketItemStatus(ticketItemNumber) INTO stat;
	SELECT menuItemQuickCode, ticketId, splitFlag, groupId INTO qc, tickNum, splitFlg, tickGrp FROM TicketItems WHERE id = ticketItemNumber;
	SELECT LOG(2, splitFlg) INTO split;
	SELECT quantity INTO qty FROM MenuItems WHERE quickCode = qc;
	IF (stat IN ('Hidden')) THEN
		-- Hidden is a subcategory of Delivered. The manager hides the item from the ticket and price ignored.
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Item Has Already Been Removed!';
	ELSEIF (stat IN ('Updated', 'Preparing')) THEN
		IF (skipRemovedState = 1) THEN
			UPDATE TicketItems SET flag = 'Hidden' WHERE id = ticketItemNumber;
		ELSE
			UPDATE TicketItems SET flag = 'Removed' WHERE id = ticketItemNumber;
		END IF;
		CALL updateTicketGroup(tickGrp, 2);
		CALL updateTicketSplitsTimeStamp(tickNum, splitFlg);
	ELSEIF (stat IN ('Removed', 'Ready', 'Delivered')) THEN
		UPDATE TicketItems SET flag = 'Hidden' WHERE id = ticketItemNumber;
		CALL updateTicketGroup(tickGrp, 2);
		CALL updateTicketSplitsTimeStamp(tickNum, splitFlg);
	ELSE		
		-- delete the actual ticket item
		DELETE FROM TicketItems WHERE id = ticketItemNumber;

		-- delete the split if it's now empty
		CALL removeSplit(tickNum, split);
		
		-- increment quantity by 1 in inventory
		IF (qty IS NOT NULL) THEN
			UPDATE MenuItems SET quantity = qty + 1 WHERE quickCode = qc;
		END IF;
		CALL updateTicketSplitsTimeStamp(tickNum, splitFlg);
	END IF;
END;

CREATE PROCEDURE moveTicketItemToSeat(IN ticketItemNumber INT UNSIGNED, IN toSeat SMALLINT UNSIGNED)
BEGIN
	DECLARE tickNum INT UNSIGNED;
	DECLARE splitFlg SMALLINT UNSIGNED;
	SELECT ticketId, splitFlag INTO tickNum, splitFlg FROM TicketItems WHERE id = ticketItemNumber;
	UPDATE TicketItems SET seat = toSeat WHERE id = ticketItemNumber;
	CALL updateTicketSplitsTimeStamp(tickNum, splitFlg);
END;

CREATE PROCEDURE moveTicketItemToSplit(IN ticketItemNumber INT UNSIGNED, IN fromSplit SMALLINT UNSIGNED, IN toSplit SMALLINT UNSIGNED)
BEGIN
	DECLARE tickNum INT UNSIGNED;
	DECLARE fromFlg SMALLINT UNSIGNED;
	DECLARE toFlg SMALLINT UNSIGNED;
	DECLARE oldSplitFlg SMALLINT UNSIGNED;
	DECLARE newSplitFlg SMALLINT UNSIGNED;
	
	SELECT ticketId, splitFlag INTO tickNum, oldSplitFlg FROM TicketItems WHERE id = ticketItemNumber;

	IF (fromSplit = 10) THEN
		SET fromFlg = 1023;
	ELSE
		SET fromFlg = POWER(2, fromSplit);
	END IF;
	
	SET toFlg = POWER(2, toSplit);
	SET newSplitFlg = ((~fromFlg) & oldSplitFlg) | toFlg;

	UPDATE TicketItems SET splitFlag = newSplitFlg WHERE id = ticketItemNumber;

	-- delete the old split if there's nothing in it anymore.
	CALL removeSplit(tickNum, fromSplit);

	-- create the new split if it doesn't exist.
	CALL addSplit(tickNum, toSplit);

	CALL updateTicketSplitsTimeStamp(tickNum, fromFlg | toFlg);
END;

CREATE PROCEDURE removeTicketItemFromSplit(IN ticketItemNumber INT UNSIGNED, IN split SMALLINT UNSIGNED)
BEGIN
	DECLARE tickNum INT UNSIGNED;
	DECLARE oldSplitFlg SMALLINT UNSIGNED;
	DECLARE splitMask SMALLINT UNSIGNED;
	DECLARE splitFlg SMALLINT UNSIGNED;
	DECLARE newSplitFlg SMALLINT UNSIGNED;
	
	DECLARE changeFlg SMALLINT UNSIGNED;
	
	SELECT ticketId, splitFlag INTO tickNum, oldSplitFlg FROM TicketItems WHERE id = ticketItemNumber;

	SET splitFlg = POWER(2, split);
	SET splitMask = splitFlg XOR 1023;
	SET newSplitFlg = oldSplitFlg & splitMask;
	SET changeFlg = newSplitFlg | splitFlg;

	IF (newSplitFlg > 0) THEN
		UPDATE TicketItems SET splitFlag = newSplitFlg WHERE id = ticketItemNumber;
	ELSE
		DELETE FROM TicketItems WHERE id = ticketItemNumber;
	END IF;

	-- delete the old split if there's nothing in it anymore.
	CALL removeSplit(tickNum, split);
	
	CALL updateTicketSplitsTimeStamp(tickNum, changeFlg);
END;

CREATE PROCEDURE addTicketItemToSplit(IN ticketItemNumber INT UNSIGNED, IN split SMALLINT UNSIGNED)
BEGIN
	DECLARE tickNum INT UNSIGNED;
	DECLARE oldSplitFlg SMALLINT UNSIGNED;
	DECLARE splitFlg SMALLINT UNSIGNED;	
	DECLARE changeFlg SMALLINT UNSIGNED;
	
	SELECT ticketId, splitFlag INTO tickNum, oldSplitFlg FROM TicketItems WHERE id = ticketItemNumber;

	SET splitFlg = POWER(2, split);
	SET changeFlg = splitFlg | oldSplitFlg;

	UPDATE TicketItems SET splitFlag = changeFlg WHERE id = ticketItemNumber;

	-- create the split if it doesn't exist
	CALL addSplit(tickNum DIV 10000, split);

	CALL updateTicketSplitsTimeStamp(tickNum, changeFlg);
END;

CREATE PROCEDURE markTicketItemAsReady(IN ticketItemNumber INT UNSIGNED)
BEGIN
	DECLARE tickNum INT UNSIGNED;
	DECLARE splitFlg SMALLINT UNSIGNED;
	DECLARE tickGrp DECIMAL(6,2);
	
	SELECT ticketId, splitFlag, groupId INTO tickNum, splitFlg, tickGrp FROM TicketItems WHERE id = ticketItemNumber;

	UPDATE TicketItems SET readyTime = NOW() WHERE id = ticketItemNumber;
	CALL updateTicketGroup(tickGrp, 2);
	CALL updateTicketSplitsTimeStamp(tickNum, splitFlg);
END;

CREATE PROCEDURE rescindTicketItemReadyState(IN ticketItemNumber INT UNSIGNED)
BEGIN
	DECLARE tickNum INT UNSIGNED;
	DECLARE splitFlg SMALLINT UNSIGNED;
	DECLARE tickGrp DECIMAL(6, 2);
	
	SELECT ticketId, splitFlag, groupId INTO tickNum, splitFlg, tickGrp FROM TicketItems WHERE id = ticketItemNumber;

	UPDATE TicketItems SET readyTime = NULL WHERE id = ticketItemNumber;
	CALL updateTicketGroup(tickGrp, 1);
	CALL updateTicketSplitsTimeStamp(tickNum, splitFlg);
END;

CREATE PROCEDURE toggleTicketItemReadyState(IN ticketItemNumber INT UNSIGNED)
BEGIN
	DECLARE timeSubmitted, timeReady, timeDelivered DATETIME;
	SELECT submitTime, readyTime, deliveredTime INTO timeSubmitted, timeReady, timeDelivered
	FROM TicketItems WHERE id = ticketItemNumber;
	IF (timeSubmitted IS NOT NULL AND timeDelivered IS NULL) THEN
		IF (timeReady IS NULL) THEN
			CALL markTicketItemAsReady(ticketItemNumber);
		ELSE
			CALL rescindTicketItemReadyState(ticketItemNumber);
		END IF;
	END IF;
END;

CREATE PROCEDURE markTicketItemAsDelivered(IN ticketItemNumber INT UNSIGNED)
BEGIN
	DECLARE tickNum INT UNSIGNED;
	DECLARE splitFlg SMALLINT UNSIGNED;
	DECLARE tickGrp DECIMAL(6, 2);
	
	SELECT ticketId, splitFlag, groupId INTO tickNum, splitFlg, tickGrp FROM TicketItems WHERE id = ticketItemNumber;
	
	UPDATE TicketItems SET deliveredTime = NOW() WHERE id = ticketItemNumber;
	CALL updateTicketGroup(tickGrp, 2);
	CALL updateTicketSplitsTimeStamp(tickNum, splitFlg);
END;

CREATE PROCEDURE reprepareTicketItem(IN ticketItemNumber INT UNSIGNED)
BEGIN
	DECLARE tickNum INT UNSIGNED;
	DECLARE splitFlg SMALLINT UNSIGNED;
	DECLARE tickGrp DECIMAL(6,2);
	
	SELECT ticketId, splitFlag, groupId INTO tickNum, splitFlg, tickGrp FROM TicketItems WHERE id = ticketItemNumber;

	UPDATE TicketItems SET readyTime = NULL, deliveredTime = NULL WHERE id = ticketItemNumber;
	CALL updateTicketGroup(tickGrp, 1);
	CALL updateTicketSplitsTimeStamp(tickNum, splitFlg);
END;


CREATE PROCEDURE hideTicketItem(IN ticketItemNumber INT UNSIGNED)
BEGIN
	DECLARE tickNum INT UNSIGNED;
	DECLARE splitFlg SMALLINT UNSIGNED;
	DECLARE flg VARCHAR(20);
	SELECT ticketItemStatus(ticketItemNumber) INTO flg;
	
	SELECT ticketId, splitFlag INTO tickNum, splitFlg FROM TicketItems WHERE id = ticketItemNumber;

	IF (flg NOT IN ('Removed', 'Hidden')) THEN
		SIGNAL SQLSTATE '45000'
		SET MESSAGE_TEXT = 'Only Removed Ticket Items May Be Hidden!';
	ELSE
		UPDATE TicketItems SET flag = 'Hidden' WHERE id = ticketItemNumber;
		CALL updateTicketSplitsTimeStamp(tickNum, splitFlg);
	END IF;
END;



-- passing in a split value of 10 indicates submit all ticket items for specified ticket
-- otherwise submits a single split 0 - 9.
CREATE PROCEDURE submitPendingTicketItems(IN ticketNumber INT UNSIGNED, IN split SMALLINT UNSIGNED)
BEGIN
	DECLARE groupNum SMALLINT UNSIGNED;
	DECLARE sf SMALLINT UNSIGNED;
	DECLARE done INT DEFAULT FALSE;
	DECLARE selectedRoute char(1);
	-- get every route involved with the items being submitted.
	DECLARE myCursor CURSOR
		FOR
			SELECT DISTINCT MenuItems.route FROM (TicketItems INNER JOIN MenuItems ON TicketItems.menuItemQuickCode = MenuItems.quickCode) WHERE ticketId = ticketNumber AND submitTime IS NULL AND ticketItemStatus(TicketItems.id) COLLATE utf8mb4_unicode_ci <> 'n/a' COLLATE utf8mb4_unicode_ci;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
	
	OPEN myCursor;
	submitLoop:
	LOOP
		FETCH myCursor INTO selectedRoute;
		IF (done = 1) THEN
			LEAVE submitLoop;
		END IF;
		-- prevent items without a route from being added to a new group.
		IF (selectedRoute IS NULL) THEN
			IF (split = 10) THEN
			UPDATE TicketItems SET submitTime = NOW() WHERE ticketId = ticketNumber AND menuItemQuickCode IN (SELECT quickCode FROM MenuItems WHERE MenuItems.route IS NULL) AND submitTime IS NULL AND ticketItemStatus(id) COLLATE utf8mb4_unicode_ci <> 'n/a' COLLATE utf8mb4_unicode_ci;
			ELSE
			SELECT POWER(2, split) INTO sf;
			UPDATE TicketItems SET submitTime = NOW() WHERE ticketId = ticketNumber AND menuItemQuickCode IN (SELECT quickCode FROM MenuItems WHERE MenuItems.route IS NULL) AND submitTime IS NULL AND (splitFlag & sf) = sf AND ticketItemStatus(id) COLLATE utf8mb4_unicode_ci <> 'n/a' COLLATE utf8mb4_unicode_ci;
			END IF;
		ELSE
			SELECT (COALESCE(MAX(groupIndex), 0) + 1) INTO groupNum FROM TicketItems WHERE ticketId = ticketNumber;
			IF (split = 10) THEN
			UPDATE TicketItems SET submitTime = NOW(), groupIndex = groupNum WHERE ticketId = ticketNumber AND menuItemQuickCode IN (SELECT quickCode FROM MenuItems WHERE MenuItems.route = selectedRoute) AND submitTime IS NULL AND ticketItemStatus(id) COLLATE utf8mb4_unicode_ci <> 'n/a' COLLATE utf8mb4_unicode_ci;
			ELSE
			SELECT POWER(2, split) INTO sf;
			UPDATE TicketItems SET submitTime = NOW(), groupIndex = groupNum WHERE ticketId = ticketNumber AND menuItemQuickCode IN (SELECT quickCode FROM MenuItems WHERE MenuItems.route = selectedRoute) AND submitTime IS NULL AND (splitFlag & sf) = sf AND ticketItemStatus(id) COLLATE utf8mb4_unicode_ci <> 'n/a' COLLATE utf8mb4_unicode_ci;
			END IF;
			CALL updateTicketGroup(ticketNumber + groupNum / 100, 1);
			CALL updateTicketSplitsTimeStamp(ticketNumber, 1023);
		END IF;
	END LOOP;
	CLOSE myCursor;
END;

/*
 -- Old version of submitPendingTicketItems
CREATE PROCEDURE submitPendingTicketItems(IN ticketNumber INT UNSIGNED, IN split SMALLINT UNSIGNED)
BEGIN
	DECLARE groupNum SMALLINT UNSIGNED;
	DECLARE sf SMALLINT UNSIGNED;
	SELECT (COALESCE(MAX(groupIndex), 0) + 1) INTO groupNum FROM TicketItems WHERE ticketId = ticketNumber;
	IF (split = 10) THEN
		UPDATE TicketItems SET submitTime = NOW(), groupIndex = groupNum WHERE ticketId = ticketNumber AND submitTime IS NULL AND ticketItemStatus(id) COLLATE utf8mb4_unicode_ci <> 'n/a' COLLATE utf8mb4_unicode_ci;
	ELSE
		SELECT POWER(2, split) INTO sf;
		UPDATE TicketItems SET submitTime = NOW(), groupIndex = groupNum WHERE ticketId = ticketNumber AND submitTime IS NULL AND (splitFlag & sf) = sf AND ticketItemStatus(id) COLLATE utf8mb4_unicode_ci <> 'n/a' COLLATE utf8mb4_unicode_ci;
	END IF;
	CALL updateTicketGroup(ticketNumber + groupNum / 100, 1);
	CALL updateTicketSplitsTimeStamp(ticketNumber, 1023);
END;
*/


-- passing in a split value of 10 indicates submit all ticket items for specified ticket
-- otherwise submits a single split 0 - 9.
CREATE PROCEDURE cancelPendingTicketItems(IN ticketNumber INT UNSIGNED, IN split SMALLINT UNSIGNED)
BEGIN
	DECLARE sf SMALLINT UNSIGNED;
	IF (split = 10) THEN
		DELETE FROM TicketItems WHERE submitTime IS NULL AND ticketId = ticketNumber AND ticketItemStatus(id) COLLATE utf8mb4_unicode_ci <> 'n/a' COLLATE utf8mb4_unicode_ci;
	ELSE
		SELECT POWER(2, split) INTO sf;
		DELETE FROM TicketItems WHERE submitTime IS NULL AND ticketId = ticketNumber AND (splitFlag & sf) = sf AND ticketItemStatus(id) COLLATE utf8mb4_unicode_ci <> 'n/a' COLLATE utf8mb4_unicode_ci;
	END IF;
	CALL updateTicketSplitsTimeStamp(ticketNumber, sf);
END;

CREATE PROCEDURE updateTicketGroup(tickGrp DECIMAL(6, 2), flag TINYINT UNSIGNED)
BEGIN
	DECLARE rte CHAR(1);
	IF (flag = 2) THEN
		-- ticket group may no longer be active.
		-- check TicketItems table and find any item with matching tickGrp that is "Preparing" or "Modified"
		
		-- if no matches are found, delete tickGrp from ActiveTicketGroups table.
		-- Occurs when ticket item is canceled after submission, or item marked as ready
		IF ((SELECT COUNT(*) FROM TicketItems WHERE groupId = tickGrp AND ticketItemStatus(id) NOT IN ('Delivered', 'Canceled', 'Hidden' ) LIMIT 1) = 0) THEN
			DELETE FROM ActiveTicketGroups WHERE id = tickGrp;
		END IF;
	ELSEIF (flag = 1) THEN
		-- ticket group may have become active again.
		-- check TicketItems table and find any item with matching tickGrp that is "Preparing" or "Modified"
		-- if a match is found, insert tickGrp into ActiveTicketGroups table.

		-- occurs when a ticket items's ready state is rescinded or
		-- ticket item is sent back to the kitchen to be reprepared.
		IF ((SELECT COUNT(*) FROM TicketItems WHERE groupId = tickGrp AND ticketItemStatus(id) IN ('Preparing', 'Updated') LIMIT 1) > 0
			AND (SELECT COUNT(*) FROM ActiveTicketGroups WHERE id = tickGrp LIMIT 1) = 0) THEN
			
			SELECT route into rte FROM TicketItems
			INNER JOIN MenuItems ON menuItemQuickCode = quickCode
			WHERE groupId = tickGrp LIMIT 1;
			INSERT INTO ActiveTicketGroups (id, route) VALUES (tickGrp, rte);
		END IF;
	END IF; 
	UPDATE ActiveTicketGroups SET updateCounter = updateCounter + 1  WHERE id = tickGrp;

END;

CREATE PROCEDURE closeTicketGroup(tickGrp DECIMAL(6, 2))
BEGIN
	IF ((SELECT COUNT(*) FROM TicketItems WHERE groupId = tickGrp AND ticketItemStatus(id) IN ('Preparing', 'Updated') LIMIT 1) = 0) THEN
			DELETE FROM ActiveTicketGroups WHERE id = tickGrp;
	END IF;
END;


CREATE PROCEDURE updateTicketSplitTimeStamp(IN ticketNumber INT UNSIGNED, IN split SMALLINT UNSIGNED)
BEGIN
	DECLARE sf SMALLINT UNSIGNED;
	
	SELECT POWER(2, split) INTO sf;
	UPDATE Tickets SET timeModified = NOW() WHERE id = ticketNumber;
	
	IF ((SELECT COUNT(*) FROM TicketItems WHERE ticketId = ticketNumber AND (splitFlag & sf) = sf LIMIT 1) = 0) THEN
		DELETE FROM Splits WHERE ticketId = ticketNumber AND splitId = split;
	ELSE
		UPDATE Splits SET timeModified = NOW() WHERE ticketId = ticketNumber AND splitId = split;
	END IF;
END;

CREATE PROCEDURE updateTicketSplitsTimeStamp(IN ticketNumber INT UNSIGNED, IN sf SMALLINT UNSIGNED)
BEGIN
	UPDATE Tickets SET timeModified = NOW() WHERE id = ticketNumber;
	
	-- if sf/split flag is 0, set it to apply to all splits.
	IF (sf = 0) THEN
		SET sf = 1023;
	END IF;

	IF ((SELECT COUNT(*) FROM TicketItems WHERE ticketId = ticketNumber AND (splitFlag & sf) = sf LIMIT 1) = 0) THEN
		DELETE FROM Splits WHERE ticketId = ticketNumber AND (POWER(2, splitId) & sf) = sf;
	ELSE
		UPDATE Splits SET timeModified = NOW() WHERE ticketId = ticketNumber AND (POWER(2, splitId) & sf) = sf;
	END IF;
END;

CREATE FUNCTION ticketItemPayStatus(tickItemNum INT UNSIGNED) RETURNS VARCHAR(7)
BEGIN
	DECLARE tickNum INT UNSIGNED;
	DECLARE splitFlg SMALLINT UNSIGNED;
	DECLARE countSplits INT UNSIGNED;
	DECLARE paidSplits INT UNSIGNED;

	SELECT ticketId, splitFlag INTO tickNum, splitFlg FROM ticketItems WHERE ticketId = tickItemNum;

	SELECT COUNT(*) INTO countSplits FROM Splits WHERE ticketId = tickNum AND (POWER(splitId, 2) & splitFlg = POWER(splitId, 2));
	SELECT COUNT(*) INTO paidSplits FROM Splits WHERE ticketId = tickNum AND (POWER(splitId, 2) & splitFlg = POWER(splitId, 2)) AND totalAmountPaid IS NOT NULL;
	IF (paidSplits = 0) THEN
		RETURN 'Unpaid';
	ELSEIF (countSplits = paidSplits) THEN
		RETURN 'Paid';
	ELSE
		RETURN 'Partial';
	END IF;	
END;

CREATE PROCEDURE addSplit(IN ticketNumber INT UNSIGNED, IN splitNumber SMALLINT UNSIGNED)
BEGIN
	-- new split. Add Split record.
	DECLARE bDay DATE;
	SELECT businessDay FROM Config INTO bDay;
	IF ((SELECT COUNT(*) FROM Splits WHERE ticketId = ticketNumber AND splitId = splitNumber LIMIT 1) = 0) THEN
		INSERT INTO Splits (ticketId, splitId, businessDay) VALUES (ticketNumber, splitNumber, bDay);
	END IF;
END;

CREATE PROCEDURE removeSplit(IN ticketNumber INT UNSIGNED, IN splitNumber SMALLINT UNSIGNED)
BEGIN
	-- new split. Add Split record.
	IF ((SELECT COUNT(*) FROM Splits WHERE ticketId = ticketNumber AND splitId = splitNumber LIMIT 1) = 0) THEN
		DELETE FROM Splits WHERE ticketId = ticketNumber AND splitId = splitNumber;
	END IF;
END;

CREATE FUNCTION splitString(ticketItemId INT UNSIGNED) RETURNS VARCHAR(25)
BEGIN
	DECLARE splitStr VARCHAR(20) DEFAULT '';
	DECLARE splitNum SMALLINT UNSIGNED DEFAULT 1;
	DECLARE splitFlg SMALLINT UNSIGNED;
	SELECT splitFlag INTO splitFlg FROM TicketItems WHERE id = ticketItemId;

	splitLoop: LOOP
  		
		IF (POWER(2, MOD(splitNum, 10)) & splitFlg <> 0) THEN
			SET splitStr = CONCAT(splitStr, '/', CONVERT(splitNum, CHAR));
		END IF;

		IF splitNum = 10 THEN
			SET splitNum = 0;
    		LEAVE splitLoop;
  		END IF;
		
		SET splitNum = splitNum + 1;

 	END LOOP splitLoop;
	RETURN SUBSTRING(splitStr,2);	
END;

/* I haven't figured out how to return cursors yet. Enabling these
procedures will cause the tables to not load in HostView amongst other problems.

// get all of the ticket items and their associated menu items for a specified groupId.
// limited by route
CREATE PROCEDURE getTicketGroup(IN grpId DECIMAL(6, 2), IN rte CHAR(1))
BEGIN
	SELECT TicketItems.*, MenuItems.* FROM TicketItems
		INNER JOIN MenuItems ON TicketItems.menuItemQuickCode = MenuItems.quickCode
		WHERE TicketItems.groupId = grpId AND MenuItems.route = rte;  
END;

// get list of all active ticket groups (groups that have ticket items with 'Preparing' status)
// limited by route
CREATE PROCEDURE getActiveTicketGroupIdList(IN rte CHAR(1))
BEGIN
	SELECT DISTINCT TicketItems.groupId AS groupId FROM TicketItems
		INNER JOIN MenuItems ON TicketItems.menuItemQuickCode = MenuItems.quickCode
		WHERE MenuItems.route = rte
		AND TicketItems.submitTime IS NOT NULL AND TicketItems.readyTime IS NULL
		AND (TicketItems.flag IS NULL OR UPPER(TicketItems.flag) = UPPER('updated')); 
END;


// returns true/false.... Well 1/0. Will return false/0 if you specify an invalid groupId.
CREATE FUNCTION ticketGroupActive(grpId DECIMAL(6, 2), rte CHAR(1)) RETURNS BOOLEAN
BEGIN
	DECLARE cnt INT UNSIGNED;
	SELECT COUNT(*) INTO cnt FROM TicketItems
		INNER JOIN MenuItems ON TicketItems.menuItemQuickCode = MenuItems.quickCode
		WHERE groupId = grpId
		AND MenuItems.route = rte
		AND TicketItems.submitTime IS NOT NULL AND TicketItems.readyTime IS NULL
		AND (TicketItems.flag IS NULL OR UPPER(TicketItems.flag) = UPPER('updated'));
	RETURN cnt > 0;
END;
*/
UPDATE MenuItems SET price = 9.99 WHERE quickCode = titleToQuickCode('Onion Ring Tower');

CALL createUser('Guy', 'Ronald', 'rguy', '$2y$10$KZKxQy90YKcXWQj6NExGreWbKMWG6CoZajwEu6ZV95ru6I5/7SBE6', 65535);
CALL createUser('Boville', 'Howard', 'hboville', '$2y$10$KZKxQy90YKcXWQj6NExGreWbKMWG6CoZajwEu6ZV95ru6I5/7SBE6', 2);
CALL createUser('Adashek', 'Jonathan', 'jadashek', '$2y$10$KZKxQy90YKcXWQj6NExGreWbKMWG6CoZajwEu6ZV95ru6I5/7SBE6', 2);
CALL createUser('Krishna', 'Arvind', 'akrishna', '$2y$10$KZKxQy90YKcXWQj6NExGreWbKMWG6CoZajwEu6ZV95ru6I5/7SBE6', 2);

INSERT INTO Tickets (nickname, partySize, businessDay) VALUES 
('Short', 2, NOW()),
('Pribble', 4, NOW()),
('Mahan', 6, NOW()),
('Utshudiema', 8, NOW()),
('Rizzo', 10, NOW());

CALL login('rguy', 65535, 'nobodycares');
CALL login('hboville', 2, 'nobodycares1');
CALL login('jadashek', 2, 'nobodycares2');
CALL login('akrishna', 2, 'nobodycares3');

INSERT INTO TableLog (action, employeeId, tableId, authorizationId) VALUES 
('Add', idFromUsername('hboville') , 'S2', idFromUsername('rguy')),
('Add', idFromUsername('hboville') , 'S4', idFromUsername('rguy')),
('Add', idFromUsername('hboville') , 'S6', idFromUsername('rguy')),
('Add', idFromUsername('hboville') , 'L7', idFromUsername('rguy')),
('Add', idFromUsername('hboville') , 'L8', idFromUsername('rguy')),
('Add', idFromUsername('hboville') , 'L9', idFromUsername('rguy')),
('Add', idFromUsername('hboville') , 'L10', idFromUsername('rguy')),

('Add', idFromUsername('jadashek') , 'L1', idFromUsername('rguy')),
('Add', idFromUsername('jadashek') , 'L2', idFromUsername('rguy')),
('Add', idFromUsername('jadashek') , 'L3', idFromUsername('rguy')),
('Add', idFromUsername('jadashek') , 'L4', idFromUsername('rguy')),
('Add', idFromUsername('jadashek') , 'L5', idFromUsername('rguy')),
('Add', idFromUsername('jadashek') , 'L6', idFromUsername('rguy')),

('Add', idFromUsername('akrishna') , 'T13', idFromUsername('rguy')),
('Add', idFromUsername('akrishna') , 'T14', idFromUsername('rguy')),
('Add', idFromUsername('akrishna') , 'T15', idFromUsername('rguy')),
('Add', idFromUsername('akrishna') , 'T16', idFromUsername('rguy')),
('Add', idFromUsername('akrishna') , 'R1', idFromUsername('rguy')),
('Add', idFromUsername('akrishna') , 'R2', idFromUsername('rguy')),
('Add', idFromUsername('akrishna') , 'R3', idFromUsername('rguy')),
('Add', idFromUsername('akrishna') , 'R4', idFromUsername('rguy')),
('Add', idFromUsername('akrishna') , 'R5', idFromUsername('rguy')),
('Add', idFromUsername('akrishna') , 'R6', idFromUsername('rguy'));

INSERT INTO TableLog (action, tableId, authorizationId) VALUES 
('Disable', 'L10', idFromUsername('rguy')),
('Disable', 'L2', idFromUsername('rguy'));

INSERT INTO TableLog (action, ticketId, tableId, authorizationId) VALUES
('Add', 2, 'S6', idFromUsername('rguy')),
('Remove', 2, 'S6', idFromUsername('rguy')),
('Add', 2, 'S2', idFromUsername('rguy')),
('Add', 4, 'L9', idFromUsername('rguy')),
('Add', 5, 'L1', idFromUsername('rguy')),
('Add', 3, 'R2', idFromUsername('rguy')),
('Add', 1, 'T16', idFromUsername('rguy'));

UPDATE Tickets SET timeReserved = DATE_ADD(now(),interval -3 minute), timeRequested = DATE_ADD(now(),interval -3 minute) WHERE partySize = 2;
UPDATE Tickets SET timeReserved = DATE_ADD(now(),interval -10 minute), timeRequested = DATE_ADD(now(),interval -10 minute) WHERE partySize = 4;
UPDATE Tickets SET timeReserved = DATE_ADD(now(),interval -9 minute), timeRequested = DATE_ADD(now(),interval -9 minute) WHERE partySize = 6;
UPDATE Tickets SET timeReserved = DATE_ADD(now(),interval -12 minute), timeRequested = DATE_ADD(now(),interval -12 minute) WHERE partySize = 8;
UPDATE Tickets SET timeReserved = DATE_ADD(now(),interval -17 minute), timeRequested = DATE_ADD(now(),interval -17 minute) WHERE partySize = 10;

CALL createTicketItem(1, 1, 1, titleToQuickCode('Onion Ring Tower'));
CALL createTicketItem(1, 1, 1, titleToQuickCode('Coca-Cola'));
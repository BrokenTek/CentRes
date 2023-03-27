DELETE FROM TableShapes;
INSERT INTO `tableshapes` (`shapeName`, `svgPathData`, `capacity`) VALUES
('booth', '<g transform="TRANSFORMDATA" id="TABLEID" class="table unassigned booth"> <rect width="5" height="42"/> <rect width="15" height="42" transform="translate(6,0)"/> <rect width="5" height="42" transform="translate(22,0)"/> <rect width="22" height="42" opacity="0"/> </g>', 4),
('hightop', 'polygon id="TABLEID" class="table unassigned hightop" points="0 0 30 0 15,30" transform="TRANSFORMDATA"', 3),
('longtable', 'rect id="TABLEID" class="table unassigned longtable" width="100" height="47" transform="TRANSFORMDATA"', 8),
('round', 'circle id="TABLEID" class="table unassigned round" cx="35" cy="35" r="17.5" transform="TRANSFORMDATA"', 6),
('square', 'rect id="TABLEID" class="table unassigned square" width="34" height="34" transform="TRANSFORMDATA"', 4),
('twotop', '<g transform="TRANSFORMDATA" id="TABLEID" class="table unassigned twotop"> <rect width="15" height="18"/> <rect width="15" height="18" transform="translate(0,20)"/> <rect width="15" height="38" opacity="0"/> </g>', 2);



/* will be populating this */
DELETE FROM Tables;
INSERT INTO `tables` (`id`, `shape`, `transformData`) VALUES 
('R1', 'round',     'translate(1038, 24)'),
('R3', 'round',     'translate(1108, 24)'),
('R5', 'round',     'translate(1178, 24)'),
('R2', 'round',     'translate(1077, 77)'),
('R4', 'round',     'translate(1147, 77)'),
('R6', 'round',     'translate(1217, 77)'),

('L1', 'longtable', 'translate(1002, 238)'),
('L2', 'longtable', 'translate(1160, 238)'),
('L3', 'longtable', 'translate(1005, 330)'),
('L4', 'longtable', 'translate(1160, 330)'),
('L5', 'longtable', 'translate(1005, 420)'),
('L6', 'longtable', 'translate(1160, 420)'),
('L7', 'longtable', 'translate(600, 380)'),
('L8', 'longtable', 'translate(758, 380)'),
('L9', 'longtable', 'translate(600, 288)'),
('L10', 'longtable', 'translate(758, 288)'),

('S1', 'square',    'translate(350, 251)'),
('S2', 'square',    'translate(470, 251)'),
('S3', 'square',    'translate(350, 340)'),
('S4', 'square',    'translate(470, 340)'),
('S5', 'square',    'translate(350, 429)'),
('S6', 'square',    'translate(470, 429)'),
('S7', 'square',    'translate(350, 586)'),
('S8', 'square',    'translate(470, 586)'),
('S9', 'square',    'translate(310, 690)'),
('S10', 'square',   'translate(430, 690)'),

('T1', 'twotop',    'translate(280, 237)'),
('T2', 'twotop',    'translate(280, 309)'),
('T3', 'twotop',    'translate(280, 381)'),
('T4', 'twotop',    'translate(280, 453)'),
('T5', 'twotop',    'translate(280, 525)'),
('T6', 'twotop',    'translate(300, 86) rotate(90)'),

('T7', 'twotop',    'translate(360, 65) rotate(90)'),
('T8', 'twotop',    'translate(415, 65) rotate(90)'),
('T9', 'twotop',    'translate(470, 65) rotate(90)'),
('T10', 'twotop',   'translate(525, 65) rotate(90)'),
('T11', 'twotop',   'translate(580, 65) rotate(90)'),
('T12', 'twotop',   'translate(635, 65) rotate(90)'),

('T13', 'twotop',   'translate(815, 25) rotate(90)'),
('T14', 'twotop',   'translate(870, 25) rotate(90)'),
('T15', 'twotop',   'translate(925, 25) rotate(90)'),
('T16', 'twotop',   'translate(980, 25) rotate(90)'),

('T17', 'twotop',   'translate(575, 707) rotate(90)'),
('T18', 'twotop',   'translate(630, 707) rotate(90)'),
('T19', 'twotop',   'translate(685, 707) rotate(90)'),
('T20', 'twotop',   'translate(740, 707) rotate(90)'),
('T21', 'twotop',   'translate(795, 707) rotate(90)'),
('T22', 'twotop',   'translate(850, 707) rotate(90)'),

('T23', 'twotop',   'translate(575, 210) rotate(90)'),
('T24', 'twotop',   'translate(630, 210) rotate(90)'),
('T25', 'twotop',   'translate(685, 210) rotate(90)'),
('T26', 'twotop',   'translate(740, 210) rotate(90)'),
('T27', 'twotop',   'translate(795, 210) rotate(90)'),
('T28', 'twotop',   'translate(850, 210) rotate(90)'),

('H1', 'hightop',  'translate(535, 620)'),
('H2', 'hightop',  'translate(632, 650) rotate(180)'),
('H3', 'hightop',  'translate(667.5, 620)'),
('H4', 'hightop',  'translate(765, 650) rotate(180)'),
('H5', 'hightop',  'translate(790, 620)'),

('B1', 'booth', 'translate(537,147)'),
('B3', 'booth', 'translate(585,147)'),
('B4', 'booth', 'translate(633,147)'),
('B5', 'booth', 'translate(681,147)'),
('B6', 'booth', 'translate(729,147)'),
('B7', 'booth', 'translate(777,147)'),
('B8', 'booth', 'translate(825,147)'),

('B9', 'booth', 'translate(537,518)'),
('B10', 'booth', 'translate(585,518)'),
('B11', 'booth', 'translate(633,518)'),
('B12', 'booth', 'translate(681,518)'),
('B13', 'booth', 'translate(729,518)'),
('B14', 'booth', 'translate(777,518)'),
('B15', 'booth', 'translate(825,518)');
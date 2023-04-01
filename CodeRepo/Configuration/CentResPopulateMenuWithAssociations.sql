
/* POPULATES THE MENU WITH ROOT (MAIN) CATEGORIES, SUB CATEGORIES TO THOSE ROOT (MAIN) CATEGORIES, AND
   MENU ITEMS. ALL ASSOCIATIONS ARE MADE IN THIS SCRIPT */

/* Remove Existing Data In These Tables */
DELETE FROM MenuAssociations;
DELETE FROM MenuItems;
DELETE FROM MenuCategories;

/* Insert The Root For Association. Script For Populating The Menu Iframe Disregards The Category 'root' So
   It Will Not Display In The Server View */
INSERT INTO `menucategories` (`quickCode`, `title`, `description`, `route`, `visible`, `defaultPrice`)
 VALUES
('root', 'root', NULL, NULL, 1, NULL);

/* Insert Root Categories And Sub Categories */
INSERT INTO `menucategories` (`quickCode`, `title`, `description`, `route`, `visible`, `defaultPrice`)
 VALUES
('R000', 'Drinks', NULL, NULL, 1, NULL),
('R001', 'Appetizers', NULL, NULL, 1, NULL),
('R002', 'Sides', NULL, NULL, 1, NULL),
('R003', 'Entrees', NULL, NULL, 1, NULL),
('R004', 'Desserts', NULL, NULL, 1, NULL),
/* 'route' Of N Means Not Tracked By Default. NULLABLE */
('S005', 'Alcoholic Beverages', NULL, 'N', 1, NULL),
('S006', 'Sodas', NULL, 'N', 1, NULL),
('S007', 'Hot Beverages', NULL, 'N', 1, NULL),
('S008', 'Fried', NULL, 'N', 1, NULL),
('S009', 'Salads', NULL, 'N', 1, NULL),
('S010', 'Shareable', NULL, 'N', 1, NULL),
('S011', 'Grilled', NULL, 'N', 1, NULL),
('S012', 'Burgers', NULL, 'N', 1, NULL),
('S013', 'Seafood', NULL, 'N', 1, NULL),
('S014', 'Steak', NULL, 'N', 1, NULL),
('S015', 'Sandwiches', NULL, 'N', 1, NULL),
('S016', 'Ice Cream', NULL, 'N', 1, NULL),
('S017', 'Pie', NULL, 'N', 1, NULL),
('S018', 'Freshly Baked', NULL, 'N', 1, NULL);

/* Insert All Menu Items */
INSERT INTO `menuitems` (`quickCode`, `title`, `description`, `price`, `route`, `quantity`, `requests`, `prepTimeInSecs`, `visible`)
 VALUES
('I000', 'Heineken', NULL, '6.00', 'B', NULL, 0, NULL, 1),
('I001', 'Budweiser', NULL, '5.00', 'B', NULL, 0, NULL, 1),
('I002', 'Corona Extra', NULL, '6.00', 'B', NULL, 0, NULL, 1),
('I003', 'Stella Artois', NULL, '5.50', 'B', NULL, 0, NULL, 1),
('I004', 'Pabst Blue Ribbon', NULL, '4.00', 'B', NULL, 0, NULL, 1),
('I005', 'Miller Lite', NULL, '4.50', 'B', NULL, 0, NULL, 1),
('I006', 'Guinness Draught', NULL, '6.00', 'B', NULL, 0, NULL, 1),
('I007', 'Modelo Especial', NULL, '5.50', 'B', NULL, 0, NULL, 1),
('I008', 'Samuel Adams Boston Lager', NULL, '6.00', 'B', NULL, 0, NULL, 1),
('I009', 'Coors Light', NULL, '4.50', 'B', NULL, 0, NULL, 1),
('I010', 'Yuengling Lager', NULL, '5.50', 'B', NULL, 0, NULL, 1),
('I011', 'Sierra Nevada Pale Ale', NULL, '5.75', 'B', NULL, 0, NULL, 1),
('I012', 'Newcastle Brown Ale', NULL, '5.00', 'B', NULL, 0, NULL, 1),
('I013', 'Dogfish Head 60 Minute IPA', NULL, '6.25', 'B', NULL, 0, NULL, 1),
('I015', 'Lagunitas IPA', NULL, '6.00', 'B', NULL, 0, NULL, 1),
('I016', 'Coca-Cola', NULL, '3.50', 'B', NULL, 0, NULL, 1),
('I017', 'Coke Zero', NULL, '3.50', 'B', NULL, 0, NULL, 1),
('I019', 'Diet Coke', NULL, '3.50', 'B', NULL, 0, NULL, 1),
('I020', 'Sprite', NULL, '3.50', 'B', NULL, 0, NULL, 1),
('I021', 'Fanta Orange', NULL, '3.50', 'B', NULL, 0, NULL, 1),
('I022', 'Fanta Grape', NULL, '3.50', 'B', NULL, 0, NULL, 1),
('I024', 'Barqs Root Beer', NULL, '3.50', 'B', NULL, 0, NULL, 1),
('I025', 'Seagrams Ginger Ale', NULL, '4.00', 'B', NULL, 0, NULL, 1),
('I026', 'Fresca', NULL, '3.50', 'B', NULL, 0, NULL, 1),
('I027', 'Mello Yello', NULL, '3.50', 'B', NULL, 0, NULL, 1),
('I028', 'Green Tea', NULL, '4.50', 'B', NULL, 0, NULL, 1),
('I029', 'Black Tea', NULL, '4.00', 'B', NULL, 0, NULL, 1),
('I030', 'Sweet Tea', NULL, '3.50', 'B', NULL, 0, NULL, 1),
('I031', 'Unsweet Tea', NULL, '3.50', 'B', NULL, 0, NULL, 1),
('I033', 'Coffee', NULL, '4.00', 'B', NULL, 0, NULL, 1),
('I035', 'Mozzarella Sticks', NULL, '7.00', 'K', NULL, 0, NULL, 1),
('I036', 'Onion Ring Tower', NULL, '10.50', 'K', NULL, 0, NULL, 1),
('I037', 'Loaded Fries', NULL, '7.50', 'K', NULL, 0, NULL, 1),
('I038', 'Buffalo Cauliflower', NULL, '8.50', 'K', NULL, 0, NULL, 1),
('I039', 'French Fries', NULL, '3.50', 'K', NULL, 0, NULL, 1),
('I041', 'Onion Rings', NULL, '3.50', 'K', NULL, 0, NULL, 1),
('I042', 'Fresh Chips', NULL, '4.00', 'K', NULL, 0, NULL, 1),
('I043', 'Seasonable Veggies', NULL, '4.25', 'K', NULL, 0, NULL, 1),
('I045', 'Rice Pilaf', NULL, '4.00', 'K', NULL, 0, NULL, 1),
('I047', 'Mashed Potatos', NULL, '4.00', 'K', NULL, 0, NULL, 1),
('I048', 'Baked Potato', NULL, '6.50', 'K', NULL, 0, NULL, 1),
('I049', 'Blackened Chicken', NULL, '10.00', 'K', NULL, 0, NULL, 1),
('I050', 'Fajita Veggies Array', NULL, '9.50', 'K', NULL, 0, NULL, 1),
('I051', 'Honey Glazed Chicken', NULL, '10.75', 'K', NULL, 0, NULL, 1),
('I052', 'Royal Burger', NULL, '12.50', 'K', NULL, 0, NULL, 1),
('I053', 'Mushroom And Swiss', NULL, '10.75', 'K', NULL, 0, NULL, 1),
('I054', 'Carolina Burger', NULL, '11.00', 'K', NULL, 0, NULL, 1),
('I055', 'Bacon Cheeseburger', NULL, '12.00', 'K', NULL, 0, NULL, 1),
('I056', 'Honey Glazed Salmon', NULL, '11.00', 'K', NULL, 0, NULL, 1),
('I058', 'Blackened Tilapia', NULL, '10.00', 'K', NULL, 0, NULL, 1),
('I059', 'Jumbo Coconut Shrimp', NULL, '11.50', 'K', NULL, 0, NULL, 1),
('I060', 'Popcorn Shrimp', NULL, '9.50', 'K', NULL, 0, NULL, 1),
('I061', 'Ribeye', NULL, '16.00', 'K', NULL, 0, NULL, 1),
('I062', 'Filet Mignon', NULL, '18.50', 'K', NULL, 0, NULL, 1),
('I063', 'T-Bone', NULL, '15.00', 'K', NULL, 0, NULL, 1),
('I064', 'Top Sirloin', NULL, '17.75', 'K', NULL, 0, NULL, 1),
('I065', 'Fresh Pesto Flatbread', NULL, '10.00', 'K', NULL, 0, NULL, 1),
('I066', 'Royal BLT', NULL, '11.50', 'K', NULL, 0, NULL, 1),
('I068', 'Buffalo Chicken Sandwich', NULL, '10.75', 'K', NULL, 0, NULL, 1),
('I069', 'Grilled Chicken Sandwich', NULL, '12.00', 'K', NULL, 0, NULL, 1),
('I070', 'Chocolate', NULL, '6.00', 'K', NULL, 0, NULL, 1),
('I071', 'Vanilla', NULL, '6.00', 'K', NULL, 0, NULL, 1),
('I072', 'Moose Tracks', NULL, '6.00', 'K', NULL, 0, NULL, 1),
('I073', 'Oreo', NULL, '6.00', 'K', NULL, 0, NULL, 1),
('I075', 'Cookie Dough', NULL, '6.00', 'K', NULL, 0, NULL, 1),
('I076', 'Pecans and Pralines', NULL, '6.25', 'K', NULL, 0, NULL, 1),
('I077', 'Apple Pie', NULL, '6.50', 'K', NULL, 0, NULL, 1),
('I078', 'Cherry Pie', NULL, '6.50', 'K', NULL, 0, NULL, 1),
('I079', 'Keylime Pie', NULL, '6.50', 'K', NULL, 0, NULL, 1),
('I081', 'Cinnamon Bun', NULL, '6.25', 'K', NULL, 0, NULL, 1),
('I082', 'Apple Tart', NULL, '6.00', 'K', NULL, 0, NULL, 1),
('I083', 'Slice Of Cake', NULL, '5.50', 'K', NULL, 0, NULL, 1);

/* Create All Associations For Menu Root/Sub (Parent/Child) Categories And Menu Items */
INSERT INTO `menuassociations` (`parentQuickCode`, `childQuickCode`, `displayIndex`)
 VALUES
('root', 'R000', NULL),
('root', 'R001', NULL),
('root', 'R002', NULL),
('root', 'R003', NULL),
('root', 'R004', NULL),
('R000', 'S005', NULL),
('R000', 'S006', NULL),
('R000', 'S007', NULL),
('R001', 'S008', NULL),
('R001', 'S009', NULL),
('R001', 'S010', NULL),
('R003', 'S011', NULL),
('R003', 'S012', NULL),
('R003', 'S013', NULL),
('R003', 'S014', NULL),
('R003', 'S015', NULL),
('R004', 'S016', NULL),
('R004', 'S017', NULL),
('R004', 'S018', NULL),
('S005', 'I000', NULL),
('S005', 'I001', NULL),
('S005', 'I002', NULL),
('S005', 'I003', NULL),
('S005', 'I004', NULL),
('S005', 'I005', NULL),
('S005', 'I006', NULL),
('S005', 'I007', NULL),
('S005', 'I008', NULL),
('S005', 'I009', NULL),
('S005', 'I010', NULL),
('S005', 'I011', NULL),
('S005', 'I012', NULL),
('S005', 'I013', NULL),
('S005', 'I015', NULL),
('S006', 'I016', NULL),
('S006', 'I017', NULL),
('S006', 'I019', NULL),
('S006', 'I020', NULL),
('S006', 'I021', NULL),
('S006', 'I022', NULL),
('S006', 'I024', NULL),
('S006', 'I025', NULL),
('S006', 'I026', NULL),
('S006', 'I027', NULL),
('S007', 'I028', NULL),
('S007', 'I029', NULL),
('S007', 'I030', NULL),
('S007', 'I031', NULL),
('S007', 'I033', NULL),
('S008', 'I035', NULL),
('S008', 'I036', NULL),
('S008', 'I037', NULL),
('S008', 'I038', NULL),
('R002', 'I039', NULL),
('R002', 'I041', NULL),
('R002', 'I042', NULL),
('R002', 'I043', NULL),
('R002', 'I045', NULL),
('R002', 'I047', NULL),
('R002', 'I048', NULL),
('S011', 'I049', NULL),
('S011', 'I050', NULL),
('S011', 'I051', NULL),
('S012', 'I052', NULL),
('S012', 'I053', NULL),
('S012', 'I054', NULL),
('S012', 'I055', NULL),
('S013', 'I056', NULL),
('S013', 'I058', NULL),
('S013', 'I059', NULL),
('S013', 'I060', NULL),
('S014', 'I061', NULL),
('S014', 'I062', NULL),
('S014', 'I063', NULL),
('S014', 'I064', NULL),
('S015', 'I065', NULL),
('S015', 'I066', NULL),
('S015', 'I068', NULL),
('S015', 'I069', NULL),
('S016', 'I070', NULL),
('S016', 'I071', NULL),
('S016', 'I072', NULL),
('S016', 'I073', NULL),
('S016', 'I075', NULL),
('S016', 'I076', NULL),
('S017', 'I077', NULL),
('S017', 'I078', NULL),
('S017', 'I079', NULL),
('S018', 'I081', NULL),
('S018', 'I082', NULL),
('S018', 'I083', NULL);

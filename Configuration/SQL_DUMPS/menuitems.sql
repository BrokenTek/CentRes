-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2023 at 02:55 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `centres`
--

--
-- Dumping data for table `menuitems`
--

INSERT INTO `menuitems` (`quickCode`, `title`, `description`, `price`, `route`, `quantity`, `requests`, `prepTimeInSecs`, `visible`) VALUES
('2y10.cmuW1MwWZc0ssh/1V/KiuifnNYZfCIrH', 'Newcastle Brown Ale', NULL, '5.00', NULL, NULL, 0, NULL, 1),
('2y10.Dn0ca1KkS1Qgy6mN33p3uPtGaEElqTwg', 'Onion Ring Tower', NULL, '10.50', NULL, NULL, 0, NULL, 1),
('2y10.YOJJBL8q36ab6DW6cOEauSY5t59.hUn9', 'Green Tea', NULL, '4.50', NULL, NULL, 0, NULL, 1),
('2y10/0h2yytV1jXz4X5GcNOPAO11fV7c26g6s', 'Top Sirloin', NULL, '17.75', NULL, NULL, 0, NULL, 1),
('2y10/KlbFUpB6Se4p5jC.tn03e/1aJHnvLcmi', 'Honey Glazed Chicken', NULL, '10.75', NULL, NULL, 0, NULL, 1),
('2y10/RVfifL9Cs5G3vgXleHZA.tm984dRhX2A', 'Unsweet Tea', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y1017IN4Vzu0MFMnoKNkg11aeSXVD4Qi.E3z', 'Cherry Pie', NULL, '6.50', NULL, NULL, 0, NULL, 1),
('2y1023gBx9JV4.lNxKt.LSUpJ.pdPAmpMmNc/', 'Honey Glazed Salmon', NULL, '11.00', NULL, NULL, 0, NULL, 1),
('2y102axwAJqvWxV1A2UUcCtzEOzKwuEBVdcsp', 'Iced Unsweet Tea', NULL, '3.75', NULL, NULL, 0, NULL, 1),
('2y103e4d9X6VbQZKpx9o7jkoSueCdm/Z6GnAE', 'Mozzarella Sticks', NULL, '7.00', NULL, NULL, 0, NULL, 1),
('2y104IJieocoyeK3Tck4ekH4cedO/tAjOWNlj', 'Budweiser', NULL, '5.00', NULL, NULL, 0, NULL, 1),
('2y104YDwbGrCgdQNDbKWBQjUMeDBoyO29AbRe', 'Chocolate Chip Cookie', NULL, '4.50', NULL, NULL, 0, NULL, 1),
('2y105BlRqqYRy1pyxQo.YbilzuhxffUmuHQsY', 'Royal Burger', NULL, '12.50', NULL, NULL, 0, NULL, 1),
('2y1060BbkeieH1wwhN7GfZtQCeDryTJz4j.SG', 'Sugar Cookie', NULL, '4.25', NULL, NULL, 0, NULL, 1),
('2y106oOJNc8TM9hPpfLbWNibu.qvPSEjovh/u', 'Mushroom And Swiss', NULL, '10.75', NULL, NULL, 0, NULL, 1),
('2y106r0ggBmje0a5Z9PFvZA6ze/JPioAC17SY', 'Oreo', NULL, '6.00', NULL, NULL, 0, NULL, 1),
('2y106tcAV9bPd3/UPP9GkchCFejeCAnD.ZwdT', 'Ribeye', NULL, '16.00', NULL, NULL, 0, NULL, 1),
('2y107nzHKLcHAUCZPVmeN85KnuyCZ1K2USogU', 'Blackened Tilapia', NULL, '10.00', NULL, NULL, 0, NULL, 1),
('2y107qC9YSbVOGH590qAPHd8wuA6wDp1w0A6f', 'Coke Zero', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y107rpJShCo4bGie9LtdPtzbeU0NDUmg0Qy/', 'Buffalo Cauliflower', NULL, '8.50', NULL, NULL, 0, NULL, 1),
('2y108FWfhLJnB9tOrV/tVI5Z1.vnVxptq7shq', 'Oatmeal Cookie', NULL, '4.50', NULL, NULL, 0, NULL, 1),
('2y108gDEuyN6uryISY8YM8SuCucsMLrD4krdq', 'Guinness Draught', NULL, '6.00', NULL, NULL, 0, NULL, 1),
('2y108Wih3TY7rLkSFyXznxQWO.4zcBgJYGYIB', 'Coors Light', NULL, '4.50', NULL, NULL, 0, NULL, 1),
('2y10C9EIH5.5C2JxGH/e8.ovS.OihCvRPJR9/', 'Mashed Potatoes', NULL, '4.00', NULL, NULL, 0, NULL, 1),
('2y10CTuZ29nN01bM6M1IpMKnme6B1aaRW/U09', 'Dogfish Head IPA', NULL, '6.25', NULL, NULL, 0, NULL, 1),
('2y10DGNYP2hRV57LlKf4nyrVvOEFy1DU9lgEP', 'Chocolate', NULL, '6.00', NULL, NULL, 0, NULL, 1),
('2y10DuvfHqHd87x0qha3voAFYeDmSndjvTxDD', 'Yuengling Lager', NULL, '5.50', NULL, NULL, 0, NULL, 1),
('2y10DYOB8TXJ7sX1PTTXlyulmeVlYresZeMGC', 'Seasonable Veggies', NULL, '4.25', NULL, NULL, 0, NULL, 1),
('2y10dyYk9a./BWUEBOMsqYhXguU2xJmuWY2G2', 'Fresca', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y10eDOFeJi1TkozHQSlFTMaReWlrqbVvbvrH', 'Pecans and Pralines', NULL, '6.25', NULL, NULL, 0, NULL, 1),
('2y10EKwmLRGdyPYqGS863z..TetMjwMjVe9zM', 'Seagrams Ginger Ale', NULL, '4.00', NULL, NULL, 0, NULL, 1),
('2y10EMnbdNNpkpUzbxyhTe2iYeuZPE21oAnOP', 'Carolina Burger', NULL, '11.00', NULL, NULL, 0, NULL, 1),
('2y10fMpeoPvt7PEb8CDUG70lBebM3slsslxb8', 'Samuel Adams Boston Lager', NULL, '6.00', NULL, NULL, 0, NULL, 1),
('2y10fpcwXItRFQCgwNWZ79wTauSQTnQFS8Igp', 'Moose Tracks', NULL, '6.00', NULL, NULL, 0, NULL, 1),
('2y10G1wOqOnNn2DAvpFSMHE1sepgoemszUUsQ', 'Sweet Tea', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y10GiYfjJRP9DY8AvJFwpVdmeNcpnMKPWA.H', 'Apple Tart', NULL, '6.00', NULL, NULL, 0, NULL, 1),
('2y10Gm/tMLtJD2FwAQl7nHjw2uRk/q0xidkh1', 'Sprite', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y10gvGo3gtgjZLK44F7tM2KLuj9kfIzstx/e', 'Heineken', NULL, '6.00', NULL, NULL, 0, NULL, 1),
('2y10hOMeqKDKoUYiqhfsYQIGX.6fX8QzelYwV', 'T-Bone', NULL, '15.00', NULL, NULL, 0, NULL, 1),
('2y10HZXQ7SfSGOFZMpnZySS7Eu.R4JN9efUcB', 'Lagunitas IPA', NULL, '6.00', NULL, NULL, 0, NULL, 1),
('2y10IKER7mmGSxQWsVQZf8zBseTKRHOVKiWqZ', 'Bacon Cheeseburger', NULL, '12.00', NULL, NULL, 0, NULL, 1),
('2y10IxDxaBx7NdzXR5qvESmAMulKUD1t2lZOX', 'Onion Rings', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y10JS1DB182DtVa/1XwwREO9ebiz67A978HL', 'Diet Coke', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y10kJRXUhzqHg1Oqkk3zIYWLOw8O7Mf2IJLm', 'Blackened Chicken', NULL, '10.00', NULL, NULL, 0, NULL, 1),
('2y10kUzWLoOnr5sLgHMmu80JfOFZZzp/RjhlA', 'Cookie Dough', NULL, '6.00', NULL, NULL, 0, NULL, 1),
('2y10lA64Zn2DLweQw04UL5Q74uvCOtjaJ/8/d', 'Fanta Orange', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y10LgtqckDgkHIb8Yf4mVOu9.983z.1gajXe', 'Cinnamon Bun', NULL, '6.25', NULL, NULL, 0, NULL, 1),
('2y10lLja5xRnUBHLc1BJpWJq2.2O6rlX.pdgg', 'Grilled Chicken Sandwich', NULL, '12.00', NULL, NULL, 0, NULL, 1),
('2y10ln3jBRtMDiHLNv8dliQJF.vok./FQlNpE', 'Rice Pilaf', NULL, '4.00', NULL, NULL, 0, NULL, 1),
('2y10lrpgE1/i2PWVWMg.sjjMtebDIwZ6oAOvf', 'Miller Lite', NULL, '4.50', NULL, NULL, 0, NULL, 1),
('2y10LWMA9zNTbMA4CrbJJzvODu7U5C605pHpG', 'French Fries', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y10mk9pDdquOa5Aj6MM67lL0Oi5XZoRiEuaR', 'Filet Mignon', NULL, '18.50', NULL, NULL, 0, NULL, 1),
('2y10mPV2H.ptqJGfysVchfd8Q.wdAAHClWY9k', 'Modelo Especial', NULL, '5.50', NULL, NULL, 0, NULL, 1),
('2y10N.XcRWGpuHaB8bgU1ay27.RKqsy.Xux3B', 'Apple Pie', NULL, '6.50', NULL, NULL, 0, NULL, 1),
('2y10naXPXfTO2tTqbYhSxm1Wguz4jh0q/yVlc', 'Sierra Nevada Pale Ale', NULL, '5.75', NULL, NULL, 0, NULL, 1),
('2y10o.jQTFOxVkdjFEz1TWWTh.FSSjQzpsCJs', 'Fajita Veggies Array', NULL, '9.50', NULL, NULL, 0, NULL, 1),
('2y10OoPcOc7Pdb0FV8lix2S3IeXkTAjn9FrxC', 'Buffalo Chicken Sandwich', NULL, '10.75', NULL, NULL, 0, NULL, 1),
('2y10p6ltVSNU.oVAefZwP1YFduAV8cv726WUj', 'Stella Artois', NULL, '5.50', NULL, NULL, 0, NULL, 1),
('2y10qomYbI1LdLNik5bl4wFxyOjkVLmmlYMn.', 'Jumbo Coconut Shrimp', NULL, '11.50', NULL, NULL, 0, NULL, 1),
('2y10qVMlYt.HkBJXliBothgCxeOYKjBKmpgng', 'Coffee', NULL, '4.00', NULL, NULL, 0, NULL, 1),
('2y10r.ZDkxjDfsK7B38quIl8IuZUPI/uCILwl', 'Loaded Fries', NULL, '7.50', NULL, NULL, 0, NULL, 1),
('2y10rJkeiprCt2/DYIV5EeXrn.JQ/eCABybgp', 'Slice Of Cake', NULL, '5.50', NULL, NULL, 0, NULL, 1),
('2y10SdpOwHVwUuuDRB/v1jJn8.6hpyPRJOhTc', 'Iced Sweet Tea', NULL, '3.75', NULL, NULL, 0, NULL, 1),
('2y10Tghs8krIwndemo.BHhP5geBEF9hsFj71x', 'Mello Yello', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y10TXEnhUoadIsWgSGF2xJV8.6bLMavmhFaj', 'Keylime Pie', NULL, '6.50', NULL, NULL, 0, NULL, 1),
('2y10UEM0EDuqXQH1U5gwHlISu.PVcHHVqX5/U', 'Fanta Grape', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y10uH2JQSfHFrOc4eHxzXNn9uKVjCBrbM3f9', 'Corona Extra', NULL, '6.00', NULL, NULL, 0, NULL, 1),
('2y10ujsv9MgC47HKNH2E4PF7bu4ChY3o3S1Kv', 'Black Tea', NULL, '4.00', NULL, NULL, 0, NULL, 1),
('2y10V79oxMLUCYcBTGHk/tsC0.hCeI9POah4o', 'Royal BLT', NULL, '11.50', NULL, NULL, 0, NULL, 1),
('2y10XFPeA.I5NcUZOOKb1l5sEuQhZ1PyWO.Oy', 'Fresh Pesto Flatbread', NULL, '10.00', NULL, NULL, 0, NULL, 1),
('2y10xNv8r9XvmXlUk8S1X2/bJegLj.FZn07iX', 'Baked Potato', NULL, '6.50', NULL, NULL, 0, NULL, 1),
('2y10xPMhMGyIOGTb3aSDew2vT.d3QuH/xNYg/', 'Barqs Root Beer', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y10xrgH7nQOayQS3xWPvagxKOlZtS6FOtsNa', 'Popcorn Shrimp', NULL, '9.50', NULL, NULL, 0, NULL, 1),
('2y10XsOpk7AjVumWRtHaX8sKp.FUJuY6BLQdG', 'Pabst Blue Ribbon', NULL, '4.00', NULL, NULL, 0, NULL, 1),
('2y10XVeWZX726GP1KRucPCAYK.2lKwbqbTp0/', 'Vanilla', NULL, '6.00', NULL, NULL, 0, NULL, 1),
('2y10YwE4Q3.2LLQX59E/u7r5AuV3KWsrcxKTi', 'Coca-Cola', NULL, '3.50', NULL, NULL, 0, NULL, 1),
('2y10Zf5ntVAKVmrjto/UBlmlqe0BZQeQjljy8', 'Fresh Chips', NULL, '4.00', NULL, NULL, 0, NULL, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

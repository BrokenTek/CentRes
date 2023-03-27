-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 14, 2023 at 02:54 PM
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
-- Dumping data for table `menuassociations`
--

INSERT INTO `menuassociations` (`parentQuickCode`, `childQuickCode`, `displayIndex`) VALUES
('root', '$2y$10$KjMBtYiReLE3iNkz/o2/k..xXeu1IbCph', NULL),
('root', '$2y$10$j1/Ka7Ryt0X3GpET6C49sOkS6LuJm9aZw', NULL),
('root', '$2y$10$HeDT59nkyp/yw0NCvK/VUOQu6S1wGzwMW', NULL),
('root', '$2y$10$Z1G6yOvdARbDAxATivFi9./9tvJTUCuO.', NULL),
('root', '$2y$10$u0nbkBX88uEsYg8RkvWgm.j7QQjCxjsR7', NULL),
('$2y$10$KjMBtYiReLE3iNkz/o2/k..xXeu1IbCph', '$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', NULL),
('$2y$10$KjMBtYiReLE3iNkz/o2/k..xXeu1IbCph', '$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', NULL),
('$2y$10$KjMBtYiReLE3iNkz/o2/k..xXeu1IbCph', '$2y$10$1mV4pBeI6AnswDVCyL2rz.o9WYNJng.ap', NULL),
('$2y$10$j1/Ka7Ryt0X3GpET6C49sOkS6LuJm9aZw', '$2y$10$lM/p6cmZx/Y/zBatelnjUuDTzfrpvTI93', NULL),
('$2y$10$j1/Ka7Ryt0X3GpET6C49sOkS6LuJm9aZw', '$2y$10$Mx3Nj4rPLSeFjacaR70pGuYDr7ht9/cZp', NULL),
('$2y$10$j1/Ka7Ryt0X3GpET6C49sOkS6LuJm9aZw', '$2y$10$56x/NbTRlygAxmqax/5Drek3MweNRgxoh', NULL),
('$2y$10$Z1G6yOvdARbDAxATivFi9./9tvJTUCuO.', '$2y$10$PUHmLUml7e.m9Uz0xH3m3ezHz3.Wugfj4', NULL),
('$2y$10$Z1G6yOvdARbDAxATivFi9./9tvJTUCuO.', '$2y$10$7wdqT6WnK5FikkB6nyImRexPjtJXLzSNi', NULL),
('$2y$10$Z1G6yOvdARbDAxATivFi9./9tvJTUCuO.', '$2y$10$jgjiXsKoEyLnfsC9GobT8.OIshiBYksHh', NULL),
('$2y$10$Z1G6yOvdARbDAxATivFi9./9tvJTUCuO.', '$2y$10$vEO1WFFbKg4DzHujvYHlLOszyst7NtTWg', NULL),
('$2y$10$Z1G6yOvdARbDAxATivFi9./9tvJTUCuO.', '$2y$10$u7H7SCudXsPJtrfTx88RTO9Hhx4WZC1L8', NULL),
('$2y$10$u0nbkBX88uEsYg8RkvWgm.j7QQjCxjsR7', '$2y$10$0Ecb/ZGc3pd/ESXhTq.xMe1buPG4uFEoR', NULL),
('$2y$10$u0nbkBX88uEsYg8RkvWgm.j7QQjCxjsR7', '$2y$10$1lfEMeKv5bngHt7qMBX.au3umk0xKkTy9', NULL),
('$2y$10$u0nbkBX88uEsYg8RkvWgm.j7QQjCxjsR7', '$2y$10$ubmoNa0xafSV2ttaOmjK2eIh4wtKOpBz7', NULL),
('$2y$10$ubmoNa0xafSV2ttaOmjK2eIh4wtKOpBz7', '$2y$10$l6rCRoNhKzMwAyVP2KK6TurfGZ7MRjpbG', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$gvGo3gtgjZLK44F7tM2KLuj9kfIzstx/e', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$4IJieocoyeK3Tck4ekH4cedO/tAjOWNlj', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$uH2JQSfHFrOc4eHxzXNn9uKVjCBrbM3f9', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$p6ltVSNU.oVAefZwP1YFduAV8cv726WUj', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$XsOpk7AjVumWRtHaX8sKp.FUJuY6BLQdG', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$lrpgE1/i2PWVWMg.sjjMtebDIwZ6oAOvf', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$8gDEuyN6uryISY8YM8SuCucsMLrD4krdq', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$mPV2H.ptqJGfysVchfd8Q.wdAAHClWY9k', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$fMpeoPvt7PEb8CDUG70lBebM3slsslxb8', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$8Wih3TY7rLkSFyXznxQWO.4zcBgJYGYIB', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$DuvfHqHd87x0qha3voAFYeDmSndjvTxDD', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$naXPXfTO2tTqbYhSxm1Wguz4jh0q/yVlc', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$.cmuW1MwWZc0ssh/1V/KiuifnNYZfCIrH', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$CTuZ29nN01bM6M1IpMKnme6B1aaRW/U09', NULL),
('$2y$10$gBplGaW9Bt/JiwZgXa1JAOvDvG9lJzC2q', '$2y$10$HZXQ7SfSGOFZMpnZySS7Eu.R4JN9efUcB', NULL),
('$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', '$2y$10$YwE4Q3.2LLQX59E/u7r5AuV3KWsrcxKTi', NULL),
('$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', '$2y$10$7qC9YSbVOGH590qAPHd8wuA6wDp1w0A6f', NULL),
('$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', '$2y$10$JS1DB182DtVa/1XwwREO9ebiz67A978HL', NULL),
('$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', '$2y$10$Gm/tMLtJD2FwAQl7nHjw2uRk/q0xidkh1', NULL),
('$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', '$2y$10$lA64Zn2DLweQw04UL5Q74uvCOtjaJ/8/d', NULL),
('$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', '$2y$10$UEM0EDuqXQH1U5gwHlISu.PVcHHVqX5/U', NULL),
('$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', '$2y$10$xPMhMGyIOGTb3aSDew2vT.d3QuH/xNYg/', NULL),
('$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', '$2y$10$EKwmLRGdyPYqGS863z..TetMjwMjVe9zM', NULL),
('$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', '$2y$10$dyYk9a./BWUEBOMsqYhXguU2xJmuWY2G2', NULL),
('$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', '$2y$10$Tghs8krIwndemo.BHhP5geBEF9hsFj71x', NULL),
('$2y$10$1mV4pBeI6AnswDVCyL2rz.o9WYNJng.ap', '$2y$10$.YOJJBL8q36ab6DW6cOEauSY5t59.hUn9', NULL),
('$2y$10$1mV4pBeI6AnswDVCyL2rz.o9WYNJng.ap', '$2y$10$ujsv9MgC47HKNH2E4PF7bu4ChY3o3S1Kv', NULL),
('$2y$10$1mV4pBeI6AnswDVCyL2rz.o9WYNJng.ap', '$2y$10$G1wOqOnNn2DAvpFSMHE1sepgoemszUUsQ', NULL),
('$2y$10$1mV4pBeI6AnswDVCyL2rz.o9WYNJng.ap', '$2y$10$/RVfifL9Cs5G3vgXleHZA.tm984dRhX2A', NULL),
('$2y$10$1mV4pBeI6AnswDVCyL2rz.o9WYNJng.ap', '$2y$10$qVMlYt.HkBJXliBothgCxeOYKjBKmpgng', NULL),
('$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', '$2y$10$SdpOwHVwUuuDRB/v1jJn8.6hpyPRJOhTc', NULL),
('$2y$10$yEJ/Kw1mzuLW8ODRuAirzOfjfmuWPKQsJ', '$2y$10$2axwAJqvWxV1A2UUcCtzEOzKwuEBVdcsp', NULL),
('$2y$10$lM/p6cmZx/Y/zBatelnjUuDTzfrpvTI93', '$2y$10$3e4d9X6VbQZKpx9o7jkoSueCdm/Z6GnAE', NULL),
('$2y$10$lM/p6cmZx/Y/zBatelnjUuDTzfrpvTI93', '$2y$10$.Dn0ca1KkS1Qgy6mN33p3uPtGaEElqTwg', NULL),
('$2y$10$lM/p6cmZx/Y/zBatelnjUuDTzfrpvTI93', '$2y$10$r.ZDkxjDfsK7B38quIl8IuZUPI/uCILwl', NULL),
('$2y$10$lM/p6cmZx/Y/zBatelnjUuDTzfrpvTI93', '$2y$10$7rpJShCo4bGie9LtdPtzbeU0NDUmg0Qy/', NULL),
('$2y$10$HeDT59nkyp/yw0NCvK/VUOQu6S1wGzwMW', '$2y$10$LWMA9zNTbMA4CrbJJzvODu7U5C605pHpG', NULL),
('$2y$10$HeDT59nkyp/yw0NCvK/VUOQu6S1wGzwMW', '$2y$10$IxDxaBx7NdzXR5qvESmAMulKUD1t2lZOX', NULL),
('$2y$10$HeDT59nkyp/yw0NCvK/VUOQu6S1wGzwMW', '$2y$10$Zf5ntVAKVmrjto/UBlmlqe0BZQeQjljy8', NULL),
('$2y$10$HeDT59nkyp/yw0NCvK/VUOQu6S1wGzwMW', '$2y$10$DYOB8TXJ7sX1PTTXlyulmeVlYresZeMGC', NULL),
('$2y$10$HeDT59nkyp/yw0NCvK/VUOQu6S1wGzwMW', '$2y$10$ln3jBRtMDiHLNv8dliQJF.vok./FQlNpE', NULL),
('$2y$10$HeDT59nkyp/yw0NCvK/VUOQu6S1wGzwMW', '$2y$10$C9EIH5.5C2JxGH/e8.ovS.OihCvRPJR9/', NULL),
('$2y$10$HeDT59nkyp/yw0NCvK/VUOQu6S1wGzwMW', '$2y$10$xNv8r9XvmXlUk8S1X2/bJegLj.FZn07iX', NULL),
('$2y$10$PUHmLUml7e.m9Uz0xH3m3ezHz3.Wugfj4', '$2y$10$kJRXUhzqHg1Oqkk3zIYWLOw8O7Mf2IJLm', NULL),
('$2y$10$PUHmLUml7e.m9Uz0xH3m3ezHz3.Wugfj4', '$2y$10$o.jQTFOxVkdjFEz1TWWTh.FSSjQzpsCJs', NULL),
('$2y$10$PUHmLUml7e.m9Uz0xH3m3ezHz3.Wugfj4', '$2y$10$/KlbFUpB6Se4p5jC.tn03e/1aJHnvLcmi', NULL),
('$2y$10$7wdqT6WnK5FikkB6nyImRexPjtJXLzSNi', '$2y$10$5BlRqqYRy1pyxQo.YbilzuhxffUmuHQsY', NULL),
('$2y$10$7wdqT6WnK5FikkB6nyImRexPjtJXLzSNi', '$2y$10$6oOJNc8TM9hPpfLbWNibu.qvPSEjovh/u', NULL),
('$2y$10$7wdqT6WnK5FikkB6nyImRexPjtJXLzSNi', '$2y$10$EMnbdNNpkpUzbxyhTe2iYeuZPE21oAnOP', NULL),
('$2y$10$7wdqT6WnK5FikkB6nyImRexPjtJXLzSNi', '$2y$10$IKER7mmGSxQWsVQZf8zBseTKRHOVKiWqZ', NULL),
('$2y$10$jgjiXsKoEyLnfsC9GobT8.OIshiBYksHh', '$2y$10$23gBx9JV4.lNxKt.LSUpJ.pdPAmpMmNc/', NULL),
('$2y$10$jgjiXsKoEyLnfsC9GobT8.OIshiBYksHh', '$2y$10$7nzHKLcHAUCZPVmeN85KnuyCZ1K2USogU', NULL),
('$2y$10$jgjiXsKoEyLnfsC9GobT8.OIshiBYksHh', '$2y$10$qomYbI1LdLNik5bl4wFxyOjkVLmmlYMn.', NULL),
('$2y$10$jgjiXsKoEyLnfsC9GobT8.OIshiBYksHh', '$2y$10$xrgH7nQOayQS3xWPvagxKOlZtS6FOtsNa', NULL),
('$2y$10$vEO1WFFbKg4DzHujvYHlLOszyst7NtTWg', '$2y$10$6tcAV9bPd3/UPP9GkchCFejeCAnD.ZwdT', NULL),
('$2y$10$vEO1WFFbKg4DzHujvYHlLOszyst7NtTWg', '$2y$10$mk9pDdquOa5Aj6MM67lL0Oi5XZoRiEuaR', NULL),
('$2y$10$vEO1WFFbKg4DzHujvYHlLOszyst7NtTWg', '$2y$10$hOMeqKDKoUYiqhfsYQIGX.6fX8QzelYwV', NULL),
('$2y$10$vEO1WFFbKg4DzHujvYHlLOszyst7NtTWg', '$2y$10$/0h2yytV1jXz4X5GcNOPAO11fV7c26g6s', NULL),
('$2y$10$u7H7SCudXsPJtrfTx88RTO9Hhx4WZC1L8', '$2y$10$XFPeA.I5NcUZOOKb1l5sEuQhZ1PyWO.Oy', NULL),
('$2y$10$u7H7SCudXsPJtrfTx88RTO9Hhx4WZC1L8', '$2y$10$V79oxMLUCYcBTGHk/tsC0.hCeI9POah4o', NULL),
('$2y$10$u7H7SCudXsPJtrfTx88RTO9Hhx4WZC1L8', '$2y$10$OoPcOc7Pdb0FV8lix2S3IeXkTAjn9FrxC', NULL),
('$2y$10$u7H7SCudXsPJtrfTx88RTO9Hhx4WZC1L8', '$2y$10$lLja5xRnUBHLc1BJpWJq2.2O6rlX.pdgg', NULL),
('$2y$10$0Ecb/ZGc3pd/ESXhTq.xMe1buPG4uFEoR', '$2y$10$DGNYP2hRV57LlKf4nyrVvOEFy1DU9lgEP', NULL),
('$2y$10$0Ecb/ZGc3pd/ESXhTq.xMe1buPG4uFEoR', '$2y$10$XVeWZX726GP1KRucPCAYK.2lKwbqbTp0/', NULL),
('$2y$10$0Ecb/ZGc3pd/ESXhTq.xMe1buPG4uFEoR', '$2y$10$fpcwXItRFQCgwNWZ79wTauSQTnQFS8Igp', NULL),
('$2y$10$0Ecb/ZGc3pd/ESXhTq.xMe1buPG4uFEoR', '$2y$10$6r0ggBmje0a5Z9PFvZA6ze/JPioAC17SY', NULL),
('$2y$10$0Ecb/ZGc3pd/ESXhTq.xMe1buPG4uFEoR', '$2y$10$kUzWLoOnr5sLgHMmu80JfOFZZzp/RjhlA', NULL),
('$2y$10$0Ecb/ZGc3pd/ESXhTq.xMe1buPG4uFEoR', '$2y$10$eDOFeJi1TkozHQSlFTMaReWlrqbVvbvrH', NULL),
('$2y$10$1lfEMeKv5bngHt7qMBX.au3umk0xKkTy9', '$2y$10$N.XcRWGpuHaB8bgU1ay27.RKqsy.Xux3B', NULL),
('$2y$10$1lfEMeKv5bngHt7qMBX.au3umk0xKkTy9', '$2y$10$17IN4Vzu0MFMnoKNkg11aeSXVD4Qi.E3z', NULL),
('$2y$10$1lfEMeKv5bngHt7qMBX.au3umk0xKkTy9', '$2y$10$TXEnhUoadIsWgSGF2xJV8.6bLMavmhFaj', NULL),
('$2y$10$ubmoNa0xafSV2ttaOmjK2eIh4wtKOpBz7', '$2y$10$LgtqckDgkHIb8Yf4mVOu9.983z.1gajXe', NULL),
('$2y$10$ubmoNa0xafSV2ttaOmjK2eIh4wtKOpBz7', '$2y$10$GiYfjJRP9DY8AvJFwpVdmeNcpnMKPWA.H', NULL),
('$2y$10$ubmoNa0xafSV2ttaOmjK2eIh4wtKOpBz7', '$2y$10$rJkeiprCt2/DYIV5EeXrn.JQ/eCABybgp', NULL),
('$2y$10$l6rCRoNhKzMwAyVP2KK6TurfGZ7MRjpbG', '$2y$10$4YDwbGrCgdQNDbKWBQjUMeDBoyO29AbRe', NULL),
('$2y$10$l6rCRoNhKzMwAyVP2KK6TurfGZ7MRjpbG', '$2y$10$60BbkeieH1wwhN7GfZtQCeDryTJz4j.SG', NULL),
('$2y$10$l6rCRoNhKzMwAyVP2KK6TurfGZ7MRjpbG', '$2y$10$8FWfhLJnB9tOrV/tVI5Z1.vnVxptq7shq', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

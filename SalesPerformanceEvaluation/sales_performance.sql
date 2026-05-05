-- Sales Performance Evaluation System
-- Fixed SQL Schema
-- Fixed: Column name mismatches (empId→emp_Id, empRole→emp_Role, empPassword→emp_Password)
-- Fixed: Broken INSERT syntax (two semicolons between rows)
-- Fixed: varchar(20) too short for email fields → varchar(100)
-- Fixed: emp_Password varchar(20) too short for hashed passwords → varchar(255)
-- Fixed: Missing invoice table that DataCashier.php inserts into
-- Fixed: Added login table that Login.php queries

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Drop tables in reverse dependency order for clean re-import
DROP TABLE IF EXISTS `invoice`;
DROP TABLE IF EXISTS `totalsales`;
DROP TABLE IF EXISTS `performance`;
DROP TABLE IF EXISTS `target`;
DROP TABLE IF EXISTS `cashier`;
DROP TABLE IF EXISTS `employee`;
DROP TABLE IF EXISTS `branch`;

-- --------------------------------------------------------
-- Table: branch
-- --------------------------------------------------------
CREATE TABLE `branch` (
  `br_Id`      VARCHAR(20)  NOT NULL,
  `br_Name`    VARCHAR(50)  NOT NULL,
  `br_Address` VARCHAR(100) NOT NULL,
  `br_Tp`      VARCHAR(20)  NOT NULL,
  `br_Email`   VARCHAR(100) NOT NULL,   -- Fixed: was VARCHAR(20), too short for emails
  `e_id`       VARCHAR(20)  NOT NULL,
  PRIMARY KEY (`br_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `branch` (`br_Id`, `br_Name`, `br_Address`, `br_Tp`, `br_Email`, `e_id`) VALUES
('01', 'Rathnapura', 'New Town,Rathnapura', '0456768096', 'rathnapura@lifebackup.com', '1');

-- --------------------------------------------------------
-- Table: employee
-- --------------------------------------------------------
CREATE TABLE `employee` (
  `emp_Id`       VARCHAR(20)  NOT NULL,
  `br_Id`        VARCHAR(20)  NOT NULL,
  `emp_Role`     VARCHAR(30)  NOT NULL,
  `emp_Password` VARCHAR(255) NOT NULL,  -- Fixed: was VARCHAR(20), must fit hashed passwords
  PRIMARY KEY (`emp_Id`),
  KEY `fk_branch_employee` (`br_Id`),
  CONSTRAINT `fk_branch_employee` FOREIGN KEY (`br_Id`) REFERENCES `branch` (`br_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Fixed: was using wrong column names (empId, empRole) instead of emp_Id, emp_Role
-- Fixed: rows 5 and 6 had stray semicolons between them breaking the INSERT
INSERT INTO `employee` (`emp_Id`, `br_Id`, `emp_Role`, `emp_Password`) VALUES
('00001', '01', 'SalesAgent',       'Agent@123'),
('00002', '01', 'InsurenceAdvisor', 'Advisor@123'),
('00003', '01', 'TeamLead',         'Lead@123'),
('00004', '01', 'Supervisor',       'Supervisor@123'),
('00005', '01', 'BranchManager',    'Manager@123'),
('00006', '01', 'Cashier',          'Cashier@123');

-- --------------------------------------------------------
-- Table: cashier
-- --------------------------------------------------------
CREATE TABLE `cashier` (
  `cashier_Id`      VARCHAR(20)  NOT NULL,
  `cashier_Name`    VARCHAR(50)  NOT NULL,
  `cashier_Address` VARCHAR(100) NOT NULL,
  `cashier_TP`      VARCHAR(20)  NOT NULL,
  `emp_Email`       VARCHAR(100) NOT NULL,   -- Fixed: was emp_Emaill (typo) and VARCHAR(20)
  `br_Id`           VARCHAR(20)  NOT NULL,
  `emp_Id`          VARCHAR(20)  NOT NULL,
  PRIMARY KEY (`cashier_Id`),
  KEY `fk_cashier_branch` (`br_Id`),
  KEY `fk_cashier_emp` (`emp_Id`),
  CONSTRAINT `fk_cashier_branch` FOREIGN KEY (`br_Id`) REFERENCES `branch` (`br_Id`),
  CONSTRAINT `fk_cashier_emp`    FOREIGN KEY (`emp_Id`) REFERENCES `employee` (`emp_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: performance
-- --------------------------------------------------------
CREATE TABLE `performance` (
  `per_Id` VARCHAR(20) NOT NULL,
  `status` INT(11)     NOT NULL,
  `emp_Id` VARCHAR(20) NOT NULL,
  `br_Id`  VARCHAR(20) NOT NULL,
  PRIMARY KEY (`per_Id`),
  KEY `fk_perf_emp` (`emp_Id`),
  KEY `fk_perf_br`  (`br_Id`),
  CONSTRAINT `fk_perf_emp` FOREIGN KEY (`emp_Id`) REFERENCES `employee` (`emp_Id`),
  CONSTRAINT `fk_perf_br`  FOREIGN KEY (`br_Id`)  REFERENCES `branch` (`br_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: target
-- --------------------------------------------------------
CREATE TABLE `target` (
  `tr_Id`       VARCHAR(20)  NOT NULL,
  `amount`      VARCHAR(100) NOT NULL,
  `assigned_By` VARCHAR(20)  NOT NULL,
  `start_Time`  DATE         NOT NULL,
  `end_Time`    DATE         NOT NULL,
  `status`      VARCHAR(10)  NOT NULL,
  `br_Id`       VARCHAR(20)  NOT NULL,
  PRIMARY KEY (`tr_Id`),
  KEY `fk_target_br` (`br_Id`),
  CONSTRAINT `fk_target_br` FOREIGN KEY (`br_Id`) REFERENCES `branch` (`br_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: totalsales
-- --------------------------------------------------------
CREATE TABLE `totalsales` (
  `sale_id`   VARCHAR(20) NOT NULL,
  `sale_Name` VARCHAR(30) NOT NULL,
  `emp_Id`    VARCHAR(20) NOT NULL,
  `br_Id`     VARCHAR(20) NOT NULL,
  KEY `emp_sales`    (`emp_Id`),
  KEY `fk_sales_br`  (`br_Id`),
  CONSTRAINT `emp_sales`   FOREIGN KEY (`emp_Id`) REFERENCES `employee` (`emp_Id`),
  CONSTRAINT `fk_sales_br` FOREIGN KEY (`br_Id`)  REFERENCES `branch` (`br_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table: invoice  ← NEW: required by DataCashier.php
-- --------------------------------------------------------
CREATE TABLE `invoice` (
  `invoice_Id`       VARCHAR(30)  NOT NULL,
  `brId`             VARCHAR(20)  NOT NULL,
  `IssueDate`        DATE         NOT NULL,
  `amount`           DECIMAL(12,2) NOT NULL,
  `supervisorId`     VARCHAR(20)  NOT NULL,
  `saleId`           VARCHAR(20)  NOT NULL,
  `policyNumber`     VARCHAR(50)  NOT NULL,
  `paymentFreq`      VARCHAR(20)  NOT NULL,
  `cashHandOverDate` DATE         NOT NULL,
  `agentSignature`   VARCHAR(100) NOT NULL,
  `teamLeaderCode`   VARCHAR(20)  NOT NULL,
  `created_at`       TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`invoice_Id`),
  KEY `fk_invoice_br` (`brId`),
  CONSTRAINT `fk_invoice_br` FOREIGN KEY (`brId`) REFERENCES `branch` (`br_Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;

-- Table: FeeCategory
CREATE TABLE `FeeCategory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fee_category` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: FeeCollectionTypes
CREATE TABLE `FeeCollectionTypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `collection_desc` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: Branches
CREATE TABLE `Branches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: EntryMode
CREATE TABLE `EntryMode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_modename` varchar(100) DEFAULT NULL,
  `crdr` char(1) DEFAULT NULL,
  `entrymodeno` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: Module
CREATE TABLE `Module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: FeeTypes
CREATE TABLE `FeeTypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fee_category_id` int(11) DEFAULT NULL,
  `f_name` varchar(100) DEFAULT NULL,
  `collection_id` int(11) DEFAULT NULL,
  `br_id` int(11) DEFAULT NULL,
  `seq_id` int(11) DEFAULT NULL,
  `fee_type_ledger` varchar(100) DEFAULT NULL,
  `fee_head_type` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fee_category_id` (`fee_category_id`),
  KEY `collection_id` (`collection_id`),
  KEY `br_id` (`br_id`),
  CONSTRAINT `feetypes_ibfk_1` FOREIGN KEY (`fee_category_id`) REFERENCES `FeeCategory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `feetypes_ibfk_2` FOREIGN KEY (`collection_id`) REFERENCES `FeeCollectionTypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `feetypes_ibfk_3` FOREIGN KEY (`br_id`) REFERENCES `Branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: FinancialTrans
CREATE TABLE `FinancialTrans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) DEFAULT NULL,
  `trans_id` varchar(100) DEFAULT NULL,
  `admno` varchar(100) DEFAULT NULL,
  `amount` varchar(100) DEFAULT NULL,
  `crdr` char(1) DEFAULT NULL,
  `tran_date` varchar(50) DEFAULT NULL,
  `academic_year` varchar(100) DEFAULT NULL,
  `entry_mode` int(11) DEFAULT NULL,
  `voucher_no` varchar(100) DEFAULT NULL,
  `br_id` int(11) DEFAULT NULL,
  `type_of_concession` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entry_mode` (`entry_mode`),
  KEY `br_id` (`br_id`),
  CONSTRAINT `financialtrans_ibfk_1` FOREIGN KEY (`entry_mode`) REFERENCES `EntryMode` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `financialtrans_ibfk_2` FOREIGN KEY (`br_id`) REFERENCES `Branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: FinancialTranDetails
CREATE TABLE `FinancialTranDetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `financial_trans_id` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `amount` varchar(100) DEFAULT NULL,
  `head_id` int(11) DEFAULT NULL,
  `crdr` char(1) DEFAULT NULL,
  `br_id` int(11) DEFAULT NULL,
  `head_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `financial_trans_id` (`financial_trans_id`),
  KEY `head_id` (`head_id`),
  KEY `br_id` (`br_id`),
  CONSTRAINT `financialtrandetails_ibfk_1` FOREIGN KEY (`financial_trans_id`) REFERENCES `FinancialTrans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `financialtrandetails_ibfk_2` FOREIGN KEY (`head_id`) REFERENCES `FeeTypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `financialtrandetails_ibfk_3` FOREIGN KEY (`br_id`) REFERENCES `Branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: CommonFeeCollection
CREATE TABLE `CommonFeeCollection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) DEFAULT NULL,
  `receipt_id` varchar(100) DEFAULT NULL,
  `amount` varchar(100) DEFAULT NULL,
  `br_id` int(11) DEFAULT NULL,
  `academic_year` varchar(100) DEFAULT NULL,
  `financial_year` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `br_id` (`br_id`),
  CONSTRAINT `commonfeecollection_ibfk_1` FOREIGN KEY (`br_id`) REFERENCES `Branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: CommonFeeCollectionHeadwise
CREATE TABLE `CommonFeeCollectionHeadwise` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `common_fee_collection_id` int(11) DEFAULT NULL,
  `module_id` int(11) DEFAULT NULL,
  `head_id` int(11) DEFAULT NULL,
  `head_name` varchar(100) DEFAULT NULL,
  `amount` varchar(100) DEFAULT NULL,
  `br_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `common_fee_collection_id` (`common_fee_collection_id`),
  KEY `head_id` (`head_id`),
  KEY `br_id` (`br_id`),
  CONSTRAINT `commonfeecollectionheadwise_ibfk_1` FOREIGN KEY (`common_fee_collection_id`) REFERENCES `CommonFeeCollection` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `commonfeecollectionheadwise_ibfk_2` FOREIGN KEY (`head_id`) REFERENCES `FeeTypes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `commonfeecollectionheadwise_ibfk_3` FOREIGN KEY (`br_id`) REFERENCES `Branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: Temporary_completedata
CREATE TABLE `Temporary_completedata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` varchar(50) DEFAULT NULL,
  `academic_year` varchar(100) DEFAULT NULL,
  `session` varchar(100) DEFAULT NULL,
  `alloted_category` varchar(100) DEFAULT NULL,
  `voucher_type` varchar(100) DEFAULT NULL,
  `voucher_no` varchar(100) DEFAULT NULL,
  `roll_no` varchar(100) DEFAULT NULL,
  `admno_uniqueid` varchar(100) DEFAULT NULL,
  `status` varchar(100) DEFAULT NULL,
  `fee_category` varchar(100) DEFAULT NULL,
  `faculty` varchar(100) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `batch` varchar(100) DEFAULT NULL,
  `receipt_no` varchar(100) DEFAULT NULL,
  `fee_head` varchar(100) DEFAULT NULL,
  `due_amount` varchar(100) DEFAULT NULL,
  `paid_amount` varchar(100) DEFAULT NULL,
  `concession_amount` varchar(100) DEFAULT NULL,
  `scholarship_amount` varchar(100) DEFAULT NULL,
  `reverse_concession_amount` varchar(100) DEFAULT NULL,
  `write_off_amount` varchar(100) DEFAULT NULL,
  `adjusted_amount` varchar(100) DEFAULT NULL,
  `refund_amount` varchar(100) DEFAULT NULL,
  `fund_transfer_amount` varchar(100) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

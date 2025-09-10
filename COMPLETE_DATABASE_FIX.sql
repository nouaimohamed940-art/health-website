-- حل شامل لمشكلة المفاتيح الخارجية وإعادة إنشاء الجداول
-- هذا الملف سيقوم بحذف جميع الجداول المرتبطة وإعادة إنشائها

USE health_staff_management;

-- تعطيل فحص المفاتيح الخارجية مؤقتاً
SET FOREIGN_KEY_CHECKS = 0;

-- حذف جميع الجداول المرتبطة بالمراكز والمستشفيات
DROP TABLE IF EXISTS center_attachments;
DROP TABLE IF EXISTS center_monthly_stats;
DROP TABLE IF EXISTS center_reports;
DROP TABLE IF EXISTS center_workforce;
DROP TABLE IF EXISTS data_entry_activity_log;
DROP TABLE IF EXISTS data_entry_users;
DROP TABLE IF EXISTS employee_transfers;
DROP TABLE IF EXISTS detailed_leaves;
DROP TABLE IF EXISTS movements;
DROP TABLE IF EXISTS movement_types;
DROP TABLE IF EXISTS attendance;
DROP TABLE IF EXISTS leaves;
DROP TABLE IF EXISTS employees;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS activity_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS centers;
DROP TABLE IF EXISTS hospitals;

-- إعادة تمكين فحص المفاتيح الخارجية
SET FOREIGN_KEY_CHECKS = 1;

-- إنشاء جدول hospitals
CREATE TABLE `hospitals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL UNIQUE,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- إنشاء جدول centers
CREATE TABLE `centers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hospital_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL UNIQUE,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `hospital_id` (`hospital_id`),
  CONSTRAINT `centers_ibfk_1` FOREIGN KEY (`hospital_id`) REFERENCES `hospitals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- إدراج المستشفيات الصحيحة
INSERT INTO hospitals (id, name, code, created_at) VALUES
(1, 'مجمع الملك عبد الله الطبي', 'KAMC', NOW()),
(2, 'مستشفى رابغ', 'RH', NOW()),
(3, 'مستشفى الملك فهد', 'KFH', NOW());

-- إدراج المراكز الصحيحة - مجمع الملك عبد الله الطبي (13 مركز)
INSERT INTO centers (id, hospital_id, name, code, description, is_active, created_at) VALUES
(1, 1, 'مركز الشراع 505', 'KAMC_ALSHARAA_505', 'مركز الشراع 505 - مجمع الملك عبد الله الطبي', 1, NOW()),
(2, 1, 'مركز ابحر الشمالية', 'KAMC_ABHAR_NORTH', 'مركز ابحر الشمالية - مجمع الملك عبد الله الطبي', 1, NOW()),
(3, 1, 'مركز الريان', 'KAMC_ALRAYAN', 'مركز الريان - مجمع الملك عبد الله الطبي', 1, NOW()),
(4, 1, 'مركز الصالحية', 'KAMC_ALSALAHIYA', 'مركز الصالحية - مجمع الملك عبد الله الطبي', 1, NOW()),
(5, 1, 'مركز الصواري', 'KAMC_ALSAWARI', 'مركز الصواري - مجمع الملك عبد الله الطبي', 1, NOW()),
(6, 1, 'مركز الفردوس', 'KAMC_ALFARDOUS', 'مركز الفردوس - مجمع الملك عبد الله الطبي', 1, NOW()),
(7, 1, 'مركز الماجد', 'KAMC_ALMAJID', 'مركز الماجد - مجمع الملك عبد الله الطبي', 1, NOW()),
(8, 1, 'مركز الوفاء', 'KAMC_ALWAFA', 'مركز الوفاء - مجمع الملك عبد الله الطبي', 1, NOW()),
(9, 1, 'مركز بريمان', 'KAMC_BARIMAN', 'مركز بريمان - مجمع الملك عبد الله الطبي', 1, NOW()),
(10, 1, 'مركز ثول', 'KAMC_THUL', 'مركز ثول - مجمع الملك عبد الله الطبي', 1, NOW()),
(11, 1, 'مركز خالد النموذجي', 'KAMC_KHALID_MODEL', 'مركز خالد النموذجي - مجمع الملك عبد الله الطبي', 1, NOW()),
(12, 1, 'مركز ذهبان', 'KAMC_DHAHBAN', 'مركز ذهبان - مجمع الملك عبد الله الطبي', 1, NOW()),
(13, 1, 'مركز مشرفة', 'KAMC_MASHRAFA', 'مركز مشرفة - مجمع الملك عبد الله الطبي', 1, NOW());

-- إدراج المراكز - مستشفى رابغ (13 مركز)
INSERT INTO centers (id, hospital_id, name, code, description, is_active, created_at) VALUES
(14, 2, 'مركز الابواء', 'RH_ALABWA', 'مركز الابواء - مستشفى رابغ', 1, NOW()),
(15, 2, 'مركز الجحفة', 'RH_ALJAHFA', 'مركز الجحفة - مستشفى رابغ', 1, NOW()),
(16, 2, 'مركز الجوبة', 'RH_ALJOUBA', 'مركز الجوبة - مستشفى رابغ', 1, NOW()),
(17, 2, 'مركز الصليب الشرقي', 'RH_ALSALIB_EAST', 'مركز الصليب الشرقي - مستشفى رابغ', 1, NOW()),
(18, 2, 'مركز المرجانية', 'RH_ALMARJANIYA', 'مركز المرجانية - مستشفى رابغ', 1, NOW()),
(19, 2, 'مركز المرخة', 'RH_ALMARAKHA', 'مركز المرخة - مستشفى رابغ', 1, NOW()),
(20, 2, 'مركز النويبع', 'RH_ALNUWEIBA', 'مركز النويبع - مستشفى رابغ', 1, NOW()),
(21, 2, 'مركز حجر', 'RH_HAJAR', 'مركز حجر - مستشفى رابغ', 1, NOW()),
(22, 2, 'مركز رابغ', 'RH_RABIGH', 'مركز رابغ - مستشفى رابغ', 1, NOW()),
(23, 2, 'مركز صعبر', 'RH_SAABAR', 'مركز صعبر - مستشفى رابغ', 1, NOW()),
(24, 2, 'مركز كلية', 'RH_KULLIYA', 'مركز كلية - مستشفى رابغ', 1, NOW()),
(25, 2, 'مركز مستورة', 'RH_MASTURA', 'مركز مستورة - مستشفى رابغ', 1, NOW()),
(26, 2, 'مركز مغينية', 'RH_MAGHINIYA', 'مركز مغينية - مستشفى رابغ', 1, NOW());

-- إدراج المراكز - مستشفى الملك فهد (12 مركز)
INSERT INTO centers (id, hospital_id, name, code, description, is_active, created_at) VALUES
(27, 3, 'مركز البوادي 2', 'KFH_BAWADI_2', 'مركز البوادي 2 - مستشفى الملك فهد', 1, NOW()),
(28, 3, 'مركز البوادي 1', 'KFH_BAWADI_1', 'مركز البوادي 1 - مستشفى الملك فهد', 1, NOW()),
(29, 3, 'مركز الربوة', 'KFH_ALRABWA', 'مركز الربوة - مستشفى الملك فهد', 1, NOW()),
(30, 3, 'مركز الرحاب', 'KFH_ALRAHAB', 'مركز الرحاب - مستشفى الملك فهد', 1, NOW()),
(31, 3, 'مركز السلامة', 'KFH_ALSALAMA', 'مركز السلامة - مستشفى الملك فهد', 1, NOW()),
(32, 3, 'مركز الشاطئ', 'KFH_ALSHAATI', 'مركز الشاطئ - مستشفى الملك فهد', 1, NOW()),
(33, 3, 'مركز الصفا 1', 'KFH_ALSAFA_1', 'مركز الصفا 1 - مستشفى الملك فهد', 1, NOW()),
(34, 3, 'مركز الصفا 2', 'KFH_ALSAFA_2', 'مركز الصفا 2 - مستشفى الملك فهد', 1, NOW()),
(35, 3, 'مركز الفيصلية', 'KFH_ALFAISALIYA', 'مركز الفيصلية - مستشفى الملك فهد', 1, NOW()),
(36, 3, 'مركز المروة', 'KFH_ALMARWA', 'مركز المروة - مستشفى الملك فهد', 1, NOW()),
(37, 3, 'مركز النعيم', 'KFH_ALNAEEM', 'مركز النعيم - مستشفى الملك فهد', 1, NOW()),
(38, 3, 'مركز النهضة', 'KFH_ALNAHDA', 'مركز النهضة - مستشفى الملك فهد', 1, NOW());

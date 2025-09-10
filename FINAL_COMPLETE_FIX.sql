-- إصلاح نهائي شامل لجميع الجداول والحسابات
USE health_staff_management;

-- تعطيل فحص المفاتيح الخارجية مؤقتاً
SET FOREIGN_KEY_CHECKS = 0;

-- حذف جميع الجداول الموجودة
DROP TABLE IF EXISTS data_entry_users;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS centers;
DROP TABLE IF EXISTS hospitals;

-- إنشاء جدول المستشفيات
CREATE TABLE hospitals (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- إنشاء جدول المراكز
CREATE TABLE centers (
    id INT(11) NOT NULL AUTO_INCREMENT,
    hospital_id INT(11) NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY hospital_id (hospital_id),
    CONSTRAINT centers_ibfk_1 FOREIGN KEY (hospital_id) REFERENCES hospitals (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- إنشاء جدول الأدوار
CREATE TABLE roles (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    permissions JSON,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- إنشاء جدول المستخدمين
CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    role_id INT(11) NOT NULL,
    hospital_id INT(11),
    center_id INT(11),
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY username (username),
    KEY role_id (role_id),
    KEY hospital_id (hospital_id),
    KEY center_id (center_id),
    CONSTRAINT users_ibfk_1 FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE,
    CONSTRAINT users_ibfk_2 FOREIGN KEY (hospital_id) REFERENCES hospitals (id) ON DELETE CASCADE,
    CONSTRAINT users_ibfk_3 FOREIGN KEY (center_id) REFERENCES centers (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- إنشاء جدول مدخلي البيانات
CREATE TABLE data_entry_users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    center_id INT(11) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY username (username),
    KEY center_id (center_id),
    CONSTRAINT data_entry_users_ibfk_1 FOREIGN KEY (center_id) REFERENCES centers (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- إدراج المستشفيات
INSERT INTO hospitals (id, name, code) VALUES
(1, 'مجمع الملك عبد الله الطبي', 'KAMC'),
(2, 'مستشفى رابغ', 'RH'),
(3, 'مستشفى الملك فهد', 'KFH');

-- إدراج المراكز
INSERT INTO centers (id, hospital_id, name, code, description, is_active) VALUES
(1, 1, 'مركز الشراع 505', 'KAMC_SHARAA_505', 'مركز الشراع 505 التابع لمجمع الملك عبد الله الطبي', 1),
(2, 1, 'مركز ابحر الشمالية', 'KAMC_ABHAR_NORTH', 'مركز ابحر الشمالية التابع لمجمع الملك عبد الله الطبي', 1),
(3, 1, 'مركز الريان', 'KAMC_ALRAYYAN', 'مركز الريان التابع لمجمع الملك عبد الله الطبي', 1),
(4, 1, 'مركز الصالحية', 'KAMC_ALSALIHIYAH', 'مركز الصالحية التابع لمجمع الملك عبد الله الطبي', 1),
(5, 1, 'مركز الصواري', 'KAMC_ALSAWARI', 'مركز الصواري التابع لمجمع الملك عبد الله الطبي', 1),
(6, 1, 'مركز الفردوس', 'KAMC_ALFERDOUS', 'مركز الفردوس التابع لمجمع الملك عبد الله الطبي', 1),
(7, 1, 'مركز الماجد', 'KAMC_ALMAJED', 'مركز الماجد التابع لمجمع الملك عبد الله الطبي', 1),
(8, 1, 'مركز الوفاء', 'KAMC_ALWAFA', 'مركز الوفاء التابع لمجمع الملك عبد الله الطبي', 1),
(9, 1, 'مركز بريمان', 'KAMC_BURAIMAN', 'مركز بريمان التابع لمجمع الملك عبد الله الطبي', 1),
(10, 1, 'مركز ثول', 'KAMC_THUWAL', 'مركز ثول التابع لمجمع الملك عبد الله الطبي', 1),
(11, 1, 'مركز خالد النموذجي', 'KAMC_KHALID_MODEL', 'مركز خالد النموذجي التابع لمجمع الملك عبد الله الطبي', 1),
(12, 1, 'مركز ذهبان', 'KAMC_DHAHBAN', 'مركز ذهبان التابع لمجمع الملك عبد الله الطبي', 1),
(13, 1, 'مركز مشرفة', 'KAMC_MUSHARRAF', 'مركز مشرفة التابع لمجمع الملك عبد الله الطبي', 1),
(14, 2, 'مركز الابواء', 'RH_ALABWA', 'مركز الابواء التابع لمستشفى رابغ', 1),
(15, 2, 'مركز الجحفة', 'RH_ALJAHFA', 'مركز الجحفة التابع لمستشفى رابغ', 1),
(16, 2, 'مركز الجوبة', 'RH_ALJOUBA', 'مركز الجوبة التابع لمستشفى رابغ', 1),
(17, 2, 'مركز الصليب الشرقي', 'RH_ALSALEEB_EAST', 'مركز الصليب الشرقي التابع لمستشفى رابغ', 1),
(18, 2, 'مركز المرجانية', 'RH_ALMARJANIYAH', 'مركز المرجانية التابع لمستشفى رابغ', 1),
(19, 2, 'مركز المرخة', 'RH_ALMARKHA', 'مركز المرخة التابع لمستشفى رابغ', 1),
(20, 2, 'مركز النويبع', 'RH_ALNUWAYBA', 'مركز النويبع التابع لمستشفى رابغ', 1),
(21, 2, 'مركز حجر', 'RH_HAJAR', 'مركز حجر التابع لمستشفى رابغ', 1),
(22, 2, 'مركز رابغ', 'RH_RABIGH', 'مركز رابغ التابع لمستشفى رابغ', 1),
(23, 2, 'مركز صعبر', 'RH_SAABAR', 'مركز صعبر التابع لمستشفى رابغ', 1),
(24, 2, 'مركز كلية', 'RH_KULAYYAH', 'مركز كلية التابع لمستشفى رابغ', 1),
(25, 2, 'مركز مستورة', 'RH_MASTURAH', 'مركز مستورة التابع لمستشفى رابغ', 1),
(26, 2, 'مركز مغينية', 'RH_MUGHINIYAH', 'مركز مغينية التابع لمستشفى رابغ', 1),
(27, 3, 'مركز البوادي 2', 'KFH_BAWADI_2', 'مركز البوادي 2 التابع لمستشفى الملك فهد', 1),
(28, 3, 'مركز البوادي 1', 'KFH_BAWADI_1', 'مركز البوادي 1 التابع لمستشفى الملك فهد', 1),
(29, 3, 'مركز الربوة', 'KFH_ALRABWA', 'مركز الربوة التابع لمستشفى الملك فهد', 1),
(30, 3, 'مركز الرحاب', 'KFH_ALREHAB', 'مركز الرحاب التابع لمستشفى الملك فهد', 1),
(31, 3, 'مركز السلامة', 'KFH_ALSALAMAH', 'مركز السلامة التابع لمستشفى الملك فهد', 1),
(32, 3, 'مركز الشاطئ', 'KFH_ALSHATIE', 'مركز الشاطئ التابع لمستشفى الملك فهد', 1),
(33, 3, 'مركز الصفا 1', 'KFH_ALSAFA_1', 'مركز الصفا 1 التابع لمستشفى الملك فهد', 1),
(34, 3, 'مركز الصفا 2', 'KFH_ALSAFA_2', 'مركز الصفا 2 التابع لمستشفى الملك فهد', 1),
(35, 3, 'مركز الفيصلية', 'KFH_ALFAISALIYAH', 'مركز الفيصلية التابع لمستشفى الملك فهد', 1),
(36, 3, 'مركز المروة', 'KFH_ALMARWAH', 'مركز المروة التابع لمستشفى الملك فهد', 1),
(37, 3, 'مركز النعيم', 'KFH_ALNAEEM', 'مركز النعيم التابع لمستشفى الملك فهد', 1),
(38, 3, 'مركز النهضة', 'KFH_ALNAHDAH', 'مركز النهضة التابع لمستشفى الملك فهد', 1);

-- إدراج الأدوار
INSERT INTO roles (id, name, display_name, description, permissions, is_active) VALUES
(1, 'super_admin', 'مدير عام على كل المراكز', 'مدير عام له صلاحية كاملة على جميع المراكز والمستشفيات', '{"all": true}', 1),
(2, 'hospital_manager', 'مدير المراكز', 'مدير المستشفى المشرف على المراكز التابعة له فقط', '{"hospital_centers": true, "reports": true, "approve": true}', 1),
(3, 'center_manager', 'مدير مركز', 'مدير مركز واحد فقط، لا يرى مراكز أخرى', '{"center_only": true, "approve": true, "reports": true}', 1),
(4, 'data_entry', 'مدخل بيانات', 'مدخل بيانات لمركز واحد فقط، يحتاج موافقة المدير', '{"data_entry": true, "center_only": true}', 1);

-- إعادة تمكين فحص المفاتيح الخارجية
SET FOREIGN_KEY_CHECKS = 1;

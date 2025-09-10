-- النظام الكامل لإدارة القوى العاملة الصحية
-- 38 مركز + 3 مستشفيات + 3 سوبر يوزر + 3 مديري مستشفيات + 38 مدير مركز + 76 مدخل بيانات

-- حذف قاعدة البيانات الموجودة وإنشاء جديدة
DROP DATABASE IF EXISTS health_management_system;
CREATE DATABASE health_management_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE health_management_system;

-- جدول المستشفيات
CREATE TABLE hospitals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول المراكز
CREATE TABLE centers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    hospital_id INT NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE
);

-- جدول الأدوار
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول المستخدمين
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    role_id INT NOT NULL,
    hospital_id INT,
    center_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE SET NULL,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE SET NULL
);

-- جدول جلسات المستخدمين
CREATE TABLE user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- إدراج المستشفيات
INSERT INTO hospitals (name, code, description) VALUES
('مجمع الملك عبد الله الطبي', 'KAMC', 'مجمع الملك عبد الله الطبي - 13 مركز'),
('مستشفى رابغ', 'RH', 'مستشفى رابغ - 13 مركز'),
('مستشفى الملك فهد', 'KFH', 'مستشفى الملك فهد - 12 مركز');

-- إدراج المراكز
-- مراكز مجمع الملك عبد الله الطبي (13 مركز)
INSERT INTO centers (name, code, hospital_id, description) VALUES
('مركز الشراع 505', 'KAMC_ALSHARAA_505', 1, 'مركز الشراع 505 - مجمع الملك عبد الله الطبي'),
('مركز ابحر الشمالية', 'KAMC_ABHAR_NORTH', 1, 'مركز ابحر الشمالية - مجمع الملك عبد الله الطبي'),
('مركز الريان', 'KAMC_ALRAYAN', 1, 'مركز الريان - مجمع الملك عبد الله الطبي'),
('مركز الصالحية', 'KAMC_ALSALAHIYA', 1, 'مركز الصالحية - مجمع الملك عبد الله الطبي'),
('مركز الصواري', 'KAMC_ALSAWARI', 1, 'مركز الصواري - مجمع الملك عبد الله الطبي'),
('مركز الفردوس', 'KAMC_ALFARDOUS', 1, 'مركز الفردوس - مجمع الملك عبد الله الطبي'),
('مركز الماجد', 'KAMC_ALMAJID', 1, 'مركز الماجد - مجمع الملك عبد الله الطبي'),
('مركز الوفاء', 'KAMC_ALWAFA', 1, 'مركز الوفاء - مجمع الملك عبد الله الطبي'),
('مركز بريمان', 'KAMC_BARIMAN', 1, 'مركز بريمان - مجمع الملك عبد الله الطبي'),
('مركز ثول', 'KAMC_THUL', 1, 'مركز ثول - مجمع الملك عبد الله الطبي'),
('مركز خالد النموذجي', 'KAMC_KHALID_MODEL', 1, 'مركز خالد النموذجي - مجمع الملك عبد الله الطبي'),
('مركز ذهبان', 'KAMC_DHAHBAN', 1, 'مركز ذهبان - مجمع الملك عبد الله الطبي'),
('مركز مشرفة', 'KAMC_MASHRAFA', 1, 'مركز مشرفة - مجمع الملك عبد الله الطبي');

-- مراكز مستشفى رابغ (13 مركز)
INSERT INTO centers (name, code, hospital_id, description) VALUES
('مركز الابواء', 'RH_ALABWA', 2, 'مركز الابواء - مستشفى رابغ'),
('مركز الجحفة', 'RH_ALJAHFA', 2, 'مركز الجحفة - مستشفى رابغ'),
('مركز الجوبة', 'RH_ALJOUBA', 2, 'مركز الجوبة - مستشفى رابغ'),
('مركز الصليب الشرقي', 'RH_ALSALIB_EAST', 2, 'مركز الصليب الشرقي - مستشفى رابغ'),
('مركز المرجانية', 'RH_ALMARJANIYA', 2, 'مركز المرجانية - مستشفى رابغ'),
('مركز المرخة', 'RH_ALMARAKHA', 2, 'مركز المرخة - مستشفى رابغ'),
('مركز النويبع', 'RH_ALNUWEIBA', 2, 'مركز النويبع - مستشفى رابغ'),
('مركز حجر', 'RH_HAJAR', 2, 'مركز حجر - مستشفى رابغ'),
('مركز رابغ', 'RH_RABIGH', 2, 'مركز رابغ - مستشفى رابغ'),
('مركز صعبر', 'RH_SAABAR', 2, 'مركز صعبر - مستشفى رابغ'),
('مركز كلية', 'RH_KULLIYA', 2, 'مركز كلية - مستشفى رابغ'),
('مركز مستورة', 'RH_MASTURA', 2, 'مركز مستورة - مستشفى رابغ'),
('مركز مغينية', 'RH_MAGHINIYA', 2, 'مركز مغينية - مستشفى رابغ');

-- مراكز مستشفى الملك فهد (12 مركز)
INSERT INTO centers (name, code, hospital_id, description) VALUES
('مركز البوادي 2', 'KFH_BAWADI_2', 3, 'مركز البوادي 2 - مستشفى الملك فهد'),
('مركز البوادي 1', 'KFH_BAWADI_1', 3, 'مركز البوادي 1 - مستشفى الملك فهد'),
('مركز الربوة', 'KFH_ALRABWA', 3, 'مركز الربوة - مستشفى الملك فهد'),
('مركز الرحاب', 'KFH_ALRAHAB', 3, 'مركز الرحاب - مستشفى الملك فهد'),
('مركز السلامة', 'KFH_ALSALAMA', 3, 'مركز السلامة - مستشفى الملك فهد'),
('مركز الشاطئ', 'KFH_ALSHAATI', 3, 'مركز الشاطئ - مستشفى الملك فهد'),
('مركز الصفا 1', 'KFH_ALSAFA_1', 3, 'مركز الصفا 1 - مستشفى الملك فهد'),
('مركز الصفا 2', 'KFH_ALSAFA_2', 3, 'مركز الصفا 2 - مستشفى الملك فهد'),
('مركز الفيصلية', 'KFH_ALFAISALIYA', 3, 'مركز الفيصلية - مستشفى الملك فهد'),
('مركز المروة', 'KFH_ALMARWA', 3, 'مركز المروة - مستشفى الملك فهد'),
('مركز النعيم', 'KFH_ALNAEEM', 3, 'مركز النعيم - مستشفى الملك فهد'),
('مركز النهضة', 'KFH_ALNAHDA', 3, 'مركز النهضة - مستشفى الملك فهد');

-- إدراج الأدوار
INSERT INTO roles (name, display_name, description, permissions) VALUES
('super_admin', 'مدير عام على كل المراكز', 'مدير عام له صلاحية كاملة على جميع المراكز والمستشفيات', '{"all": true}'),
('hospital_manager', 'مدير المراكز', 'مدير المستشفى المشرف على المراكز التابعة له فقط', '{"hospital_centers": true, "reports": true, "approve": true}'),
('center_manager', 'مدير مركز', 'مدير مركز واحد فقط، لا يرى مراكز أخرى', '{"center_only": true, "approve": true, "reports": true}'),
('data_entry', 'مدخل بيانات', 'مدخل بيانات لمركز واحد فقط، يحتاج موافقة المدير', '{"data_entry": true, "center_only": true}');

-- إدراج السوبر يوزر (3 حسابات)
INSERT INTO users (username, password_hash, full_name, email, phone, role_id, is_active) VALUES
('super_admin_1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'السوبر يوزر الأول', 'super1@health.gov.sa', '0500000001', 1, TRUE),
('super_admin_2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'السوبر يوزر الثاني', 'super2@health.gov.sa', '0500000002', 1, TRUE),
('super_admin_3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'السوبر يوزر الثالث', 'super3@health.gov.sa', '0500000003', 1, TRUE);

-- إدراج مديري المستشفيات (3 حسابات)
INSERT INTO users (username, password_hash, full_name, email, phone, role_id, hospital_id, is_active) VALUES
('hospital_manager_kamc', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مجمع الملك عبد الله الطبي', 'kamc.manager@health.gov.sa', '0501000001', 2, 1, TRUE),
('hospital_manager_rh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مستشفى رابغ', 'rh.manager@health.gov.sa', '0501000002', 2, 2, TRUE),
('hospital_manager_kfh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مستشفى الملك فهد', 'kfh.manager@health.gov.sa', '0501000003', 2, 3, TRUE);

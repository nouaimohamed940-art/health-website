-- نظام إدارة القوى العاملة الصحية - قاعدة البيانات المصححة
-- Health Staff Management System - Fixed Database Schema

-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS health_staff_management 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE health_staff_management;

-- جدول المستشفيات
CREATE TABLE hospitals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(255),
    manager_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- جدول المراكز
CREATE TABLE centers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    hospital_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(10) UNIQUE NOT NULL,
    description TEXT,
    manager_name VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE
);

-- جدول الأدوار
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) UNIQUE NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    permissions JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول المستخدمين
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role_id INT NOT NULL,
    hospital_id INT,
    center_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE SET NULL,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE SET NULL
);

-- جدول الموظفين
CREATE TABLE employees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id VARCHAR(20) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    job_title VARCHAR(255) NOT NULL,
    department VARCHAR(255) NOT NULL,
    center_id INT NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(255),
    hire_date DATE NOT NULL,
    salary DECIMAL(10,2),
    status ENUM('active', 'inactive', 'terminated') DEFAULT 'active',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول أنواع الحركات
CREATE TABLE movement_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    description TEXT,
    requires_approval BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- جدول الحركات
CREATE TABLE movements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    movement_type_id INT NOT NULL,
    movement_date DATE NOT NULL,
    start_date DATE,
    end_date DATE,
    duration_days INT,
    from_department VARCHAR(255),
    to_department VARCHAR(255),
    destination VARCHAR(255),
    cost DECIMAL(10,2),
    notes TEXT,
    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (movement_type_id) REFERENCES movement_types(id),
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول الإجازات
CREATE TABLE leaves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    leave_type ENUM('annual', 'exceptional', 'maternity', 'sick', 'emergency') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    duration_days INT NOT NULL,
    reason TEXT,
    medical_certificate VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected', 'taken') DEFAULT 'pending',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول الحضور والغياب
CREATE TABLE attendance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    employee_id INT NOT NULL,
    attendance_date DATE NOT NULL,
    check_in_time TIME,
    check_out_time TIME,
    status ENUM('present', 'absent', 'late', 'early_leave') DEFAULT 'present',
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (employee_id, attendance_date)
);

-- جدول التقارير
CREATE TABLE reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    report_type ENUM('daily', 'weekly', 'monthly', 'quarterly', 'yearly') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    hospital_id INT,
    center_id INT,
    filters JSON,
    data JSON,
    status ENUM('generating', 'completed', 'failed') DEFAULT 'generating',
    file_path VARCHAR(500),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE SET NULL,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول الإشعارات
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    action_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول سجل الأنشطة
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول الجلسات
CREATE TABLE user_sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول الإعدادات
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    key_name VARCHAR(100) UNIQUE NOT NULL,
    value TEXT,
    description TEXT,
    category VARCHAR(50),
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- إدراج البيانات الأساسية

-- إدراج المستشفيات
INSERT INTO hospitals (name, code, address, phone, email, manager_name) VALUES
('مستشفى الملك فهد التخصصي', 'KFSH', 'الرياض، المملكة العربية السعودية', '0112345678', 'info@kfsh.med.sa', 'د. أحمد محمد'),
('مستشفى الملك عبدالعزيز', 'KAUH', 'جدة، المملكة العربية السعودية', '0123456789', 'info@kau.edu.sa', 'د. فاطمة أحمد'),
('مستشفى الملك خالد', 'KKUH', 'الرياض، المملكة العربية السعودية', '0119876543', 'info@kkuh.med.sa', 'د. محمد عبدالله');

-- إدراج المراكز (38 مركز)
INSERT INTO centers (hospital_id, name, code, description, manager_name, phone, email) VALUES
-- مراكز مستشفى الملك فهد التخصصي (13 مركز)
(1, 'مركز الطب الباطني', 'IMC-01', 'مركز متخصص في الطب الباطني', 'د. سارة محمد', '0112345679', 'imc01@kfsh.med.sa'),
(1, 'مركز الطوارئ', 'EMC-01', 'مركز الطوارئ والحوادث', 'د. خالد أحمد', '0112345680', 'emc01@kfsh.med.sa'),
(1, 'مركز الجراحة', 'SURG-01', 'مركز الجراحة العامة والتخصصية', 'د. نورا عبدالله', '0112345681', 'surg01@kfsh.med.sa'),
(1, 'مركز طب الأطفال', 'PED-01', 'مركز طب الأطفال والرضع', 'د. عمر محمد', '0112345682', 'ped01@kfsh.med.sa'),
(1, 'مركز أمراض القلب', 'CARD-01', 'مركز أمراض القلب والشرايين', 'د. لينا أحمد', '0112345683', 'card01@kfsh.med.sa'),
(1, 'مركز الأشعة', 'RAD-01', 'مركز الأشعة والتصوير الطبي', 'د. يوسف حسن', '0112345684', 'rad01@kfsh.med.sa'),
(1, 'مركز المختبر', 'LAB-01', 'مركز المختبرات الطبية', 'د. هدى علي', '0112345685', 'lab01@kfsh.med.sa'),
(1, 'مركز الصيدلة', 'PHARM-01', 'مركز الصيدلة السريرية', 'د. عبدالرحمن سعد', '0112345686', 'pharm01@kfsh.med.sa'),
(1, 'مركز التمريض', 'NURS-01', 'مركز التمريض المتخصص', 'د. مريم حسن', '0112345687', 'nurs01@kfsh.med.sa'),
(1, 'مركز الإدارة', 'ADMIN-01', 'مركز الإدارة والخدمات', 'د. عبدالله سعد', '0112345688', 'admin01@kfsh.med.sa'),
(1, 'مركز العناية المركزة', 'ICU-01', 'مركز العناية المركزة', 'د. فاطمة محمد', '0112345689', 'icu01@kfsh.med.sa'),
(1, 'مركز العظام', 'ORTH-01', 'مركز جراحة العظام', 'د. أحمد يوسف', '0112345690', 'orth01@kfsh.med.sa'),
(1, 'مركز العيون', 'EYE-01', 'مركز طب وجراحة العيون', 'د. نور الدين', '0112345691', 'eye01@kfsh.med.sa'),

-- مراكز مستشفى الملك عبدالعزيز (13 مركز)
(2, 'مركز الطب الباطني', 'IMC-02', 'مركز الطب الباطني - جدة', 'د. يوسف محمد', '0123456790', 'imc02@kau.edu.sa'),
(2, 'مركز الطوارئ', 'EMC-02', 'مركز الطوارئ - جدة', 'د. هدى أحمد', '0123456791', 'emc02@kau.edu.sa'),
(2, 'مركز الجراحة', 'SURG-02', 'مركز الجراحة - جدة', 'د. عبدالرحمن علي', '0123456792', 'surg02@kau.edu.sa'),
(2, 'مركز طب الأطفال', 'PED-02', 'مركز طب الأطفال - جدة', 'د. سارة حسن', '0123456793', 'ped02@kau.edu.sa'),
(2, 'مركز أمراض القلب', 'CARD-02', 'مركز أمراض القلب - جدة', 'د. محمد عبدالله', '0123456794', 'card02@kau.edu.sa'),
(2, 'مركز الأشعة', 'RAD-02', 'مركز الأشعة - جدة', 'د. فاطمة يوسف', '0123456795', 'rad02@kau.edu.sa'),
(2, 'مركز المختبر', 'LAB-02', 'مركز المختبرات - جدة', 'د. خالد أحمد', '0123456796', 'lab02@kau.edu.sa'),
(2, 'مركز الصيدلة', 'PHARM-02', 'مركز الصيدلة - جدة', 'د. نورا محمد', '0123456797', 'pharm02@kau.edu.sa'),
(2, 'مركز التمريض', 'NURS-02', 'مركز التمريض - جدة', 'د. عمر حسن', '0123456798', 'nurs02@kau.edu.sa'),
(2, 'مركز الإدارة', 'ADMIN-02', 'مركز الإدارة - جدة', 'د. لينا سعد', '0123456799', 'admin02@kau.edu.sa'),
(2, 'مركز العناية المركزة', 'ICU-02', 'مركز العناية المركزة - جدة', 'د. عبدالله علي', '0123456800', 'icu02@kau.edu.sa'),
(2, 'مركز العظام', 'ORTH-02', 'مركز جراحة العظام - جدة', 'د. مريم يوسف', '0123456801', 'orth02@kau.edu.sa'),
(2, 'مركز العيون', 'EYE-02', 'مركز طب العيون - جدة', 'د. أحمد نور', '0123456802', 'eye02@kau.edu.sa'),

-- مراكز مستشفى الملك خالد (12 مركز)
(3, 'مركز الطب الباطني', 'IMC-03', 'مركز الطب الباطني - الرياض', 'د. مريم حسن', '0119876544', 'imc03@kkuh.med.sa'),
(3, 'مركز الطوارئ', 'EMC-03', 'مركز الطوارئ - الرياض', 'د. عبدالله سعد', '0119876545', 'emc03@kkuh.med.sa'),
(3, 'مركز الجراحة', 'SURG-03', 'مركز الجراحة - الرياض', 'د. فاطمة أحمد', '0119876546', 'surg03@kkuh.med.sa'),
(3, 'مركز طب الأطفال', 'PED-03', 'مركز طب الأطفال - الرياض', 'د. يوسف محمد', '0119876547', 'ped03@kkuh.med.sa'),
(3, 'مركز أمراض القلب', 'CARD-03', 'مركز أمراض القلب - الرياض', 'د. نورا عبدالله', '0119876548', 'card03@kkuh.med.sa'),
(3, 'مركز الأشعة', 'RAD-03', 'مركز الأشعة - الرياض', 'د. خالد حسن', '0119876549', 'rad03@kkuh.med.sa'),
(3, 'مركز المختبر', 'LAB-03', 'مركز المختبرات - الرياض', 'د. سارة علي', '0119876550', 'lab03@kkuh.med.sa'),
(3, 'مركز الصيدلة', 'PHARM-03', 'مركز الصيدلة - الرياض', 'د. عمر يوسف', '0119876551', 'pharm03@kkuh.med.sa'),
(3, 'مركز التمريض', 'NURS-03', 'مركز التمريض - الرياض', 'د. هدى محمد', '0119876552', 'nurs03@kkuh.med.sa'),
(3, 'مركز الإدارة', 'ADMIN-03', 'مركز الإدارة - الرياض', 'د. عبدالرحمن سعد', '0119876553', 'admin03@kkuh.med.sa'),
(3, 'مركز العناية المركزة', 'ICU-03', 'مركز العناية المركزة - الرياض', 'د. لينا أحمد', '0119876554', 'icu03@kkuh.med.sa'),
(3, 'مركز العظام', 'ORTH-03', 'مركز جراحة العظام - الرياض', 'د. محمد نور', '0119876555', 'orth03@kkuh.med.sa');

-- إدراج الأدوار
INSERT INTO roles (name, display_name, description, permissions) VALUES
('center_manager', 'مدير المركز', 'مدير مركز واحد فقط', '{"view_own_center": true, "edit_own_center": true, "view_own_reports": true, "export_own_reports": true, "manage_own_employees": true}'),
('hospital_supervisor', 'مشرف المستشفى', 'مشرف على جميع مراكز مستشفى واحد', '{"view_hospital_centers": true, "view_hospital_reports": true, "export_hospital_reports": true, "approve_requests": true, "view_hospital_employees": true}'),
('super_admin', 'سوبر أدمن', 'مدير النظام العام', '{"view_all_centers": true, "view_all_reports": true, "export_all_reports": true, "manage_all_users": true, "manage_all_employees": true, "system_settings": true}');

-- إدراج المستخدمين
-- سوبر أدمن (2 حسابات)
INSERT INTO users (username, email, password_hash, full_name, phone, role_id, hospital_id, center_id) VALUES
('super_admin_1', 'admin1@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير النظام العام الأول', '0501234567', 3, NULL, NULL),
('super_admin_2', 'admin2@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير النظام العام الثاني', '0501234568', 3, NULL, NULL);

-- مشرفي المستشفيات (3 حسابات)
INSERT INTO users (username, email, password_hash, full_name, phone, role_id, hospital_id, center_id) VALUES
('kfsh_supervisor', 'supervisor@kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مشرف مستشفى الملك فهد', '0501234569', 2, 1, NULL),
('kau_supervisor', 'supervisor@kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مشرف مستشفى الملك عبدالعزيز', '0501234570', 2, 2, NULL),
('kkuh_supervisor', 'supervisor@kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مشرف مستشفى الملك خالد', '0501234571', 2, 3, NULL);

-- مديري المراكز (38 حساب) - عينة من 10 مراكز
INSERT INTO users (username, email, password_hash, full_name, phone, role_id, hospital_id, center_id) VALUES
('imc01_manager', 'manager@imc01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطب الباطني - KFSH', '0501234572', 1, 1, 1),
('emc01_manager', 'manager@emc01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطوارئ - KFSH', '0501234573', 1, 1, 2),
('surg01_manager', 'manager@surg01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الجراحة - KFSH', '0501234574', 1, 1, 3),
('ped01_manager', 'manager@ped01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز طب الأطفال - KFSH', '0501234575', 1, 1, 4),
('card01_manager', 'manager@card01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز أمراض القلب - KFSH', '0501234576', 1, 1, 5),
('imc02_manager', 'manager@imc02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطب الباطني - KAUH', '0501234577', 1, 2, 14),
('emc02_manager', 'manager@emc02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطوارئ - KAUH', '0501234578', 1, 2, 15),
('surg02_manager', 'manager@surg02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الجراحة - KAUH', '0501234579', 1, 2, 16),
('imc03_manager', 'manager@imc03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطب الباطني - KKUH', '0501234580', 1, 3, 27),
('emc03_manager', 'manager@emc03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطوارئ - KKUH', '0501234581', 1, 3, 28);

-- إدراج أنواع الحركات
INSERT INTO movement_types (name, code, description, requires_approval) VALUES
('حضور', 'ATTENDANCE', 'تسجيل حضور الموظف', FALSE),
('غياب', 'ABSENCE', 'تسجيل غياب الموظف', TRUE),
('إجازة سنوية', 'ANNUAL_LEAVE', 'إجازة سنوية مدفوعة الأجر', TRUE),
('إجازة استثنائية', 'EXCEPTIONAL_LEAVE', 'إجازة استثنائية', TRUE),
('إجازة أمومة', 'MATERNITY_LEAVE', 'إجازة أمومة', TRUE),
('إجازة مرضية', 'SICK_LEAVE', 'إجازة مرضية', TRUE),
('تنقل', 'TRANSFER', 'تنقل الموظف بين الأقسام', TRUE),
('إيفاد', 'DELEGATION', 'إيفاد الموظف للخارج', TRUE),
('تكليف', 'ASSIGNMENT', 'تكليف الموظف بمهمة خاصة', TRUE),
('ابتعاث', 'SCHOLARSHIP', 'ابتعاث الموظف للدراسة', TRUE);

-- إدراج الإعدادات
INSERT INTO settings (key_name, value, description, category, is_public) VALUES
('system_name', 'نظام إدارة القوى العاملة الصحية', 'اسم النظام', 'general', TRUE),
('system_version', '2.0.0', 'إصدار النظام', 'general', TRUE),
('max_login_attempts', '5', 'الحد الأقصى لمحاولات تسجيل الدخول', 'security', FALSE),
('session_timeout', '3600', 'مهلة انتهاء الجلسة بالثواني', 'security', FALSE),
('password_min_length', '8', 'الحد الأدنى لطول كلمة المرور', 'security', FALSE),
('max_annual_leave_days', '30', 'الحد الأقصى للإجازة السنوية', 'leaves', FALSE),
('max_exceptional_leave_days', '5', 'الحد الأقصى للإجازة الاستثنائية', 'leaves', FALSE),
('maternity_leave_days', '90', 'مدة إجازة الأمومة', 'leaves', FALSE),
('sick_leave_max_days', '30', 'الحد الأقصى للإجازة المرضية', 'leaves', FALSE),
('work_hours_start', '08:00', 'ساعة بداية العمل', 'attendance', FALSE),
('work_hours_end', '16:00', 'ساعة نهاية العمل', 'attendance', FALSE),
('late_tolerance_minutes', '15', 'تسامح التأخير بالدقائق', 'attendance', FALSE),
('auto_approve_leave_days', '3', 'الموافقة التلقائية للإجازات أقل من', 'approval', FALSE);

-- إنشاء الفهارس لتحسين الأداء
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role_id ON users(role_id);
CREATE INDEX idx_users_hospital_id ON users(hospital_id);
CREATE INDEX idx_users_center_id ON users(center_id);
CREATE INDEX idx_users_is_active ON users(is_active);
CREATE INDEX idx_employees_center_id ON employees(center_id);
CREATE INDEX idx_employees_status ON employees(status);
CREATE INDEX idx_movements_employee_id ON movements(employee_id);
CREATE INDEX idx_movements_status ON movements(status);
CREATE INDEX idx_movements_date ON movements(movement_date);
CREATE INDEX idx_leaves_employee_id ON leaves(employee_id);
CREATE INDEX idx_leaves_status ON leaves(status);
CREATE INDEX idx_attendance_employee_date ON attendance(employee_id, attendance_date);
CREATE INDEX idx_notifications_user_id ON notifications(user_id);
CREATE INDEX idx_activity_logs_user_id ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_created_at ON activity_logs(created_at);
CREATE INDEX idx_user_sessions_user_id ON user_sessions(user_id);

-- إنشاء Views للتقارير
CREATE VIEW v_user_permissions AS
SELECT 
    u.id,
    u.username,
    u.full_name,
    u.role_id,
    r.name as role_name,
    r.display_name as role_display_name,
    r.permissions,
    u.hospital_id,
    h.name as hospital_name,
    u.center_id,
    c.name as center_name
FROM users u
JOIN roles r ON u.role_id = r.id
LEFT JOIN hospitals h ON u.hospital_id = h.id
LEFT JOIN centers c ON u.center_id = c.id
WHERE u.is_active = TRUE;

CREATE VIEW v_employee_summary AS
SELECT 
    e.id,
    e.employee_id,
    e.full_name,
    e.job_title,
    e.department,
    e.center_id,
    c.name as center_name,
    c.hospital_id,
    h.name as hospital_name,
    e.status,
    e.hire_date
FROM employees e
JOIN centers c ON e.center_id = c.id
JOIN hospitals h ON c.hospital_id = h.id;

-- إنشاء Stored Procedures
DELIMITER //

-- إجراء لتسجيل الدخول
CREATE PROCEDURE LoginUser(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT
)
BEGIN
    DECLARE user_id INT DEFAULT NULL;
    DECLARE user_password VARCHAR(255) DEFAULT NULL;
    DECLARE is_locked BOOLEAN DEFAULT FALSE;
    DECLARE login_attempts INT DEFAULT 0;
    
    -- البحث عن المستخدم
    SELECT id, password_hash, login_attempts, 
           CASE WHEN locked_until > NOW() THEN TRUE ELSE FALSE END as is_locked
    INTO user_id, user_password, login_attempts, is_locked
    FROM users 
    WHERE username = p_username AND is_active = TRUE;
    
    -- التحقق من حالة القفل
    IF is_locked THEN
        SELECT 'locked' as status, 'تم قفل الحساب مؤقتاً' as message;
    ELSEIF user_id IS NULL THEN
        SELECT 'not_found' as status, 'اسم المستخدم غير صحيح' as message;
    ELSEIF user_password IS NULL OR NOT (user_password = p_password) THEN
        -- زيادة عدد محاولات الدخول
        UPDATE users 
        SET login_attempts = login_attempts + 1,
            locked_until = CASE 
                WHEN login_attempts + 1 >= 5 THEN DATE_ADD(NOW(), INTERVAL 30 MINUTE)
                ELSE locked_until
            END
        WHERE id = user_id;
        
        SELECT 'invalid_password' as status, 'كلمة المرور غير صحيحة' as message;
    ELSE
        -- تسجيل الدخول الناجح
        UPDATE users 
        SET last_login = NOW(), 
            login_attempts = 0, 
            locked_until = NULL
        WHERE id = user_id;
        
        -- إنشاء جلسة جديدة
        INSERT INTO user_sessions (id, user_id, ip_address, user_agent)
        VALUES (UUID(), user_id, p_ip_address, p_user_agent);
        
        -- إرجاع بيانات المستخدم
        SELECT 'success' as status, 'تم تسجيل الدخول بنجاح' as message,
               u.id, u.username, u.full_name, u.role_id, r.name as role_name,
               u.hospital_id, h.name as hospital_name, u.center_id, c.name as center_name
        FROM users u
        JOIN roles r ON u.role_id = r.id
        LEFT JOIN hospitals h ON u.hospital_id = h.id
        LEFT JOIN centers c ON u.center_id = c.id
        WHERE u.id = user_id;
    END IF;
END //

-- إجراء للحصول على صلاحيات المستخدم
CREATE PROCEDURE GetUserPermissions(IN p_user_id INT)
BEGIN
    SELECT 
        u.id,
        u.username,
        u.full_name,
        r.name as role_name,
        r.display_name as role_display_name,
        r.permissions,
        u.hospital_id,
        h.name as hospital_name,
        u.center_id,
        c.name as center_name
    FROM users u
    JOIN roles r ON u.role_id = r.id
    LEFT JOIN hospitals h ON u.hospital_id = h.id
    LEFT JOIN centers c ON u.center_id = c.id
    WHERE u.id = p_user_id AND u.is_active = TRUE;
END //

DELIMITER ;

-- إنشاء مستخدم قاعدة البيانات
CREATE USER IF NOT EXISTS 'health_staff_user'@'localhost' IDENTIFIED BY 'HealthStaff2024!';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.* TO 'health_staff_user'@'localhost';
FLUSH PRIVILEGES;

COMMIT;

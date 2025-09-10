-- قاعدة بيانات نظام إدارة القوى العاملة الصحية
-- Health Staff Management System Database

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

-- جدول المستخدمين
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('super_admin', 'hospital_admin', 'center_manager', 'employee') NOT NULL,
    hospital_id INT,
    center_id INT,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
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

-- إدراج المراكز
INSERT INTO centers (hospital_id, name, code, description, manager_name, phone, email) VALUES
-- مراكز مستشفى الملك فهد التخصصي
(1, 'مركز الطب الباطني', 'IMC', 'مركز متخصص في الطب الباطني', 'د. سارة محمد', '0112345679', 'imc@kfsh.med.sa'),
(1, 'مركز الطوارئ', 'EMC', 'مركز الطوارئ والحوادث', 'د. خالد أحمد', '0112345680', 'emc@kfsh.med.sa'),
(1, 'مركز الجراحة', 'SURG', 'مركز الجراحة العامة والتخصصية', 'د. نورا عبدالله', '0112345681', 'surg@kfsh.med.sa'),
(1, 'مركز طب الأطفال', 'PED', 'مركز طب الأطفال والرضع', 'د. عمر محمد', '0112345682', 'ped@kfsh.med.sa'),
(1, 'مركز أمراض القلب', 'CARD', 'مركز أمراض القلب والشرايين', 'د. لينا أحمد', '0112345683', 'card@kfsh.med.sa'),

-- مراكز مستشفى الملك عبدالعزيز
(2, 'مركز الطب الباطني', 'IMC-J', 'مركز الطب الباطني - جدة', 'د. يوسف محمد', '0123456790', 'imc@kau.edu.sa'),
(2, 'مركز الطوارئ', 'EMC-J', 'مركز الطوارئ - جدة', 'د. هدى أحمد', '0123456791', 'emc@kau.edu.sa'),
(2, 'مركز الجراحة', 'SURG-J', 'مركز الجراحة - جدة', 'د. عبدالرحمن علي', '0123456792', 'surg@kau.edu.sa'),

-- مراكز مستشفى الملك خالد
(3, 'مركز الطب الباطني', 'IMC-K', 'مركز الطب الباطني - الرياض', 'د. مريم حسن', '0119876544', 'imc@kkuh.med.sa'),
(3, 'مركز الطوارئ', 'EMC-K', 'مركز الطوارئ - الرياض', 'د. عبدالله سعد', '0119876545', 'emc@kkuh.med.sa');

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

-- إدراج المستخدمين
INSERT INTO users (username, email, password_hash, full_name, phone, role, hospital_id, center_id) VALUES
('super_admin', 'admin@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير النظام العام', '0501234567', 'super_admin', NULL, NULL),
('kfsh_admin', 'admin@kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مستشفى الملك فهد', '0501234568', 'hospital_admin', 1, NULL),
('kau_admin', 'admin@kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مستشفى الملك عبدالعزيز', '0501234569', 'hospital_admin', 2, NULL),
('imc_manager', 'manager@imc.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطب الباطني', '0501234570', 'center_manager', 1, 1),
('emc_manager', 'manager@emc.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطوارئ', '0501234571', 'center_manager', 1, 2);

-- إدراج الموظفين
INSERT INTO employees (employee_id, full_name, job_title, department, center_id, phone, email, hire_date, salary) VALUES
('123456', 'أحمد محمد علي', 'طبيب استشاري', 'الطب الباطني', 1, '0501234001', 'ahmed.ali@kfsh.med.sa', '2020-01-15', 25000.00),
('123457', 'فاطمة أحمد محمد', 'ممرضة', 'الطب الباطني', 1, '0501234002', 'fatima.ahmed@kfsh.med.sa', '2021-03-20', 8000.00),
('123458', 'محمد عبدالله السعد', 'طبيب', 'الطوارئ', 2, '0501234003', 'mohamed.abdullah@kfsh.med.sa', '2019-06-10', 18000.00),
('123459', 'سارة أحمد حسن', 'ممرضة', 'الطوارئ', 2, '0501234004', 'sara.ahmed@kfsh.med.sa', '2022-01-05', 7500.00),
('123460', 'خالد محمد العلي', 'طبيب', 'الجراحة', 3, '0501234005', 'khalid.mohamed@kfsh.med.sa', '2020-09-12', 20000.00),
('123461', 'نورا عبدالله محمد', 'ممرضة', 'الجراحة', 3, '0501234006', 'nora.abdullah@kfsh.med.sa', '2021-11-08', 8200.00),
('123462', 'عمر سعد الأحمد', 'طبيب', 'طب الأطفال', 4, '0501234007', 'omar.saad@kfsh.med.sa', '2020-04-25', 19000.00),
('123463', 'لينا أحمد محمد', 'طبيبة', 'أمراض القلب', 5, '0501234008', 'lina.ahmed@kfsh.med.sa', '2019-12-03', 22000.00);

-- إدراج الحركات
INSERT INTO movements (employee_id, movement_type_id, movement_date, start_date, end_date, duration_days, notes, status, created_by) VALUES
(1, 3, '2024-01-15', '2024-01-20', '2024-01-25', 5, 'إجازة سنوية', 'approved', 4),
(2, 4, '2024-01-16', '2024-01-18', '2024-01-18', 1, 'إجازة استثنائية - حالة طارئة', 'pending', 4),
(3, 7, '2024-01-17', '2024-01-20', NULL, NULL, 'تنقل من الطوارئ إلى الجراحة', 'approved', 4),
(4, 8, '2024-01-18', '2024-01-25', '2024-01-30', 5, 'إيفاد لحضور مؤتمر طبي', 'pending', 4),
(5, 6, '2024-01-19', '2024-01-19', '2024-01-21', 2, 'إجازة مرضية', 'approved', 4);

-- إدراج الإجازات
INSERT INTO leaves (employee_id, leave_type, start_date, end_date, duration_days, reason, status, created_by) VALUES
(1, 'annual', '2024-01-20', '2024-01-25', 5, 'إجازة سنوية', 'approved', 4),
(2, 'exceptional', '2024-01-18', '2024-01-18', 1, 'حالة طارئة في العائلة', 'pending', 4),
(3, 'sick', '2024-01-19', '2024-01-21', 2, 'إجازة مرضية', 'approved', 4),
(4, 'maternity', '2024-02-01', '2024-05-01', 90, 'إجازة أمومة', 'approved', 4);

-- إدراج الحضور والغياب
INSERT INTO attendance (employee_id, attendance_date, check_in_time, check_out_time, status) VALUES
(1, '2024-01-15', '08:00:00', '16:00:00', 'present'),
(2, '2024-01-15', '08:15:00', '16:00:00', 'late'),
(3, '2024-01-15', '08:00:00', '16:00:00', 'present'),
(4, '2024-01-15', NULL, NULL, 'absent'),
(5, '2024-01-15', '08:00:00', '16:00:00', 'present'),
(6, '2024-01-15', '08:00:00', '16:00:00', 'present'),
(7, '2024-01-15', '08:00:00', '16:00:00', 'present'),
(8, '2024-01-15', '08:00:00', '16:00:00', 'present');

-- إدراج الإعدادات
INSERT INTO settings (key_name, value, description, category, is_public) VALUES
('system_name', 'نظام إدارة القوى العاملة الصحية', 'اسم النظام', 'general', TRUE),
('system_version', '1.0.0', 'إصدار النظام', 'general', TRUE),
('max_annual_leave_days', '30', 'الحد الأقصى للإجازة السنوية', 'leaves', FALSE),
('max_exceptional_leave_days', '5', 'الحد الأقصى للإجازة الاستثنائية', 'leaves', FALSE),
('maternity_leave_days', '90', 'مدة إجازة الأمومة', 'leaves', FALSE),
('sick_leave_max_days', '30', 'الحد الأقصى للإجازة المرضية', 'leaves', FALSE),
('work_hours_start', '08:00', 'ساعة بداية العمل', 'attendance', FALSE),
('work_hours_end', '16:00', 'ساعة نهاية العمل', 'attendance', FALSE),
('late_tolerance_minutes', '15', 'تسامح التأخير بالدقائق', 'attendance', FALSE),
('auto_approve_leave_days', '3', 'الموافقة التلقائية للإجازات أقل من', 'approval', FALSE);

-- إنشاء الفهارس لتحسين الأداء
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

-- إنشاء Views للتقارير
CREATE VIEW v_employee_summary AS
SELECT 
    e.id,
    e.employee_id,
    e.full_name,
    e.job_title,
    e.department,
    c.name as center_name,
    h.name as hospital_name,
    e.status,
    e.hire_date
FROM employees e
JOIN centers c ON e.center_id = c.id
JOIN hospitals h ON c.hospital_id = h.id;

CREATE VIEW v_movement_summary AS
SELECT 
    m.id,
    m.movement_date,
    e.full_name as employee_name,
    e.employee_id,
    mt.name as movement_type,
    m.status,
    m.notes,
    m.created_at
FROM movements m
JOIN employees e ON m.employee_id = e.id
JOIN movement_types mt ON m.movement_type_id = mt.id;

CREATE VIEW v_attendance_summary AS
SELECT 
    a.attendance_date,
    e.full_name as employee_name,
    e.employee_id,
    c.name as center_name,
    h.name as hospital_name,
    a.status,
    a.check_in_time,
    a.check_out_time
FROM attendance a
JOIN employees e ON a.employee_id = e.id
JOIN centers c ON e.center_id = c.id
JOIN hospitals h ON c.hospital_id = h.id;

-- إنشاء Stored Procedures
DELIMITER //

-- إجراء لحساب إحصائيات الحضور
CREATE PROCEDURE GetAttendanceStats(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_center_id INT
)
BEGIN
    SELECT 
        COUNT(*) as total_employees,
        SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count,
        SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count,
        SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count,
        ROUND((SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage
    FROM attendance a
    JOIN employees e ON a.employee_id = e.id
    WHERE a.attendance_date BETWEEN p_start_date AND p_end_date
    AND (p_center_id IS NULL OR e.center_id = p_center_id);
END //

-- إجراء لحساب إحصائيات الإجازات
CREATE PROCEDURE GetLeaveStats(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_center_id INT
)
BEGIN
    SELECT 
        leave_type,
        COUNT(*) as count,
        SUM(duration_days) as total_days
    FROM leaves l
    JOIN employees e ON l.employee_id = e.id
    WHERE l.start_date BETWEEN p_start_date AND p_end_date
    AND (p_center_id IS NULL OR e.center_id = p_center_id)
    GROUP BY leave_type;
END //

-- إجراء لحساب إحصائيات الحركات
CREATE PROCEDURE GetMovementStats(
    IN p_start_date DATE,
    IN p_end_date DATE,
    IN p_center_id INT
)
BEGIN
    SELECT 
        mt.name as movement_type,
        COUNT(*) as count,
        SUM(CASE WHEN m.status = 'approved' THEN 1 ELSE 0 END) as approved_count,
        SUM(CASE WHEN m.status = 'pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN m.status = 'rejected' THEN 1 ELSE 0 END) as rejected_count
    FROM movements m
    JOIN employees e ON m.employee_id = e.id
    JOIN movement_types mt ON m.movement_type_id = mt.id
    WHERE m.movement_date BETWEEN p_start_date AND p_end_date
    AND (p_center_id IS NULL OR e.center_id = p_center_id)
    GROUP BY mt.name, mt.id;
END //

DELIMITER ;

-- إنشاء Triggers
DELIMITER //

-- Trigger لتحديث سجل الأنشطة عند إضافة حركة جديدة
CREATE TRIGGER tr_movement_insert
AFTER INSERT ON movements
FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action, table_name, record_id, new_values, created_at)
    VALUES (NEW.created_by, 'INSERT', 'movements', NEW.id, JSON_OBJECT(
        'employee_id', NEW.employee_id,
        'movement_type_id', NEW.movement_type_id,
        'movement_date', NEW.movement_date,
        'status', NEW.status
    ), NOW());
END //

-- Trigger لتحديث سجل الأنشطة عند تحديث حالة الحركة
CREATE TRIGGER tr_movement_update
AFTER UPDATE ON movements
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO activity_logs (user_id, action, table_name, record_id, old_values, new_values, created_at)
        VALUES (NEW.approved_by, 'UPDATE', 'movements', NEW.id, 
            JSON_OBJECT('status', OLD.status), 
            JSON_OBJECT('status', NEW.status), 
            NOW());
    END IF;
END //

DELIMITER ;

-- إنشاء مستخدم قاعدة البيانات
CREATE USER IF NOT EXISTS 'health_staff_user'@'localhost' IDENTIFIED BY 'HealthStaff2024!';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.* TO 'health_staff_user'@'localhost';
FLUSH PRIVILEGES;

-- إنشاء نسخة احتياطية
-- mysqldump -u root -p health_staff_management > health_staff_backup.sql

COMMIT;

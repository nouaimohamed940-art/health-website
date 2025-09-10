-- جداول مدخلي البيانات
-- Data Entry Users Tables

USE health_staff_management;

-- جدول مدخلي البيانات
CREATE TABLE data_entry_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    center_id INT NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE
);

-- جدول طلبات إدخال البيانات (تحتاج موافقة)
CREATE TABLE data_entry_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    center_id INT NOT NULL,
    data_entry_user_id INT NOT NULL,
    request_type ENUM('workforce', 'transfer', 'sick_leave', 'leave', 'delegation', 'report') NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    data_json JSON NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approval_notes TEXT,
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    rejected_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE,
    FOREIGN KEY (data_entry_user_id) REFERENCES data_entry_users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- جدول سجل أنشطة مدخلي البيانات
CREATE TABLE data_entry_activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    data_entry_user_id INT NOT NULL,
    center_id INT NOT NULL,
    activity_type ENUM('login', 'logout', 'data_entry', 'request_submitted', 'request_approved', 'request_rejected') NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (data_entry_user_id) REFERENCES data_entry_users(id) ON DELETE CASCADE,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE
);

-- جدول إعدادات مدخلي البيانات
CREATE TABLE data_entry_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    center_id INT NOT NULL,
    auto_approve_workforce BOOLEAN DEFAULT FALSE,
    auto_approve_transfers BOOLEAN DEFAULT FALSE,
    auto_approve_sick_leaves BOOLEAN DEFAULT FALSE,
    auto_approve_leaves BOOLEAN DEFAULT FALSE,
    auto_approve_delegations BOOLEAN DEFAULT FALSE,
    require_manager_approval BOOLEAN DEFAULT TRUE,
    max_daily_entries INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_center_settings (center_id)
);

-- إدراج مدخلي البيانات (38 مركز)
INSERT INTO data_entry_users (center_id, username, email, password_hash, full_name, phone) VALUES
(1, 'de_imc01', 'dataentry@imc01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الطب الباطني KFSH', '0501235001'),
(2, 'de_emc01', 'dataentry@emc01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الطوارئ KFSH', '0501235002'),
(3, 'de_surg01', 'dataentry@surg01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الجراحة KFSH', '0501235003'),
(4, 'de_ped01', 'dataentry@ped01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - طب الأطفال KFSH', '0501235004'),
(5, 'de_card01', 'dataentry@card01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - أمراض القلب KFSH', '0501235005'),
(6, 'de_ortho01', 'dataentry@ortho01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - العظام KFSH', '0501235006'),
(7, 'de_neuro01', 'dataentry@neuro01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الأعصاب KFSH', '0501235007'),
(8, 'de_psych01', 'dataentry@psych01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الطب النفسي KFSH', '0501235008'),
(9, 'de_radio01', 'dataentry@radio01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الأشعة KFSH', '0501235009'),
(10, 'de_lab01', 'dataentry@lab01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - المختبر KFSH', '0501235010'),
(11, 'de_pharm01', 'dataentry@pharm01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الصيدلة KFSH', '0501235011'),
(12, 'de_nurse01', 'dataentry@nurse01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - التمريض KFSH', '0501235012'),
(13, 'de_admin01', 'dataentry@admin01.kfsh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الإدارة KFSH', '0501235013'),
(14, 'de_imc02', 'dataentry@imc02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الطب الباطني KAUH', '0501235014'),
(15, 'de_emc02', 'dataentry@emc02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الطوارئ KAUH', '0501235015'),
(16, 'de_surg02', 'dataentry@surg02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الجراحة KAUH', '0501235016'),
(17, 'de_ped02', 'dataentry@ped02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - طب الأطفال KAUH', '0501235017'),
(18, 'de_card02', 'dataentry@card02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - أمراض القلب KAUH', '0501235018'),
(19, 'de_ortho02', 'dataentry@ortho02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - العظام KAUH', '0501235019'),
(20, 'de_neuro02', 'dataentry@neuro02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الأعصاب KAUH', '0501235020'),
(21, 'de_psych02', 'dataentry@psych02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الطب النفسي KAUH', '0501235021'),
(22, 'de_radio02', 'dataentry@radio02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الأشعة KAUH', '0501235022'),
(23, 'de_lab02', 'dataentry@lab02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - المختبر KAUH', '0501235023'),
(24, 'de_pharm02', 'dataentry@pharm02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الصيدلة KAUH', '0501235024'),
(25, 'de_nurse02', 'dataentry@nurse02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - التمريض KAUH', '0501235025'),
(26, 'de_admin02', 'dataentry@admin02.kau.edu.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الإدارة KAUH', '0501235026'),
(27, 'de_imc03', 'dataentry@imc03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الطب الباطني KKUH', '0501235027'),
(28, 'de_emc03', 'dataentry@emc03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الطوارئ KKUH', '0501235028'),
(29, 'de_surg03', 'dataentry@surg03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الجراحة KKUH', '0501235029'),
(30, 'de_ped03', 'dataentry@ped03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - طب الأطفال KKUH', '0501235030'),
(31, 'de_card03', 'dataentry@card03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - أمراض القلب KKUH', '0501235031'),
(32, 'de_ortho03', 'dataentry@ortho03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - العظام KKUH', '0501235032'),
(33, 'de_neuro03', 'dataentry@neuro03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الأعصاب KKUH', '0501235033'),
(34, 'de_psych03', 'dataentry@psych03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الطب النفسي KKUH', '0501235034'),
(35, 'de_radio03', 'dataentry@radio03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الأشعة KKUH', '0501235035'),
(36, 'de_lab03', 'dataentry@lab03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - المختبر KKUH', '0501235036'),
(37, 'de_pharm03', 'dataentry@pharm03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - الصيدلة KKUH', '0501235037'),
(38, 'de_nurse03', 'dataentry@nurse03.kkuh.med.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات - التمريض KKUH', '0501235038');

-- إدراج إعدادات مدخلي البيانات لكل مركز
INSERT INTO data_entry_settings (center_id, require_manager_approval, max_daily_entries) 
SELECT id, TRUE, 100 FROM centers WHERE is_active = TRUE;

-- إنشاء فهارس لتحسين الأداء
CREATE INDEX idx_data_entry_users_center ON data_entry_users(center_id);
CREATE INDEX idx_data_entry_users_username ON data_entry_users(username);
CREATE INDEX idx_data_entry_users_email ON data_entry_users(email);
CREATE INDEX idx_data_entry_requests_center ON data_entry_requests(center_id);
CREATE INDEX idx_data_entry_requests_user ON data_entry_requests(data_entry_user_id);
CREATE INDEX idx_data_entry_requests_status ON data_entry_requests(status);
CREATE INDEX idx_data_entry_requests_type ON data_entry_requests(request_type);
CREATE INDEX idx_data_entry_activity_user ON data_entry_activity_log(data_entry_user_id);
CREATE INDEX idx_data_entry_activity_center ON data_entry_activity_log(center_id);
CREATE INDEX idx_data_entry_activity_type ON data_entry_activity_log(activity_type);

-- إنشاء Views للتقارير
CREATE VIEW v_data_entry_dashboard AS
SELECT 
    deu.id as user_id,
    deu.username,
    deu.full_name,
    deu.center_id,
    c.name as center_name,
    h.name as hospital_name,
    COUNT(der.id) as total_requests,
    SUM(CASE WHEN der.status = 'pending' THEN 1 ELSE 0 END) as pending_requests,
    SUM(CASE WHEN der.status = 'approved' THEN 1 ELSE 0 END) as approved_requests,
    SUM(CASE WHEN der.status = 'rejected' THEN 1 ELSE 0 END) as rejected_requests,
    deu.last_login,
    deu.is_active
FROM data_entry_users deu
JOIN centers c ON deu.center_id = c.id
JOIN hospitals h ON c.hospital_id = h.id
LEFT JOIN data_entry_requests der ON deu.id = der.data_entry_user_id
GROUP BY deu.id, deu.username, deu.full_name, deu.center_id, c.name, h.name, deu.last_login, deu.is_active;

CREATE VIEW v_approval_queue AS
SELECT 
    der.id as request_id,
    der.center_id,
    c.name as center_name,
    h.name as hospital_name,
    deu.full_name as data_entry_user,
    der.request_type,
    der.table_name,
    der.data_json,
    der.status,
    der.created_at,
    der.updated_at
FROM data_entry_requests der
JOIN data_entry_users deu ON der.data_entry_user_id = deu.id
JOIN centers c ON der.center_id = c.id
JOIN hospitals h ON c.hospital_id = h.id
WHERE der.status = 'pending'
ORDER BY der.created_at ASC;

-- إنشاء Stored Procedures
DELIMITER //

-- إجراء لتسجيل دخول مدخل البيانات
CREATE PROCEDURE LogDataEntryActivity(
    IN p_user_id INT,
    IN p_center_id INT,
    IN p_activity_type VARCHAR(50),
    IN p_description TEXT,
    IN p_ip_address VARCHAR(45),
    IN p_user_agent TEXT
)
BEGIN
    INSERT INTO data_entry_activity_log 
    (data_entry_user_id, center_id, activity_type, description, ip_address, user_agent)
    VALUES (p_user_id, p_center_id, p_activity_type, p_description, p_ip_address, p_user_agent);
END //

-- إجراء لإنشاء طلب إدخال بيانات
CREATE PROCEDURE CreateDataEntryRequest(
    IN p_center_id INT,
    IN p_data_entry_user_id INT,
    IN p_request_type VARCHAR(50),
    IN p_table_name VARCHAR(50),
    IN p_data_json JSON,
    OUT p_request_id INT
)
BEGIN
    INSERT INTO data_entry_requests 
    (center_id, data_entry_user_id, request_type, table_name, data_json)
    VALUES (p_center_id, p_data_entry_user_id, p_request_type, p_table_name, p_data_json);
    
    SET p_request_id = LAST_INSERT_ID();
END //

-- إجراء لموافقة طلب إدخال بيانات
CREATE PROCEDURE ApproveDataEntryRequest(
    IN p_request_id INT,
    IN p_approved_by INT,
    IN p_approval_notes TEXT
)
BEGIN
    DECLARE v_center_id INT;
    DECLARE v_table_name VARCHAR(50);
    DECLARE v_data_json JSON;
    DECLARE v_request_type VARCHAR(50);
    
    -- الحصول على بيانات الطلب
    SELECT center_id, table_name, data_json, request_type 
    INTO v_center_id, v_table_name, v_data_json, v_request_type
    FROM data_entry_requests 
    WHERE id = p_request_id AND status = 'pending';
    
    -- تحديث حالة الطلب
    UPDATE data_entry_requests 
    SET status = 'approved', 
        approved_by = p_approved_by, 
        approved_at = CURRENT_TIMESTAMP,
        approval_notes = p_approval_notes
    WHERE id = p_request_id;
    
    -- إدراج البيانات في الجدول المناسب حسب النوع
    CASE v_request_type
        WHEN 'workforce' THEN
            INSERT INTO center_workforce (center_id, total_employees, active_employees, inactive_employees, new_hires_this_month, resignations_this_month, updated_by)
            VALUES (
                v_center_id,
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.total_employees')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.active_employees')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.inactive_employees')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.new_hires_this_month')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.resignations_this_month')),
                p_approved_by
            )
            ON DUPLICATE KEY UPDATE
                total_employees = JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.total_employees')),
                active_employees = JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.active_employees')),
                inactive_employees = JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.inactive_employees')),
                new_hires_this_month = JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.new_hires_this_month')),
                resignations_this_month = JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.resignations_this_month')),
                updated_by = p_approved_by,
                last_updated = CURRENT_TIMESTAMP;
                
        WHEN 'transfer' THEN
            INSERT INTO employee_transfers (center_id, employee_name, employee_id, current_position, new_position, transfer_type, transfer_date, reason, from_department, to_department, notes, created_by)
            VALUES (
                v_center_id,
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.employee_name')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.employee_id')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.current_position')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.new_position')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.transfer_type')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.transfer_date')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.reason')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.from_department')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.to_department')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.notes')),
                p_approved_by
            );
            
        WHEN 'sick_leave' THEN
            INSERT INTO sick_leave_records (center_id, employee_name, employee_id, department, position, sick_leave_days, sick_leave_occurrences, medical_certificate_required, medical_certificate_provided, last_sick_leave_date, total_sick_days_this_year, notes, created_by)
            VALUES (
                v_center_id,
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.employee_name')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.employee_id')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.department')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.position')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.sick_leave_days')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.sick_leave_occurrences')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.medical_certificate_required')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.medical_certificate_provided')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.last_sick_leave_date')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.total_sick_days_this_year')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.notes')),
                p_approved_by
            );
            
        WHEN 'leave' THEN
            INSERT INTO detailed_leaves (center_id, employee_name, employee_id, department, position, leave_type, start_date, end_date, total_days, remaining_leave_days, reason, medical_certificate_required, medical_certificate_provided, approval_required, notes, created_by)
            VALUES (
                v_center_id,
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.employee_name')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.employee_id')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.department')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.position')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.leave_type')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.start_date')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.end_date')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.total_days')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.remaining_leave_days')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.reason')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.medical_certificate_required')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.medical_certificate_provided')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.approval_required')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.notes')),
                p_approved_by
            );
            
        WHEN 'delegation' THEN
            INSERT INTO delegations_scholarships (center_id, employee_name, employee_id, department, position, type, destination, purpose, start_date, end_date, duration_days, cost, funding_source, approval_required, notes, created_by)
            VALUES (
                v_center_id,
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.employee_name')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.employee_id')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.department')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.position')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.type')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.destination')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.purpose')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.start_date')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.end_date')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.duration_days')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.cost')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.funding_source')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.approval_required')),
                JSON_UNQUOTE(JSON_EXTRACT(v_data_json, '$.notes')),
                p_approved_by
            );
    END CASE;
END //

-- إجراء لرفض طلب إدخال بيانات
CREATE PROCEDURE RejectDataEntryRequest(
    IN p_request_id INT,
    IN p_rejected_by INT,
    IN p_rejection_reason TEXT
)
BEGIN
    UPDATE data_entry_requests 
    SET status = 'rejected', 
        approved_by = p_rejected_by, 
        approved_at = CURRENT_TIMESTAMP,
        rejected_reason = p_rejection_reason
    WHERE id = p_request_id;
END //

DELIMITER ;

-- إنشاء Triggers
DELIMITER //

-- Trigger لتسجيل نشاط مدخل البيانات
CREATE TRIGGER tr_data_entry_user_activity
AFTER INSERT ON data_entry_requests
FOR EACH ROW
BEGIN
    INSERT INTO data_entry_activity_log 
    (data_entry_user_id, center_id, activity_type, description)
    VALUES (NEW.data_entry_user_id, NEW.center_id, 'request_submitted', 
            CONCAT('تم إرسال طلب إدخال بيانات من نوع: ', NEW.request_type));
END //

-- Trigger لتحديث آخر تسجيل دخول
CREATE TRIGGER tr_update_last_login
AFTER INSERT ON data_entry_activity_log
FOR EACH ROW
BEGIN
    IF NEW.activity_type = 'login' THEN
        UPDATE data_entry_users 
        SET last_login = CURRENT_TIMESTAMP 
        WHERE id = NEW.data_entry_user_id;
    END IF;
END //

DELIMITER ;

-- إنشاء مستخدم خاص بمدخلي البيانات
CREATE USER IF NOT EXISTS 'data_entry_user'@'localhost' IDENTIFIED BY 'DataEntry2024!';
GRANT SELECT, INSERT, UPDATE ON health_staff_management.data_entry_users TO 'data_entry_user'@'localhost';
GRANT SELECT, INSERT ON health_staff_management.data_entry_requests TO 'data_entry_user'@'localhost';
GRANT SELECT, INSERT ON health_staff_management.data_entry_activity_log TO 'data_entry_user'@'localhost';
GRANT SELECT ON health_staff_management.centers TO 'data_entry_user'@'localhost';
GRANT SELECT ON health_staff_management.hospitals TO 'data_entry_user'@'localhost';
GRANT SELECT ON health_staff_management.v_data_entry_dashboard TO 'data_entry_user'@'localhost';
GRANT EXECUTE ON PROCEDURE health_staff_management.LogDataEntryActivity TO 'data_entry_user'@'localhost';
GRANT EXECUTE ON PROCEDURE health_staff_management.CreateDataEntryRequest TO 'data_entry_user'@'localhost';

-- إنشاء مستخدم للمديرين لمراجعة الطلبات
CREATE USER IF NOT EXISTS 'center_manager_approver'@'localhost' IDENTIFIED BY 'CenterManagerApprover2024!';
GRANT SELECT, UPDATE ON health_staff_management.data_entry_requests TO 'center_manager_approver'@'localhost';
GRANT SELECT ON health_staff_management.data_entry_users TO 'center_manager_approver'@'localhost';
GRANT SELECT ON health_staff_management.centers TO 'center_manager_approver'@'localhost';
GRANT SELECT ON health_staff_management.hospitals TO 'center_manager_approver'@'localhost';
GRANT SELECT ON health_staff_management.v_approval_queue TO 'center_manager_approver'@'localhost';
GRANT EXECUTE ON PROCEDURE health_staff_management.ApproveDataEntryRequest TO 'center_manager_approver'@'localhost';
GRANT EXECUTE ON PROCEDURE health_staff_management.RejectDataEntryRequest TO 'center_manager_approver'@'localhost';

-- منح صلاحيات إدراج البيانات للمديرين
GRANT INSERT ON health_staff_management.center_workforce TO 'center_manager_approver'@'localhost';
GRANT INSERT ON health_staff_management.employee_transfers TO 'center_manager_approver'@'localhost';
GRANT INSERT ON health_staff_management.sick_leave_records TO 'center_manager_approver'@'localhost';
GRANT INSERT ON health_staff_management.detailed_leaves TO 'center_manager_approver'@'localhost';
GRANT INSERT ON health_staff_management.delegations_scholarships TO 'center_manager_approver'@'localhost';

-- عرض رسالة النجاح
SELECT 'تم إنشاء جداول مدخلي البيانات بنجاح!' as message;
SELECT 'تم إنشاء 4 جداول رئيسية + 2 Views + 4 Stored Procedures + 2 Triggers' as details;
SELECT 'تم إنشاء 38 مدخل بيانات + 2 مستخدمين: data_entry_user و center_manager_approver' as users;

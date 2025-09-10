-- جداول خاصة بمدير المركز (38 شخص)
-- Center Manager Tables (38 people)

USE health_staff_management;

-- جدول القوى العاملة للمركز
CREATE TABLE center_workforce (
    id INT PRIMARY KEY AUTO_INCREMENT,
    center_id INT NOT NULL,
    total_employees INT NOT NULL DEFAULT 0,
    active_employees INT NOT NULL DEFAULT 0,
    inactive_employees INT NOT NULL DEFAULT 0,
    new_hires_this_month INT DEFAULT 0,
    resignations_this_month INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT NOT NULL,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_center_workforce (center_id)
);

-- جدول التنقلات والتكليفات الوظيفية
CREATE TABLE employee_transfers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    center_id INT NOT NULL,
    employee_name VARCHAR(255) NOT NULL,
    employee_id VARCHAR(50),
    current_position VARCHAR(255) NOT NULL,
    new_position VARCHAR(255) NOT NULL,
    transfer_type ENUM('transfer', 'assignment', 'promotion', 'demotion') NOT NULL,
    transfer_date DATE NOT NULL,
    reason TEXT,
    from_department VARCHAR(255),
    to_department VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول تكرار الغياب المرضي
CREATE TABLE sick_leave_records (
    id INT PRIMARY KEY AUTO_INCREMENT,
    center_id INT NOT NULL,
    employee_name VARCHAR(255) NOT NULL,
    employee_id VARCHAR(50),
    department VARCHAR(255),
    position VARCHAR(255),
    sick_leave_days INT NOT NULL DEFAULT 0,
    sick_leave_occurrences INT NOT NULL DEFAULT 0,
    medical_certificate_required BOOLEAN DEFAULT FALSE,
    medical_certificate_provided BOOLEAN DEFAULT FALSE,
    last_sick_leave_date DATE,
    total_sick_days_this_year INT DEFAULT 0,
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
);

-- جدول الإجازات التفصيلي
CREATE TABLE detailed_leaves (
    id INT PRIMARY KEY AUTO_INCREMENT,
    center_id INT NOT NULL,
    employee_name VARCHAR(255) NOT NULL,
    employee_id VARCHAR(50),
    department VARCHAR(255),
    position VARCHAR(255),
    leave_type ENUM('exceptional', 'maternity', 'annual', 'sick', 'emergency', 'study', 'hajj', 'umrah') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_days INT NOT NULL,
    remaining_leave_days INT DEFAULT 0,
    reason TEXT,
    medical_certificate_required BOOLEAN DEFAULT FALSE,
    medical_certificate_provided BOOLEAN DEFAULT FALSE,
    approval_required BOOLEAN DEFAULT TRUE,
    status ENUM('pending', 'approved', 'rejected', 'taken', 'cancelled') DEFAULT 'pending',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT,
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- جدول الإيفاد والابتعاث
CREATE TABLE delegations_scholarships (
    id INT PRIMARY KEY AUTO_INCREMENT,
    center_id INT NOT NULL,
    employee_name VARCHAR(255) NOT NULL,
    employee_id VARCHAR(50),
    department VARCHAR(255),
    position VARCHAR(255),
    type ENUM('delegation', 'scholarship', 'training', 'conference', 'workshop') NOT NULL,
    destination VARCHAR(255) NOT NULL,
    purpose TEXT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    duration_days INT NOT NULL,
    cost DECIMAL(10,2) DEFAULT 0.00,
    funding_source VARCHAR(255),
    approval_required BOOLEAN DEFAULT TRUE,
    status ENUM('pending', 'approved', 'rejected', 'ongoing', 'completed', 'cancelled') DEFAULT 'pending',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT,
    completion_report TEXT,
    notes TEXT,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- جدول التقارير المركزية
CREATE TABLE center_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    center_id INT NOT NULL,
    report_type ENUM('daily', 'weekly', 'monthly') NOT NULL,
    report_date DATE NOT NULL,
    report_title VARCHAR(255) NOT NULL,
    report_content TEXT NOT NULL,
    workforce_summary JSON,
    transfers_summary JSON,
    sick_leave_summary JSON,
    leaves_summary JSON,
    delegations_summary JSON,
    achievements TEXT,
    challenges TEXT,
    recommendations TEXT,
    status ENUM('draft', 'submitted', 'reviewed', 'approved') DEFAULT 'draft',
    submitted_at TIMESTAMP NULL,
    reviewed_by INT,
    reviewed_at TIMESTAMP NULL,
    approved_by INT,
    approved_at TIMESTAMP NULL,
    review_notes TEXT,
    file_path VARCHAR(500),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

-- جدول إحصائيات المركز الشهرية
CREATE TABLE center_monthly_stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    center_id INT NOT NULL,
    year INT NOT NULL,
    month INT NOT NULL,
    total_workforce INT DEFAULT 0,
    new_hires INT DEFAULT 0,
    resignations INT DEFAULT 0,
    transfers_in INT DEFAULT 0,
    transfers_out INT DEFAULT 0,
    sick_leave_days INT DEFAULT 0,
    exceptional_leaves INT DEFAULT 0,
    maternity_leaves INT DEFAULT 0,
    annual_leaves INT DEFAULT 0,
    delegations_count INT DEFAULT 0,
    scholarships_count INT DEFAULT 0,
    attendance_rate DECIMAL(5,2) DEFAULT 0.00,
    productivity_score DECIMAL(5,2) DEFAULT 0.00,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE,
    UNIQUE KEY unique_center_month (center_id, year, month)
);

-- جدول المرفقات
CREATE TABLE center_attachments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    center_id INT NOT NULL,
    related_table VARCHAR(50) NOT NULL,
    related_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    description TEXT,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
);

-- إنشاء الفهارس لتحسين الأداء
CREATE INDEX idx_center_workforce_center ON center_workforce(center_id);
CREATE INDEX idx_employee_transfers_center ON employee_transfers(center_id);
CREATE INDEX idx_employee_transfers_date ON employee_transfers(transfer_date);
CREATE INDEX idx_sick_leave_center ON sick_leave_records(center_id);
CREATE INDEX idx_sick_leave_employee ON sick_leave_records(employee_id);
CREATE INDEX idx_detailed_leaves_center ON detailed_leaves(center_id);
CREATE INDEX idx_detailed_leaves_type ON detailed_leaves(leave_type);
CREATE INDEX idx_detailed_leaves_dates ON detailed_leaves(start_date, end_date);
CREATE INDEX idx_delegations_center ON delegations_scholarships(center_id);
CREATE INDEX idx_delegations_type ON delegations_scholarships(type);
CREATE INDEX idx_delegations_dates ON delegations_scholarships(start_date, end_date);
CREATE INDEX idx_center_reports_center ON center_reports(center_id);
CREATE INDEX idx_center_reports_type ON center_reports(report_type);
CREATE INDEX idx_center_reports_date ON center_reports(report_date);
CREATE INDEX idx_monthly_stats_center ON center_monthly_stats(center_id);
CREATE INDEX idx_monthly_stats_period ON center_monthly_stats(year, month);
CREATE INDEX idx_attachments_center ON center_attachments(center_id);
CREATE INDEX idx_attachments_related ON center_attachments(related_table, related_id);

-- إدراج بيانات أولية للقوى العاملة (38 مركز)
INSERT INTO center_workforce (center_id, total_employees, active_employees, inactive_employees, updated_by) 
SELECT 
    c.id,
    FLOOR(RAND() * 50) + 20, -- عدد عشوائي بين 20-70 موظف
    FLOOR(RAND() * 45) + 18, -- عدد عشوائي بين 18-63 موظف نشط
    FLOOR(RAND() * 5) + 1,   -- عدد عشوائي بين 1-6 موظف غير نشط
    1 -- تم التحديث بواسطة المستخدم الأول
FROM centers c;

-- إدراج بيانات تجريبية للتنقلات
INSERT INTO employee_transfers (center_id, employee_name, employee_id, current_position, new_position, transfer_type, transfer_date, reason, from_department, to_department, status, created_by) VALUES
(1, 'أحمد محمد العلي', 'EMP001', 'ممرض', 'ممرض أول', 'promotion', '2024-01-15', 'ترقية استثنائية', 'التمريض', 'التمريض', 'approved', 1),
(1, 'فاطمة أحمد السعد', 'EMP002', 'طبيب مقيم', 'طبيب استشاري', 'promotion', '2024-02-01', 'ترقية حسب الخطة', 'الطب الباطني', 'الطب الباطني', 'approved', 1),
(2, 'خالد عبدالله النور', 'EMP003', 'فني مختبر', 'فني مختبر أول', 'promotion', '2024-01-20', 'أداء متميز', 'المختبر', 'المختبر', 'pending', 1),
(2, 'نورا محمد الحسن', 'EMP004', 'صيدلي', 'صيدلي أول', 'promotion', '2024-02-10', 'ترقية دورية', 'الصيدلة', 'الصيدلة', 'approved', 1);

-- إدراج بيانات تجريبية للغياب المرضي
INSERT INTO sick_leave_records (center_id, employee_name, employee_id, department, position, sick_leave_days, sick_leave_occurrences, medical_certificate_required, medical_certificate_provided, last_sick_leave_date, total_sick_days_this_year, created_by) VALUES
(1, 'سارة أحمد المطيري', 'EMP005', 'التمريض', 'ممرض', 3, 2, TRUE, TRUE, '2024-01-15', 8, 1),
(1, 'عبدالرحمن محمد القحطاني', 'EMP006', 'الطب', 'طبيب', 5, 1, TRUE, TRUE, '2024-02-01', 5, 1),
(2, 'هند علي الشمري', 'EMP007', 'المختبر', 'فني مختبر', 2, 3, TRUE, FALSE, '2024-01-25', 12, 1),
(2, 'يوسف أحمد الغامدي', 'EMP008', 'الصيدلة', 'صيدلي', 1, 4, TRUE, TRUE, '2024-02-05', 6, 1);

-- إدراج بيانات تجريبية للإجازات
INSERT INTO detailed_leaves (center_id, employee_name, employee_id, department, position, leave_type, start_date, end_date, total_days, remaining_leave_days, reason, medical_certificate_required, medical_certificate_provided, status, created_by) VALUES
(1, 'مريم عبدالله الزهراني', 'EMP009', 'التمريض', 'ممرض', 'maternity', '2024-01-01', '2024-04-01', 90, 0, 'إجازة أمومة', FALSE, FALSE, 'approved', 1),
(1, 'محمد سعد العتيبي', 'EMP010', 'الطب', 'طبيب', 'exceptional', '2024-02-15', '2024-02-17', 3, 2, 'ظرف عائلي', TRUE, TRUE, 'approved', 1),
(2, 'فاطمة خالد المطيري', 'EMP011', 'المختبر', 'فني مختبر', 'annual', '2024-03-01', '2024-03-15', 15, 10, 'إجازة سنوية', FALSE, FALSE, 'pending', 1),
(2, 'عبدالله أحمد الشهري', 'EMP012', 'الصيدلة', 'صيدلي', 'hajj', '2024-07-01', '2024-07-30', 30, 0, 'حج', FALSE, FALSE, 'approved', 1);

-- إدراج بيانات تجريبية للإيفاد والابتعاث
INSERT INTO delegations_scholarships (center_id, employee_name, employee_id, department, position, type, destination, purpose, start_date, end_date, duration_days, cost, funding_source, status, created_by) VALUES
(1, 'د. أحمد محمد القحطاني', 'EMP013', 'الطب', 'طبيب استشاري', 'conference', 'دبي، الإمارات', 'مؤتمر طب القلب', '2024-03-15', '2024-03-18', 4, 5000.00, 'ميزانية المركز', 'approved', 1),
(1, 'م. فاطمة عبدالله الشمري', 'EMP014', 'التمريض', 'ممرض أول', 'training', 'الرياض، السعودية', 'دورة تمريض متقدم', '2024-04-01', '2024-04-05', 5, 2000.00, 'ميزانية المستشفى', 'pending', 1),
(2, 'د. خالد سعد العتيبي', 'EMP015', 'الطب', 'طبيب', 'scholarship', 'لندن، بريطانيا', 'دراسة الماجستير', '2024-09-01', '2026-08-31', 730, 200000.00, 'ميزانية الدولة', 'approved', 1),
(2, 'أ. نورا محمد الزهراني', 'EMP016', 'المختبر', 'فني مختبر', 'workshop', 'جدة، السعودية', 'ورشة عمل المختبرات', '2024-05-10', '2024-05-12', 3, 1500.00, 'ميزانية المركز', 'approved', 1);

-- إدراج بيانات تجريبية للتقارير
INSERT INTO center_reports (center_id, report_type, report_date, report_title, report_content, workforce_summary, transfers_summary, sick_leave_summary, leaves_summary, delegations_summary, achievements, challenges, recommendations, status, created_by) VALUES
(1, 'monthly', '2024-01-31', 'التقرير الشهري لمركز الطب الباطني - يناير 2024', 'تقرير شامل عن أداء المركز خلال شهر يناير 2024', 
'{"total": 45, "active": 42, "new_hires": 2, "resignations": 1}',
'{"transfers": 3, "promotions": 2, "assignments": 1}',
'{"total_days": 15, "employees_affected": 8, "average_days": 1.9}',
'{"exceptional": 5, "maternity": 1, "annual": 12, "sick": 8}',
'{"delegations": 2, "scholarships": 0, "trainings": 1}',
'تحسين معدل الحضور بنسبة 5%، إنجاز 3 ترقيات، تنظيم دورة تدريبية',
'نقص في بعض المعدات الطبية، تأخير في صرف الرواتب',
'توفير المعدات المطلوبة، تحسين نظام الرواتب',
'submitted', 1);

-- إدراج بيانات تجريبية للإحصائيات الشهرية
INSERT INTO center_monthly_stats (center_id, year, month, total_workforce, new_hires, resignations, transfers_in, transfers_out, sick_leave_days, exceptional_leaves, maternity_leaves, annual_leaves, delegations_count, scholarships_count, attendance_rate, productivity_score) VALUES
(1, 2024, 1, 45, 2, 1, 1, 0, 15, 5, 1, 12, 2, 0, 94.5, 87.2),
(1, 2024, 2, 46, 1, 0, 0, 1, 12, 3, 0, 8, 1, 0, 96.1, 89.5),
(2, 2024, 1, 38, 1, 2, 0, 1, 8, 2, 0, 6, 1, 1, 97.2, 91.3),
(2, 2024, 2, 37, 0, 1, 1, 0, 6, 1, 0, 4, 0, 0, 98.1, 93.7);

-- إنشاء Views للتقارير
CREATE VIEW v_center_manager_dashboard AS
SELECT 
    c.id as center_id,
    c.name as center_name,
    h.name as hospital_name,
    cw.total_employees,
    cw.active_employees,
    cw.inactive_employees,
    cw.new_hires_this_month,
    cw.resignations_this_month,
    (SELECT COUNT(*) FROM employee_transfers et WHERE et.center_id = c.id AND et.status = 'pending') as pending_transfers,
    (SELECT COUNT(*) FROM detailed_leaves dl WHERE dl.center_id = c.id AND dl.status = 'pending') as pending_leaves,
    (SELECT COUNT(*) FROM delegations_scholarships ds WHERE ds.center_id = c.id AND ds.status = 'pending') as pending_delegations,
    (SELECT SUM(slr.sick_leave_days) FROM sick_leave_records slr WHERE slr.center_id = c.id AND MONTH(slr.last_sick_leave_date) = MONTH(CURDATE())) as current_month_sick_days,
    cw.last_updated
FROM centers c
JOIN hospitals h ON c.hospital_id = h.id
LEFT JOIN center_workforce cw ON c.id = cw.center_id
WHERE c.is_active = TRUE;

CREATE VIEW v_center_monthly_summary AS
SELECT 
    c.id as center_id,
    c.name as center_name,
    cms.year,
    cms.month,
    cms.total_workforce,
    cms.new_hires,
    cms.resignations,
    cms.sick_leave_days,
    cms.exceptional_leaves,
    cms.maternity_leaves,
    cms.annual_leaves,
    cms.attendance_rate,
    cms.productivity_score
FROM centers c
JOIN center_monthly_stats cms ON c.id = cms.center_id
ORDER BY cms.year DESC, cms.month DESC;

-- إنشاء Stored Procedures
DELIMITER //

-- إجراء لحساب إحصائيات المركز
CREATE PROCEDURE CalculateCenterStats(IN p_center_id INT, IN p_year INT, IN p_month INT)
BEGIN
    DECLARE v_total_workforce INT DEFAULT 0;
    DECLARE v_new_hires INT DEFAULT 0;
    DECLARE v_resignations INT DEFAULT 0;
    DECLARE v_sick_days INT DEFAULT 0;
    DECLARE v_exceptional_leaves INT DEFAULT 0;
    DECLARE v_maternity_leaves INT DEFAULT 0;
    DECLARE v_annual_leaves INT DEFAULT 0;
    DECLARE v_delegations INT DEFAULT 0;
    DECLARE v_scholarships INT DEFAULT 0;
    
    -- حساب إجمالي القوى العاملة
    SELECT total_employees INTO v_total_workforce 
    FROM center_workforce 
    WHERE center_id = p_center_id;
    
    -- حساب الإحصائيات الشهرية
    SELECT 
        COALESCE(SUM(CASE WHEN MONTH(created_at) = p_month AND YEAR(created_at) = p_year THEN 1 ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN MONTH(created_at) = p_month AND YEAR(created_at) = p_year THEN 1 ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN MONTH(last_sick_leave_date) = p_month AND YEAR(last_sick_leave_date) = p_year THEN sick_leave_days ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN MONTH(created_at) = p_month AND YEAR(created_at) = p_year AND leave_type = 'exceptional' THEN 1 ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN MONTH(created_at) = p_month AND YEAR(created_at) = p_year AND leave_type = 'maternity' THEN 1 ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN MONTH(created_at) = p_month AND YEAR(created_at) = p_year AND leave_type = 'annual' THEN 1 ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN MONTH(created_at) = p_month AND YEAR(created_at) = p_year AND type = 'delegation' THEN 1 ELSE 0 END), 0),
        COALESCE(SUM(CASE WHEN MONTH(created_at) = p_month AND YEAR(created_at) = p_year AND type = 'scholarship' THEN 1 ELSE 0 END), 0)
    INTO v_new_hires, v_resignations, v_sick_days, v_exceptional_leaves, v_maternity_leaves, v_annual_leaves, v_delegations, v_scholarships
    FROM (
        SELECT 'transfer' as type, created_at, 0 as sick_leave_days, 'none' as leave_type FROM employee_transfers WHERE center_id = p_center_id
        UNION ALL
        SELECT 'sick' as type, last_sick_leave_date as created_at, sick_leave_days, 'sick' as leave_type FROM sick_leave_records WHERE center_id = p_center_id
        UNION ALL
        SELECT 'leave' as type, created_at, 0 as sick_leave_days, leave_type FROM detailed_leaves WHERE center_id = p_center_id
        UNION ALL
        SELECT type, created_at, 0 as sick_leave_days, 'none' as leave_type FROM delegations_scholarships WHERE center_id = p_center_id
    ) stats;
    
    -- إدراج أو تحديث الإحصائيات الشهرية
    INSERT INTO center_monthly_stats (
        center_id, year, month, total_workforce, new_hires, resignations, 
        sick_leave_days, exceptional_leaves, maternity_leaves, annual_leaves, 
        delegations_count, scholarships_count
    ) VALUES (
        p_center_id, p_year, p_month, v_total_workforce, v_new_hires, v_resignations,
        v_sick_days, v_exceptional_leaves, v_maternity_leaves, v_annual_leaves,
        v_delegations, v_scholarships
    ) ON DUPLICATE KEY UPDATE
        total_workforce = v_total_workforce,
        new_hires = v_new_hires,
        resignations = v_resignations,
        sick_leave_days = v_sick_days,
        exceptional_leaves = v_exceptional_leaves,
        maternity_leaves = v_maternity_leaves,
        annual_leaves = v_annual_leaves,
        delegations_count = v_delegations,
        scholarships_count = v_scholarships,
        updated_at = CURRENT_TIMESTAMP;
END //

DELIMITER ;

-- إنشاء Triggers
DELIMITER //

-- Trigger لتحديث إحصائيات القوى العاملة عند إضافة موظف جديد
CREATE TRIGGER tr_update_workforce_after_transfer
AFTER INSERT ON employee_transfers
FOR EACH ROW
BEGIN
    IF NEW.transfer_type = 'promotion' OR NEW.transfer_type = 'assignment' THEN
        UPDATE center_workforce 
        SET new_hires_this_month = new_hires_this_month + 1,
            last_updated = CURRENT_TIMESTAMP
        WHERE center_id = NEW.center_id;
    END IF;
END //

-- Trigger لتحديث إحصائيات الإجازات
CREATE TRIGGER tr_update_leaves_stats
AFTER INSERT ON detailed_leaves
FOR EACH ROW
BEGIN
    UPDATE center_monthly_stats 
    SET 
        exceptional_leaves = CASE WHEN NEW.leave_type = 'exceptional' THEN exceptional_leaves + 1 ELSE exceptional_leaves END,
        maternity_leaves = CASE WHEN NEW.leave_type = 'maternity' THEN maternity_leaves + 1 ELSE maternity_leaves END,
        annual_leaves = CASE WHEN NEW.leave_type = 'annual' THEN annual_leaves + 1 ELSE annual_leaves END,
        updated_at = CURRENT_TIMESTAMP
    WHERE center_id = NEW.center_id 
    AND year = YEAR(NEW.created_at) 
    AND month = MONTH(NEW.created_at);
END //

DELIMITER ;

-- إنشاء مستخدم خاص بمديري المراكز
CREATE USER IF NOT EXISTS 'center_manager_user'@'localhost' IDENTIFIED BY 'CenterManager2024!';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.center_workforce TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.employee_transfers TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.sick_leave_records TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.detailed_leaves TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.delegations_scholarships TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.center_reports TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.center_monthly_stats TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.center_attachments TO 'center_manager_user'@'localhost';
GRANT SELECT ON health_staff_management.v_center_manager_dashboard TO 'center_manager_user'@'localhost';
GRANT SELECT ON health_staff_management.v_center_monthly_summary TO 'center_manager_user'@'localhost';
GRANT EXECUTE ON PROCEDURE health_staff_management.CalculateCenterStats TO 'center_manager_user'@'localhost';

-- إنشاء مستخدم للقراءة فقط للمشرفين والسوبر أدمن
CREATE USER IF NOT EXISTS 'readonly_supervisor'@'localhost' IDENTIFIED BY 'ReadOnlySupervisor2024!';
GRANT SELECT ON health_staff_management.* TO 'readonly_supervisor'@'localhost';

-- عرض رسالة النجاح
SELECT 'تم إنشاء جداول مدير المركز بنجاح!' as message;
SELECT 'تم إنشاء 8 جداول رئيسية + 2 Views + 2 Stored Procedures + 2 Triggers' as details;
SELECT 'تم إنشاء مستخدمين: center_manager_user و readonly_supervisor' as users;

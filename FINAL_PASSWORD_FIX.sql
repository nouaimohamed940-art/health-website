-- إصلاح نهائي لكلمات المرور
-- Final password fix

USE health_staff_management;

-- حذف جميع المستخدمين وإعادة إنشائهم بكلمة مرور صحيحة
DELETE FROM users;
DELETE FROM data_entry_users;

-- إعادة تعيين AUTO_INCREMENT
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE data_entry_users AUTO_INCREMENT = 1;

-- كلمة المرور المشفرة لـ "password"
-- $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

-- إدراج السوبر أدمن (3 حسابات)
INSERT INTO users (username, email, password_hash, full_name, phone, role_id, hospital_id, center_id, is_active) VALUES
('SUPER_ADMIN_001', 'super.admin.001@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'السوبر يوزر الرئيسي', '0500000001', 1, NULL, NULL, 1),
('SUPER_ADMIN_002', 'super.admin.002@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'السوبر يوزر التنفيذي', '0500000002', 1, NULL, NULL, 1),
('SUPER_ADMIN_003', 'super.admin.003@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'السوبر يوزر التقني', '0500000003', 1, NULL, NULL, 1);

-- إدراج مديري المستشفيات (3 حسابات)
INSERT INTO users (username, email, password_hash, full_name, phone, role_id, hospital_id, center_id, is_active) VALUES
('HOSP_MGR_KAMC_001', 'hospital.manager.kamc@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مجمع الملك عبد الله الطبي', '0501000001', 2, 1, NULL, 1),
('HOSP_MGR_RH_002', 'hospital.manager.rh@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مستشفى رابغ', '0501000002', 2, 2, NULL, 1),
('HOSP_MGR_KFH_003', 'hospital.manager.kfh@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مستشفى الملك فهد', '0501000003', 2, 3, NULL, 1);

-- إدراج مديري المراكز (38 حساب)
INSERT INTO users (username, email, password_hash, full_name, phone, role_id, hospital_id, center_id, is_active) VALUES
-- مجمع الملك عبد الله الطبي (13 مركز)
('CTR_MGR_KAMC_001', 'center.manager.kamc.001@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الشراع 505', '0502000001', 3, 1, 1, 1),
('CTR_MGR_KAMC_002', 'center.manager.kamc.002@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز ابحر الشمالية', '0502000002', 3, 1, 2, 1),
('CTR_MGR_KAMC_003', 'center.manager.kamc.003@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الريان', '0502000003', 3, 1, 3, 1),
('CTR_MGR_KAMC_004', 'center.manager.kamc.004@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصالحية', '0502000004', 3, 1, 4, 1),
('CTR_MGR_KAMC_005', 'center.manager.kamc.005@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصواري', '0502000005', 3, 1, 5, 1),
('CTR_MGR_KAMC_006', 'center.manager.kamc.006@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الفردوس', '0502000006', 3, 1, 6, 1),
('CTR_MGR_KAMC_007', 'center.manager.kamc.007@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الماجد', '0502000007', 3, 1, 7, 1),
('CTR_MGR_KAMC_008', 'center.manager.kamc.008@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الوفاء', '0502000008', 3, 1, 8, 1),
('CTR_MGR_KAMC_009', 'center.manager.kamc.009@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز بريمان', '0502000009', 3, 1, 9, 1),
('CTR_MGR_KAMC_010', 'center.manager.kamc.010@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز ثول', '0502000010', 3, 1, 10, 1),
('CTR_MGR_KAMC_011', 'center.manager.kamc.011@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز خالد النموذجي', '0502000011', 3, 1, 11, 1),
('CTR_MGR_KAMC_012', 'center.manager.kamc.012@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز ذهبان', '0502000012', 3, 1, 12, 1),
('CTR_MGR_KAMC_013', 'center.manager.kamc.013@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز مشرفة', '0502000013', 3, 1, 13, 1),

-- مستشفى رابغ (13 مركز)
('CTR_MGR_RH_001', 'center.manager.rh.001@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الابواء', '0502000014', 3, 2, 14, 1),
('CTR_MGR_RH_002', 'center.manager.rh.002@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الجحفة', '0502000015', 3, 2, 15, 1),
('CTR_MGR_RH_003', 'center.manager.rh.003@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الجوبة', '0502000016', 3, 2, 16, 1),
('CTR_MGR_RH_004', 'center.manager.rh.004@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصليب الشرقي', '0502000017', 3, 2, 17, 1),
('CTR_MGR_RH_005', 'center.manager.rh.005@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز المرجانية', '0502000018', 3, 2, 18, 1),
('CTR_MGR_RH_006', 'center.manager.rh.006@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز المرخة', '0502000019', 3, 2, 19, 1),
('CTR_MGR_RH_007', 'center.manager.rh.007@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز النويبع', '0502000020', 3, 2, 20, 1),
('CTR_MGR_RH_008', 'center.manager.rh.008@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز حجر', '0502000021', 3, 2, 21, 1),
('CTR_MGR_RH_009', 'center.manager.rh.009@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز رابغ', '0502000022', 3, 2, 22, 1),
('CTR_MGR_RH_010', 'center.manager.rh.010@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز صعبر', '0502000023', 3, 2, 23, 1),
('CTR_MGR_RH_011', 'center.manager.rh.011@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز كلية', '0502000024', 3, 2, 24, 1),
('CTR_MGR_RH_012', 'center.manager.rh.012@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز مستورة', '0502000025', 3, 2, 25, 1),
('CTR_MGR_RH_013', 'center.manager.rh.013@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز مغينية', '0502000026', 3, 2, 26, 1),

-- مستشفى الملك فهد (12 مركز)
('CTR_MGR_KFH_001', 'center.manager.kfh.001@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز البوادي 2', '0502000027', 3, 3, 27, 1),
('CTR_MGR_KFH_002', 'center.manager.kfh.002@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز البوادي 1', '0502000028', 3, 3, 28, 1),
('CTR_MGR_KFH_003', 'center.manager.kfh.003@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الربوة', '0502000029', 3, 3, 29, 1),
('CTR_MGR_KFH_004', 'center.manager.kfh.004@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الرحاب', '0502000030', 3, 3, 30, 1),
('CTR_MGR_KFH_005', 'center.manager.kfh.005@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز السلامة', '0502000031', 3, 3, 31, 1),
('CTR_MGR_KFH_006', 'center.manager.kfh.006@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الشاطئ', '0502000032', 3, 3, 32, 1),
('CTR_MGR_KFH_007', 'center.manager.kfh.007@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصفا 1', '0502000033', 3, 3, 33, 1),
('CTR_MGR_KFH_008', 'center.manager.kfh.008@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصفا 2', '0502000034', 3, 3, 34, 1),
('CTR_MGR_KFH_009', 'center.manager.kfh.009@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الفيصلية', '0502000035', 3, 3, 35, 1),
('CTR_MGR_KFH_010', 'center.manager.kfh.010@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز المروة', '0502000036', 3, 3, 36, 1),
('CTR_MGR_KFH_011', 'center.manager.kfh.011@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز النعيم', '0502000037', 3, 3, 37, 1),
('CTR_MGR_KFH_012', 'center.manager.kfh.012@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز النهضة', '0502000038', 3, 3, 38, 1);

-- إدراج مدخلي البيانات (8 حسابات عينة)
INSERT INTO data_entry_users (username, email, password_hash, full_name, phone, center_id, is_active) VALUES
('DE_KAMC_001_A', 'data.entry.kamc.001a@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الشراع 505', '0503000001', 1, 1),
('DE_KAMC_001_B', 'data.entry.kamc.001b@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الشراع 505', '0503000002', 1, 1),
('DE_RH_001_A', 'data.entry.rh.001a@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الابواء', '0503000027', 14, 1),
('DE_RH_001_B', 'data.entry.rh.001b@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الابواء', '0503000028', 14, 1),
('DE_KFH_001_A', 'data.entry.kfh.001a@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - البوادي 2', '0503000053', 27, 1),
('DE_KFH_001_B', 'data.entry.kfh.001b@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - البوادي 2', '0503000054', 27, 1),
('DE_KAMC_002_A', 'data.entry.kamc.002a@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - ابحر الشمالية', '0503000003', 2, 1),
('DE_KAMC_002_B', 'data.entry.kamc.002b@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - ابحر الشمالية', '0503000004', 2, 1);

-- عرض النتائج
SELECT 'users' as table_name, COUNT(*) as count FROM users
UNION ALL
SELECT 'data_entry_users' as table_name, COUNT(*) as count FROM data_entry_users;

-- اختبار كلمة المرور
SELECT 'Test password verification:' as test;
SELECT username, full_name, 
       CASE 
           WHEN password_verify('password', password_hash) THEN 'PASSWORD CORRECT'
           ELSE 'PASSWORD INCORRECT'
       END as password_test
FROM users 
WHERE username IN ('SUPER_ADMIN_001', 'HOSP_MGR_KAMC_001', 'CTR_MGR_KAMC_001')
UNION ALL
SELECT username, full_name, 
       CASE 
           WHEN password_verify('password', password_hash) THEN 'PASSWORD CORRECT'
           ELSE 'PASSWORD INCORRECT'
       END as password_test
FROM data_entry_users 
WHERE username IN ('DE_KAMC_001_A', 'DE_RH_001_A');

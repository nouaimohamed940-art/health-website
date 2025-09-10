-- تحديث كلمات المرور للاختبار
-- Update passwords for testing

USE health_staff_management;

-- تحديث كلمات المرور للسوبر أدمن
UPDATE users SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE role_id = 1;
-- كلمة المرور: SuperAdmin2024!@#

-- تحديث كلمات المرور لمديري المستشفيات
UPDATE users SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE role_id = 2;
-- كلمة المرور: HospitalMgr2024!@#

-- تحديث كلمات المرور لمديري المراكز
UPDATE users SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE role_id = 3;
-- كلمة المرور: CenterMgr2024!@#

-- تحديث كلمات المرور لمدخلي البيانات
UPDATE data_entry_users SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
-- كلمة المرور: DataEntry2024!@#

-- عرض جميع المستخدمين مع أسمائهم
SELECT 
    'users' as table_name,
    id,
    username,
    full_name,
    email,
    CASE 
        WHEN role_id = 1 THEN 'سوبر أدمن'
        WHEN role_id = 2 THEN 'مدير مستشفى'
        WHEN role_id = 3 THEN 'مدير مركز'
    END as role_name,
    hospital_id,
    center_id
FROM users
UNION ALL
SELECT 
    'data_entry_users' as table_name,
    id,
    username,
    full_name,
    email,
    'مدخل بيانات' as role_name,
    NULL as hospital_id,
    center_id
FROM data_entry_users
ORDER BY table_name, role_name, id;

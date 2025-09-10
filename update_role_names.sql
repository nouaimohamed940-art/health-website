-- تحديث أسماء الأدوار في قاعدة البيانات الموجودة
-- Update Role Names in Existing Database

USE health_staff_management;

-- تحديث أسماء الأدوار
UPDATE roles SET 
    display_name = 'مدير مركز',
    description = 'مدير مركز واحد فقط'
WHERE name = 'center_manager';

UPDATE roles SET 
    display_name = 'مدير المراكز',
    description = 'مدير على جميع مراكز مستشفى واحد'
WHERE name = 'hospital_supervisor';

UPDATE roles SET 
    display_name = 'مدير عام على كل المراكز',
    description = 'مدير عام على جميع المراكز والمستشفيات'
WHERE name = 'super_admin';

-- تحديث أسماء المستخدمين
UPDATE users SET 
    full_name = 'مدير عام على كل المراكز الأول'
WHERE username = 'super_admin_1';

UPDATE users SET 
    full_name = 'مدير عام على كل المراكز الثاني'
WHERE username = 'super_admin_2';

UPDATE users SET 
    full_name = 'مدير المراكز - مستشفى الملك فهد'
WHERE username = 'kfsh_supervisor';

UPDATE users SET 
    full_name = 'مدير المراكز - مستشفى الملك عبدالعزيز'
WHERE username = 'kau_supervisor';

UPDATE users SET 
    full_name = 'مدير المراكز - مستشفى الملك خالد'
WHERE username = 'kkuh_supervisor';

-- تحديث أسماء مديري المراكز
UPDATE users SET 
    full_name = 'مدير مركز - الطب الباطني KFSH'
WHERE username = 'imc01_manager';

UPDATE users SET 
    full_name = 'مدير مركز - الطوارئ KFSH'
WHERE username = 'emc01_manager';

UPDATE users SET 
    full_name = 'مدير مركز - الجراحة KFSH'
WHERE username = 'surg01_manager';

UPDATE users SET 
    full_name = 'مدير مركز - طب الأطفال KFSH'
WHERE username = 'ped01_manager';

UPDATE users SET 
    full_name = 'مدير مركز - أمراض القلب KFSH'
WHERE username = 'card01_manager';

UPDATE users SET 
    full_name = 'مدير مركز - الطب الباطني KAUH'
WHERE username = 'imc02_manager';

UPDATE users SET 
    full_name = 'مدير مركز - الطوارئ KAUH'
WHERE username = 'emc02_manager';

UPDATE users SET 
    full_name = 'مدير مركز - الجراحة KAUH'
WHERE username = 'surg02_manager';

UPDATE users SET 
    full_name = 'مدير مركز - الطب الباطني KKUH'
WHERE username = 'imc03_manager';

UPDATE users SET 
    full_name = 'مدير مركز - الطوارئ KKUH'
WHERE username = 'emc03_manager';

-- عرض النتائج
SELECT 'تم تحديث أسماء الأدوار بنجاح' as message;
SELECT id, name, display_name, description FROM roles;
SELECT id, username, full_name, role_id FROM users WHERE role_id IN (1,2,3) LIMIT 10;

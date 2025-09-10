-- تحديث المستخدمين بالمراكز الصحيحة
-- حذف المستخدمين الموجودة وإضافة المستخدمين الصحيحة

USE health_staff_management;

-- حذف جميع المستخدمين الموجودة
DELETE FROM users;

-- حذف جميع الأدوار الموجودة
DELETE FROM roles;

-- إدراج الأدوار الصحيحة
INSERT INTO roles (id, name, display_name, description, permissions, created_at) VALUES
(1, 'super_admin', 'مدير عام على كل المراكز', 'مدير عام له صلاحية كاملة على جميع المراكز والمستشفيات', '{"all": true}', NOW()),
(2, 'hospital_manager', 'مدير المراكز', 'مدير المستشفى المشرف على المراكز التابعة له فقط', '{"hospital_centers": true, "reports": true, "approve": true}', NOW()),
(3, 'center_manager', 'مدير مركز', 'مدير مركز واحد فقط، لا يرى مراكز أخرى', '{"center_only": true, "approve": true, "reports": true}', NOW()),
(4, 'data_entry', 'مدخل بيانات', 'مدخل بيانات لمركز واحد فقط، يحتاج موافقة المدير', '{"data_entry": true, "center_only": true}', NOW());

-- إدراج السوبر يوزر (3 حسابات)
INSERT INTO users (id, username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active, created_at) VALUES
(1, 'super_admin_1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'السوبر يوزر الأول', 'super1@health.gov.sa', '0500000001', 1, NULL, NULL, 1, NOW()),
(2, 'super_admin_2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'السوبر يوزر الثاني', 'super2@health.gov.sa', '0500000002', 1, NULL, NULL, 1, NOW()),
(3, 'super_admin_3', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'السوبر يوزر الثالث', 'super3@health.gov.sa', '0500000003', 1, NULL, NULL, 1, NOW());

-- إدراج مديري المستشفيات (3 حسابات)
INSERT INTO users (id, username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active, created_at) VALUES
(4, 'hospital_manager_kamc', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مجمع الملك عبد الله الطبي', 'kamc.manager@health.gov.sa', '0501000001', 2, 1, NULL, 1, NOW()),
(5, 'hospital_manager_rh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مستشفى رابغ', 'rh.manager@health.gov.sa', '0501000002', 2, 2, NULL, 1, NOW()),
(6, 'hospital_manager_kfh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مستشفى الملك فهد', 'kfh.manager@health.gov.sa', '0501000003', 2, 3, NULL, 1, NOW());

-- إدراج مديري المراكز (38 مدير) - مجمع الملك عبد الله الطبي
INSERT INTO users (id, username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active, created_at) VALUES
(7, 'center_manager_kamc_sharaa_505', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الشراع 505', 'kamc.sharaa.manager@health.gov.sa', '0502000001', 3, 1, 1, 1, NOW()),
(8, 'center_manager_kamc_abhar_north', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز ابحر الشمالية', 'kamc.abhar.manager@health.gov.sa', '0502000002', 3, 1, 2, 1, NOW()),
(9, 'center_manager_kamc_alrayan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الريان', 'kamc.rayan.manager@health.gov.sa', '0502000003', 3, 1, 3, 1, NOW()),
(10, 'center_manager_kamc_alsalahiya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصالحية', 'kamc.salahiya.manager@health.gov.sa', '0502000004', 3, 1, 4, 1, NOW()),
(11, 'center_manager_kamc_alsawari', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصواري', 'kamc.sawari.manager@health.gov.sa', '0502000005', 3, 1, 5, 1, NOW()),
(12, 'center_manager_kamc_alfardous', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الفردوس', 'kamc.fardous.manager@health.gov.sa', '0502000006', 3, 1, 6, 1, NOW()),
(13, 'center_manager_kamc_almajid', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الماجد', 'kamc.majid.manager@health.gov.sa', '0502000007', 3, 1, 7, 1, NOW()),
(14, 'center_manager_kamc_alwafa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الوفاء', 'kamc.wafa.manager@health.gov.sa', '0502000008', 3, 1, 8, 1, NOW()),
(15, 'center_manager_kamc_bariman', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز بريمان', 'kamc.bariman.manager@health.gov.sa', '0502000009', 3, 1, 9, 1, NOW()),
(16, 'center_manager_kamc_thul', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز ثول', 'kamc.thul.manager@health.gov.sa', '0502000010', 3, 1, 10, 1, NOW()),
(17, 'center_manager_kamc_khalid_model', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز خالد النموذجي', 'kamc.khalid.manager@health.gov.sa', '0502000011', 3, 1, 11, 1, NOW()),
(18, 'center_manager_kamc_dhahban', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز ذهبان', 'kamc.dhahban.manager@health.gov.sa', '0502000012', 3, 1, 12, 1, NOW()),
(19, 'center_manager_kamc_mashrafa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز مشرفة', 'kamc.mashrafa.manager@health.gov.sa', '0502000013', 3, 1, 13, 1, NOW());

-- إدراج مديري المراكز - مستشفى رابغ
INSERT INTO users (id, username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active, created_at) VALUES
(20, 'center_manager_rh_alabwa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الابواء', 'rh.abwa.manager@health.gov.sa', '0502000014', 3, 2, 14, 1, NOW()),
(21, 'center_manager_rh_aljahfa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الجحفة', 'rh.jahfa.manager@health.gov.sa', '0502000015', 3, 2, 15, 1, NOW()),
(22, 'center_manager_rh_aljouba', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الجوبة', 'rh.jouba.manager@health.gov.sa', '0502000016', 3, 2, 16, 1, NOW()),
(23, 'center_manager_rh_alsalib_east', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصليب الشرقي', 'rh.salib.manager@health.gov.sa', '0502000017', 3, 2, 17, 1, NOW()),
(24, 'center_manager_rh_almarjaniya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز المرجانية', 'rh.marjaniya.manager@health.gov.sa', '0502000018', 3, 2, 18, 1, NOW()),
(25, 'center_manager_rh_almarakha', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز المرخة', 'rh.marakha.manager@health.gov.sa', '0502000019', 3, 2, 19, 1, NOW()),
(26, 'center_manager_rh_alnuweiba', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز النويبع', 'rh.nuweiba.manager@health.gov.sa', '0502000020', 3, 2, 20, 1, NOW()),
(27, 'center_manager_rh_hajar', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز حجر', 'rh.hajar.manager@health.gov.sa', '0502000021', 3, 2, 21, 1, NOW()),
(28, 'center_manager_rh_rabigh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز رابغ', 'rh.rabigh.manager@health.gov.sa', '0502000022', 3, 2, 22, 1, NOW()),
(29, 'center_manager_rh_saabar', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز صعبر', 'rh.saabar.manager@health.gov.sa', '0502000023', 3, 2, 23, 1, NOW()),
(30, 'center_manager_rh_kulliya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز كلية', 'rh.kulliya.manager@health.gov.sa', '0502000024', 3, 2, 24, 1, NOW()),
(31, 'center_manager_rh_mastura', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز مستورة', 'rh.mastura.manager@health.gov.sa', '0502000025', 3, 2, 25, 1, NOW()),
(32, 'center_manager_rh_maghiniya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز مغينية', 'rh.maghiniya.manager@health.gov.sa', '0502000026', 3, 2, 26, 1, NOW());

-- إدراج مديري المراكز - مستشفى الملك فهد
INSERT INTO users (id, username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active, created_at) VALUES
(33, 'center_manager_kfh_bawadi_2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز البوادي 2', 'kfh.bawadi2.manager@health.gov.sa', '0502000027', 3, 3, 27, 1, NOW()),
(34, 'center_manager_kfh_bawadi_1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز البوادي 1', 'kfh.bawadi1.manager@health.gov.sa', '0502000028', 3, 3, 28, 1, NOW()),
(35, 'center_manager_kfh_alrabwa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الربوة', 'kfh.rabwa.manager@health.gov.sa', '0502000029', 3, 3, 29, 1, NOW()),
(36, 'center_manager_kfh_alrahab', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الرحاب', 'kfh.rahab.manager@health.gov.sa', '0502000030', 3, 3, 30, 1, NOW()),
(37, 'center_manager_kfh_alsalama', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز السلامة', 'kfh.salama.manager@health.gov.sa', '0502000031', 3, 3, 31, 1, NOW()),
(38, 'center_manager_kfh_alshaati', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الشاطئ', 'kfh.shaati.manager@health.gov.sa', '0502000032', 3, 3, 32, 1, NOW()),
(39, 'center_manager_kfh_alsafa_1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصفا 1', 'kfh.safa1.manager@health.gov.sa', '0502000033', 3, 3, 33, 1, NOW()),
(40, 'center_manager_kfh_alsafa_2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصفا 2', 'kfh.safa2.manager@health.gov.sa', '0502000034', 3, 3, 34, 1, NOW()),
(41, 'center_manager_kfh_alfaisaliya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الفيصلية', 'kfh.faisaliya.manager@health.gov.sa', '0502000035', 3, 3, 35, 1, NOW()),
(42, 'center_manager_kfh_almarwa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز المروة', 'kfh.marwa.manager@health.gov.sa', '0502000036', 3, 3, 36, 1, NOW()),
(43, 'center_manager_kfh_alnaeem', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز النعيم', 'kfh.naeem.manager@health.gov.sa', '0502000037', 3, 3, 37, 1, NOW()),
(44, 'center_manager_kfh_alnahda', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز النهضة', 'kfh.nahda.manager@health.gov.sa', '0502000038', 3, 3, 38, 1, NOW());

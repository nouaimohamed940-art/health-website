-- قاعدة البيانات الكاملة المحدثة
-- Updated Complete Database System

USE health_staff_management;

-- تحديث جدول المستشفيات
UPDATE hospitals SET 
    name = 'مجمع الملك عبد الله الطبي',
    name_en = 'King Abdullah Medical Complex',
    code = 'KAMC'
WHERE id = 1;

UPDATE hospitals SET 
    name = 'مستشفى رابغ',
    name_en = 'Rabigh Hospital', 
    code = 'RH'
WHERE id = 2;

UPDATE hospitals SET 
    name = 'مستشفى الملك فهد',
    name_en = 'King Fahd Hospital',
    code = 'KFH'
WHERE id = 3;

-- حذف المراكز الموجودة وإعادة إنشائها
DELETE FROM centers;

-- إدراج المراكز الجديدة
-- مجمع الملك عبد الله الطبي (13 مركز)
INSERT INTO centers (hospital_id, name, name_en, code, description) VALUES
(1, 'الطب الباطني', 'Internal Medicine', 'KAMC_IM', 'قسم الطب الباطني - مجمع الملك عبد الله الطبي'),
(1, 'طب الأسرة', 'Family Medicine', 'KAMC_FM', 'قسم طب الأسرة - مجمع الملك عبد الله الطبي'),
(1, 'الأطفال', 'Pediatrics', 'KAMC_PED', 'قسم الأطفال - مجمع الملك عبد الله الطبي'),
(1, 'النساء والولادة', 'Obstetrics & Gynecology', 'KAMC_OG', 'قسم النساء والولادة - مجمع الملك عبد الله الطبي'),
(1, 'الطوارئ', 'Emergency Medicine', 'KAMC_ER', 'قسم الطوارئ - مجمع الملك عبد الله الطبي'),
(1, 'العناية المركزة', 'Intensive Care Unit', 'KAMC_ICU', 'وحدة العناية المركزة - مجمع الملك عبد الله الطبي'),
(1, 'الأشعة', 'Radiology', 'KAMC_RAD', 'قسم الأشعة - مجمع الملك عبد الله الطبي'),
(1, 'المختبر', 'Laboratory', 'KAMC_LAB', 'قسم المختبر - مجمع الملك عبد الله الطبي'),
(1, 'الصيدلة', 'Pharmacy', 'KAMC_PHARM', 'قسم الصيدلة - مجمع الملك عبد الله الطبي'),
(1, 'التمريض', 'Nursing', 'KAMC_NURS', 'قسم التمريض - مجمع الملك عبد الله الطبي'),
(1, 'العمليات', 'Operating Rooms', 'KAMC_OR', 'قسم العمليات - مجمع الملك عبد الله الطبي'),
(1, 'الإدارة', 'Administration', 'KAMC_ADM', 'قسم الإدارة - مجمع الملك عبد الله الطبي'),
(1, 'خدمة العملاء', 'Customer Service', 'KAMC_CS', 'قسم خدمة العملاء - مجمع الملك عبد الله الطبي'),

-- مستشفى رابغ (13 مركز)
(2, 'الطب الباطني', 'Internal Medicine', 'RH_IM', 'قسم الطب الباطني - مستشفى رابغ'),
(2, 'طب الأسرة', 'Family Medicine', 'RH_FM', 'قسم طب الأسرة - مستشفى رابغ'),
(2, 'الأطفال', 'Pediatrics', 'RH_PED', 'قسم الأطفال - مستشفى رابغ'),
(2, 'النساء والولادة', 'Obstetrics & Gynecology', 'RH_OG', 'قسم النساء والولادة - مستشفى رابغ'),
(2, 'الطوارئ', 'Emergency Medicine', 'RH_ER', 'قسم الطوارئ - مستشفى رابغ'),
(2, 'العناية المركزة', 'Intensive Care Unit', 'RH_ICU', 'وحدة العناية المركزة - مستشفى رابغ'),
(2, 'الأشعة', 'Radiology', 'RH_RAD', 'قسم الأشعة - مستشفى رابغ'),
(2, 'المختبر', 'Laboratory', 'RH_LAB', 'قسم المختبر - مستشفى رابغ'),
(2, 'الصيدلة', 'Pharmacy', 'RH_PHARM', 'قسم الصيدلة - مستشفى رابغ'),
(2, 'التمريض', 'Nursing', 'RH_NURS', 'قسم التمريض - مستشفى رابغ'),
(2, 'العمليات', 'Operating Rooms', 'RH_OR', 'قسم العمليات - مستشفى رابغ'),
(2, 'الإدارة', 'Administration', 'RH_ADM', 'قسم الإدارة - مستشفى رابغ'),
(2, 'خدمة العملاء', 'Customer Service', 'RH_CS', 'قسم خدمة العملاء - مستشفى رابغ'),

-- مستشفى الملك فهد (12 مركز)
(3, 'الطب الباطني', 'Internal Medicine', 'KFH_IM', 'قسم الطب الباطني - مستشفى الملك فهد'),
(3, 'طب الأسرة', 'Family Medicine', 'KFH_FM', 'قسم طب الأسرة - مستشفى الملك فهد'),
(3, 'الأطفال', 'Pediatrics', 'KFH_PED', 'قسم الأطفال - مستشفى الملك فهد'),
(3, 'النساء والولادة', 'Obstetrics & Gynecology', 'KFH_OG', 'قسم النساء والولادة - مستشفى الملك فهد'),
(3, 'الطوارئ', 'Emergency Medicine', 'KFH_ER', 'قسم الطوارئ - مستشفى الملك فهد'),
(3, 'العناية المركزة', 'Intensive Care Unit', 'KFH_ICU', 'وحدة العناية المركزة - مستشفى الملك فهد'),
(3, 'الأشعة', 'Radiology', 'KFH_RAD', 'قسم الأشعة - مستشفى الملك فهد'),
(3, 'المختبر', 'Laboratory', 'KFH_LAB', 'قسم المختبر - مستشفى الملك فهد'),
(3, 'الصيدلة', 'Pharmacy', 'KFH_PHARM', 'قسم الصيدلة - مستشفى الملك فهد'),
(3, 'التمريض', 'Nursing', 'KFH_NURS', 'قسم التمريض - مستشفى الملك فهد'),
(3, 'العمليات', 'Operating Rooms', 'KFH_OR', 'قسم العمليات - مستشفى الملك فهد'),
(3, 'الإدارة', 'Administration', 'KFH_ADM', 'قسم الإدارة - مستشفى الملك فهد');

-- حذف المستخدمين الموجودين وإعادة إنشائهم
DELETE FROM users;

-- إدراج المستخدمين الجدد

-- السوبر يوزر (3 حسابات)
INSERT INTO users (username, email, password_hash, full_name, phone, role_id, hospital_id, center_id) VALUES
('super_admin_1', 'superadmin1@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'المدير العام الأول', '0501111001', 3, NULL, NULL),
('super_admin_2', 'superadmin2@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'المدير العام الثاني', '0501111002', 3, NULL, NULL),
('super_admin_3', 'superadmin3@health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'المدير العام الثالث', '0501111003', 3, NULL, NULL);

-- مديري المستشفيات (3 حسابات)
INSERT INTO users (username, email, password_hash, full_name, phone, role_id, hospital_id, center_id) VALUES
('hospital_manager_kamc', 'manager@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مجمع الملك عبد الله الطبي', '0502222001', 2, 1, NULL),
('hospital_manager_rh', 'manager@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مستشفى رابغ', '0502222002', 2, 2, NULL),
('hospital_manager_kfh', 'manager@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مستشفى الملك فهد', '0502222003', 2, 3, NULL);

-- مديري المراكز (38 حساب)
-- مجمع الملك عبد الله الطبي
INSERT INTO users (username, email, password_hash, full_name, phone, role_id, hospital_id, center_id) VALUES
('center_manager_kamc_im', 'manager.im@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطب الباطني - مجمع الملك عبد الله', '0503333001', 1, 1, 1),
('center_manager_kamc_fm', 'manager.fm@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز طب الأسرة - مجمع الملك عبد الله', '0503333002', 1, 1, 2),
('center_manager_kamc_ped', 'manager.ped@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الأطفال - مجمع الملك عبد الله', '0503333003', 1, 1, 3),
('center_manager_kamc_og', 'manager.og@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز النساء والولادة - مجمع الملك عبد الله', '0503333004', 1, 1, 4),
('center_manager_kamc_er', 'manager.er@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطوارئ - مجمع الملك عبد الله', '0503333005', 1, 1, 5),
('center_manager_kamc_icu', 'manager.icu@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز العناية المركزة - مجمع الملك عبد الله', '0503333006', 1, 1, 6),
('center_manager_kamc_rad', 'manager.rad@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الأشعة - مجمع الملك عبد الله', '0503333007', 1, 1, 7),
('center_manager_kamc_lab', 'manager.lab@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز المختبر - مجمع الملك عبد الله', '0503333008', 1, 1, 8),
('center_manager_kamc_pharm', 'manager.pharm@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصيدلة - مجمع الملك عبد الله', '0503333009', 1, 1, 9),
('center_manager_kamc_nurs', 'manager.nurs@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز التمريض - مجمع الملك عبد الله', '0503333010', 1, 1, 10),
('center_manager_kamc_or', 'manager.or@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز العمليات - مجمع الملك عبد الله', '0503333011', 1, 1, 11),
('center_manager_kamc_adm', 'manager.adm@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الإدارة - مجمع الملك عبد الله', '0503333012', 1, 1, 12),
('center_manager_kamc_cs', 'manager.cs@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز خدمة العملاء - مجمع الملك عبد الله', '0503333013', 1, 1, 13);

-- مستشفى رابغ  
INSERT INTO users (username, email, password_hash, full_name, phone, role_id, hospital_id, center_id) VALUES
('center_manager_rh_im', 'manager.im@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطب الباطني - مستشفى رابغ', '0503333014', 1, 2, 14),
('center_manager_rh_fm', 'manager.fm@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز طب الأسرة - مستشفى رابغ', '0503333015', 1, 2, 15),
('center_manager_rh_ped', 'manager.ped@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الأطفال - مستشفى رابغ', '0503333016', 1, 2, 16),
('center_manager_rh_og', 'manager.og@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز النساء والولادة - مستشفى رابغ', '0503333017', 1, 2, 17),
('center_manager_rh_er', 'manager.er@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطوارئ - مستشفى رابغ', '0503333018', 1, 2, 18),
('center_manager_rh_icu', 'manager.icu@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز العناية المركزة - مستشفى رابغ', '0503333019', 1, 2, 19),
('center_manager_rh_rad', 'manager.rad@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الأشعة - مستشفى رابغ', '0503333020', 1, 2, 20),
('center_manager_rh_lab', 'manager.lab@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز المختبر - مستشفى رابغ', '0503333021', 1, 2, 21),
('center_manager_rh_pharm', 'manager.pharm@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصيدلة - مستشفى رابغ', '0503333022', 1, 2, 22),
('center_manager_rh_nurs', 'manager.nurs@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز التمريض - مستشفى رابغ', '0503333023', 1, 2, 23),
('center_manager_rh_or', 'manager.or@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز العمليات - مستشفى رابغ', '0503333024', 1, 2, 24),
('center_manager_rh_adm', 'manager.adm@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الإدارة - مستشفى رابغ', '0503333025', 1, 2, 25),
('center_manager_rh_cs', 'manager.cs@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز خدمة العملاء - مستشفى رابغ', '0503333026', 1, 2, 26);

-- مستشفى الملك فهد
INSERT INTO users (username, email, password_hash, full_name, phone, role_id, hospital_id, center_id) VALUES
('center_manager_kfh_im', 'manager.im@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطب الباطني - مستشفى الملك فهد', '0503333027', 1, 3, 27),
('center_manager_kfh_fm', 'manager.fm@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز طب الأسرة - مستشفى الملك فهد', '0503333028', 1, 3, 28),
('center_manager_kfh_ped', 'manager.ped@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الأطفال - مستشفى الملك فهد', '0503333029', 1, 3, 29),
('center_manager_kfh_og', 'manager.og@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز النساء والولادة - مستشفى الملك فهد', '0503333030', 1, 3, 30),
('center_manager_kfh_er', 'manager.er@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الطوارئ - مستشفى الملك فهد', '0503333031', 1, 3, 31),
('center_manager_kfh_icu', 'manager.icu@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز العناية المركزة - مستشفى الملك فهد', '0503333032', 1, 3, 32),
('center_manager_kfh_rad', 'manager.rad@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الأشعة - مستشفى الملك فهد', '0503333033', 1, 3, 33),
('center_manager_kfh_lab', 'manager.lab@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز المختبر - مستشفى الملك فهد', '0503333034', 1, 3, 34),
('center_manager_kfh_pharm', 'manager.pharm@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصيدلة - مستشفى الملك فهد', '0503333035', 1, 3, 35),
('center_manager_kfh_nurs', 'manager.nurs@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز التمريض - مستشفى الملك فهد', '0503333036', 1, 3, 36),
('center_manager_kfh_or', 'manager.or@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز العمليات - مستشفى الملك فهد', '0503333037', 1, 3, 37),
('center_manager_kfh_adm', 'manager.adm@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الإدارة - مستشفى الملك فهد', '0503333038', 1, 3, 38);

-- تحديث جدول مدخلي البيانات الموجود
DELETE FROM data_entry_users;

-- مدخلي البيانات (76 حساب - 2 لكل مركز)
-- مجمع الملك عبد الله الطبي
INSERT INTO data_entry_users (center_id, username, email, password_hash, full_name, phone) VALUES
(1, 'data_entry_kamc_im_1', 'de1.im@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الطب الباطني KAMC', '0504444001'),
(1, 'data_entry_kamc_im_2', 'de2.im@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الطب الباطني KAMC', '0504444002'),
(2, 'data_entry_kamc_fm_1', 'de1.fm@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - طب الأسرة KAMC', '0504444003'),
(2, 'data_entry_kamc_fm_2', 'de2.fm@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - طب الأسرة KAMC', '0504444004'),
(3, 'data_entry_kamc_ped_1', 'de1.ped@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الأطفال KAMC', '0504444005'),
(3, 'data_entry_kamc_ped_2', 'de2.ped@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الأطفال KAMC', '0504444006'),
(4, 'data_entry_kamc_og_1', 'de1.og@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - النساء والولادة KAMC', '0504444007'),
(4, 'data_entry_kamc_og_2', 'de2.og@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - النساء والولادة KAMC', '0504444008'),
(5, 'data_entry_kamc_er_1', 'de1.er@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الطوارئ KAMC', '0504444009'),
(5, 'data_entry_kamc_er_2', 'de2.er@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الطوارئ KAMC', '0504444010'),
(6, 'data_entry_kamc_icu_1', 'de1.icu@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - العناية المركزة KAMC', '0504444011'),
(6, 'data_entry_kamc_icu_2', 'de2.icu@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - العناية المركزة KAMC', '0504444012'),
(7, 'data_entry_kamc_rad_1', 'de1.rad@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الأشعة KAMC', '0504444013'),
(7, 'data_entry_kamc_rad_2', 'de2.rad@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الأشعة KAMC', '0504444014'),
(8, 'data_entry_kamc_lab_1', 'de1.lab@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - المختبر KAMC', '0504444015'),
(8, 'data_entry_kamc_lab_2', 'de2.lab@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - المختبر KAMC', '0504444016'),
(9, 'data_entry_kamc_pharm_1', 'de1.pharm@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الصيدلة KAMC', '0504444017'),
(9, 'data_entry_kamc_pharm_2', 'de2.pharm@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الصيدلة KAMC', '0504444018'),
(10, 'data_entry_kamc_nurs_1', 'de1.nurs@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - التمريض KAMC', '0504444019'),
(10, 'data_entry_kamc_nurs_2', 'de2.nurs@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - التمريض KAMC', '0504444020'),
(11, 'data_entry_kamc_or_1', 'de1.or@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - العمليات KAMC', '0504444021'),
(11, 'data_entry_kamc_or_2', 'de2.or@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - العمليات KAMC', '0504444022'),
(12, 'data_entry_kamc_adm_1', 'de1.adm@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الإدارة KAMC', '0504444023'),
(12, 'data_entry_kamc_adm_2', 'de2.adm@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الإدارة KAMC', '0504444024'),
(13, 'data_entry_kamc_cs_1', 'de1.cs@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - خدمة العملاء KAMC', '0504444025'),
(13, 'data_entry_kamc_cs_2', 'de2.cs@kamc.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - خدمة العملاء KAMC', '0504444026');

-- إعادة تعيين AUTO_INCREMENT للجداول
SELECT 'تم إنشاء النظام الكامل بنجاح!' as message;
SELECT 'المستشفيات: 3 | المراكز: 38 | المديرين: 44 | مدخلي البيانات: 76' as summary;

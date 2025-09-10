-- إكمال مدخلي البيانات للمراكز المتبقية
-- Complete Data Entry Users for Remaining Centers

USE health_staff_management;

-- مستشفى رابغ - مدخلي البيانات (26 حساب)
INSERT INTO data_entry_users (center_id, username, email, password_hash, full_name, phone) VALUES
(14, 'data_entry_rh_im_1', 'de1.im@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الطب الباطني RH', '0504444027'),
(14, 'data_entry_rh_im_2', 'de2.im@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الطب الباطني RH', '0504444028'),
(15, 'data_entry_rh_fm_1', 'de1.fm@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - طب الأسرة RH', '0504444029'),
(15, 'data_entry_rh_fm_2', 'de2.fm@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - طب الأسرة RH', '0504444030'),
(16, 'data_entry_rh_ped_1', 'de1.ped@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الأطفال RH', '0504444031'),
(16, 'data_entry_rh_ped_2', 'de2.ped@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الأطفال RH', '0504444032'),
(17, 'data_entry_rh_og_1', 'de1.og@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - النساء والولادة RH', '0504444033'),
(17, 'data_entry_rh_og_2', 'de2.og@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - النساء والولادة RH', '0504444034'),
(18, 'data_entry_rh_er_1', 'de1.er@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الطوارئ RH', '0504444035'),
(18, 'data_entry_rh_er_2', 'de2.er@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الطوارئ RH', '0504444036'),
(19, 'data_entry_rh_icu_1', 'de1.icu@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - العناية المركزة RH', '0504444037'),
(19, 'data_entry_rh_icu_2', 'de2.icu@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - العناية المركزة RH', '0504444038'),
(20, 'data_entry_rh_rad_1', 'de1.rad@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الأشعة RH', '0504444039'),
(20, 'data_entry_rh_rad_2', 'de2.rad@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الأشعة RH', '0504444040'),
(21, 'data_entry_rh_lab_1', 'de1.lab@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - المختبر RH', '0504444041'),
(21, 'data_entry_rh_lab_2', 'de2.lab@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - المختبر RH', '0504444042'),
(22, 'data_entry_rh_pharm_1', 'de1.pharm@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الصيدلة RH', '0504444043'),
(22, 'data_entry_rh_pharm_2', 'de2.pharm@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الصيدلة RH', '0504444044'),
(23, 'data_entry_rh_nurs_1', 'de1.nurs@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - التمريض RH', '0504444045'),
(23, 'data_entry_rh_nurs_2', 'de2.nurs@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - التمريض RH', '0504444046'),
(24, 'data_entry_rh_or_1', 'de1.or@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - العمليات RH', '0504444047'),
(24, 'data_entry_rh_or_2', 'de2.or@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - العمليات RH', '0504444048'),
(25, 'data_entry_rh_adm_1', 'de1.adm@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الإدارة RH', '0504444049'),
(25, 'data_entry_rh_adm_2', 'de2.adm@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الإدارة RH', '0504444050'),
(26, 'data_entry_rh_cs_1', 'de1.cs@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - خدمة العملاء RH', '0504444051'),
(26, 'data_entry_rh_cs_2', 'de2.cs@rh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - خدمة العملاء RH', '0504444052');

-- مستشفى الملك فهد - مدخلي البيانات (24 حساب)
INSERT INTO data_entry_users (center_id, username, email, password_hash, full_name, phone) VALUES
(27, 'data_entry_kfh_im_1', 'de1.im@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الطب الباطني KFH', '0504444053'),
(27, 'data_entry_kfh_im_2', 'de2.im@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الطب الباطني KFH', '0504444054'),
(28, 'data_entry_kfh_fm_1', 'de1.fm@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - طب الأسرة KFH', '0504444055'),
(28, 'data_entry_kfh_fm_2', 'de2.fm@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - طب الأسرة KFH', '0504444056'),
(29, 'data_entry_kfh_ped_1', 'de1.ped@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الأطفال KFH', '0504444057'),
(29, 'data_entry_kfh_ped_2', 'de2.ped@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الأطفال KFH', '0504444058'),
(30, 'data_entry_kfh_og_1', 'de1.og@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - النساء والولادة KFH', '0504444059'),
(30, 'data_entry_kfh_og_2', 'de2.og@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - النساء والولادة KFH', '0504444060'),
(31, 'data_entry_kfh_er_1', 'de1.er@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الطوارئ KFH', '0504444061'),
(31, 'data_entry_kfh_er_2', 'de2.er@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الطوارئ KFH', '0504444062'),
(32, 'data_entry_kfh_icu_1', 'de1.icu@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - العناية المركزة KFH', '0504444063'),
(32, 'data_entry_kfh_icu_2', 'de2.icu@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - العناية المركزة KFH', '0504444064'),
(33, 'data_entry_kfh_rad_1', 'de1.rad@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الأشعة KFH', '0504444065'),
(33, 'data_entry_kfh_rad_2', 'de2.rad@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الأشعة KFH', '0504444066'),
(34, 'data_entry_kfh_lab_1', 'de1.lab@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - المختبر KFH', '0504444067'),
(34, 'data_entry_kfh_lab_2', 'de2.lab@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - المختبر KFH', '0504444068'),
(35, 'data_entry_kfh_pharm_1', 'de1.pharm@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الصيدلة KFH', '0504444069'),
(35, 'data_entry_kfh_pharm_2', 'de2.pharm@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الصيدلة KFH', '0504444070'),
(36, 'data_entry_kfh_nurs_1', 'de1.nurs@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - التمريض KFH', '0504444071'),
(36, 'data_entry_kfh_nurs_2', 'de2.nurs@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - التمريض KFH', '0504444072'),
(37, 'data_entry_kfh_or_1', 'de1.or@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - العمليات KFH', '0504444073'),
(37, 'data_entry_kfh_or_2', 'de2.or@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - العمليات KFH', '0504444074'),
(38, 'data_entry_kfh_adm_1', 'de1.adm@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات أول - الإدارة KFH', '0504444075'),
(38, 'data_entry_kfh_adm_2', 'de2.adm@kfh.health.gov.sa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدخل بيانات ثاني - الإدارة KFH', '0504444076');

-- تحديث إعدادات مدخلي البيانات
DELETE FROM data_entry_settings;
INSERT INTO data_entry_settings (center_id, require_manager_approval, max_daily_entries) 
SELECT id, TRUE, 100 FROM centers WHERE is_active = TRUE;

SELECT 'تم إكمال إنشاء جميع مدخلي البيانات بنجاح!' as message;
SELECT 'إجمالي مدخلي البيانات: 76 (2 لكل مركز)' as summary;

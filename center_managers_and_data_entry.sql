-- مديري المراكز ومدخلي البيانات
-- 38 مدير مركز + 76 مدخل بيانات

-- مديري مراكز مجمع الملك عبد الله الطبي (13 مدير)
INSERT INTO users (username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active) VALUES
('center_manager_kamc_sharaa_505', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الشراع 505', 'kamc.sharaa.manager@health.gov.sa', '0502000001', 3, 1, 1, TRUE),
('center_manager_kamc_abhar_north', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز ابحر الشمالية', 'kamc.abhar.manager@health.gov.sa', '0502000002', 3, 1, 2, TRUE),
('center_manager_kamc_alrayan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الريان', 'kamc.rayan.manager@health.gov.sa', '0502000003', 3, 1, 3, TRUE),
('center_manager_kamc_alsalahiya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصالحية', 'kamc.salahiya.manager@health.gov.sa', '0502000004', 3, 1, 4, TRUE),
('center_manager_kamc_alsawari', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصواري', 'kamc.sawari.manager@health.gov.sa', '0502000005', 3, 1, 5, TRUE),
('center_manager_kamc_alfardous', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الفردوس', 'kamc.fardous.manager@health.gov.sa', '0502000006', 3, 1, 6, TRUE),
('center_manager_kamc_almajid', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الماجد', 'kamc.majid.manager@health.gov.sa', '0502000007', 3, 1, 7, TRUE),
('center_manager_kamc_alwafa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الوفاء', 'kamc.wafa.manager@health.gov.sa', '0502000008', 3, 1, 8, TRUE),
('center_manager_kamc_bariman', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز بريمان', 'kamc.bariman.manager@health.gov.sa', '0502000009', 3, 1, 9, TRUE),
('center_manager_kamc_thul', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز ثول', 'kamc.thul.manager@health.gov.sa', '0502000010', 3, 1, 10, TRUE),
('center_manager_kamc_khalid_model', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز خالد النموذجي', 'kamc.khalid.manager@health.gov.sa', '0502000011', 3, 1, 11, TRUE),
('center_manager_kamc_dhahban', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز ذهبان', 'kamc.dhahban.manager@health.gov.sa', '0502000012', 3, 1, 12, TRUE),
('center_manager_kamc_mashrafa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز مشرفة', 'kamc.mashrafa.manager@health.gov.sa', '0502000013', 3, 1, 13, TRUE);

-- مديري مراكز مستشفى رابغ (13 مدير)
INSERT INTO users (username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active) VALUES
('center_manager_rh_alabwa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الابواء', 'rh.abwa.manager@health.gov.sa', '0502000014', 3, 2, 14, TRUE),
('center_manager_rh_aljahfa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الجحفة', 'rh.jahfa.manager@health.gov.sa', '0502000015', 3, 2, 15, TRUE),
('center_manager_rh_aljouba', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الجوبة', 'rh.jouba.manager@health.gov.sa', '0502000016', 3, 2, 16, TRUE),
('center_manager_rh_alsalib_east', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصليب الشرقي', 'rh.salib.manager@health.gov.sa', '0502000017', 3, 2, 17, TRUE),
('center_manager_rh_almarjaniya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز المرجانية', 'rh.marjaniya.manager@health.gov.sa', '0502000018', 3, 2, 18, TRUE),
('center_manager_rh_almarakha', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز المرخة', 'rh.marakha.manager@health.gov.sa', '0502000019', 3, 2, 19, TRUE),
('center_manager_rh_alnuweiba', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز النويبع', 'rh.nuweiba.manager@health.gov.sa', '0502000020', 3, 2, 20, TRUE),
('center_manager_rh_hajar', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز حجر', 'rh.hajar.manager@health.gov.sa', '0502000021', 3, 2, 21, TRUE),
('center_manager_rh_rabigh', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز رابغ', 'rh.rabigh.manager@health.gov.sa', '0502000022', 3, 2, 22, TRUE),
('center_manager_rh_saabar', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز صعبر', 'rh.saabar.manager@health.gov.sa', '0502000023', 3, 2, 23, TRUE),
('center_manager_rh_kulliya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز كلية', 'rh.kulliya.manager@health.gov.sa', '0502000024', 3, 2, 24, TRUE),
('center_manager_rh_mastura', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز مستورة', 'rh.mastura.manager@health.gov.sa', '0502000025', 3, 2, 25, TRUE),
('center_manager_rh_maghiniya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز مغينية', 'rh.maghiniya.manager@health.gov.sa', '0502000026', 3, 2, 26, TRUE);

-- مديري مراكز مستشفى الملك فهد (12 مدير)
INSERT INTO users (username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active) VALUES
('center_manager_kfh_bawadi_2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز البوادي 2', 'kfh.bawadi2.manager@health.gov.sa', '0502000027', 3, 3, 27, TRUE),
('center_manager_kfh_bawadi_1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز البوادي 1', 'kfh.bawadi1.manager@health.gov.sa', '0502000028', 3, 3, 28, TRUE),
('center_manager_kfh_alrabwa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الربوة', 'kfh.rabwa.manager@health.gov.sa', '0502000029', 3, 3, 29, TRUE),
('center_manager_kfh_alrahab', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الرحاب', 'kfh.rahab.manager@health.gov.sa', '0502000030', 3, 3, 30, TRUE),
('center_manager_kfh_alsalama', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز السلامة', 'kfh.salama.manager@health.gov.sa', '0502000031', 3, 3, 31, TRUE),
('center_manager_kfh_alshaati', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الشاطئ', 'kfh.shaati.manager@health.gov.sa', '0502000032', 3, 3, 32, TRUE),
('center_manager_kfh_alsafa_1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصفا 1', 'kfh.safa1.manager@health.gov.sa', '0502000033', 3, 3, 33, TRUE),
('center_manager_kfh_alsafa_2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الصفا 2', 'kfh.safa2.manager@health.gov.sa', '0502000034', 3, 3, 34, TRUE),
('center_manager_kfh_alfaisaliya', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز الفيصلية', 'kfh.faisaliya.manager@health.gov.sa', '0502000035', 3, 3, 35, TRUE),
('center_manager_kfh_almarwa', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز المروة', 'kfh.marwa.manager@health.gov.sa', '0502000036', 3, 3, 36, TRUE),
('center_manager_kfh_alnaeem', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز النعيم', 'kfh.naeem.manager@health.gov.sa', '0502000037', 3, 3, 37, TRUE),
('center_manager_kfh_alnahda', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'مدير مركز النهضة', 'kfh.nahda.manager@health.gov.sa', '0502000038', 3, 3, 38, TRUE);

-- إنشاء 3 حسابات مديري مستشفيات فريدة مع أسماء مستخدمين وكلمات مرور صعبة
USE health_staff_management;

-- حذف الحسابات القديمة إذا كانت موجودة
DELETE FROM users WHERE role_id = 2;

-- إدراج 3 حسابات مديري مستشفيات فريدة
INSERT INTO users (id, username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active, created_at) VALUES
(4, 'HOSP_MGR_KAMC_001', '$2y$10$Oz1yT0pQ5sN8uV0zG6aB9xK1rR4dV7gI9jK2mN5oQ8tU1wX4yZ7aC0', 'مدير مجمع الملك عبد الله الطبي', 'hospital.manager.kamc@health.gov.sa', '0501000001', 2, 1, NULL, 1, NOW()),
(5, 'HOSP_MGR_RH_002', '$2y$10$Pz2zU1qR6tO9vW1aH7bC0yL2sS5eW8hJ0kL3nO6pR9uV2xY5zA8bD1', 'مدير مستشفى رابغ', 'hospital.manager.rh@health.gov.sa', '0501000002', 2, 2, NULL, 1, NOW()),
(6, 'HOSP_MGR_KFH_003', '$2y$10$Qz3aV2rS7uP0wX2bI8cD1zM3tT6fX9iK1lM4oP7qS0vW3yZ6aB9cE2', 'مدير مستشفى الملك فهد', 'hospital.manager.kfh@health.gov.sa', '0501000003', 2, 3, NULL, 1, NOW());

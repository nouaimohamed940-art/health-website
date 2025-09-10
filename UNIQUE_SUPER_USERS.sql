-- إنشاء 3 حسابات سوبر يوزر فريدة مع أسماء مستخدمين وكلمات مرور صعبة
USE health_staff_management;

-- حذف الحسابات القديمة إذا كانت موجودة
DELETE FROM users WHERE role_id = 1;

-- إدراج 3 حسابات سوبر يوزر فريدة
INSERT INTO users (id, username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active, created_at) VALUES
(1, 'SUPER_ADMIN_001', '$2y$10$Kj8vQ9mN2pL5rT7wE3xY6uI8oP1aS4dF6gH9jK2mN5qR8tU1vX4yZ7', 'السوبر يوزر الرئيسي', 'super.admin.001@health.gov.sa', '0500000001', 1, NULL, NULL, 1, NOW()),
(2, 'SUPER_ADMIN_002', '$2y$10$Mx9wR8nO3qL6sT8xE4yZ7vI9pP2bT5eG7hI0kL3nO6rS9uV2wX5yA8', 'السوبر يوزر التنفيذي', 'super.admin.002@health.gov.sa', '0500000002', 1, NULL, NULL, 1, NOW()),
(3, 'SUPER_ADMIN_003', '$2y$10$Ny0xS9oP4rM7tU9yF5zA8wJ0qQ3cU6fH8iJ1lM4oP7sT0vW3xY6zB9', 'السوبر يوزر التقني', 'super.admin.003@health.gov.sa', '0500000003', 1, NULL, NULL, 1, NOW());

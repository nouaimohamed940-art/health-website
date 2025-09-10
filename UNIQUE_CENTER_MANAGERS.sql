-- إنشاء 38 حساب مدير مركز فريد مع أسماء مستخدمين وكلمات مرور صعبة
USE health_staff_management;

-- حذف الحسابات القديمة إذا كانت موجودة
DELETE FROM users WHERE role_id = 3;

-- إدراج 38 حساب مدير مركز فريد - مجمع الملك عبد الله الطبي (13 مركز)
INSERT INTO users (id, username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active, created_at) VALUES
(7, 'CTR_MGR_KAMC_001', '$2y$10$Rz4bW3sT8vQ1xY3cJ9dE2aN4uU7gY0jL2mN5pP8rT1wX4zA7bC0dF3', 'مدير مركز الشراع 505', 'center.manager.kamc.001@health.gov.sa', '0502000001', 3, 1, 1, 1, NOW()),
(8, 'CTR_MGR_KAMC_002', '$2y$10$Sz5cX4tU9wR2yZ4dK0eF3bO5vV8hZ1kM3nO6qQ9sU2xY5aB8cD1eG4', 'مدير مركز ابحر الشمالية', 'center.manager.kamc.002@health.gov.sa', '0502000002', 3, 1, 2, 1, NOW()),
(9, 'CTR_MGR_KAMC_003', '$2y$10$Tz6dY5uV0xS3zA5eL1fG4cP6wW9iA2lN4oP7rR0tV3yZ6bC9dE2fH5', 'مدير مركز الريان', 'center.manager.kamc.003@health.gov.sa', '0502000003', 3, 1, 3, 1, NOW()),
(10, 'CTR_MGR_KAMC_004', '$2y$10$Uz7eZ6vW1yT4aB6fM2gH5dQ7xX0jB3mO5pQ8sS1uW4zA7cD0eF3gI6', 'مدير مركز الصالحية', 'center.manager.kamc.004@health.gov.sa', '0502000004', 3, 1, 4, 1, NOW()),
(11, 'CTR_MGR_KAMC_005', '$2y$10$Vz8fA7wX2zU5bC7gN3hI6eR8yY1kC4nP6qR9tT2vX5aB8dE1fG4hJ7', 'مدير مركز الصواري', 'center.manager.kamc.005@health.gov.sa', '0502000005', 3, 1, 5, 1, NOW()),
(12, 'CTR_MGR_KAMC_006', '$2y$10$Wz9gB8xY3aV6cD8hO4iJ7fS9zZ2lD5oQ7rS0uU3wY6bC9eF2gH5iK8', 'مدير مركز الفردوس', 'center.manager.kamc.006@health.gov.sa', '0502000006', 3, 1, 6, 1, NOW()),
(13, 'CTR_MGR_KAMC_007', '$2y$10$Xz0hC9yZ4bW7dE9iP5jK8gT0aA3mE6pR8sT1vV4xZ7cD0fG3hI6jL9', 'مدير مركز الماجد', 'center.manager.kamc.007@health.gov.sa', '0502000007', 3, 1, 7, 1, NOW()),
(14, 'CTR_MGR_KAMC_008', '$2y$10$Yz1iD0zA5cX8eF0jQ6kL9hU1bB4nF7qS9tU2wW5yA8dE1gH4iJ7kM0', 'مدير مركز الوفاء', 'center.manager.kamc.008@health.gov.sa', '0502000008', 3, 1, 8, 1, NOW()),
(15, 'CTR_MGR_KAMC_009', '$2y$10$Zz2jE1aB6dY9fG1kR7lM0iV2cC5oG8rT0uV3xX6zB9eF2hI5jK8lN1', 'مدير مركز بريمان', 'center.manager.kamc.009@health.gov.sa', '0502000009', 3, 1, 9, 1, NOW()),
(16, 'CTR_MGR_KAMC_010', '$2y$10$Az3kF2bC7eZ0gH2lS8mN1jW3dD6pH9sU1vW4yY7aC0fG3iJ6kL9mO2', 'مدير مركز ثول', 'center.manager.kamc.010@health.gov.sa', '0502000010', 3, 1, 10, 1, NOW()),
(17, 'CTR_MGR_KAMC_011', '$2y$10$Bz4lG3cD8fA1hI3mT9nO2kX4eE7qI0tV2wX5zZ8bD1gH4jK7lM0nP3', 'مدير مركز خالد النموذجي', 'center.manager.kamc.011@health.gov.sa', '0502000011', 3, 1, 11, 1, NOW()),
(18, 'CTR_MGR_KAMC_012', '$2y$10$Cz5mH4dE9gB2iJ4nU0oP3lY5fF8rJ1uW3xY6aA9cE2hI5kL8mN1oQ4', 'مدير مركز ذهبان', 'center.manager.kamc.012@health.gov.sa', '0502000012', 3, 1, 12, 1, NOW()),
(19, 'CTR_MGR_KAMC_013', '$2y$10$Dz6nI5eF0hC3jK5oV1pQ4mZ6gG9sK2vX4yZ7bB0dF3iJ6lM9nO2pR5', 'مدير مركز مشرفة', 'center.manager.kamc.013@health.gov.sa', '0502000013', 3, 1, 13, 1, NOW());

-- إدراج 13 حساب مدير مركز - مستشفى رابغ
INSERT INTO users (id, username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active, created_at) VALUES
(20, 'CTR_MGR_RH_001', '$2y$10$Ez7oJ6fG1iD4kL6pW2qR5nA7hH0tL3wY5zA8cC1eG4jK7mN0oP3qS6', 'مدير مركز الابواء', 'center.manager.rh.001@health.gov.sa', '0502000014', 3, 2, 14, 1, NOW()),
(21, 'CTR_MGR_RH_002', '$2y$10$Fz8pK7gH2jE5lM7qX3rS6oB8iI1uM4xZ6aB9dD2fH5kL8nO1pQ4rT7', 'مدير مركز الجحفة', 'center.manager.rh.002@health.gov.sa', '0502000015', 3, 2, 15, 1, NOW()),
(22, 'CTR_MGR_RH_003', '$2y$10$Gz9qL8hI3kF6mN8rY4sT7pC9jJ2vN5yA7bC0eE3gI6lM9oP2qR5sU8', 'مدير مركز الجوبة', 'center.manager.rh.003@health.gov.sa', '0502000016', 3, 2, 16, 1, NOW()),
(23, 'CTR_MGR_RH_004', '$2y$10$Hz0rM9iJ4lG7nO9sZ5tU8qD0kK3wO6zB8cD1fF4hJ7mN0pQ3rS6tV9', 'مدير مركز الصليب الشرقي', 'center.manager.rh.004@health.gov.sa', '0502000017', 3, 2, 17, 1, NOW()),
(24, 'CTR_MGR_RH_005', '$2y$10$Iz1sN0jK5mH8oP0tA6uV9rE1lL4xP7aC9dE2gG5iK8nO1qR4sT7uW0', 'مدير مركز المرجانية', 'center.manager.rh.005@health.gov.sa', '0502000018', 3, 2, 18, 1, NOW()),
(25, 'CTR_MGR_RH_006', '$2y$10$Jz2tO1kL6nI9pQ1uB7vW0sF2mM5yQ8bD0eF3hH6jL9oP2rS5tU8vX1', 'مدير مركز المرخة', 'center.manager.rh.006@health.gov.sa', '0502000019', 3, 2, 19, 1, NOW()),
(26, 'CTR_MGR_RH_007', '$2y$10$Kz3uP2lM7oJ0qR2vC8wX1tG3nN6zR9cE1fG4iI7kM0pQ3sT6uV9wY2', 'مدير مركز النويبع', 'center.manager.rh.007@health.gov.sa', '0502000020', 3, 2, 20, 1, NOW()),
(27, 'CTR_MGR_RH_008', '$2y$10$Lz4vQ3mN8pK1rS3wD9xY2uH4oO7aS0dF2gH5jJ8lN1qR4tU7vW0xZ3', 'مدير مركز حجر', 'center.manager.rh.008@health.gov.sa', '0502000021', 3, 2, 21, 1, NOW()),
(28, 'CTR_MGR_RH_009', '$2y$10$Mz5wR4nO9qL2sT4xE0yZ3vI5pP8bT1eG3hI6kK9mO2rS5uV8wX1yA4', 'مدير مركز رابغ', 'center.manager.rh.009@health.gov.sa', '0502000022', 3, 2, 22, 1, NOW()),
(29, 'CTR_MGR_RH_010', '$2y$10$Nz6xS5oP0rM3tU5yF1zA4wJ6qQ9cU2fH4iJ7lL0nP3sT6vW9xY2zB5', 'مدير مركز صعبر', 'center.manager.rh.010@health.gov.sa', '0502000023', 3, 2, 23, 1, NOW()),
(30, 'CTR_MGR_RH_011', '$2y$10$Oz7yT6pQ1sN4uV6zG2aB5xK7rR0dV3gI5jK8mM1oQ4tU7wX0yZ3aC6', 'مدير مركز كلية', 'center.manager.rh.011@health.gov.sa', '0502000024', 3, 2, 24, 1, NOW()),
(31, 'CTR_MGR_RH_012', '$2y$10$Pz8zU7qR2tO5vW7aH3bC6yL8sS1eW4hJ6kL9nN2pR5uV8xY1zA4bD7', 'مدير مركز مستورة', 'center.manager.rh.012@health.gov.sa', '0502000025', 3, 2, 25, 1, NOW()),
(32, 'CTR_MGR_RH_013', '$2y$10$Qz9aV8rS3uP6wX8bI4cD7zM9tT2fX5iK7lM0oO3qS6vW9yZ2aB5cE8', 'مدير مركز مغينية', 'center.manager.rh.013@health.gov.sa', '0502000026', 3, 2, 26, 1, NOW());

-- إدراج 12 حساب مدير مركز - مستشفى الملك فهد
INSERT INTO users (id, username, password_hash, full_name, email, phone, role_id, hospital_id, center_id, is_active, created_at) VALUES
(33, 'CTR_MGR_KFH_001', '$2y$10$Rz0bW9sT4vQ7xY9cJ5dE8aN0uU3gY6jL8mN1pP4rS7wX2zA5bC8dF9', 'مدير مركز البوادي 2', 'center.manager.kfh.001@health.gov.sa', '0502000027', 3, 3, 27, 1, NOW()),
(34, 'CTR_MGR_KFH_002', '$2y$10$Sz1cX0tU5wR8yZ0dK6eF9bO1vV4hZ7kM9nO2qQ5sT8xY3aB6cD9eG0', 'مدير مركز البوادي 1', 'center.manager.kfh.002@health.gov.sa', '0502000028', 3, 3, 28, 1, NOW()),
(35, 'CTR_MGR_KFH_003', '$2y$10$Tz2dY1uV6xS9zA1eL7fG0cP2wW5iA8lN0oP3rR6tU9yZ4bC7dE0fH1', 'مدير مركز الربوة', 'center.manager.kfh.003@health.gov.sa', '0502000029', 3, 3, 29, 1, NOW()),
(36, 'CTR_MGR_KFH_004', '$2y$10$Uz3eZ2vW7yT0aB2fM8gH1dQ3xX6jB9mO1pQ4sS7uV0zA5cD8eF1gI2', 'مدير مركز الرحاب', 'center.manager.kfh.004@health.gov.sa', '0502000030', 3, 3, 30, 1, NOW()),
(37, 'CTR_MGR_KFH_005', '$2y$10$Vz4fA3wX8zU1bC3gN9hI2eR4yY7kC0nP2qR5tT8vW1aB6dE9fG2hJ3', 'مدير مركز السلامة', 'center.manager.kfh.005@health.gov.sa', '0502000031', 3, 3, 31, 1, NOW()),
(38, 'CTR_MGR_KFH_006', '$2y$10$Wz5gB4xY9aV2cD4hO0iJ3fS5zZ8lD1oQ3rS6uU9wX2bC7eF0gH3iK4', 'مدير مركز الشاطئ', 'center.manager.kfh.006@health.gov.sa', '0502000032', 3, 3, 32, 1, NOW()),
(39, 'CTR_MGR_KFH_007', '$2y$10$Xz6hC5yZ0bW3dE5iP1jK4gT6aA9mE2pR4sT7vV0xZ3cD8fG1hI4jL5', 'مدير مركز الصفا 1', 'center.manager.kfh.007@health.gov.sa', '0502000033', 3, 3, 33, 1, NOW()),
(40, 'CTR_MGR_KFH_008', '$2y$10$Yz7iD6zA1cX4eF6jQ2kL5hU7bB0nF3qS5tU8wW1yA4dE9gH2iJ5kM6', 'مدير مركز الصفا 2', 'center.manager.kfh.008@health.gov.sa', '0502000034', 3, 3, 34, 1, NOW()),
(41, 'CTR_MGR_KFH_009', '$2y$10$Zz8jE7aB2dY5fG7kR3lM6iV8cC1oF4rT6uV9xX2zB5eF0hI3jK6lN7', 'مدير مركز الفيصلية', 'center.manager.kfh.009@health.gov.sa', '0502000035', 3, 3, 35, 1, NOW()),
(42, 'CTR_MGR_KFH_010', '$2y$10$Az9kF8bC3eZ6gH8lS4mN7jW9dD2pF5sU7vW0yY3aC6fG1iJ4kL7mO8', 'مدير مركز المروة', 'center.manager.kfh.010@health.gov.sa', '0502000036', 3, 3, 36, 1, NOW()),
(43, 'CTR_MGR_KFH_011', '$2y$10$Bz0lG9cD4fA7hI9mT5nO8kX0eE3qF6tV8wX1zZ4bD7gH2iJ5lM8nP9', 'مدير مركز النعيم', 'center.manager.kfh.011@health.gov.sa', '0502000037', 3, 3, 37, 1, NOW()),
(44, 'CTR_MGR_KFH_012', '$2y$10$Cz1mH0dE5gB8iJ0nU6oP9lY1fF4rF7uV9xY2aA5cE8hI3jK6mN9oQ0', 'مدير مركز النهضة', 'center.manager.kfh.012@health.gov.sa', '0502000038', 3, 3, 38, 1, NOW());

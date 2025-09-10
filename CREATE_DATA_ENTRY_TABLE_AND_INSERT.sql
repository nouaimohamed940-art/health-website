-- إنشاء جدول مدخلي البيانات وإدراجهم
USE health_staff_management;

-- حذف جدول مدخلي البيانات إذا كان موجوداً
DROP TABLE IF EXISTS data_entry_users;

-- إنشاء جدول مدخلي البيانات
CREATE TABLE data_entry_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    center_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (center_id) REFERENCES centers(id) ON DELETE CASCADE
);

-- 76 حساب مدخل بيانات - مجمع الملك عبد الله الطبي (26 حساب)
INSERT INTO data_entry_users (username, email, password_hash, full_name, phone, center_id, is_active) VALUES
('DE_KAMC_001_A', 'data.entry.kamc.001a@health.gov.sa', '$2y$10$Dz6nI5eF0hC3jK5oV1pQ4mZ6gG9sK2vX4yZ7bB0dF3iJ6lM9nO2pR5', 'مدخل بيانات أول - الشراع 505', '0503000001', 1, 1),
('DE_KAMC_001_B', 'data.entry.kamc.001b@health.gov.sa', '$2y$10$Ez7oJ6fG1iD4kL6pW2qR5nA7hH0tL3wY5zA8cC1eG4jK7mN0oP3qS6', 'مدخل بيانات ثاني - الشراع 505', '0503000002', 1, 1),
('DE_KAMC_002_A', 'data.entry.kamc.002a@health.gov.sa', '$2y$10$Fz8pK7gH2jE5lM7qX3rS6oB8iI1uM4xZ6aB9dD2fH5kL8nO1pQ4rT7', 'مدخل بيانات أول - ابحر الشمالية', '0503000003', 2, 1),
('DE_KAMC_002_B', 'data.entry.kamc.002b@health.gov.sa', '$2y$10$Gz9qL8hI3kF6mN8rY4sT7pC9jJ2vN5yA7bC0eE3gI6lM9oP2qR5sU8', 'مدخل بيانات ثاني - ابحر الشمالية', '0503000004', 2, 1),
('DE_KAMC_003_A', 'data.entry.kamc.003a@health.gov.sa', '$2y$10$Hz0rM9iJ4lG7nO9sZ5tU8qD0kK3wO6zB8cD1fF4hJ7mN0pQ3rS6tV9', 'مدخل بيانات أول - الريان', '0503000005', 3, 1),
('DE_KAMC_003_B', 'data.entry.kamc.003b@health.gov.sa', '$2y$10$Iz1sN0jK5mH8oP0tA6uV9rE1lL4xP7aC9dE2gG5iK8nO1qR4sT7uW0', 'مدخل بيانات ثاني - الريان', '0503000006', 3, 1),
('DE_KAMC_004_A', 'data.entry.kamc.004a@health.gov.sa', '$2y$10$Jz2tO1kL6nI9pQ1uB7vW0sF2mM5yQ8bD0eF3hH6jL9oP2rS5tU8vX1', 'مدخل بيانات أول - الصالحية', '0503000007', 4, 1),
('DE_KAMC_004_B', 'data.entry.kamc.004b@health.gov.sa', '$2y$10$Kz3uP2lM7oJ0qR2vC8wX1tG3nN6zR9cE1fG4iI7kM0pQ3sT6uV9wY2', 'مدخل بيانات ثاني - الصالحية', '0503000008', 4, 1),
('DE_KAMC_005_A', 'data.entry.kamc.005a@health.gov.sa', '$2y$10$Lz4vQ3mN8pK1rS3wD9xY2uH4oO7aS0dF2gH5jJ8lN1qR4tU7vW0xZ3', 'مدخل بيانات أول - الصواري', '0503000009', 5, 1),
('DE_KAMC_005_B', 'data.entry.kamc.005b@health.gov.sa', '$2y$10$Mz5wR4nO9qL2sT4xE0yZ3vI5pP8bT1eG3hI6kK9mO2rS5uV8wX1yA4', 'مدخل بيانات ثاني - الصواري', '0503000010', 5, 1),
('DE_KAMC_006_A', 'data.entry.kamc.006a@health.gov.sa', '$2y$10$Nz6xS5oP0rM3tU5yF1zA4wJ6qQ9cU2fH4iJ7lL0nP3sT6vW9xY2zB5', 'مدخل بيانات أول - الفردوس', '0503000011', 6, 1),
('DE_KAMC_006_B', 'data.entry.kamc.006b@health.gov.sa', '$2y$10$Oz7yT6pQ1sN4uV6zG2aB5xK7rR0dV3gI5jK8mM1oQ4tU7wX0yZ3aC6', 'مدخل بيانات ثاني - الفردوس', '0503000012', 6, 1),
('DE_KAMC_007_A', 'data.entry.kamc.007a@health.gov.sa', '$2y$10$Pz8zU7qR2tO5vW7aH3bC6yL8sS1eW4hJ6kL9nN2pR5uV8xY1zA4bD7', 'مدخل بيانات أول - الماجد', '0503000013', 7, 1),
('DE_KAMC_007_B', 'data.entry.kamc.007b@health.gov.sa', '$2y$10$Qz9aV8rS3uP6wX8bI4cD7zM9tT2fX5iK7lM0oO3qS6vW9yZ2aB5cE8', 'مدخل بيانات ثاني - الماجد', '0503000014', 7, 1),
('DE_KAMC_008_A', 'data.entry.kamc.008a@health.gov.sa', '$2y$10$Rz0bW9sT4vQ7xY9cJ5dE8aN0uU3gY6jL8mN1pP4rS7wX2zA5bC8dF9', 'مدخل بيانات أول - الوفاء', '0503000015', 8, 1),
('DE_KAMC_008_B', 'data.entry.kamc.008b@health.gov.sa', '$2y$10$Sz1cX0tU5wR8yZ0dK6eF9bO1vV4hZ7kM9nO2qQ5sT8xY3aB6cD9eG0', 'مدخل بيانات ثاني - الوفاء', '0503000016', 8, 1),
('DE_KAMC_009_A', 'data.entry.kamc.009a@health.gov.sa', '$2y$10$Tz2dY1uV6xS9zA1eL7fG0cP2wW5iA8lN0oP3rR6tU9yZ4bC7dE0fH1', 'مدخل بيانات أول - بريمان', '0503000017', 9, 1),
('DE_KAMC_009_B', 'data.entry.kamc.009b@health.gov.sa', '$2y$10$Uz3eZ2vW7yT0aB2fM8gH1dQ3xX6jB9mO1pQ4sS7uV0zA5cD8eF1gI2', 'مدخل بيانات ثاني - بريمان', '0503000018', 9, 1),
('DE_KAMC_010_A', 'data.entry.kamc.010a@health.gov.sa', '$2y$10$Vz4fA3wX8zU1bC3gN9hI2eR4yY7kC0nP2qR5tT8vW1aB6dE9fG2hJ3', 'مدخل بيانات أول - ثول', '0503000019', 10, 1),
('DE_KAMC_010_B', 'data.entry.kamc.010b@health.gov.sa', '$2y$10$Wz5gB4xY9aV2cD4hO0iJ3fS5zZ8lD1oQ3rS6uU9wX2bC7eF0gH3iK4', 'مدخل بيانات ثاني - ثول', '0503000020', 10, 1),
('DE_KAMC_011_A', 'data.entry.kamc.011a@health.gov.sa', '$2y$10$Xz6hC5yZ0bW3dE5iP1jK4gT6aA9mE2pR4sT7vV0xZ3cD8fG1hI4jL5', 'مدخل بيانات أول - خالد النموذجي', '0503000021', 11, 1),
('DE_KAMC_011_B', 'data.entry.kamc.011b@health.gov.sa', '$2y$10$Yz7iD6zA1cX4eF6jQ2kL5hU7bB0nF3qS5tU8wW1yA4dE9gH2iJ5kM6', 'مدخل بيانات ثاني - خالد النموذجي', '0503000022', 11, 1),
('DE_KAMC_012_A', 'data.entry.kamc.012a@health.gov.sa', '$2y$10$Zz8jE7aB2dY5fG7kR3lM6iV8cC1oF4rT6uV9xX2zB5eF0hI3jK6lN7', 'مدخل بيانات أول - ذهبان', '0503000023', 12, 1),
('DE_KAMC_012_B', 'data.entry.kamc.012b@health.gov.sa', '$2y$10$Az9kF8bC3eZ6gH8lS4mN7jW9dD2pF5sU7vW0yY3aC6fG1iJ4kL7mO8', 'مدخل بيانات ثاني - ذهبان', '0503000024', 12, 1),
('DE_KAMC_013_A', 'data.entry.kamc.013a@health.gov.sa', '$2y$10$Bz0lG9cD4fA7hI9mT5nO8kX0eE3qF6tV8wX1zZ4bD7gH2iJ5lM8nP9', 'مدخل بيانات أول - مشرفة', '0503000025', 13, 1),
('DE_KAMC_013_B', 'data.entry.kamc.013b@health.gov.sa', '$2y$10$Cz1mH0dE5gB8iJ0nU6oP9lY1fF4rF7uV9xY2aA5cE8hI3jK6mN9oQ0', 'مدخل بيانات ثاني - مشرفة', '0503000026', 13, 1);

-- 26 حساب مدخل بيانات - مستشفى رابغ
INSERT INTO data_entry_users (username, email, password_hash, full_name, phone, center_id, is_active) VALUES
('DE_RH_001_A', 'data.entry.rh.001a@health.gov.sa', '$2y$10$Dz2nI1eF6gB9jK2oV3pQ5mZ7hH0tL4wY6zA9cC2eG5jK8mN1oP4qS7', 'مدخل بيانات أول - الابواء', '0503000027', 14, 1),
('DE_RH_001_B', 'data.entry.rh.001b@health.gov.sa', '$2y$10$Ez3oJ2fG7hC0kL3pW4qR6nA8iI1uM5xZ7aB0dD3fH6kL9nO2pQ5rT8', 'مدخل بيانات ثاني - الابواء', '0503000028', 14, 1),
('DE_RH_002_A', 'data.entry.rh.002a@health.gov.sa', '$2y$10$Fz4pK3gH8iD1lM4qX5rS7oB9jJ2vN6yA8bC1eE4gI7lM0oP3qR6sU9', 'مدخل بيانات أول - الجحفة', '0503000029', 15, 1),
('DE_RH_002_B', 'data.entry.rh.002b@health.gov.sa', '$2y$10$Gz5qL4hI9jE2mN5rY6sT8pC0kK3wO7zB9cD2fF5hJ8mN1pQ4rS7tV0', 'مدخل بيانات ثاني - الجحفة', '0503000030', 15, 1),
('DE_RH_003_A', 'data.entry.rh.003a@health.gov.sa', '$2y$10$Hz6rM5iJ0kF3nO6sZ7tU9qD1lL4xP8aC0dE3gG6iK9nO2qR5sT8uW1', 'مدخل بيانات أول - الجوبة', '0503000031', 16, 1),
('DE_RH_003_B', 'data.entry.rh.003b@health.gov.sa', '$2y$10$Iz7sN6jK1lG4oP7tA8uV0rE2mM5yQ9bD1eF4hH7jL0oP3rS6tU9vX2', 'مدخل بيانات ثاني - الجوبة', '0503000032', 16, 1),
('DE_RH_004_A', 'data.entry.rh.004a@health.gov.sa', '$2y$10$Jz8tO7kL2mH5pQ8uB9vW1sF3nN6zR0cE2fG5iI8kM1pQ4sT7uV0wY3', 'مدخل بيانات أول - الصليب الشرقي', '0503000033', 17, 1),
('DE_RH_004_B', 'data.entry.rh.004b@health.gov.sa', '$2y$10$Kz9uP8lM3nI6qR9vC0wX2tG4oO7aS1dF3gH6jJ9lN2qR5tU8vW1xZ4', 'مدخل بيانات ثاني - الصليب الشرقي', '0503000034', 17, 1),
('DE_RH_005_A', 'data.entry.rh.005a@health.gov.sa', '$2y$10$Lz0vQ9mN4oJ7rS0wD1xY3uH5pP8bT2eG4hI7kK0mO3rS6uV9wX2yA5', 'مدخل بيانات أول - المرجانية', '0503000035', 18, 1),
('DE_RH_005_B', 'data.entry.rh.005b@health.gov.sa', '$2y$10$Mz1wR0nO5pK8sT1xE2yZ4vI6qQ9cU3fH5iJ8lL1nP4sT7vW0xY3zB6', 'مدخل بيانات ثاني - المرجانية', '0503000036', 18, 1),
('DE_RH_006_A', 'data.entry.rh.006a@health.gov.sa', '$2y$10$Nz2xS1oP6qL9tU2yF3zA5wJ7rR0dV4gI6jK9mM2oQ5tU8wX1yZ4aC7', 'مدخل بيانات أول - المرخة', '0503000037', 19, 1),
('DE_RH_006_B', 'data.entry.rh.006b@health.gov.sa', '$2y$10$Oz3yT2pQ7rM0uV3zG4aB6xK8sS1eW5hJ7kL0nN3pR6uV9xY2zA5bD8', 'مدخل بيانات ثاني - المرخة', '0503000038', 19, 1),
('DE_RH_007_A', 'data.entry.rh.007a@health.gov.sa', '$2y$10$Pz4zU3qR8sN1vW4aH5bC7yL9tT2fX6iK8lM1oO4qS7vW0yZ3aB6cE9', 'مدخل بيانات أول - النويبع', '0503000039', 20, 1),
('DE_RH_007_B', 'data.entry.rh.007b@health.gov.sa', '$2y$10$Qz5aV4rS9tO2wX5bI6cD8zM0uU3gY7jL9mN2pP5rT8wX1zA4bC7dF0', 'مدخل بيانات ثاني - النويبع', '0503000040', 20, 1),
('DE_RH_008_A', 'data.entry.rh.008a@health.gov.sa', '$2y$10$Rz6bW5sT0uP3xY6cJ7dE9aN1vV4hZ8kM0nO3qQ6sU9xY2aB5cD8eG1', 'مدخل بيانات أول - حجر', '0503000041', 21, 1),
('DE_RH_008_B', 'data.entry.rh.008b@health.gov.sa', '$2y$10$Sz7cX6tU1vQ4yZ7dK8eF0bO2wW5iA9lN1oP4rR7tV0yZ3bC6dE9fH2', 'مدخل بيانات ثاني - حجر', '0503000042', 21, 1),
('DE_RH_009_A', 'data.entry.rh.009a@health.gov.sa', '$2y$10$Tz8dY7uV2wR5zA8eL9fG1cP3xX6jB0mO2pQ5sS8uW1zA4cD7eF0gI3', 'مدخل بيانات أول - رابغ', '0503000043', 22, 1),
('DE_RH_009_B', 'data.entry.rh.009b@health.gov.sa', '$2y$10$Uz9eZ8vW3xS6aB9fM0gH2dQ4yY7kC1nP3qR6tT9vX2aB5dE8fG1hJ4', 'مدخل بيانات ثاني - رابغ', '0503000044', 22, 1),
('DE_RH_010_A', 'data.entry.rh.010a@health.gov.sa', '$2y$10$Vz0fA9wX4yT7bC0gN1hI3eR5zZ8lD2oQ4rS7uU0wY3bC6eF9gH2iK5', 'مدخل بيانات أول - صعبر', '0503000045', 23, 1),
('DE_RH_010_B', 'data.entry.rh.010b@health.gov.sa', '$2y$10$Wz1gB0xY5zU8cD1hO2iJ4fS6aA9mE3pR5sT8vV1xZ4cD7fG0hI3jL6', 'مدخل بيانات ثاني - صعبر', '0503000046', 23, 1),
('DE_RH_011_A', 'data.entry.rh.011a@health.gov.sa', '$2y$10$Xz2hC1yZ6aV9dE2iP3jK5gT7bB0nF4qS6tU9wW2yA5dE8gH1iJ4kM7', 'مدخل بيانات أول - كلية', '0503000047', 24, 1),
('DE_RH_011_B', 'data.entry.rh.011b@health.gov.sa', '$2y$10$Yz3iD2zA7bW0eF3jQ4kL6hU8cC1oG5rT7uV0xX3zB6eF9hI2jK5lN8', 'مدخل بيانات ثاني - كلية', '0503000048', 24, 1),
('DE_RH_012_A', 'data.entry.rh.012a@health.gov.sa', '$2y$10$Zz4jE3aB8cX1fG4kR5lM7iV9dD2pH6sU8vW1yY4aC7fG0iJ3kL6mO9', 'مدخل بيانات أول - مستورة', '0503000049', 25, 1),
('DE_RH_012_B', 'data.entry.rh.012b@health.gov.sa', '$2y$10$Az5kF4bC9dY2gH5lS6mN8jW0eE3qI7tV9wX2zZ5bD8gH1jK4lM7nP0', 'مدخل بيانات ثاني - مستورة', '0503000050', 25, 1),
('DE_RH_013_A', 'data.entry.rh.013a@health.gov.sa', '$2y$10$Bz6lG5cD0eZ3hI6mT7nO9kX1fF4rJ8uW0xY3aA6cE9hI2kL5mN8oQ1', 'مدخل بيانات أول - مغينية', '0503000051', 26, 1),
('DE_RH_013_B', 'data.entry.rh.013b@health.gov.sa', '$2y$10$Cz7mH6dE1fA4iJ7nU8oP0lY2gG5sK9vX1yZ4bB7dF0iJ3lM6nO9pR2', 'مدخل بيانات ثاني - مغينية', '0503000052', 26, 1);

-- 24 حساب مدخل بيانات - مستشفى الملك فهد
INSERT INTO data_entry_users (username, email, password_hash, full_name, phone, center_id, is_active) VALUES
('DE_KFH_001_A', 'data.entry.kfh.001a@health.gov.sa', '$2y$10$Dz8nI7eF2gB5jK8oV1pQ6mZ9hH2tL5wY8zA1cC4eG7jK0mN3oP6qS9', 'مدخل بيانات أول - البوادي 2', '0503000053', 27, 1),
('DE_KFH_001_B', 'data.entry.kfh.001b@health.gov.sa', '$2y$10$Ez9oJ8fG3hC6kL9pW2qR7nA0iI3uM6xZ9aB2dD5fH8kL1nO4pQ7rT0', 'مدخل بيانات ثاني - البوادي 2', '0503000054', 27, 1),
('DE_KFH_002_A', 'data.entry.kfh.002a@health.gov.sa', '$2y$10$Fz0pK9gH4iD7lM0qX3rS8oB1jJ4vN7yA0bC3eE6gI9lM2oP5qR8sU1', 'مدخل بيانات أول - البوادي 1', '0503000055', 28, 1),
('DE_KFH_002_B', 'data.entry.kfh.002b@health.gov.sa', '$2y$10$Gz1qL0hI5jE8mN1rY4sT9pC2kK5wO8zB1cD4fF7hJ0mN3pQ6rS9tV2', 'مدخل بيانات ثاني - البوادي 1', '0503000056', 28, 1),
('DE_KFH_003_A', 'data.entry.kfh.003a@health.gov.sa', '$2y$10$Hz2rM1iJ6kF9nO2sZ5tU0qD3lL6xP9aC2dE5gG8iK1nO4qR7tU0uW3', 'مدخل بيانات أول - الربوة', '0503000057', 29, 1),
('DE_KFH_003_B', 'data.entry.kfh.003b@health.gov.sa', '$2y$10$Iz3sN2jK7lG0oP3tA6uV1rE4mM7yQ0bD3eF6hH9jL2oP5rS8uV1vX4', 'مدخل بيانات ثاني - الربوة', '0503000058', 29, 1),
('DE_KFH_004_A', 'data.entry.kfh.004a@health.gov.sa', '$2y$10$Jz4tO3kL8mH1pQ4uB7vW2sF5nN8zR1cE4fG7iI0kM3pQ6sT9vW2wY5', 'مدخل بيانات أول - الرحاب', '0503000059', 30, 1),
('DE_KFH_004_B', 'data.entry.kfh.004b@health.gov.sa', '$2y$10$Kz5uP4lM9nI2qR5vC8wX3tG6oO9aS2dF5gH8jJ1lN4qR7tU0wX3xZ6', 'مدخل بيانات ثاني - الرحاب', '0503000060', 30, 1),
('DE_KFH_005_A', 'data.entry.kfh.005a@health.gov.sa', '$2y$10$Lz6vQ5mN0oJ3rS6wD9xY4uH7pP0bT3eG6hI9kK2mO5rS8uV1xY4yA7', 'مدخل بيانات أول - السلامة', '0503000061', 31, 1),
('DE_KFH_005_B', 'data.entry.kfh.005b@health.gov.sa', '$2y$10$Mz7wR6nO1pK4sT7xE0yZ5vI8qQ1cU4fH7iJ0lL3nP6sT9vW2yZ5zB8', 'مدخل بيانات ثاني - السلامة', '0503000062', 31, 1),
('DE_KFH_006_A', 'data.entry.kfh.006a@health.gov.sa', '$2y$10$Nz8xS7oP2qL5tU8yF1zA6wJ9rR2dV5gI8jK1mM4oQ7tU0wX3zA6aC9', 'مدخل بيانات أول - الشاطئ', '0503000063', 32, 1),
('DE_KFH_006_B', 'data.entry.kfh.006b@health.gov.sa', '$2y$10$Oz9yT8pQ3rM6uV9zG2aB7xK0sS3eW6hJ9kL2nN5pR8uV1xY4aB7bD0', 'مدخل بيانات ثاني - الشاطئ', '0503000064', 32, 1),
('DE_KFH_007_A', 'data.entry.kfh.007a@health.gov.sa', '$2y$10$Pz0zU9qR4sN7vW0aH3bC8yL1tT4fX7iK0lM3oO6qS9vW2yZ5bC8cE1', 'مدخل بيانات أول - الصفا 1', '0503000065', 33, 1),
('DE_KFH_007_B', 'data.entry.kfh.007b@health.gov.sa', '$2y$10$Qz1aV0rS5tO8wX1bI4cD9zM2uU5gY8jL1mN4pP7rT0wX3zA6cD9dF2', 'مدخل بيانات ثاني - الصفا 1', '0503000066', 33, 1),
('DE_KFH_008_A', 'data.entry.kfh.008a@health.gov.sa', '$2y$10$Rz2bW1sT6uP9xY2cJ5dE0aN3vV6hZ9kM2nO5qQ8sU1xY4aB7dE0eG3', 'مدخل بيانات أول - الصفا 2', '0503000067', 34, 1),
('DE_KFH_008_B', 'data.entry.kfh.008b@health.gov.sa', '$2y$10$Sz3cX2tU7vQ0yZ3dK6eF1bO4wW7iA0lN3oP6rR9tV2yZ5bC8eF1fH4', 'مدخل بيانات ثاني - الصفا 2', '0503000068', 34, 1),
('DE_KFH_009_A', 'data.entry.kfh.009a@health.gov.sa', '$2y$10$Tz4dY3uV8wR1zA4eL7fG2cP5xX8jB1mO4pQ7sS0uW3zA6cD9fG2gI5', 'مدخل بيانات أول - الفيصلية', '0503000069', 35, 1),
('DE_KFH_009_B', 'data.entry.kfh.009b@health.gov.sa', '$2y$10$Uz5eZ4vW9xS2aB5fM8gH3dQ6yY9kC2nP5qR8tT1vX4aB7dE0gH3hJ6', 'مدخل بيانات ثاني - الفيصلية', '0503000070', 35, 1),
('DE_KFH_010_A', 'data.entry.kfh.010a@health.gov.sa', '$2y$10$Vz6fA5wX0yT3bC6gN9hI4eR7zZ0lD3oQ6rS9uU2wY5bC8eF1hI4iK7', 'مدخل بيانات أول - المروة', '0503000071', 36, 1),
('DE_KFH_010_B', 'data.entry.kfh.010b@health.gov.sa', '$2y$10$Wz7gB6xY1zU4cD7hO0iJ5fS8aA1mE4pR7sT0vV3xZ6cD9fG2iJ5jL8', 'مدخل بيانات ثاني - المروة', '0503000072', 36, 1),
('DE_KFH_011_A', 'data.entry.kfh.011a@health.gov.sa', '$2y$10$Xz8hC7yZ2aV5dE8iP1jK6gT9bB2nF5qS8tU1wW4yA7dE0gH3jK6kM9', 'مدخل بيانات أول - النعيم', '0503000073', 37, 1),
('DE_KFH_011_B', 'data.entry.kfh.011b@health.gov.sa', '$2y$10$Yz9iD8zA3bW6eF9jQ2kL7hU0cC3oG6rT9uV2xX5zB8eF1hI4kL7lN0', 'مدخل بيانات ثاني - النعيم', '0503000074', 37, 1),
('DE_KFH_012_A', 'data.entry.kfh.012a@health.gov.sa', '$2y$10$Zz0jE9aB4cX7fG0kR3lM8iV1dD4pH7sU0vW3yY6aC9fG2iJ5lM8mO1', 'مدخل بيانات أول - النهضة', '0503000075', 38, 1),
('DE_KFH_012_B', 'data.entry.kfh.012b@health.gov.sa', '$2y$10$Az1kF0bC5dY8gH1lS4mN9jW2eE5qI8tV1wX4zZ7bD0gH3jK6mN9nP2', 'مدخل بيانات ثاني - النهضة', '0503000076', 38, 1);

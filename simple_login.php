<?php
/**
 * صفحة تسجيل دخول مبسطة للاختبار
 * Simple Login Page for Testing
 */

// بدء الجلسة
session_start();

// إعدادات قاعدة البيانات
$host = 'localhost';
$port = '3307';
$db_name = 'health_staff_management';
$username = 'health_staff_user';
$password = 'HealthStaff2024!';

$error_message = '';
$success_message = '';

// معالجة تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username_input = trim($_POST['username'] ?? '');
    $password_input = $_POST['password'] ?? '';
    
    if (empty($username_input) || empty($password_input)) {
        $error_message = 'يرجى إدخال اسم المستخدم وكلمة المرور';
    } else {
        try {
            // الاتصال بقاعدة البيانات
            $dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            // البحث في جدول المستخدمين العاديين
            $stmt = $pdo->prepare("
                SELECT u.*, r.display_name as role_display_name, h.name as hospital_name, c.name as center_name
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.id 
                LEFT JOIN hospitals h ON u.hospital_id = h.id 
                LEFT JOIN centers c ON u.center_id = c.id 
                WHERE u.username = ? AND u.is_active = 1
            ");
            $stmt->execute([$username_input]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password_input, $user['password_hash'])) {
                // تسجيل الدخول ناجح
                $_SESSION['user'] = $user;
                $_SESSION['user_id'] = $user['id'];
                
                // توجيه حسب الدور
                if ($user['role_id'] == 4) { // مدخل بيانات
                    header('Location: data_entry_dashboard.php');
                } elseif ($user['role_id'] == 3) { // مدير مركز
                    header('Location: center_manager_dashboard.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            } else {
                // محاولة البحث في جدول مدخلي البيانات
                $stmt = $pdo->prepare("
                    SELECT de.*, 'مدخل بيانات' as role_display_name, h.name as hospital_name, c.name as center_name
                    FROM data_entry_users de 
                    JOIN centers c ON de.center_id = c.id 
                    JOIN hospitals h ON c.hospital_id = h.id 
                    WHERE de.username = ? AND de.is_active = 1
                ");
                $stmt->execute([$username_input]);
                $data_entry_user = $stmt->fetch();
                
                if ($data_entry_user && password_verify($password_input, $data_entry_user['password_hash'])) {
                    // تسجيل دخول مدخل البيانات ناجح
                    $data_entry_user['role_id'] = 4; // إضافة role_id
                    $_SESSION['user'] = $data_entry_user;
                    $_SESSION['user_id'] = $data_entry_user['id'];
                    header('Location: data_entry_dashboard.php');
                    exit;
                } else {
                    $error_message = 'اسم المستخدم أو كلمة المرور غير صحيحة';
                }
            }
            
        } catch (Exception $e) {
            $error_message = 'خطأ في الاتصال بقاعدة البيانات: ' . $e->getMessage();
        }
    }
}

// معالجة رسائل النظام
if (isset($_GET['timeout'])) {
    $error_message = 'انتهت مهلة الجلسة. يرجى تسجيل الدخول مرة أخرى';
} elseif (isset($_GET['logout'])) {
    $success_message = 'تم تسجيل الخروج بنجاح';
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام إدارة القوى العاملة الصحية</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .login-header {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .login-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .login-header p {
            font-size: 0.9rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .login-form {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 15px 50px 15px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .alert-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .test-accounts {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .test-accounts h3 {
            color: #1e293b;
            font-size: 1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .account-item {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .account-item:hover {
            border-color: #3b82f6;
            background: #f0f9ff;
        }

        .account-item:last-child {
            margin-bottom: 0;
        }

        .account-role {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.9rem;
        }

        .account-credentials {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 4px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-hospital"></i> نظام إدارة القوى العاملة الصحية</h1>
            <p>تسجيل الدخول للنظام</p>
        </div>

        <div class="login-form">
            <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username">اسم المستخدم</label>
                    <div class="input-group">
                        <input type="text" id="username" name="username" class="form-control" 
                               placeholder="أدخل اسم المستخدم" required autocomplete="username">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" 
                               placeholder="أدخل كلمة المرور" required autocomplete="current-password">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <button type="submit" name="login" class="btn">
                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </button>
            </form>

            <div class="test-accounts">
                <h3><i class="fas fa-users"></i> حسابات الاختبار</h3>
                
                <div class="account-item" onclick="fillCredentials('SUPER_ADMIN_001')">
                    <div class="account-role">سوبر أدمن</div>
                    <div class="account-credentials">SUPER_ADMIN_001 / password</div>
                </div>
                
                <div class="account-item" onclick="fillCredentials('HOSP_MGR_KAMC_001')">
                    <div class="account-role">مدير مستشفى</div>
                    <div class="account-credentials">HOSP_MGR_KAMC_001 / password</div>
                </div>
                
                <div class="account-item" onclick="fillCredentials('CTR_MGR_KAMC_001')">
                    <div class="account-role">مدير مركز</div>
                    <div class="account-credentials">CTR_MGR_KAMC_001 / password</div>
                </div>
                
                <div class="account-item" onclick="fillCredentials('DE_KAMC_001_A')">
                    <div class="account-role">مدخل بيانات</div>
                    <div class="account-credentials">DE_KAMC_001_A / password</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>&copy; 2024 نظام إدارة القوى العاملة الصحية. جميع الحقوق محفوظة.</p>
        </div>
    </div>

    <script>
        function fillCredentials(username) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = 'password';
        }

        // إضافة تأثير التحميل
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري تسجيل الدخول...';
            btn.disabled = true;
        });
    </script>
</body>
</html>

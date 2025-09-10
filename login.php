<?php
/**
 * صفحة تسجيل الدخول
 * Login Page
 */

require_once 'config/config.php';
require_once 'classes/User.php';

// إذا كان المستخدم مسجل دخول بالفعل، إعادة التوجيه للداشبورد
if (isLoggedIn()) {
    redirect('/dashboard.php');
}

$error_message = '';
$success_message = '';

// معالجة تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'يرجى إدخال اسم المستخدم وكلمة المرور';
    } else {
        $user = new User();
        $result = $user->login($username, $password, $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '');
        
        if ($result['success']) {
            $_SESSION['user'] = $result['user'];
            
            // توجيه المستخدم حسب دوره
            $role_id = $result['user']['role_id'];
            if ($role_id == ROLE_DATA_ENTRY) {
                redirect('/data_entry_dashboard.php');
            } elseif ($role_id == ROLE_CENTER_MANAGER) {
                redirect('/center_manager_dashboard.php');
            } else {
                redirect('/dashboard.php');
            }
        } else {
            // محاولة تسجيل الدخول كمدخل بيانات
            $data_entry_result = loginDataEntryUser($username, $password);
            if ($data_entry_result['success']) {
                $_SESSION['user'] = $data_entry_result['user'];
                redirect('/data_entry_dashboard.php');
            } else {
                $error_message = $result['message'];
            }
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
    <title>تسجيل الدخول - <?php echo SITE_NAME; ?></title>
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
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }

        .login-header .logo {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: #1e3a8a;
            position: relative;
            z-index: 1;
        }

        .login-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .login-header p {
            font-size: 1rem;
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
            font-weight: 600;
            color: #374151;
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
            padding: 15px 45px 15px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Cairo', sans-serif;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .alert {
            padding: 15px;
            border-radius: 12px;
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

        .login-footer {
            text-align: center;
            padding: 20px 30px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }

        .login-footer p {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .demo-accounts {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .demo-accounts h3 {
            color: #0369a1;
            font-size: 1rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .demo-account {
            background: white;
            border: 1px solid #e0f2fe;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .demo-account:hover {
            background: #e0f2fe;
            transform: translateX(-5px);
        }

        .demo-account:last-child {
            margin-bottom: 0;
        }

        .demo-account .role {
            font-weight: 600;
            color: #0369a1;
            font-size: 0.9rem;
        }

        .demo-account .credentials {
            color: #64748b;
            font-size: 0.8rem;
            margin-top: 4px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
                border-radius: 15px;
            }

            .login-header {
                padding: 30px 20px;
            }

            .login-form {
                padding: 30px 20px;
            }

            .login-footer {
                padding: 15px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-hospital"></i>
            </div>
            <h1>مرحباً بك</h1>
            <p>نظام إدارة القوى العاملة الصحية</p>
        </div>

        <div class="login-form">
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <div class="demo-accounts">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    حسابات تجريبية للاختبار
                </h3>
                <div class="demo-account" onclick="fillCredentials('SUPER_ADMIN_001', 'password')">
                    <div class="role">مدير عام على كل المراكز</div>
                    <div class="credentials">SUPER_ADMIN_001 / password</div>
                </div>
                <div class="demo-account" onclick="fillCredentials('HOSP_MGR_KAMC_001', 'password')">
                    <div class="role">مدير مجمع الملك عبد الله الطبي</div>
                    <div class="credentials">HOSP_MGR_KAMC_001 / password</div>
                </div>
                <div class="demo-account" onclick="fillCredentials('CTR_MGR_KAMC_001', 'password')">
                    <div class="role">مدير مركز الشراع 505 - مجمع الملك عبد الله الطبي</div>
                    <div class="credentials">CTR_MGR_KAMC_001 / password</div>
                </div>
                <div class="demo-account" onclick="fillCredentials('DE_KAMC_001_A', 'password')">
                    <div class="role">مدخل بيانات أول - الشراع 505</div>
                    <div class="credentials">DE_KAMC_001_A / password</div>
                </div>
                <div class="demo-account" onclick="fillCredentials('CTR_MGR_RH_001', 'password')">
                    <div class="role">مدير مركز الابواء - مستشفى رابغ</div>
                    <div class="credentials">CTR_MGR_RH_001 / password</div>
                </div>
                <div class="demo-account" onclick="fillCredentials('CTR_MGR_KFH_001', 'password')">
                    <div class="role">مدير مركز البوادي 2 - مستشفى الملك فهد</div>
                    <div class="credentials">CTR_MGR_KFH_001 / password</div>
                </div>
                <div class="demo-account" onclick="fillCredentials('DE_RH_001_A', 'password')">
                    <div class="role">مدخل بيانات أول - الابواء</div>
                    <div class="credentials">DE_RH_001_A / password</div>
                </div>
                <div class="demo-account" onclick="fillCredentials('DE_KFH_001_A', 'password')">
                    <div class="role">مدخل بيانات أول - البوادي 2</div>
                    <div class="credentials">DE_KFH_001_A / password</div>
                </div>
            </div>

            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label for="username">اسم المستخدم</label>
                    <div class="input-group">
                        <input type="text" id="username" name="username" class="form-control" required autocomplete="username">
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">كلمة المرور</label>
                    <div class="input-group">
                        <input type="password" id="password" name="password" class="form-control" required autocomplete="current-password">
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <button type="submit" name="login" class="btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    تسجيل الدخول
                </button>
            </form>

            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p>جاري تسجيل الدخول...</p>
            </div>
        </div>

        <div class="login-footer">
            <p>&copy; 2024 نظام إدارة القوى العاملة الصحية. جميع الحقوق محفوظة.</p>
        </div>
    </div>

    <script>
        function fillCredentials(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginBtn = document.getElementById('loginBtn');
            const loading = document.getElementById('loading');
            
            loginBtn.style.display = 'none';
            loading.style.display = 'block';
        });

        // إضافة تأثيرات بصرية
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // منع إرسال النموذج بالضغط على Enter في الحقول
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('loginForm').submit();
                }
            });
        });
    </script>
</body>
</html>

<?php
/**
 * صفحة تسجيل دخول مدخلي البيانات
 * Data Entry Users Login Page
 */

require_once 'config/config.php';
require_once 'config/database.php';

// إذا كان المستخدم مسجل دخول بالفعل، إعادة التوجيه
if (isLoggedIn()) {
    $current_user = getCurrentUser();
    if ($current_user['role_id'] == ROLE_CENTER_MANAGER) {
        redirect('/center_manager_dashboard.php');
    } elseif ($current_user['role_id'] == ROLE_HOSPITAL_MANAGER || $current_user['role_id'] == ROLE_SUPER_ADMIN) {
        redirect('/dashboard.php');
    }
}

$error_message = '';

// معالجة تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'يرجى إدخال اسم المستخدم وكلمة المرور';
    } else {
        try {
            $db = new Database();
            $stmt = $db->prepare("
                SELECT deu.*, c.name as center_name, h.name as hospital_name 
                FROM data_entry_users deu 
                JOIN centers c ON deu.center_id = c.id 
                JOIN hospitals h ON c.hospital_id = h.id 
                WHERE deu.username = ? AND deu.is_active = TRUE
            ");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // تسجيل نشاط تسجيل الدخول
                $stmt = $db->prepare("
                    INSERT INTO data_entry_activity_log 
                    (data_entry_user_id, center_id, activity_type, description, ip_address, user_agent) 
                    VALUES (?, ?, 'login', 'تسجيل دخول مدخل البيانات', ?, ?)
                ");
                $stmt->execute([
                    $user['id'], 
                    $user['center_id'], 
                    $_SERVER['REMOTE_ADDR'] ?? '', 
                    $_SERVER['HTTP_USER_AGENT'] ?? ''
                ]);
                
                // إنشاء جلسة خاصة بمدخلي البيانات
                $_SESSION['data_entry_user'] = $user;
                $_SESSION['user_type'] = 'data_entry';
                
                redirect('/data_entry_dashboard.php');
            } else {
                $error_message = 'اسم المستخدم أو كلمة المرور غير صحيحة';
            }
        } catch (Exception $e) {
            error_log("Data entry login error: " . $e->getMessage());
            $error_message = 'حدث خطأ في النظام، يرجى المحاولة لاحقاً';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول مدخلي البيانات - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e40af;
            --primary-dark: #1e3a8a;
            --primary-light: #3b82f6;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --info-color: #0891b2;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --border-radius: 12px;
            --border-radius-lg: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-800);
            line-height: 1.6;
        }

        .login-container {
            background: var(--white);
            padding: 3rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light), var(--info-color));
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--white);
            margin: 0 auto 1.5rem;
            box-shadow: var(--shadow);
        }

        .login-title {
            font-size: 1.875rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: var(--gray-600);
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background: var(--white);
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1.125rem;
        }

        .form-input {
            padding-right: 3rem;
        }

        .btn {
            width: 100%;
            background: var(--primary-color);
            color: var(--white);
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .demo-accounts {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-top: 2rem;
            border: 1px solid var(--gray-200);
        }

        .demo-accounts h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .demo-account {
            background: var(--white);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 0.75rem;
            border: 1px solid var(--gray-200);
            cursor: pointer;
            transition: var(--transition);
        }

        .demo-account:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .demo-account:last-child {
            margin-bottom: 0;
        }

        .role {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .credentials {
            font-size: 0.75rem;
            opacity: 0.8;
            font-family: monospace;
        }

        .login-links {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--gray-200);
        }

        .login-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .login-links a:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .login-links .separator {
            margin: 0 1rem;
            color: var(--gray-400);
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }

            .login-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-icon">
                <i class="fas fa-keyboard"></i>
            </div>
            <h1 class="login-title">تسجيل دخول مدخلي البيانات</h1>
            <p class="login-subtitle">نظام إدخال البيانات - مدخلي البيانات</p>
        </div>

        <?php if ($error_message): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label" for="username">اسم المستخدم</label>
                <div class="input-group">
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-input" 
                        required
                        placeholder="أدخل اسم المستخدم"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                    >
                    <i class="fas fa-user input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">كلمة المرور</label>
                <div class="input-group">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        required
                        placeholder="أدخل كلمة المرور"
                    >
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-sign-in-alt"></i>
                تسجيل الدخول
            </button>
        </form>

        <div class="demo-accounts">
            <h3>
                <i class="fas fa-info-circle"></i>
                حسابات تجريبية للاختبار
            </h3>
            <div class="demo-account" onclick="fillCredentials('data_entry_kamc_im_1', 'password')">
                <div class="role">مدخل بيانات أول - الطب الباطني KAMC</div>
                <div class="credentials">data_entry_kamc_im_1 / password</div>
            </div>
            <div class="demo-account" onclick="fillCredentials('data_entry_rh_er_1', 'password')">
                <div class="role">مدخل بيانات أول - الطوارئ RH</div>
                <div class="credentials">data_entry_rh_er_1 / password</div>
            </div>
            <div class="demo-account" onclick="fillCredentials('data_entry_kfh_ped_2', 'password')">
                <div class="role">مدخل بيانات ثاني - الأطفال KFH</div>
                <div class="credentials">data_entry_kfh_ped_2 / password</div>
            </div>
        </div>

        <div class="login-links">
            <a href="login.php">تسجيل دخول المديرين</a>
            <span class="separator">|</span>
            <a href="index.html">الصفحة الرئيسية</a>
        </div>
    </div>

    <script>
        function fillCredentials(username, password) {
            document.getElementById('username').value = username;
            document.getElementById('password').value = password;
        }

        // تأثيرات تفاعلية
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-input');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            // تأثير تحميل الصفحة
            const container = document.querySelector('.login-container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                container.style.transition = 'all 0.6s ease';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>

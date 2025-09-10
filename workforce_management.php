<?php
/**
 * إدارة القوى العاملة للمركز
 * Workforce Management for Center
 */

require_once 'config/config.php';
require_once 'config/database.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect('/login.php');
}

$current_user = getCurrentUser();

// التحقق من أن المستخدم مدير مركز
if ($current_user['role_id'] != ROLE_CENTER_MANAGER) {
    redirect('/dashboard.php');
}

$center_id = $current_user['center_id'];
$success_message = '';
$error_message = '';

// معالجة تحديث القوى العاملة
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    try {
        $db = new Database();
        
        if ($_POST['action'] == 'update_workforce') {
            $total_employees = (int)$_POST['total_employees'];
            $active_employees = (int)$_POST['active_employees'];
            $inactive_employees = (int)$_POST['inactive_employees'];
            $new_hires = (int)$_POST['new_hires_this_month'];
            $resignations = (int)$_POST['resignations_this_month'];
            
            // التحقق من صحة البيانات
            if ($total_employees < 0 || $active_employees < 0 || $inactive_employees < 0) {
                throw new Exception('يجب أن تكون الأرقام موجبة');
            }
            
            if ($active_employees + $inactive_employees != $total_employees) {
                throw new Exception('إجمالي الموظفين يجب أن يساوي النشطين + غير النشطين');
            }
            
            // تحديث أو إدراج بيانات القوى العاملة
            $stmt = $db->prepare("
                INSERT INTO center_workforce 
                (center_id, total_employees, active_employees, inactive_employees, new_hires_this_month, resignations_this_month, updated_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                total_employees = VALUES(total_employees),
                active_employees = VALUES(active_employees),
                inactive_employees = VALUES(inactive_employees),
                new_hires_this_month = VALUES(new_hires_this_month),
                resignations_this_month = VALUES(resignations_this_month),
                updated_by = VALUES(updated_by),
                last_updated = CURRENT_TIMESTAMP
            ");
            
            $stmt->execute([$center_id, $total_employees, $active_employees, $inactive_employees, $new_hires, $resignations, $current_user['id']]);
            
            $success_message = 'تم تحديث بيانات القوى العاملة بنجاح';
        }
        
    } catch (Exception $e) {
        $error_message = 'خطأ: ' . $e->getMessage();
    }
}

// الحصول على بيانات القوى العاملة الحالية
$workforce_data = null;
try {
    $db = new Database();
    $stmt = $db->prepare("SELECT * FROM center_workforce WHERE center_id = ?");
    $stmt->execute([$center_id]);
    $workforce_data = $stmt->fetch();
} catch (Exception $e) {
    error_log("Workforce data error: " . $e->getMessage());
}

// الحصول على معلومات المركز
$center_info = null;
try {
    $stmt = $db->prepare("SELECT c.*, h.name as hospital_name FROM centers c JOIN hospitals h ON c.hospital_id = h.id WHERE c.id = ?");
    $stmt->execute([$center_id]);
    $center_info = $stmt->fetch();
} catch (Exception $e) {
    error_log("Center info error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة القوى العاملة - <?php echo SITE_NAME; ?></title>
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
            color: var(--gray-800);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            color: var(--white);
            padding: 1.5rem 2rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .header-title {
            font-size: 1.875rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .header-subtitle {
            font-size: 1rem;
            opacity: 0.9;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: var(--white);
            padding: 0.75rem 1.25rem;
            border: none;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition);
            backdrop-filter: blur(10px);
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }

        .form-section {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            padding: 0.75rem 1rem;
            border: 1px solid var(--gray-200);
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn {
            background: var(--primary-color);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            justify-content: center;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-success {
            background: var(--success-color);
        }

        .btn-success:hover {
            background: #047857;
        }

        .stats-section {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
            border: 1px solid var(--gray-200);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--gray-600);
            font-weight: 600;
        }

        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .info-section {
            background: var(--info-color);
            color: var(--white);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-top: 1.5rem;
        }

        .info-title {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .info-list {
            list-style: none;
        }

        .info-list li {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">إدارة القوى العاملة</h1>
                    <p class="header-subtitle">
                        <?php if ($center_info): ?>
                            <?php echo htmlspecialchars($center_info['name']); ?> - <?php echo htmlspecialchars($center_info['hospital_name']); ?>
                        <?php endif; ?>
                    </p>
                </div>
                <a href="center_manager_dashboard.php" class="back-btn">
                    <i class="fas fa-arrow-right"></i>
                    العودة للداشبورد
                </a>
            </div>
        </div>

        <?php if ($success_message): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <div class="main-content">
            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-edit"></i>
                    تحديث بيانات القوى العاملة
                </h2>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_workforce">
                    
                    <div class="form-group">
                        <label class="form-label" for="total_employees">إجمالي الموظفين</label>
                        <input 
                            type="number" 
                            id="total_employees" 
                            name="total_employees" 
                            class="form-input" 
                            value="<?php echo $workforce_data['total_employees'] ?? 0; ?>"
                            min="0"
                            required
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="active_employees">الموظفين النشطين</label>
                            <input 
                                type="number" 
                                id="active_employees" 
                                name="active_employees" 
                                class="form-input" 
                                value="<?php echo $workforce_data['active_employees'] ?? 0; ?>"
                                min="0"
                                required
                            >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="inactive_employees">الموظفين غير النشطين</label>
                            <input 
                                type="number" 
                                id="inactive_employees" 
                                name="inactive_employees" 
                                class="form-input" 
                                value="<?php echo $workforce_data['inactive_employees'] ?? 0; ?>"
                                min="0"
                                required
                            >
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="new_hires_this_month">المعينين هذا الشهر</label>
                            <input 
                                type="number" 
                                id="new_hires_this_month" 
                                name="new_hires_this_month" 
                                class="form-input" 
                                value="<?php echo $workforce_data['new_hires_this_month'] ?? 0; ?>"
                                min="0"
                                required
                            >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="resignations_this_month">المستقيلين هذا الشهر</label>
                            <input 
                                type="number" 
                                id="resignations_this_month" 
                                name="resignations_this_month" 
                                class="form-input" 
                                value="<?php echo $workforce_data['resignations_this_month'] ?? 0; ?>"
                                min="0"
                                required
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        حفظ التحديثات
                    </button>
                </form>
            </div>

            <div class="stats-section">
                <h2 class="section-title">
                    <i class="fas fa-chart-bar"></i>
                    الإحصائيات الحالية
                </h2>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-value"><?php echo number_format($workforce_data['total_employees'] ?? 0); ?></div>
                        <div class="stat-label">إجمالي الموظفين</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo number_format($workforce_data['active_employees'] ?? 0); ?></div>
                        <div class="stat-label">النشطين</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo number_format($workforce_data['inactive_employees'] ?? 0); ?></div>
                        <div class="stat-label">غير النشطين</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo number_format($workforce_data['new_hires_this_month'] ?? 0); ?></div>
                        <div class="stat-label">جدد هذا الشهر</div>
                    </div>
                </div>

                <div class="info-section">
                    <h3 class="info-title">
                        <i class="fas fa-info-circle"></i>
                        نصائح مهمة
                    </h3>
                    <ul class="info-list">
                        <li>
                            <i class="fas fa-check"></i>
                            تأكد من أن إجمالي الموظفين = النشطين + غير النشطين
                        </li>
                        <li>
                            <i class="fas fa-check"></i>
                            قم بتحديث البيانات شهرياً للحصول على إحصائيات دقيقة
                        </li>
                        <li>
                            <i class="fas fa-check"></i>
                            البيانات المحدثة ستظهر في تقارير المشرف والسوبر يوزر
                        </li>
                        <li>
                            <i class="fas fa-check"></i>
                            يمكنك تعديل البيانات في أي وقت حسب التغييرات
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script>
        // التحقق من صحة البيانات
        document.addEventListener('DOMContentLoaded', function() {
            const totalEmployees = document.getElementById('total_employees');
            const activeEmployees = document.getElementById('active_employees');
            const inactiveEmployees = document.getElementById('inactive_employees');

            function validateTotals() {
                const total = parseInt(totalEmployees.value) || 0;
                const active = parseInt(activeEmployees.value) || 0;
                const inactive = parseInt(inactiveEmployees.value) || 0;

                if (active + inactive !== total) {
                    totalEmployees.style.borderColor = 'var(--danger-color)';
                    activeEmployees.style.borderColor = 'var(--danger-color)';
                    inactiveEmployees.style.borderColor = 'var(--danger-color)';
                } else {
                    totalEmployees.style.borderColor = 'var(--gray-200)';
                    activeEmployees.style.borderColor = 'var(--gray-200)';
                    inactiveEmployees.style.borderColor = 'var(--gray-200)';
                }
            }

            totalEmployees.addEventListener('input', validateTotals);
            activeEmployees.addEventListener('input', validateTotals);
            inactiveEmployees.addEventListener('input', validateTotals);

            // تحديث تلقائي للمجموع
            activeEmployees.addEventListener('input', function() {
                const total = parseInt(totalEmployees.value) || 0;
                const active = parseInt(this.value) || 0;
                inactiveEmployees.value = Math.max(0, total - active);
                validateTotals();
            });

            inactiveEmployees.addEventListener('input', function() {
                const total = parseInt(totalEmployees.value) || 0;
                const inactive = parseInt(this.value) || 0;
                activeEmployees.value = Math.max(0, total - inactive);
                validateTotals();
            });
        });
    </script>
</body>
</html>

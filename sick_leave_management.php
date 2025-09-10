<?php
/**
 * إدارة الغياب المرضي
 * Sick Leave Management
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

// معالجة إضافة غياب مرضي جديد
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    try {
        $db = new Database();
        
        if ($_POST['action'] == 'add_sick_leave') {
            $employee_name = trim($_POST['employee_name']);
            $employee_id = trim($_POST['employee_id']);
            $department = trim($_POST['department']);
            $position = trim($_POST['position']);
            $sick_leave_days = (int)$_POST['sick_leave_days'];
            $sick_leave_occurrences = (int)$_POST['sick_leave_occurrences'];
            $medical_certificate_required = isset($_POST['medical_certificate_required']) ? 1 : 0;
            $medical_certificate_provided = isset($_POST['medical_certificate_provided']) ? 1 : 0;
            $last_sick_leave_date = $_POST['last_sick_leave_date'];
            $total_sick_days_this_year = (int)$_POST['total_sick_days_this_year'];
            $notes = trim($_POST['notes']);
            
            // التحقق من صحة البيانات
            if (empty($employee_name) || $sick_leave_days < 0 || $sick_leave_occurrences < 0) {
                throw new Exception('يرجى ملء جميع الحقول المطلوبة بشكل صحيح');
            }
            
            // إدراج الغياب المرضي الجديد
            $stmt = $db->prepare("
                INSERT INTO sick_leave_records 
                (center_id, employee_name, employee_id, department, position, sick_leave_days, sick_leave_occurrences, medical_certificate_required, medical_certificate_provided, last_sick_leave_date, total_sick_days_this_year, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $center_id, $employee_name, $employee_id, $department, $position, 
                $sick_leave_days, $sick_leave_occurrences, $medical_certificate_required, 
                $medical_certificate_provided, $last_sick_leave_date, $total_sick_days_this_year, 
                $notes, $current_user['id']
            ]);
            
            $success_message = 'تم إضافة سجل الغياب المرضي بنجاح';
        }
        
    } catch (Exception $e) {
        $error_message = 'خطأ: ' . $e->getMessage();
    }
}

// الحصول على قائمة سجلات الغياب المرضي
$sick_leaves = [];
try {
    $db = new Database();
    $stmt = $db->prepare("
        SELECT * FROM sick_leave_records 
        WHERE center_id = ? 
        ORDER BY last_sick_leave_date DESC, created_at DESC 
        LIMIT 50
    ");
    $stmt->execute([$center_id]);
    $sick_leaves = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Sick leaves data error: " . $e->getMessage());
}

// الحصول على إحصائيات الغياب المرضي
$sick_stats = [
    'total_employees_with_sick_leave' => 0,
    'total_sick_days_this_month' => 0,
    'total_sick_days_this_year' => 0,
    'average_sick_days_per_employee' => 0,
    'employees_without_certificate' => 0
];

try {
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total_employees,
            SUM(sick_leave_days) as total_days,
            SUM(total_sick_days_this_year) as total_year_days,
            AVG(sick_leave_days) as avg_days,
            SUM(CASE WHEN medical_certificate_required = 1 AND medical_certificate_provided = 0 THEN 1 ELSE 0 END) as without_cert
        FROM sick_leave_records 
        WHERE center_id = ?
    ");
    $stmt->execute([$center_id]);
    $stats = $stmt->fetch();
    
    $sick_stats['total_employees_with_sick_leave'] = $stats['total_employees'] ?? 0;
    $sick_stats['total_sick_days_this_month'] = $stats['total_days'] ?? 0;
    $sick_stats['total_sick_days_this_year'] = $stats['total_year_days'] ?? 0;
    $sick_stats['average_sick_days_per_employee'] = round($stats['avg_days'] ?? 0, 1);
    $sick_stats['employees_without_certificate'] = $stats['without_cert'] ?? 0;
} catch (Exception $e) {
    error_log("Sick leave stats error: " . $e->getMessage());
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
    <title>إدارة الغياب المرضي - <?php echo SITE_NAME; ?></title>
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
            max-width: 1400px;
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

        .stats-section {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
            border: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
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

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
            background: var(--white);
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-color);
        }

        .checkbox-group label {
            font-size: 0.875rem;
            color: var(--gray-700);
            cursor: pointer;
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

        .sick-leaves-section {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
        }

        .sick-leaves-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .sick-leave-item {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            border: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .sick-leave-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .sick-leave-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .sick-leave-employee {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .sick-leave-days {
            background: var(--warning-color);
            color: var(--white);
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .sick-leave-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .sick-leave-detail {
            font-size: 0.875rem;
        }

        .sick-leave-detail strong {
            color: var(--gray-700);
        }

        .certificate-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .certificate-status.required {
            color: var(--warning-color);
        }

        .certificate-status.provided {
            color: var(--success-color);
        }

        .certificate-status.missing {
            color: var(--danger-color);
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

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .sick-leave-details {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">إدارة الغياب المرضي</h1>
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

        <div class="stats-section">
            <h2 class="section-title">
                <i class="fas fa-chart-bar"></i>
                إحصائيات الغياب المرضي
            </h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($sick_stats['total_employees_with_sick_leave']); ?></div>
                    <div class="stat-label">موظف لديه غياب مرضي</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($sick_stats['total_sick_days_this_month']); ?></div>
                    <div class="stat-label">يوم غياب هذا الشهر</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($sick_stats['total_sick_days_this_year']); ?></div>
                    <div class="stat-label">يوم غياب هذا العام</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $sick_stats['average_sick_days_per_employee']; ?></div>
                    <div class="stat-label">متوسط الأيام لكل موظف</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($sick_stats['employees_without_certificate']); ?></div>
                    <div class="stat-label">بدون شهادة طبية</div>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-plus"></i>
                    إضافة سجل غياب مرضي
                </h2>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_sick_leave">
                    
                    <div class="form-group">
                        <label class="form-label" for="employee_name">اسم الموظف *</label>
                        <input 
                            type="text" 
                            id="employee_name" 
                            name="employee_name" 
                            class="form-input" 
                            required
                            placeholder="اسم الموظف الكامل"
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="employee_id">رقم الموظف</label>
                            <input 
                                type="text" 
                                id="employee_id" 
                                name="employee_id" 
                                class="form-input" 
                                placeholder="رقم الموظف (اختياري)"
                            >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="department">القسم</label>
                            <input 
                                type="text" 
                                id="department" 
                                name="department" 
                                class="form-input" 
                                placeholder="القسم"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="position">المنصب</label>
                        <input 
                            type="text" 
                            id="position" 
                            name="position" 
                            class="form-input" 
                            placeholder="المنصب"
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="sick_leave_days">عدد أيام الغياب *</label>
                            <input 
                                type="number" 
                                id="sick_leave_days" 
                                name="sick_leave_days" 
                                class="form-input" 
                                required
                                min="0"
                                placeholder="عدد الأيام"
                            >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="sick_leave_occurrences">عدد مرات الغياب *</label>
                            <input 
                                type="number" 
                                id="sick_leave_occurrences" 
                                name="sick_leave_occurrences" 
                                class="form-input" 
                                required
                                min="0"
                                placeholder="عدد المرات"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="last_sick_leave_date">تاريخ آخر غياب مرضي</label>
                        <input 
                            type="date" 
                            id="last_sick_leave_date" 
                            name="last_sick_leave_date" 
                            class="form-input" 
                            value="<?php echo date('Y-m-d'); ?>"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="total_sick_days_this_year">إجمالي أيام الغياب هذا العام</label>
                        <input 
                            type="number" 
                            id="total_sick_days_this_year" 
                            name="total_sick_days_this_year" 
                            class="form-input" 
                            min="0"
                            placeholder="إجمالي الأيام"
                        >
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="medical_certificate_required" name="medical_certificate_required">
                        <label for="medical_certificate_required">يتطلب شهادة طبية</label>
                    </div>

                    <div class="checkbox-group">
                        <input type="checkbox" id="medical_certificate_provided" name="medical_certificate_provided">
                        <label for="medical_certificate_provided">تم تقديم الشهادة الطبية</label>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notes">ملاحظات</label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            class="form-textarea" 
                            placeholder="ملاحظات إضافية حول الغياب المرضي"
                        ></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        إضافة السجل
                    </button>
                </form>
            </div>

            <div class="sick-leaves-section">
                <h2 class="section-title">
                    <i class="fas fa-list"></i>
                    سجلات الغياب المرضي
                </h2>

                <div class="sick-leaves-list">
                    <?php if (empty($sick_leaves)): ?>
                        <div class="empty-state">
                            <i class="fas fa-thermometer-half"></i>
                            <h3>لا توجد سجلات غياب مرضي</h3>
                            <p>ابدأ بإضافة سجل جديد باستخدام النموذج</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($sick_leaves as $sick_leave): ?>
                            <div class="sick-leave-item">
                                <div class="sick-leave-header">
                                    <div class="sick-leave-employee"><?php echo htmlspecialchars($sick_leave['employee_name']); ?></div>
                                    <div class="sick-leave-days"><?php echo $sick_leave['sick_leave_days']; ?> يوم</div>
                                </div>
                                
                                <div class="sick-leave-details">
                                    <div class="sick-leave-detail">
                                        <strong>القسم:</strong><br>
                                        <?php echo htmlspecialchars($sick_leave['department'] ?: 'غير محدد'); ?>
                                    </div>
                                    <div class="sick-leave-detail">
                                        <strong>المنصب:</strong><br>
                                        <?php echo htmlspecialchars($sick_leave['position'] ?: 'غير محدد'); ?>
                                    </div>
                                    <div class="sick-leave-detail">
                                        <strong>عدد المرات:</strong><br>
                                        <?php echo $sick_leave['sick_leave_occurrences']; ?> مرة
                                    </div>
                                    <div class="sick-leave-detail">
                                        <strong>تاريخ آخر غياب:</strong><br>
                                        <?php echo date('Y-m-d', strtotime($sick_leave['last_sick_leave_date'])); ?>
                                    </div>
                                </div>

                                <div class="certificate-status <?php 
                                    if ($sick_leave['medical_certificate_required'] && !$sick_leave['medical_certificate_provided']) {
                                        echo 'missing';
                                    } elseif ($sick_leave['medical_certificate_provided']) {
                                        echo 'provided';
                                    } else {
                                        echo 'required';
                                    }
                                ?>">
                                    <i class="fas fa-<?php 
                                        if ($sick_leave['medical_certificate_required'] && !$sick_leave['medical_certificate_provided']) {
                                            echo 'exclamation-triangle';
                                        } elseif ($sick_leave['medical_certificate_provided']) {
                                            echo 'check-circle';
                                        } else {
                                            echo 'info-circle';
                                        }
                                    ?>"></i>
                                    <?php 
                                    if ($sick_leave['medical_certificate_required'] && !$sick_leave['medical_certificate_provided']) {
                                        echo 'شهادة طبية مطلوبة - غير مرفقة';
                                    } elseif ($sick_leave['medical_certificate_provided']) {
                                        echo 'شهادة طبية مرفقة';
                                    } else {
                                        echo 'لا يتطلب شهادة طبية';
                                    }
                                    ?>
                                </div>

                                <?php if ($sick_leave['notes']): ?>
                                    <div style="background: var(--white); padding: 1rem; border-radius: var(--border-radius); border: 1px solid var(--gray-200); margin-top: 1rem; font-size: 0.875rem; color: var(--gray-600);">
                                        <strong>ملاحظات:</strong> <?php echo htmlspecialchars($sick_leave['notes']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تأثير تحميل العناصر
            const items = document.querySelectorAll('.sick-leave-item');
            items.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    item.style.transition = 'all 0.6s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // تحديث تلقائي لإجمالي أيام الغياب
            const sickLeaveDays = document.getElementById('sick_leave_days');
            const totalSickDays = document.getElementById('total_sick_days_this_year');
            
            sickLeaveDays.addEventListener('input', function() {
                if (totalSickDays.value === '') {
                    totalSickDays.value = this.value;
                }
            });
        });
    </script>
</body>
</html>

<?php
/**
 * إدارة الإجازات
 * Leaves Management
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

// معالجة إضافة إجازة جديدة
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    try {
        $db = new Database();
        
        if ($_POST['action'] == 'add_leave') {
            $employee_name = trim($_POST['employee_name']);
            $employee_id = trim($_POST['employee_id']);
            $department = trim($_POST['department']);
            $position = trim($_POST['position']);
            $leave_type = $_POST['leave_type'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $total_days = (int)$_POST['total_days'];
            $remaining_leave_days = (int)$_POST['remaining_leave_days'];
            $reason = trim($_POST['reason']);
            $medical_certificate_required = isset($_POST['medical_certificate_required']) ? 1 : 0;
            $medical_certificate_provided = isset($_POST['medical_certificate_provided']) ? 1 : 0;
            $approval_required = isset($_POST['approval_required']) ? 1 : 0;
            $notes = trim($_POST['notes']);
            
            // التحقق من صحة البيانات
            if (empty($employee_name) || empty($start_date) || empty($end_date) || $total_days < 0) {
                throw new Exception('يرجى ملء جميع الحقول المطلوبة بشكل صحيح');
            }
            
            if (strtotime($end_date) < strtotime($start_date)) {
                throw new Exception('تاريخ انتهاء الإجازة يجب أن يكون بعد تاريخ البداية');
            }
            
            // إدراج الإجازة الجديدة
            $stmt = $db->prepare("
                INSERT INTO detailed_leaves 
                (center_id, employee_name, employee_id, department, position, leave_type, start_date, end_date, total_days, remaining_leave_days, reason, medical_certificate_required, medical_certificate_provided, approval_required, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $center_id, $employee_name, $employee_id, $department, $position, 
                $leave_type, $start_date, $end_date, $total_days, $remaining_leave_days, 
                $reason, $medical_certificate_required, $medical_certificate_provided, 
                $approval_required, $notes, $current_user['id']
            ]);
            
            $success_message = 'تم إضافة الإجازة بنجاح';
        }
        
    } catch (Exception $e) {
        $error_message = 'خطأ: ' . $e->getMessage();
    }
}

// الحصول على قائمة الإجازات
$leaves = [];
try {
    $db = new Database();
    $stmt = $db->prepare("
        SELECT * FROM detailed_leaves 
        WHERE center_id = ? 
        ORDER BY start_date DESC, created_at DESC 
        LIMIT 50
    ");
    $stmt->execute([$center_id]);
    $leaves = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Leaves data error: " . $e->getMessage());
}

// الحصول على إحصائيات الإجازات
$leaves_stats = [
    'total_leaves' => 0,
    'pending_leaves' => 0,
    'approved_leaves' => 0,
    'active_leaves' => 0,
    'exceptional_leaves' => 0,
    'maternity_leaves' => 0,
    'annual_leaves' => 0
];

try {
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'approved' AND CURDATE() BETWEEN start_date AND end_date THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN leave_type = 'exceptional' THEN 1 ELSE 0 END) as exceptional,
            SUM(CASE WHEN leave_type = 'maternity' THEN 1 ELSE 0 END) as maternity,
            SUM(CASE WHEN leave_type = 'annual' THEN 1 ELSE 0 END) as annual
        FROM detailed_leaves 
        WHERE center_id = ?
    ");
    $stmt->execute([$center_id]);
    $stats = $stmt->fetch();
    
    $leaves_stats['total_leaves'] = $stats['total'] ?? 0;
    $leaves_stats['pending_leaves'] = $stats['pending'] ?? 0;
    $leaves_stats['approved_leaves'] = $stats['approved'] ?? 0;
    $leaves_stats['active_leaves'] = $stats['active'] ?? 0;
    $leaves_stats['exceptional_leaves'] = $stats['exceptional'] ?? 0;
    $leaves_stats['maternity_leaves'] = $stats['maternity'] ?? 0;
    $leaves_stats['annual_leaves'] = $stats['annual'] ?? 0;
} catch (Exception $e) {
    error_log("Leaves stats error: " . $e->getMessage());
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
    <title>إدارة الإجازات - <?php echo SITE_NAME; ?></title>
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
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
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

        .leaves-section {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
        }

        .leaves-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .leave-item {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            border: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .leave-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .leave-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .leave-employee {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .leave-status {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-taken {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-cancelled {
            background: #f3f4f6;
            color: #6b7280;
        }

        .leave-type {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .type-exceptional {
            background: #fef3c7;
            color: #92400e;
        }

        .type-maternity {
            background: #fce7f3;
            color: #be185d;
        }

        .type-annual {
            background: #d1fae5;
            color: #065f46;
        }

        .type-sick {
            background: #fee2e2;
            color: #991b1b;
        }

        .type-emergency {
            background: #fef2f2;
            color: #dc2626;
        }

        .type-study {
            background: #e0e7ff;
            color: #3730a3;
        }

        .type-hajj {
            background: #f0fdf4;
            color: #166534;
        }

        .type-umrah {
            background: #f0f9ff;
            color: #0c4a6e;
        }

        .leave-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .leave-detail {
            font-size: 0.875rem;
        }

        .leave-detail strong {
            color: var(--gray-700);
        }

        .leave-dates {
            background: var(--white);
            padding: 1rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-top: 1rem;
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

            .leave-details {
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
                    <h1 class="header-title">إدارة الإجازات</h1>
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
                إحصائيات الإجازات
            </h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($leaves_stats['total_leaves']); ?></div>
                    <div class="stat-label">إجمالي الإجازات</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($leaves_stats['pending_leaves']); ?></div>
                    <div class="stat-label">في الانتظار</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($leaves_stats['approved_leaves']); ?></div>
                    <div class="stat-label">معتمدة</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($leaves_stats['active_leaves']); ?></div>
                    <div class="stat-label">نشطة حالياً</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($leaves_stats['exceptional_leaves']); ?></div>
                    <div class="stat-label">استثنائية</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($leaves_stats['maternity_leaves']); ?></div>
                    <div class="stat-label">أمومة</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo number_format($leaves_stats['annual_leaves']); ?></div>
                    <div class="stat-label">سنوية</div>
                </div>
            </div>
        </div>

        <div class="main-content">
            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-plus"></i>
                    إضافة إجازة جديدة
                </h2>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_leave">
                    
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

                    <div class="form-group">
                        <label class="form-label" for="leave_type">نوع الإجازة *</label>
                        <select id="leave_type" name="leave_type" class="form-select" required>
                            <option value="">اختر نوع الإجازة</option>
                            <option value="exceptional">استثنائية</option>
                            <option value="maternity">أمومة</option>
                            <option value="annual">سنوية</option>
                            <option value="sick">مرضية</option>
                            <option value="emergency">طارئة</option>
                            <option value="study">دراسية</option>
                            <option value="hajj">حج</option>
                            <option value="umrah">عمرة</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="start_date">تاريخ البداية *</label>
                            <input 
                                type="date" 
                                id="start_date" 
                                name="start_date" 
                                class="form-input" 
                                required
                            >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="end_date">تاريخ النهاية *</label>
                            <input 
                                type="date" 
                                id="end_date" 
                                name="end_date" 
                                class="form-input" 
                                required
                            >
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="total_days">إجمالي الأيام *</label>
                            <input 
                                type="number" 
                                id="total_days" 
                                name="total_days" 
                                class="form-input" 
                                required
                                min="0"
                                placeholder="عدد الأيام"
                            >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="remaining_leave_days">الأيام المتبقية</label>
                            <input 
                                type="number" 
                                id="remaining_leave_days" 
                                name="remaining_leave_days" 
                                class="form-input" 
                                min="0"
                                placeholder="الأيام المتبقية"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reason">سبب الإجازة</label>
                        <input 
                            type="text" 
                            id="reason" 
                            name="reason" 
                            class="form-input" 
                            placeholder="سبب الإجازة"
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

                    <div class="checkbox-group">
                        <input type="checkbox" id="approval_required" name="approval_required" checked>
                        <label for="approval_required">يتطلب موافقة</label>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notes">ملاحظات</label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            class="form-textarea" 
                            placeholder="ملاحظات إضافية حول الإجازة"
                        ></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        إضافة الإجازة
                    </button>
                </form>
            </div>

            <div class="leaves-section">
                <h2 class="section-title">
                    <i class="fas fa-list"></i>
                    سجلات الإجازات
                </h2>

                <div class="leaves-list">
                    <?php if (empty($leaves)): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt"></i>
                            <h3>لا توجد إجازات مسجلة</h3>
                            <p>ابدأ بإضافة إجازة جديدة باستخدام النموذج</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($leaves as $leave): ?>
                            <div class="leave-item">
                                <div class="leave-header">
                                    <div class="leave-employee"><?php echo htmlspecialchars($leave['employee_name']); ?></div>
                                    <div class="leave-status status-<?php echo $leave['status']; ?>">
                                        <?php
                                        switch ($leave['status']) {
                                            case 'pending': echo 'معلق'; break;
                                            case 'approved': echo 'معتمد'; break;
                                            case 'rejected': echo 'مرفوض'; break;
                                            case 'taken': echo 'مأخوذة'; break;
                                            case 'cancelled': echo 'ملغاة'; break;
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="leave-type type-<?php echo $leave['leave_type']; ?>">
                                    <?php
                                    switch ($leave['leave_type']) {
                                        case 'exceptional': echo 'استثنائية'; break;
                                        case 'maternity': echo 'أمومة'; break;
                                        case 'annual': echo 'سنوية'; break;
                                        case 'sick': echo 'مرضية'; break;
                                        case 'emergency': echo 'طارئة'; break;
                                        case 'study': echo 'دراسية'; break;
                                        case 'hajj': echo 'حج'; break;
                                        case 'umrah': echo 'عمرة'; break;
                                    }
                                    ?>
                                </div>
                                
                                <div class="leave-details">
                                    <div class="leave-detail">
                                        <strong>القسم:</strong><br>
                                        <?php echo htmlspecialchars($leave['department'] ?: 'غير محدد'); ?>
                                    </div>
                                    <div class="leave-detail">
                                        <strong>المنصب:</strong><br>
                                        <?php echo htmlspecialchars($leave['position'] ?: 'غير محدد'); ?>
                                    </div>
                                    <div class="leave-detail">
                                        <strong>إجمالي الأيام:</strong><br>
                                        <?php echo $leave['total_days']; ?> يوم
                                    </div>
                                    <div class="leave-detail">
                                        <strong>الأيام المتبقية:</strong><br>
                                        <?php echo $leave['remaining_leave_days']; ?> يوم
                                    </div>
                                </div>

                                <div class="leave-dates">
                                    <strong>فترة الإجازة:</strong> 
                                    من <?php echo date('Y-m-d', strtotime($leave['start_date'])); ?> 
                                    إلى <?php echo date('Y-m-d', strtotime($leave['end_date'])); ?>
                                    <?php if ($leave['reason']): ?>
                                        <br><strong>السبب:</strong> <?php echo htmlspecialchars($leave['reason']); ?>
                                    <?php endif; ?>
                                    <?php if ($leave['notes']): ?>
                                        <br><strong>ملاحظات:</strong> <?php echo htmlspecialchars($leave['notes']); ?>
                                    <?php endif; ?>
                                </div>
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
            const items = document.querySelectorAll('.leave-item');
            items.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    item.style.transition = 'all 0.6s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // حساب تلقائي للأيام
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            const totalDays = document.getElementById('total_days');

            function calculateDays() {
                if (startDate.value && endDate.value) {
                    const start = new Date(startDate.value);
                    const end = new Date(endDate.value);
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    totalDays.value = diffDays;
                }
            }

            startDate.addEventListener('change', calculateDays);
            endDate.addEventListener('change', calculateDays);
        });
    </script>
</body>
</html>

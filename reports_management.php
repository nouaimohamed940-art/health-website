<?php
/**
 * إدارة التقارير المركزية
 * Center Reports Management
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

// معالجة إنشاء تقرير جديد
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    try {
        $db = new Database();
        
        if ($_POST['action'] == 'create_report') {
            $report_type = $_POST['report_type'];
            $report_date = $_POST['report_date'];
            $report_title = trim($_POST['report_title']);
            $report_content = trim($_POST['report_content']);
            $achievements = trim($_POST['achievements']);
            $challenges = trim($_POST['challenges']);
            $recommendations = trim($_POST['recommendations']);
            
            // التحقق من صحة البيانات
            if (empty($report_title) || empty($report_content)) {
                throw new Exception('يرجى ملء العنوان والمحتوى');
            }
            
            // إعداد بيانات JSON للإحصائيات
            $workforce_summary = [
                'total_employees' => 0,
                'active_employees' => 0,
                'new_hires' => 0,
                'resignations' => 0
            ];
            
            $transfers_summary = [
                'total_transfers' => 0,
                'promotions' => 0,
                'assignments' => 0
            ];
            
            $sick_leave_summary = [
                'total_days' => 0,
                'employees_affected' => 0,
                'average_days' => 0
            ];
            
            $leaves_summary = [
                'exceptional' => 0,
                'maternity' => 0,
                'annual' => 0,
                'sick' => 0
            ];
            
            $delegations_summary = [
                'delegations' => 0,
                'scholarships' => 0,
                'trainings' => 0
            ];
            
            // جمع الإحصائيات الفعلية
            try {
                // إحصائيات القوى العاملة
                $stmt = $db->prepare("SELECT * FROM center_workforce WHERE center_id = ?");
                $stmt->execute([$center_id]);
                $workforce = $stmt->fetch();
                if ($workforce) {
                    $workforce_summary = [
                        'total_employees' => $workforce['total_employees'],
                        'active_employees' => $workforce['active_employees'],
                        'new_hires' => $workforce['new_hires_this_month'],
                        'resignations' => $workforce['resignations_this_month']
                    ];
                }
                
                // إحصائيات التنقلات
                $stmt = $db->prepare("
                    SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN transfer_type = 'promotion' THEN 1 ELSE 0 END) as promotions,
                        SUM(CASE WHEN transfer_type = 'assignment' THEN 1 ELSE 0 END) as assignments
                    FROM employee_transfers 
                    WHERE center_id = ? AND MONTH(created_at) = MONTH(?) AND YEAR(created_at) = YEAR(?)
                ");
                $stmt->execute([$center_id, $report_date, $report_date]);
                $transfers = $stmt->fetch();
                $transfers_summary = [
                    'total_transfers' => $transfers['total'] ?? 0,
                    'promotions' => $transfers['promotions'] ?? 0,
                    'assignments' => $transfers['assignments'] ?? 0
                ];
                
                // إحصائيات الغياب المرضي
                $stmt = $db->prepare("
                    SELECT 
                        SUM(sick_leave_days) as total_days,
                        COUNT(*) as employees_affected,
                        AVG(sick_leave_days) as avg_days
                    FROM sick_leave_records 
                    WHERE center_id = ? AND MONTH(last_sick_leave_date) = MONTH(?) AND YEAR(last_sick_leave_date) = YEAR(?)
                ");
                $stmt->execute([$center_id, $report_date, $report_date]);
                $sick = $stmt->fetch();
                $sick_leave_summary = [
                    'total_days' => $sick['total_days'] ?? 0,
                    'employees_affected' => $sick['employees_affected'] ?? 0,
                    'average_days' => round($sick['avg_days'] ?? 0, 1)
                ];
                
                // إحصائيات الإجازات
                $stmt = $db->prepare("
                    SELECT 
                        SUM(CASE WHEN leave_type = 'exceptional' THEN 1 ELSE 0 END) as exceptional,
                        SUM(CASE WHEN leave_type = 'maternity' THEN 1 ELSE 0 END) as maternity,
                        SUM(CASE WHEN leave_type = 'annual' THEN 1 ELSE 0 END) as annual,
                        SUM(CASE WHEN leave_type = 'sick' THEN 1 ELSE 0 END) as sick
                    FROM detailed_leaves 
                    WHERE center_id = ? AND MONTH(created_at) = MONTH(?) AND YEAR(created_at) = YEAR(?)
                ");
                $stmt->execute([$center_id, $report_date, $report_date]);
                $leaves = $stmt->fetch();
                $leaves_summary = [
                    'exceptional' => $leaves['exceptional'] ?? 0,
                    'maternity' => $leaves['maternity'] ?? 0,
                    'annual' => $leaves['annual'] ?? 0,
                    'sick' => $leaves['sick'] ?? 0
                ];
                
                // إحصائيات الإيفاد والابتعاث
                $stmt = $db->prepare("
                    SELECT 
                        SUM(CASE WHEN type = 'delegation' THEN 1 ELSE 0 END) as delegations,
                        SUM(CASE WHEN type = 'scholarship' THEN 1 ELSE 0 END) as scholarships,
                        SUM(CASE WHEN type = 'training' THEN 1 ELSE 0 END) as trainings
                    FROM delegations_scholarships 
                    WHERE center_id = ? AND MONTH(created_at) = MONTH(?) AND YEAR(created_at) = YEAR(?)
                ");
                $stmt->execute([$center_id, $report_date, $report_date]);
                $delegations = $stmt->fetch();
                $delegations_summary = [
                    'delegations' => $delegations['delegations'] ?? 0,
                    'scholarships' => $delegations['scholarships'] ?? 0,
                    'trainings' => $delegations['trainings'] ?? 0
                ];
                
            } catch (Exception $e) {
                error_log("Stats collection error: " . $e->getMessage());
            }
            
            // إدراج التقرير الجديد
            $stmt = $db->prepare("
                INSERT INTO center_reports 
                (center_id, report_type, report_date, report_title, report_content, workforce_summary, transfers_summary, sick_leave_summary, leaves_summary, delegations_summary, achievements, challenges, recommendations, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $center_id, $report_type, $report_date, $report_title, $report_content,
                json_encode($workforce_summary), json_encode($transfers_summary), 
                json_encode($sick_leave_summary), json_encode($leaves_summary), 
                json_encode($delegations_summary), $achievements, $challenges, 
                $recommendations, $current_user['id']
            ]);
            
            $success_message = 'تم إنشاء التقرير بنجاح';
        }
        
    } catch (Exception $e) {
        $error_message = 'خطأ: ' . $e->getMessage();
    }
}

// الحصول على قائمة التقارير
$reports = [];
try {
    $db = new Database();
    $stmt = $db->prepare("
        SELECT * FROM center_reports 
        WHERE center_id = ? 
        ORDER BY report_date DESC, created_at DESC 
        LIMIT 20
    ");
    $stmt->execute([$center_id]);
    $reports = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Reports data error: " . $e->getMessage());
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
    <title>إدارة التقارير المركزية - <?php echo SITE_NAME; ?></title>
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
            min-height: 120px;
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

        .reports-section {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
        }

        .reports-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .report-item {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            border: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .report-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .report-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .report-status {
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-draft {
            background: #f3f4f6;
            color: #6b7280;
        }

        .status-submitted {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-reviewed {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .report-type {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .type-daily {
            background: #dbeafe;
            color: #1e40af;
        }

        .type-weekly {
            background: #fef3c7;
            color: #92400e;
        }

        .type-monthly {
            background: #d1fae5;
            color: #065f46;
        }

        .report-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .report-detail {
            font-size: 0.875rem;
        }

        .report-detail strong {
            color: var(--gray-700);
        }

        .report-content {
            background: var(--white);
            padding: 1rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-top: 1rem;
            max-height: 100px;
            overflow-y: auto;
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

        .stats-preview {
            background: var(--info-color);
            color: var(--white);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-top: 1rem;
        }

        .stats-preview h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            font-size: 0.875rem;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .report-details {
                grid-template-columns: 1fr;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">إدارة التقارير المركزية</h1>
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
                    <i class="fas fa-plus"></i>
                    إنشاء تقرير جديد
                </h2>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="create_report">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="report_type">نوع التقرير *</label>
                            <select id="report_type" name="report_type" class="form-select" required>
                                <option value="">اختر نوع التقرير</option>
                                <option value="daily">يومي</option>
                                <option value="weekly">أسبوعي</option>
                                <option value="monthly">شهري</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="report_date">تاريخ التقرير *</label>
                            <input 
                                type="date" 
                                id="report_date" 
                                name="report_date" 
                                class="form-input" 
                                required
                                value="<?php echo date('Y-m-d'); ?>"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="report_title">عنوان التقرير *</label>
                        <input 
                            type="text" 
                            id="report_title" 
                            name="report_title" 
                            class="form-input" 
                            required
                            placeholder="عنوان التقرير"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="report_content">محتوى التقرير *</label>
                        <textarea 
                            id="report_content" 
                            name="report_content" 
                            class="form-textarea" 
                            required
                            placeholder="اكتب محتوى التقرير هنا..."
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="achievements">الإنجازات</label>
                        <textarea 
                            id="achievements" 
                            name="achievements" 
                            class="form-textarea" 
                            placeholder="اذكر الإنجازات المحققة..."
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="challenges">التحديات</label>
                        <textarea 
                            id="challenges" 
                            name="challenges" 
                            class="form-textarea" 
                            placeholder="اذكر التحديات التي واجهتها..."
                        ></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="recommendations">التوصيات</label>
                        <textarea 
                            id="recommendations" 
                            name="recommendations" 
                            class="form-textarea" 
                            placeholder="اذكر توصياتك للتحسين..."
                        ></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        إنشاء التقرير
                    </button>
                </form>

                <div class="stats-preview">
                    <h4>
                        <i class="fas fa-chart-bar"></i>
                        معاينة الإحصائيات
                    </h4>
                    <p>سيتم جمع الإحصائيات تلقائياً من البيانات المسجلة في النظام</p>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <span>القوى العاملة</span>
                            <span>سيتم تحديثها تلقائياً</span>
                        </div>
                        <div class="stat-item">
                            <span>التنقلات</span>
                            <span>سيتم تحديثها تلقائياً</span>
                        </div>
                        <div class="stat-item">
                            <span>الغياب المرضي</span>
                            <span>سيتم تحديثها تلقائياً</span>
                        </div>
                        <div class="stat-item">
                            <span>الإجازات</span>
                            <span>سيتم تحديثها تلقائياً</span>
                        </div>
                        <div class="stat-item">
                            <span>الإيفاد والابتعاث</span>
                            <span>سيتم تحديثها تلقائياً</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="reports-section">
                <h2 class="section-title">
                    <i class="fas fa-list"></i>
                    التقارير المنشأة
                </h2>

                <div class="reports-list">
                    <?php if (empty($reports)): ?>
                        <div class="empty-state">
                            <i class="fas fa-file-alt"></i>
                            <h3>لا توجد تقارير منشأة</h3>
                            <p>ابدأ بإنشاء تقرير جديد باستخدام النموذج</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($reports as $report): ?>
                            <div class="report-item">
                                <div class="report-header">
                                    <div class="report-title"><?php echo htmlspecialchars($report['report_title']); ?></div>
                                    <div class="report-status status-<?php echo $report['status']; ?>">
                                        <?php
                                        switch ($report['status']) {
                                            case 'draft': echo 'مسودة'; break;
                                            case 'submitted': echo 'مرسل'; break;
                                            case 'reviewed': echo 'مراجع'; break;
                                            case 'approved': echo 'معتمد'; break;
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="report-type type-<?php echo $report['report_type']; ?>">
                                    <?php
                                    switch ($report['report_type']) {
                                        case 'daily': echo 'تقرير يومي'; break;
                                        case 'weekly': echo 'تقرير أسبوعي'; break;
                                        case 'monthly': echo 'تقرير شهري'; break;
                                    }
                                    ?>
                                </div>
                                
                                <div class="report-details">
                                    <div class="report-detail">
                                        <strong>تاريخ التقرير:</strong><br>
                                        <?php echo date('Y-m-d', strtotime($report['report_date'])); ?>
                                    </div>
                                    <div class="report-detail">
                                        <strong>تاريخ الإنشاء:</strong><br>
                                        <?php echo date('Y-m-d H:i', strtotime($report['created_at'])); ?>
                                    </div>
                                </div>

                                <div class="report-content">
                                    <strong>المحتوى:</strong><br>
                                    <?php echo htmlspecialchars(substr($report['report_content'], 0, 200)); ?>
                                    <?php if (strlen($report['report_content']) > 200): ?>
                                        ...
                                    <?php endif; ?>
                                </div>

                                <?php if ($report['achievements'] || $report['challenges'] || $report['recommendations']): ?>
                                    <div style="margin-top: 1rem; font-size: 0.875rem;">
                                        <?php if ($report['achievements']): ?>
                                            <div style="margin-bottom: 0.5rem;">
                                                <strong>الإنجازات:</strong> <?php echo htmlspecialchars(substr($report['achievements'], 0, 100)); ?>
                                                <?php if (strlen($report['achievements']) > 100): ?>...<?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($report['challenges']): ?>
                                            <div style="margin-bottom: 0.5rem;">
                                                <strong>التحديات:</strong> <?php echo htmlspecialchars(substr($report['challenges'], 0, 100)); ?>
                                                <?php if (strlen($report['challenges']) > 100): ?>...<?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($report['recommendations']): ?>
                                            <div>
                                                <strong>التوصيات:</strong> <?php echo htmlspecialchars(substr($report['recommendations'], 0, 100)); ?>
                                                <?php if (strlen($report['recommendations']) > 100): ?>...<?php endif; ?>
                                            </div>
                                        <?php endif; ?>
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
            const items = document.querySelectorAll('.report-item');
            items.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    item.style.transition = 'all 0.6s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // تحديث تلقائي للعنوان حسب نوع التقرير والتاريخ
            const reportType = document.getElementById('report_type');
            const reportDate = document.getElementById('report_date');
            const reportTitle = document.getElementById('report_title');

            function updateTitle() {
                if (reportType.value && reportDate.value) {
                    const date = new Date(reportDate.value);
                    const dateStr = date.toLocaleDateString('ar-SA');
                    
                    let typeStr = '';
                    switch (reportType.value) {
                        case 'daily': typeStr = 'يومي'; break;
                        case 'weekly': typeStr = 'أسبوعي'; break;
                        case 'monthly': typeStr = 'شهري'; break;
                    }
                    
                    reportTitle.value = `تقرير ${typeStr} - ${dateStr}`;
                }
            }

            reportType.addEventListener('change', updateTitle);
            reportDate.addEventListener('change', updateTitle);
        });
    </script>
</body>
</html>

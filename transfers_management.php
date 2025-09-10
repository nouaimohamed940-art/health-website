<?php
/**
 * إدارة التنقلات والتكليفات
 * Transfers and Assignments Management
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

// معالجة إضافة تنقل جديد
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    try {
        $db = new Database();
        
        if ($_POST['action'] == 'add_transfer') {
            $employee_name = trim($_POST['employee_name']);
            $employee_id = trim($_POST['employee_id']);
            $current_position = trim($_POST['current_position']);
            $new_position = trim($_POST['new_position']);
            $transfer_type = $_POST['transfer_type'];
            $transfer_date = $_POST['transfer_date'];
            $reason = trim($_POST['reason']);
            $from_department = trim($_POST['from_department']);
            $to_department = trim($_POST['to_department']);
            $notes = trim($_POST['notes']);
            
            // التحقق من صحة البيانات
            if (empty($employee_name) || empty($current_position) || empty($new_position) || empty($transfer_date)) {
                throw new Exception('يرجى ملء جميع الحقول المطلوبة');
            }
            
            // إدراج التنقل الجديد
            $stmt = $db->prepare("
                INSERT INTO employee_transfers 
                (center_id, employee_name, employee_id, current_position, new_position, transfer_type, transfer_date, reason, from_department, to_department, notes, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $center_id, $employee_name, $employee_id, $current_position, $new_position, 
                $transfer_type, $transfer_date, $reason, $from_department, $to_department, 
                $notes, $current_user['id']
            ]);
            
            $success_message = 'تم إضافة التنقل بنجاح';
        }
        
    } catch (Exception $e) {
        $error_message = 'خطأ: ' . $e->getMessage();
    }
}

// الحصول على قائمة التنقلات
$transfers = [];
try {
    $db = new Database();
    $stmt = $db->prepare("
        SELECT * FROM employee_transfers 
        WHERE center_id = ? 
        ORDER BY created_at DESC 
        LIMIT 50
    ");
    $stmt->execute([$center_id]);
    $transfers = $stmt->fetchAll();
} catch (Exception $e) {
    error_log("Transfers data error: " . $e->getMessage());
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
    <title>إدارة التنقلات والتكليفات - <?php echo SITE_NAME; ?></title>
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
            min-height: 80px;
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

        .transfers-section {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
        }

        .transfers-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .transfer-item {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            border: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .transfer-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .transfer-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .transfer-employee {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .transfer-status {
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

        .transfer-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .transfer-detail {
            font-size: 0.875rem;
        }

        .transfer-detail strong {
            color: var(--gray-700);
        }

        .transfer-notes {
            background: var(--white);
            padding: 1rem;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            font-size: 0.875rem;
            color: var(--gray-600);
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

            .transfer-details {
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
                    <h1 class="header-title">إدارة التنقلات والتكليفات</h1>
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
                    إضافة تنقل جديد
                </h2>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="add_transfer">
                    
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

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="current_position">المنصب الحالي *</label>
                            <input 
                                type="text" 
                                id="current_position" 
                                name="current_position" 
                                class="form-input" 
                                required
                                placeholder="المنصب الحالي"
                            >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="new_position">المنصب الجديد *</label>
                            <input 
                                type="text" 
                                id="new_position" 
                                name="new_position" 
                                class="form-input" 
                                required
                                placeholder="المنصب الجديد"
                            >
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="transfer_type">نوع التنقل *</label>
                            <select id="transfer_type" name="transfer_type" class="form-select" required>
                                <option value="">اختر نوع التنقل</option>
                                <option value="transfer">تنقل</option>
                                <option value="assignment">تكليف</option>
                                <option value="promotion">ترقية</option>
                                <option value="demotion">تنزيل</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="transfer_date">تاريخ التنقل *</label>
                            <input 
                                type="date" 
                                id="transfer_date" 
                                name="transfer_date" 
                                class="form-input" 
                                required
                                value="<?php echo date('Y-m-d'); ?>"
                            >
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="from_department">القسم الحالي</label>
                            <input 
                                type="text" 
                                id="from_department" 
                                name="from_department" 
                                class="form-input" 
                                placeholder="القسم الحالي"
                            >
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="to_department">القسم الجديد</label>
                            <input 
                                type="text" 
                                id="to_department" 
                                name="to_department" 
                                class="form-input" 
                                placeholder="القسم الجديد"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="reason">سبب التنقل</label>
                        <input 
                            type="text" 
                            id="reason" 
                            name="reason" 
                            class="form-input" 
                            placeholder="سبب التنقل أو التكليف"
                        >
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notes">ملاحظات إضافية</label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            class="form-textarea" 
                            placeholder="ملاحظات إضافية حول التنقل"
                        ></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i>
                        إضافة التنقل
                    </button>
                </form>
            </div>

            <div class="transfers-section">
                <h2 class="section-title">
                    <i class="fas fa-list"></i>
                    التنقلات المسجلة
                </h2>

                <div class="transfers-list">
                    <?php if (empty($transfers)): ?>
                        <div class="empty-state">
                            <i class="fas fa-exchange-alt"></i>
                            <h3>لا توجد تنقلات مسجلة</h3>
                            <p>ابدأ بإضافة تنقل جديد باستخدام النموذج</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($transfers as $transfer): ?>
                            <div class="transfer-item">
                                <div class="transfer-header">
                                    <div class="transfer-employee"><?php echo htmlspecialchars($transfer['employee_name']); ?></div>
                                    <div class="transfer-status status-<?php echo $transfer['status']; ?>">
                                        <?php
                                        switch ($transfer['status']) {
                                            case 'pending': echo 'معلق'; break;
                                            case 'approved': echo 'معتمد'; break;
                                            case 'rejected': echo 'مرفوض'; break;
                                        }
                                        ?>
                                    </div>
                                </div>
                                
                                <div class="transfer-details">
                                    <div class="transfer-detail">
                                        <strong>المنصب الحالي:</strong><br>
                                        <?php echo htmlspecialchars($transfer['current_position']); ?>
                                    </div>
                                    <div class="transfer-detail">
                                        <strong>المنصب الجديد:</strong><br>
                                        <?php echo htmlspecialchars($transfer['new_position']); ?>
                                    </div>
                                    <div class="transfer-detail">
                                        <strong>نوع التنقل:</strong><br>
                                        <?php
                                        switch ($transfer['transfer_type']) {
                                            case 'transfer': echo 'تنقل'; break;
                                            case 'assignment': echo 'تكليف'; break;
                                            case 'promotion': echo 'ترقية'; break;
                                            case 'demotion': echo 'تنزيل'; break;
                                        }
                                        ?>
                                    </div>
                                    <div class="transfer-detail">
                                        <strong>تاريخ التنقل:</strong><br>
                                        <?php echo date('Y-m-d', strtotime($transfer['transfer_date'])); ?>
                                    </div>
                                </div>

                                <?php if ($transfer['reason'] || $transfer['notes']): ?>
                                    <div class="transfer-notes">
                                        <?php if ($transfer['reason']): ?>
                                            <strong>السبب:</strong> <?php echo htmlspecialchars($transfer['reason']); ?><br>
                                        <?php endif; ?>
                                        <?php if ($transfer['notes']): ?>
                                            <strong>ملاحظات:</strong> <?php echo htmlspecialchars($transfer['notes']); ?>
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
            const items = document.querySelectorAll('.transfer-item');
            items.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    item.style.transition = 'all 0.6s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>

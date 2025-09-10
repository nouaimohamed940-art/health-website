<?php
/**
 * صفحة إدخال بيانات القوى العاملة - مدخلي البيانات
 * Data Entry Workforce Page
 */

require_once 'config/config.php';
require_once 'config/database.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn() || !isset($_SESSION['data_entry_user'])) {
    redirect('/data_entry_login.php');
}

$current_user = $_SESSION['data_entry_user'];
$center_id = $current_user['center_id'];
$success_message = '';
$error_message = '';

// معالجة إرسال البيانات
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $total_employees = (int)($_POST['total_employees'] ?? 0);
    $active_employees = (int)($_POST['active_employees'] ?? 0);
    $inactive_employees = (int)($_POST['inactive_employees'] ?? 0);
    $new_hires_this_month = (int)($_POST['new_hires_this_month'] ?? 0);
    $resignations_this_month = (int)($_POST['resignations_this_month'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    
    if ($total_employees > 0) {
        try {
            $db = new Database();
            
            // إنشاء طلب إدخال البيانات
            $data_json = json_encode([
                'total_employees' => $total_employees,
                'active_employees' => $active_employees,
                'inactive_employees' => $inactive_employees,
                'new_hires_this_month' => $new_hires_this_month,
                'resignations_this_month' => $resignations_this_month,
                'notes' => $notes,
                'submitted_at' => date('Y-m-d H:i:s')
            ]);
            
            $stmt = $db->prepare("
                INSERT INTO data_entry_requests 
                (center_id, data_entry_user_id, request_type, table_name, data_json) 
                VALUES (?, ?, 'workforce', 'center_workforce', ?)
            ");
            $stmt->execute([$center_id, $current_user['id'], $data_json]);
            
            // تسجيل النشاط
            $stmt = $db->prepare("
                INSERT INTO data_entry_activity_log 
                (data_entry_user_id, center_id, activity_type, description, ip_address, user_agent) 
                VALUES (?, ?, 'data_entry', 'تم إرسال طلب إدخال بيانات القوى العاملة', ?, ?)
            ");
            $stmt->execute([
                $current_user['id'], 
                $center_id, 
                $_SERVER['REMOTE_ADDR'] ?? '', 
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            $success_message = 'تم إرسال طلب إدخال بيانات القوى العاملة بنجاح. سيتم مراجعته من قبل مدير المركز.';
            
        } catch (Exception $e) {
            error_log("Workforce data entry error: " . $e->getMessage());
            $error_message = 'حدث خطأ في إرسال البيانات، يرجى المحاولة لاحقاً';
        }
    } else {
        $error_message = 'يرجى إدخال عدد الموظفين الإجمالي';
    }
}

// الحصول على آخر البيانات المدخلة
try {
    $db = new Database();
    $stmt = $db->prepare("
        SELECT * FROM data_entry_requests 
        WHERE data_entry_user_id = ? AND request_type = 'workforce' 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $stmt->execute([$current_user['id']]);
    $recent_entries = $stmt->fetchAll();
} catch (Exception $e) {
    $recent_entries = [];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدخال بيانات القوى العاملة - <?php echo SITE_NAME; ?></title>
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
            --light-bg: #f8fafc;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
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
            background: var(--light-bg);
            color: var(--gray-800);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .header-subtitle {
            opacity: 0.9;
            font-size: 1.125rem;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: var(--white);
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
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
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-input {
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

        .form-textarea {
            padding: 0.875rem 1rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius);
            font-size: 1rem;
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            background: var(--primary-color);
            color: var(--white);
            padding: 0.875rem 2rem;
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
            width: 100%;
        }

        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn:active {
            transform: translateY(0);
        }

        .recent-section {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
            height: fit-content;
        }

        .recent-item {
            background: var(--gray-50);
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 0.75rem;
            border: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .recent-item:hover {
            background: var(--white);
            box-shadow: var(--shadow);
        }

        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .recent-date {
            color: var(--gray-500);
            font-size: 0.875rem;
        }

        .recent-status {
            padding: 0.25rem 0.75rem;
            border-radius: var(--border-radius);
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

        .recent-data {
            font-size: 0.875rem;
            color: var(--gray-600);
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

        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 2rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .form-grid {
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
                    <h1 class="header-title">إدخال بيانات القوى العاملة</h1>
                    <p class="header-subtitle">تسجيل إحصائيات الموظفين في المركز</p>
                </div>
                <a href="data_entry_dashboard.php" class="back-btn">
                    <i class="fas fa-arrow-right"></i>
                    العودة للوحة التحكم
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

        <div class="content-grid">
            <div class="form-section">
                <h2 class="section-title">
                    <i class="fas fa-users"></i>
                    بيانات القوى العاملة
                </h2>

                <div class="info-box">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>ملاحظة:</strong> سيتم إرسال هذه البيانات لمدير المركز للمراجعة والموافقة قبل إضافتها للنظام.
                    </div>
                </div>

                <form method="POST" action="">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="total_employees">إجمالي الموظفين *</label>
                            <input 
                                type="number" 
                                id="total_employees" 
                                name="total_employees" 
                                class="form-input" 
                                required
                                min="0"
                                placeholder="أدخل العدد الإجمالي للموظفين"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="active_employees">الموظفين النشطين</label>
                            <input 
                                type="number" 
                                id="active_employees" 
                                name="active_employees" 
                                class="form-input" 
                                min="0"
                                placeholder="عدد الموظفين النشطين"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="inactive_employees">الموظفين غير النشطين</label>
                            <input 
                                type="number" 
                                id="inactive_employees" 
                                name="inactive_employees" 
                                class="form-input" 
                                min="0"
                                placeholder="عدد الموظفين غير النشطين"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="new_hires_this_month">المعينين هذا الشهر</label>
                            <input 
                                type="number" 
                                id="new_hires_this_month" 
                                name="new_hires_this_month" 
                                class="form-input" 
                                min="0"
                                placeholder="عدد المعينين الجدد"
                            >
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="resignations_this_month">المستقيلين هذا الشهر</label>
                            <input 
                                type="number" 
                                id="resignations_this_month" 
                                name="resignations_this_month" 
                                class="form-input" 
                                min="0"
                                placeholder="عدد المستقيلين"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="notes">ملاحظات إضافية</label>
                        <textarea 
                            id="notes" 
                            name="notes" 
                            class="form-textarea" 
                            placeholder="أدخل أي ملاحظات إضافية حول بيانات القوى العاملة..."
                        ></textarea>
                    </div>

                    <button type="submit" class="btn">
                        <i class="fas fa-paper-plane"></i>
                        إرسال البيانات للمراجعة
                    </button>
                </form>
            </div>

            <div class="recent-section">
                <h2 class="section-title">
                    <i class="fas fa-history"></i>
                    آخر الإدخالات
                </h2>

                <?php if (empty($recent_entries)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>لا توجد إدخالات سابقة</p>
                </div>
                <?php else: ?>
                <?php foreach ($recent_entries as $entry): ?>
                <div class="recent-item">
                    <div class="recent-header">
                        <div class="recent-date">
                            <i class="fas fa-clock"></i>
                            <?php echo date('Y-m-d H:i', strtotime($entry['created_at'])); ?>
                        </div>
                        <span class="recent-status status-<?php echo $entry['status']; ?>">
                            <?php
                            $status_names = [
                                'pending' => 'في الانتظار',
                                'approved' => 'تمت الموافقة',
                                'rejected' => 'تم الرفض'
                            ];
                            echo $status_names[$entry['status']] ?? $entry['status'];
                            ?>
                        </span>
                    </div>
                    <div class="recent-data">
                        <?php
                        $data = json_decode($entry['data_json'], true);
                        echo "إجمالي الموظفين: " . ($data['total_employees'] ?? 'غير محدد');
                        if (isset($data['active_employees'])) {
                            echo " | النشطين: " . $data['active_employees'];
                        }
                        ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // التحقق من صحة البيانات
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const totalEmployees = document.getElementById('total_employees');
            const activeEmployees = document.getElementById('active_employees');
            const inactiveEmployees = document.getElementById('inactive_employees');

            // التحقق من أن الموظفين النشطين + غير النشطين = الإجمالي
            function validateNumbers() {
                const total = parseInt(totalEmployees.value) || 0;
                const active = parseInt(activeEmployees.value) || 0;
                const inactive = parseInt(inactiveEmployees.value) || 0;

                if (total > 0 && active + inactive > total) {
                    alert('عدد الموظفين النشطين + غير النشطين لا يمكن أن يكون أكبر من الإجمالي');
                    return false;
                }
                return true;
            }

            form.addEventListener('submit', function(e) {
                if (!validateNumbers()) {
                    e.preventDefault();
                }
            });

            // تحديث القيم تلقائياً
            totalEmployees.addEventListener('input', function() {
                const total = parseInt(this.value) || 0;
                const active = parseInt(activeEmployees.value) || 0;
                const inactive = parseInt(inactiveEmployees.value) || 0;

                if (active + inactive > total && total > 0) {
                    activeEmployees.value = '';
                    inactiveEmployees.value = '';
                }
            });

            // تأثيرات تفاعلية
            const inputs = document.querySelectorAll('.form-input, .form-textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>

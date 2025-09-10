<?php
/**
 * لوحة تحكم مدخل البيانات
 * Data Entry Dashboard
 */

require_once 'config/config.php';
require_once 'config/database.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect('/login.php');
}

$current_user = getCurrentUser();

// التحقق من أن المستخدم مدخل بيانات
if ($current_user['role_id'] != ROLE_DATA_ENTRY) {
    redirect('/dashboard.php');
}

// الحصول على إحصائيات المركز
$stats = getUserStatistics();

try {
    $db = new Database();
    
    // إحصائيات إضافية لمدخل البيانات
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM employees WHERE center_id = ? AND status = 'active'");
    $stmt->execute([$current_user['center_id']]);
    $stats['total_employees'] = $stmt->fetch()['count'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM movements m JOIN employees e ON m.employee_id = e.id WHERE e.center_id = ? AND m.created_by = ? AND m.status = 'pending'");
    $stmt->execute([$current_user['center_id'], $current_user['id']]);
    $stats['my_pending_entries'] = $stmt->fetch()['count'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM movements m JOIN employees e ON m.employee_id = e.id WHERE e.center_id = ? AND m.created_by = ? AND m.status = 'approved'");
    $stmt->execute([$current_user['center_id'], $current_user['id']]);
    $stats['my_approved_entries'] = $stmt->fetch()['count'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM attendance a JOIN employees e ON a.employee_id = e.id WHERE e.center_id = ? AND a.created_by = ? AND a.attendance_date = CURDATE()");
    $stmt->execute([$current_user['center_id'], $current_user['id']]);
    $stats['today_entries'] = $stmt->fetch()['count'];
    
    // الحصول على معلومات المركز
    $stmt = $db->prepare("SELECT c.*, h.name as hospital_name FROM centers c JOIN hospitals h ON c.hospital_id = h.id WHERE c.id = ?");
    $stmt->execute([$current_user['center_id']]);
    $center_info = $stmt->fetch();
    
    // الحصول على الموظفين في المركز
    $stmt = $db->prepare("SELECT * FROM employees WHERE center_id = ? AND status = 'active' ORDER BY full_name");
    $stmt->execute([$current_user['center_id']]);
    $employees = $stmt->fetchAll();
    
    // الحصول على آخر الإدخالات
    $stmt = $db->prepare("
        SELECT m.*, e.full_name as employee_name, e.job_title, mt.name as movement_type_name
        FROM movements m 
        JOIN employees e ON m.employee_id = e.id 
        JOIN movement_types mt ON m.movement_type_id = mt.id
        WHERE e.center_id = ? AND m.created_by = ?
        ORDER BY m.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$current_user['center_id'], $current_user['id']]);
    $my_entries = $stmt->fetchAll();
    
} catch (Exception $e) {
    logError("Data Entry Dashboard error: " . $e->getMessage());
    $stats = [];
    $center_info = null;
    $employees = [];
    $my_entries = [];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم مدخل البيانات - <?php echo $center_info['name'] ?? 'غير محدد'; ?></title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <h1><i class="fas fa-hospital"></i> نظام إدارة القوى العاملة الصحية</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span class="user-name"><?php echo $current_user['full_name']; ?></span>
                        <span class="user-role"><?php echo $current_user['role_display_name']; ?></span>
                    </div>
                    <div class="user-actions">
                        <a href="logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- User Info Cards -->
            <div class="user-info-cards">
                <div class="info-card">
                    <i class="fas fa-user-edit"></i>
                    <div class="info-content">
                        <h3>الدور</h3>
                        <p><?php echo $current_user['role_display_name']; ?></p>
                    </div>
                </div>
                <div class="info-card">
                    <i class="fas fa-building"></i>
                    <div class="info-content">
                        <h3>المستشفى</h3>
                        <p><?php echo $current_user['hospital_name'] ?? 'غير محدد'; ?></p>
                    </div>
                </div>
                <div class="info-card">
                    <i class="fas fa-clinic-medical"></i>
                    <div class="info-content">
                        <h3>المركز</h3>
                        <p><?php echo $current_user['center_name'] ?? 'غير محدد'; ?></p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total_employees'] ?? 0; ?></h3>
                        <p>إجمالي الموظفين</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['my_pending_entries'] ?? 0; ?></h3>
                        <p>إدخالات معلقة</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['my_approved_entries'] ?? 0; ?></h3>
                        <p>إدخالات معتمدة</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['today_entries'] ?? 0; ?></h3>
                        <p>إدخالات اليوم</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2><i class="fas fa-bolt"></i> الإجراءات السريعة</h2>
                <div class="actions-grid">
                    <a href="add_employee.php" class="action-card">
                        <i class="fas fa-user-plus"></i>
                        <h3>إضافة موظف</h3>
                        <p>إضافة موظف جديد للمركز</p>
                    </a>
                    
                    <a href="attendance_entry.php" class="action-card">
                        <i class="fas fa-calendar-check"></i>
                        <h3>تسجيل الحضور</h3>
                        <p>تسجيل حضور وغياب الموظفين</p>
                    </a>
                    
                    <a href="leave_request.php" class="action-card">
                        <i class="fas fa-calendar-alt"></i>
                        <h3>طلب إجازة</h3>
                        <p>إدخال طلب إجازة لموظف</p>
                    </a>
                    
                    <a href="movement_entry.php" class="action-card">
                        <i class="fas fa-exchange-alt"></i>
                        <h3>حركة موظف</h3>
                        <p>تسجيل حركة موظف (تنقل، إيفاد، إلخ)</p>
                    </a>
                    
                    <a href="my_entries.php" class="action-card">
                        <i class="fas fa-list"></i>
                        <h3>إدخالاتي</h3>
                        <p>عرض جميع إدخالاتي</p>
                    </a>
                    
                    <a href="employees.php" class="action-card">
                        <i class="fas fa-users"></i>
                        <h3>الموظفين</h3>
                        <p>عرض موظفي المركز</p>
                    </a>
                </div>
            </div>

            <!-- My Recent Entries -->
            <?php if (!empty($my_entries)): ?>
            <div class="recent-entries">
                <h2><i class="fas fa-history"></i> آخر إدخالاتي</h2>
                <div class="entries-table">
                    <table>
                        <thead>
                            <tr>
                                <th>الموظف</th>
                                <th>نوع الحركة</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($my_entries as $entry): ?>
                            <tr>
                                <td>
                                    <div class="employee-info">
                                        <strong><?php echo $entry['employee_name']; ?></strong>
                                        <small><?php echo $entry['job_title']; ?></small>
                                    </div>
                                </td>
                                <td><?php echo $entry['movement_type_name']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($entry['created_at'])); ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    $status_text = '';
                                    switch ($entry['status']) {
                                        case 'pending':
                                            $status_class = 'status-pending';
                                            $status_text = 'معلق';
                                            break;
                                        case 'approved':
                                            $status_class = 'status-approved';
                                            $status_text = 'معتمد';
                                            break;
                                        case 'rejected':
                                            $status_class = 'status-rejected';
                                            $status_text = 'مرفوض';
                                            break;
                                        default:
                                            $status_class = 'status-pending';
                                            $status_text = 'معلق';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="view_entry.php?id=<?php echo $entry['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                        <?php if ($entry['status'] == 'pending'): ?>
                                        <a href="edit_entry.php?id=<?php echo $entry['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <!-- Important Notice -->
            <div class="notice-box">
                <div class="notice-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="notice-content">
                    <h3>تنبيه مهم</h3>
                    <p>جميع البيانات التي تدخلها تحتاج إلى موافقة مدير المركز قبل أن تصبح فعالة في النظام. يرجى التأكد من دقة البيانات قبل الإرسال.</p>
                </div>
            </div>

            <!-- Center Information -->
            <div class="center-info">
                <h2><i class="fas fa-info-circle"></i> معلومات المركز</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>اسم المركز:</label>
                        <span><?php echo $center_info['name'] ?? 'غير محدد'; ?></span>
                    </div>
                    <div class="info-item">
                        <label>المستشفى التابع له:</label>
                        <span><?php echo $center_info['hospital_name'] ?? 'غير محدد'; ?></span>
                    </div>
                    <div class="info-item">
                        <label>مدير المركز:</label>
                        <span><?php echo $center_info['manager_name'] ?? 'غير محدد'; ?></span>
                    </div>
                    <div class="info-item">
                        <label>الهاتف:</label>
                        <span><?php echo $center_info['phone'] ?? 'غير محدد'; ?></span>
                    </div>
                    <div class="info-item">
                        <label>البريد الإلكتروني:</label>
                        <span><?php echo $center_info['email'] ?? 'غير محدد'; ?></span>
                    </div>
                    <div class="info-item">
                        <label>الحالة:</label>
                        <span class="status-badge <?php echo ($center_info['is_active'] ?? 0) ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo ($center_info['is_active'] ?? 0) ? 'نشط' : 'غير نشط'; ?>
                        </span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="assets/js/dashboard.js"></script>
</body>
</html>
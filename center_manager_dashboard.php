<?php
/**
 * لوحة تحكم مدير المركز
 * Center Manager Dashboard
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

// الحصول على إحصائيات المركز
$stats = getUserStatistics();

try {
    $db = new Database();
    
    // إحصائيات إضافية لمدير المركز
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM movements m JOIN employees e ON m.employee_id = e.id WHERE e.center_id = ? AND m.status = 'pending'");
    $stmt->execute([$current_user['center_id']]);
    $stats['pending_approvals'] = $stmt->fetch()['count'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM attendance a JOIN employees e ON a.employee_id = e.id WHERE e.center_id = ? AND a.attendance_date = CURDATE() AND a.status = 'present'");
    $stmt->execute([$current_user['center_id']]);
    $stats['today_attendance'] = $stmt->fetch()['count'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM attendance a JOIN employees e ON a.employee_id = e.id WHERE e.center_id = ? AND a.attendance_date = CURDATE() AND a.status = 'absent'");
    $stmt->execute([$current_user['center_id']]);
    $stats['today_absence'] = $stmt->fetch()['count'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM leaves l JOIN employees e ON l.employee_id = e.id WHERE e.center_id = ? AND l.status = 'approved' AND l.start_date <= CURDATE() AND l.end_date >= CURDATE()");
    $stmt->execute([$current_user['center_id']]);
    $stats['active_leaves'] = $stmt->fetch()['count'];
    
    // إحصائيات الإجازات حسب النوع
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM leaves l JOIN employees e ON l.employee_id = e.id WHERE e.center_id = ? AND l.leave_type = 'annual' AND l.status = 'approved' AND l.start_date <= CURDATE() AND l.end_date >= CURDATE()");
    $stmt->execute([$current_user['center_id']]);
    $stats['annual_leaves'] = $stmt->fetch()['count'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM leaves l JOIN employees e ON l.employee_id = e.id WHERE e.center_id = ? AND l.leave_type = 'sick' AND l.status = 'approved' AND l.start_date <= CURDATE() AND l.end_date >= CURDATE()");
    $stmt->execute([$current_user['center_id']]);
    $stats['sick_leaves'] = $stmt->fetch()['count'];
    
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM leaves l JOIN employees e ON l.employee_id = e.id WHERE e.center_id = ? AND l.leave_type = 'maternity' AND l.status = 'approved' AND l.start_date <= CURDATE() AND l.end_date >= CURDATE()");
    $stmt->execute([$current_user['center_id']]);
    $stats['maternity_leaves'] = $stmt->fetch()['count'];
    
    // الحصول على معلومات المركز
    $stmt = $db->prepare("SELECT c.*, h.name as hospital_name FROM centers c JOIN hospitals h ON c.hospital_id = h.id WHERE c.id = ?");
    $stmt->execute([$current_user['center_id']]);
    $center_info = $stmt->fetch();
    
    // الحصول على الموظفين في المركز
    $stmt = $db->prepare("SELECT * FROM employees WHERE center_id = ? AND status = 'active' ORDER BY full_name");
    $stmt->execute([$current_user['center_id']]);
    $employees = $stmt->fetchAll();
    
    // الحصول على الطلبات المعلقة
    $stmt = $db->prepare("
        SELECT m.*, e.full_name as employee_name, e.job_title, mt.name as movement_type_name
        FROM movements m 
        JOIN employees e ON m.employee_id = e.id 
        JOIN movement_types mt ON m.movement_type_id = mt.id
        WHERE e.center_id = ? AND m.status = 'pending'
        ORDER BY m.created_at DESC
        LIMIT 10
    ");
    $stmt->execute([$current_user['center_id']]);
    $pending_requests = $stmt->fetchAll();
    
} catch (Exception $e) {
    logError("Dashboard error: " . $e->getMessage());
    $stats = [];
    $center_info = null;
    $employees = [];
    $pending_requests = [];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة تحكم مدير المركز - <?php echo $center_info['name'] ?? 'غير محدد'; ?></title>
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
                    <i class="fas fa-user-tie"></i>
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
                        <h3><?php echo $stats['today_attendance'] ?? 0; ?></h3>
                        <p>حضور اليوم</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['today_absence'] ?? 0; ?></h3>
                        <p>غياب اليوم</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['pending_approvals'] ?? 0; ?></h3>
                        <p>طلبات في انتظار الموافقة</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['active_leaves'] ?? 0; ?></h3>
                        <p>إجازات نشطة</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['sick_leaves'] ?? 0; ?></h3>
                        <p>إجازات مرضية</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2><i class="fas fa-bolt"></i> الإجراءات السريعة</h2>
                <div class="actions-grid">
                    <?php if (hasPermission('manage_own_employees')): ?>
                    <a href="employees.php" class="action-card">
                        <i class="fas fa-users"></i>
                        <h3>إدارة الموظفين</h3>
                        <p>عرض وإدارة موظفي المركز</p>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (hasPermission('approve_data_entry')): ?>
                    <a href="approvals.php" class="action-card">
                        <i class="fas fa-check-circle"></i>
                        <h3>الموافقات</h3>
                        <p>مراجعة واعتماد الطلبات</p>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (hasPermission('view_own_reports')): ?>
                    <a href="reports.php" class="action-card">
                        <i class="fas fa-chart-bar"></i>
                        <h3>التقارير</h3>
                        <p>عرض تقارير المركز</p>
                    </a>
                    <?php endif; ?>
                    
                    <a href="attendance.php" class="action-card">
                        <i class="fas fa-calendar-check"></i>
                        <h3>الحضور والغياب</h3>
                        <p>تسجيل ومتابعة الحضور</p>
                    </a>
                    
                    <a href="leaves.php" class="action-card">
                        <i class="fas fa-calendar-alt"></i>
                        <h3>الإجازات</h3>
                        <p>إدارة طلبات الإجازات</p>
                    </a>
                    
                    <a href="movements.php" class="action-card">
                        <i class="fas fa-exchange-alt"></i>
                        <h3>الحركات</h3>
                        <p>إدارة حركات الموظفين</p>
                    </a>
                </div>
            </div>

            <!-- Pending Requests -->
            <?php if (!empty($pending_requests)): ?>
            <div class="pending-requests">
                <h2><i class="fas fa-exclamation-triangle"></i> الطلبات المعلقة</h2>
                <div class="requests-table">
                    <table>
                        <thead>
                            <tr>
                                <th>الموظف</th>
                                <th>نوع الطلب</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pending_requests as $request): ?>
                            <tr>
                                <td>
                                    <div class="employee-info">
                                        <strong><?php echo $request['employee_name']; ?></strong>
                                        <small><?php echo $request['job_title']; ?></small>
                                    </div>
                                </td>
                                <td><?php echo $request['movement_type_name']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($request['created_at'])); ?></td>
                                <td>
                                    <span class="status-badge status-pending">معلق</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="view_request.php?id=<?php echo $request['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                        <a href="approve_request.php?id=<?php echo $request['id']; ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> موافقة
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

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
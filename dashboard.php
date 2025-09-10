<?php
/**
 * صفحة الداشبورد الرئيسية
 * Main Dashboard Page
 */

require_once 'config/config.php';
require_once 'config/database.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect('/login.php');
}

$current_user = getCurrentUser();

// إعادة توجيه مديري المراكز إلى لوحة التحكم الخاصة بهم
if ($current_user['role_id'] == ROLE_CENTER_MANAGER) {
    redirect('/center_manager_dashboard.php');
}

// الحصول على الإحصائيات حسب الصلاحيات
$stats = getUserStatistics();

try {
    $db = new Database();
    
    // إحصائيات إضافية حسب الدور
    if ($current_user['role_id'] == ROLE_SUPER_ADMIN) {
        // مدير عام على كل المراكز - جميع البيانات
        $stmt = $db->query("SELECT COUNT(*) as count FROM movements WHERE status = 'pending'");
        $stats['pending_approvals'] = $stmt->fetch()['count'];
        
        $stmt = $db->query("SELECT COUNT(*) as count FROM attendance WHERE attendance_date = CURDATE() AND status = 'present'");
        $stats['today_attendance'] = $stmt->fetch()['count'];
        
        $stmt = $db->query("SELECT COUNT(*) as count FROM attendance WHERE attendance_date = CURDATE() AND status = 'absent'");
        $stats['today_absence'] = $stmt->fetch()['count'];
        
        $stmt = $db->query("SELECT COUNT(*) as count FROM leaves WHERE status = 'approved' AND start_date <= CURDATE() AND end_date >= CURDATE()");
        $stats['active_leaves'] = $stmt->fetch()['count'];
        
    } elseif ($current_user['role_id'] == ROLE_HOSPITAL_MANAGER) {
        // مدير المستشفى - بيانات مستشفاه فقط
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM movements m JOIN employees e ON m.employee_id = e.id JOIN centers c ON e.center_id = c.id WHERE c.hospital_id = ? AND m.status = 'pending'");
        $stmt->execute([$current_user['hospital_id']]);
        $stats['pending_approvals'] = $stmt->fetch()['count'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM attendance a JOIN employees e ON a.employee_id = e.id JOIN centers c ON e.center_id = c.id WHERE c.hospital_id = ? AND a.attendance_date = CURDATE() AND a.status = 'present'");
        $stmt->execute([$current_user['hospital_id']]);
        $stats['today_attendance'] = $stmt->fetch()['count'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM attendance a JOIN employees e ON a.employee_id = e.id JOIN centers c ON e.center_id = c.id WHERE c.hospital_id = ? AND a.attendance_date = CURDATE() AND a.status = 'absent'");
        $stmt->execute([$current_user['hospital_id']]);
        $stats['today_absence'] = $stmt->fetch()['count'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM leaves l JOIN employees e ON l.employee_id = e.id JOIN centers c ON e.center_id = c.id WHERE c.hospital_id = ? AND l.status = 'approved' AND l.start_date <= CURDATE() AND l.end_date >= CURDATE()");
        $stmt->execute([$current_user['hospital_id']]);
        $stats['active_leaves'] = $stmt->fetch()['count'];
        
        $stmt = $db->query("SELECT COUNT(*) as count FROM movements WHERE status = 'pending'");
        $stats['pending_approvals'] = $stmt->fetch()['count'];
        
        $stmt = $db->query("SELECT COUNT(*) as count FROM attendance WHERE attendance_date = CURDATE() AND status = 'present'");
        $stats['today_attendance'] = $stmt->fetch()['count'];
        
        $stmt = $db->query("SELECT COUNT(*) as count FROM attendance WHERE attendance_date = CURDATE() AND status = 'absent'");
        $stats['today_absence'] = $stmt->fetch()['count'];
        
    } elseif ($current_user['role_id'] == ROLE_HOSPITAL_MANAGER) {
        // مدير المراكز - بيانات مستشفاه فقط
        $hospital_id = $current_user['hospital_id'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM employees e JOIN centers c ON e.center_id = c.id WHERE c.hospital_id = ? AND e.status = 'active'");
        $stmt->execute([$hospital_id]);
        $stats['total_employees'] = $stmt->fetch()['count'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM centers WHERE hospital_id = ? AND is_active = 1");
        $stmt->execute([$hospital_id]);
        $stats['total_centers'] = $stmt->fetch()['count'];
        
        $stats['total_hospitals'] = 1;
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM movements m JOIN employees e ON m.employee_id = e.id JOIN centers c ON e.center_id = c.id WHERE c.hospital_id = ? AND m.status = 'pending'");
        $stmt->execute([$hospital_id]);
        $stats['pending_approvals'] = $stmt->fetch()['count'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM attendance a JOIN employees e ON a.employee_id = e.id JOIN centers c ON e.center_id = c.id WHERE c.hospital_id = ? AND a.attendance_date = CURDATE() AND a.status = 'present'");
        $stmt->execute([$hospital_id]);
        $stats['today_attendance'] = $stmt->fetch()['count'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM attendance a JOIN employees e ON a.employee_id = e.id JOIN centers c ON e.center_id = c.id WHERE c.hospital_id = ? AND a.attendance_date = CURDATE() AND a.status = 'absent'");
        $stmt->execute([$hospital_id]);
        $stats['today_absence'] = $stmt->fetch()['count'];
        
    } elseif ($current_user['role_id'] == ROLE_CENTER_MANAGER) {
        // مدير مركز - بيانات مركزه فقط
        $center_id = $current_user['center_id'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM employees WHERE center_id = ? AND status = 'active'");
        $stmt->execute([$center_id]);
        $stats['total_employees'] = $stmt->fetch()['count'];
        
        $stats['total_centers'] = 1;
        $stats['total_hospitals'] = 1;
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM movements m JOIN employees e ON m.employee_id = e.id WHERE e.center_id = ? AND m.status = 'pending'");
        $stmt->execute([$center_id]);
        $stats['pending_approvals'] = $stmt->fetch()['count'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM attendance a JOIN employees e ON a.employee_id = e.id WHERE e.center_id = ? AND a.attendance_date = CURDATE() AND a.status = 'present'");
        $stmt->execute([$center_id]);
        $stats['today_attendance'] = $stmt->fetch()['count'];
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM attendance a JOIN employees e ON a.employee_id = e.id WHERE e.center_id = ? AND a.attendance_date = CURDATE() AND a.status = 'absent'");
        $stmt->execute([$center_id]);
        $stats['today_absence'] = $stmt->fetch()['count'];
    }
    
    // إحصائيات الإجازات
    $stmt = $db->query("SELECT COUNT(*) as count FROM leaves WHERE status = 'approved' AND CURDATE() BETWEEN start_date AND end_date");
    $stats['active_leaves'] = $stmt->fetch()['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM leaves WHERE leave_type = 'exceptional' AND status = 'approved' AND CURDATE() BETWEEN start_date AND end_date");
    $stats['exceptional_leaves'] = $stmt->fetch()['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM leaves WHERE leave_type = 'maternity' AND status = 'approved' AND CURDATE() BETWEEN start_date AND end_date");
    $stats['maternity_leaves'] = $stmt->fetch()['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM leaves WHERE leave_type = 'sick' AND status = 'approved' AND CURDATE() BETWEEN start_date AND end_date");
    $stats['sick_leaves'] = $stmt->fetch()['count'];
    
} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $stats = [
        'total_employees' => 0,
        'total_centers' => 0,
        'total_hospitals' => 0,
        'pending_approvals' => 0,
        'today_attendance' => 0,
        'today_absence' => 0,
        'active_leaves' => 0,
        'exceptional_leaves' => 0,
        'maternity_leaves' => 0,
        'sick_leaves' => 0
    ];
}

// تحديد اسم الدور
$role_name = '';
switch ($current_user['role_id']) {
    case ROLE_SUPER_ADMIN:
        $role_name = 'مدير عام على كل المراكز';
        break;
    case ROLE_HOSPITAL_MANAGER:
        $role_name = 'مدير المراكز';
        break;
    case ROLE_CENTER_MANAGER:
        $role_name = 'مدير مركز';
        break;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e40af;
            --primary-dark: #1e3a8a;
            --primary-light: #3b82f6;
            --secondary-color: #64748b;
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
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
            font-size: 14px;
        }

        .dashboard-container {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .header {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
            color: var(--white);
            padding: 0;
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem 2rem;
            position: relative;
            z-index: 1;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo {
            width: 50px;
            height: 50px;
            background: var(--white);
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--primary-color);
            box-shadow: var(--shadow-md);
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.025em;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.75rem 1.25rem;
            border-radius: var(--border-radius);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: var(--white);
            box-shadow: var(--shadow);
        }

        .user-details h3 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .user-details p {
            font-size: 0.875rem;
            opacity: 0.9;
            font-weight: 500;
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.9);
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

        .logout-btn:hover {
            background: var(--danger-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        .welcome-section {
            background: var(--white);
            padding: 2.5rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light), var(--info-color));
        }

        .welcome-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
            letter-spacing: -0.025em;
        }

        .welcome-subtitle {
            color: var(--gray-600);
            font-size: 1.125rem;
            font-weight: 500;
        }

        .role-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 1rem;
            box-shadow: var(--shadow);
        }

        .user-info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .info-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .info-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .role-card .info-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .center-card .info-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .hospital-card .info-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .info-content h3 {
            color: var(--gray-200);
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0 0 0.5rem 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-content p {
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
            position: relative;
            overflow: hidden;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-xl);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-color);
        }

        .stat-card.success::before {
            background: var(--success-color);
        }

        .stat-card.warning::before {
            background: var(--warning-color);
        }

        .stat-card.danger::before {
            background: var(--danger-color);
        }

        .stat-card.info::before {
            background: var(--info-color);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }

        .stat-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent);
        }

        .stat-icon.blue {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        }

        .stat-icon.green {
            background: linear-gradient(135deg, var(--success-color), #10b981);
        }

        .stat-icon.orange {
            background: linear-gradient(135deg, var(--warning-color), #f59e0b);
        }

        .stat-icon.red {
            background: linear-gradient(135deg, var(--danger-color), #ef4444);
        }

        .stat-icon.purple {
            background: linear-gradient(135deg, #8b5cf6, #a855f7);
        }

        .stat-icon.cyan {
            background: linear-gradient(135deg, var(--info-color), #06b6d4);
        }

        .stat-title {
            font-size: 0.875rem;
            color: var(--gray-600);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            letter-spacing: -0.025em;
        }

        .stat-description {
            font-size: 0.875rem;
            color: var(--gray-500);
            font-weight: 500;
        }

        .stat-trend {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .stat-trend.positive {
            color: var(--success-color);
        }

        .stat-trend.negative {
            color: var(--danger-color);
        }

        /* Actions Section */
        .actions-section {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
        }

        .actions-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 1.5rem;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .action-btn {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            text-decoration: none;
            color: var(--gray-700);
            font-weight: 600;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }

        .action-btn:hover::before {
            left: 100%;
        }

        .action-btn:hover {
            background: var(--primary-color);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .action-icon {
            width: 40px;
            height: 40px;
            border-radius: var(--border-radius);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: var(--primary-color);
            color: var(--white);
            transition: var(--transition);
        }

        .action-btn:hover .action-icon {
            background: var(--white);
            color: var(--primary-color);
        }

        .action-content h3 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .action-content p {
            font-size: 0.875rem;
            opacity: 0.8;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .user-section {
                flex-direction: column;
                gap: 1rem;
                width: 100%;
            }

            .user-info {
                width: 100%;
                justify-content: center;
            }

            .main-content {
                padding: 1rem;
            }

            .welcome-section {
                padding: 1.5rem;
            }

            .welcome-title {
                font-size: 1.875rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .stat-card {
                padding: 1.5rem;
            }

            .stat-value {
                font-size: 2rem;
            }

            .action-btn {
                padding: 1rem;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <header class="header">
            <div class="header-content">
                <div class="logo-section">
                    <div class="logo">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <div class="logo-text"><?php echo SITE_NAME; ?></div>
                </div>
                <div class="user-section">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="user-details">
                            <h3><?php echo htmlspecialchars($current_user['full_name']); ?></h3>
                            <p><?php echo $role_name; ?></p>
                        </div>
                    </div>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        تسجيل الخروج
                    </a>
                </div>
            </div>
        </header>

        <main class="main-content">
            <div class="welcome-section">
                <h1 class="welcome-title">مرحباً بك، <?php echo htmlspecialchars($current_user['full_name']); ?></h1>
                
                <!-- عرض الرتبة والمركز والمستشفى -->
                <div class="user-info-cards">
                    <div class="info-card role-card">
                        <div class="info-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="info-content">
                            <h3>الرتبة</h3>
                            <p><?php echo htmlspecialchars($current_user['role_display_name'] ?? $role_name); ?></p>
                        </div>
                    </div>
                    
                    <?php if ($current_user['center_name']): ?>
                    <div class="info-card center-card">
                        <div class="info-icon">
                            <i class="fas fa-clinic-medical"></i>
                        </div>
                        <div class="info-content">
                            <h3>المركز</h3>
                            <p><?php echo htmlspecialchars($current_user['center_name']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($current_user['hospital_name']): ?>
                    <div class="info-card hospital-card">
                        <div class="info-icon">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <div class="info-content">
                            <h3>المستشفى</h3>
                            <p><?php echo htmlspecialchars($current_user['hospital_name']); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="role-badge">
                    <i class="fas fa-crown"></i>
                    <?php echo $role_name; ?>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon blue">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-title">إجمالي الموظفين</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['total_employees']); ?></div>
                    <div class="stat-description">موظف نشط في النظام</div>
                    <div class="stat-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>+5.2% من الشهر الماضي</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon cyan">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-title">المراكز الطبية</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['total_centers']); ?></div>
                    <div class="stat-description">مركز طبي نشط</div>
                    <div class="stat-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>100% تشغيل</span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon purple">
                            <i class="fas fa-hospital"></i>
                        </div>
                        <div class="stat-title">المستشفيات</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['total_hospitals']); ?></div>
                    <div class="stat-description">مستشفى متصل بالنظام</div>
                    <div class="stat-trend positive">
                        <i class="fas fa-check"></i>
                        <span>جميعها نشطة</span>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-header">
                        <div class="stat-icon orange">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-title">طلبات في الانتظار</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['pending_approvals']); ?></div>
                    <div class="stat-description">طلب يحتاج مراجعة</div>
                    <div class="stat-trend negative">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>يتطلب إجراء فوري</span>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-header">
                        <div class="stat-icon green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-title">الحضور اليوم</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['today_attendance']); ?></div>
                    <div class="stat-description">موظف حضر اليوم</div>
                    <div class="stat-trend positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>معدل حضور ممتاز</span>
                    </div>
                </div>

                <div class="stat-card danger">
                    <div class="stat-header">
                        <div class="stat-icon red">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-title">الغياب اليوم</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['today_absence']); ?></div>
                    <div class="stat-description">موظف غاب اليوم</div>
                    <div class="stat-trend negative">
                        <i class="fas fa-arrow-down"></i>
                        <span>يحتاج متابعة</span>
                    </div>
                </div>

                <div class="stat-card info">
                    <div class="stat-header">
                        <div class="stat-icon blue">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-title">الإجازات النشطة</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['active_leaves']); ?></div>
                    <div class="stat-description">إجازة حالية</div>
                    <div class="stat-trend positive">
                        <i class="fas fa-info-circle"></i>
                        <span>مُدارة بنجاح</span>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-header">
                        <div class="stat-icon orange">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-title">الإجازات الاستثنائية</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['exceptional_leaves']); ?></div>
                    <div class="stat-description">إجازة استثنائية</div>
                    <div class="stat-trend positive">
                        <i class="fas fa-check"></i>
                        <span>معتمدة</span>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-header">
                        <div class="stat-icon green">
                            <i class="fas fa-baby"></i>
                        </div>
                        <div class="stat-title">إجازة الأمومة</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['maternity_leaves']); ?></div>
                    <div class="stat-description">إجازة أمومة</div>
                    <div class="stat-trend positive">
                        <i class="fas fa-heart"></i>
                        <span>حق مكفول</span>
                    </div>
                </div>

                <div class="stat-card danger">
                    <div class="stat-header">
                        <div class="stat-icon red">
                            <i class="fas fa-thermometer-half"></i>
                        </div>
                        <div class="stat-title">الإجازات المرضية</div>
                    </div>
                    <div class="stat-value"><?php echo number_format($stats['sick_leaves']); ?></div>
                    <div class="stat-description">إجازة مرضية</div>
                    <div class="stat-trend negative">
                        <i class="fas fa-arrow-up"></i>
                        <span>تتطلب متابعة طبية</span>
                    </div>
                </div>
            </div>

            <div class="actions-section">
                <h2 class="actions-title">
                    <i class="fas fa-tasks"></i>
                    الإجراءات السريعة
                </h2>
                <div class="actions-grid">
                    <a href="data-entry.html" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="action-content">
                            <h3>إدخال بيانات جديدة</h3>
                            <p>إضافة موظفين، حركات، وإجازات</p>
                        </div>
                    </a>

                    <a href="approval.html" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="action-content">
                            <h3>مراجعة الطلبات</h3>
                            <p>اعتماد أو رفض الطلبات المعلقة</p>
                        </div>
                    </a>

                    <a href="reports.html" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="action-content">
                            <h3>التقارير والإحصائيات</h3>
                            <p>تقارير مفصلة ورسوم بيانية</p>
                        </div>
                    </a>

                    <a href="attendance.html" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="action-content">
                            <h3>الحضور والغياب</h3>
                            <p>تسجيل ومتابعة الحضور اليومي</p>
                        </div>
                    </a>

                    <?php if ($current_user['role_id'] == ROLE_SUPER_ADMIN): ?>
                    <a href="user-management.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="action-content">
                            <h3>إدارة المستخدمين</h3>
                            <p>إضافة وتعديل حسابات المستخدمين</p>
                        </div>
                    </a>

        <a href="all_users_list.php" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="action-content">
                <h3>قائمة جميع المستخدمين</h3>
                <p>عرض جميع الحسابات مرتبة حسب المراكز</p>
            </div>
        </a>

        <a href="add_center_users.php" class="action-btn">
            <div class="action-icon">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="action-content">
                <h3>إضافة مركز ومستخدمين</h3>
                <p>إضافة مركز جديد مع مديره ومدخلي البيانات</p>
            </div>
        </a>

                    <a href="settings.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="action-content">
                            <h3>إعدادات النظام</h3>
                            <p>تكوين النظام والإعدادات العامة</p>
                        </div>
                    </a>
                    <?php endif; ?>

                    <a href="notifications.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="action-content">
                            <h3>الإشعارات</h3>
                            <p>عرض الإشعارات والتنبيهات</p>
                        </div>
                    </a>

                    <a href="help.php" class="action-btn">
                        <div class="action-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="action-content">
                            <h3>المساعدة والدعم</h3>
                            <p>دليل الاستخدام والدعم الفني</p>
                        </div>
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        // إضافة تأثيرات تفاعلية
        document.addEventListener('DOMContentLoaded', function() {
            // تأثير تحميل البطاقات
            const cards = document.querySelectorAll('.stat-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // تأثير تحميل أزرار الإجراءات
            const actionBtns = document.querySelectorAll('.action-btn');
            actionBtns.forEach((btn, index) => {
                btn.style.opacity = '0';
                btn.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    btn.style.transition = 'all 0.6s ease';
                    btn.style.opacity = '1';
                    btn.style.transform = 'translateX(0)';
                }, (cards.length * 100) + (index * 50));
            });

            // تحديث الوقت
            function updateTime() {
                const now = new Date();
                const timeString = now.toLocaleString('ar-SA', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                
                // يمكن إضافة عرض الوقت في مكان مناسب
                console.log('الوقت الحالي:', timeString);
            }

            setInterval(updateTime, 1000);
        });
    </script>
</body>
</html>

<?php
/**
 * صفحة إدخال الحضور والغياب - مدخل البيانات
 * Attendance Entry Page - Data Entry
 */

require_once 'config/config.php';
require_once 'config/database.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect('/login.php');
}

$current_user = getCurrentUser();

// التحقق من الصلاحية
if (!hasPermission('data_entry')) {
    redirect('/dashboard.php');
}

$success_message = '';
$error_message = '';

// معالجة إدخال البيانات
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_attendance'])) {
    $employee_id = (int)$_POST['employee_id'];
    $attendance_date = $_POST['attendance_date'];
    $status = $_POST['status'];
    $check_in_time = $_POST['check_in_time'] ?? null;
    $check_out_time = $_POST['check_out_time'] ?? null;
    $notes = sanitize($_POST['notes'] ?? '');
    
    try {
        $db = new Database();
        
        // التحقق من أن الموظف يخص مركز المستخدم
        $stmt = $db->prepare("SELECT id FROM employees WHERE id = ? AND center_id = ?");
        $stmt->execute([$employee_id, $current_user['center_id']]);
        $employee = $stmt->fetch();
        
        if (!$employee) {
            $error_message = 'الموظف غير موجود أو لا توجد صلاحية للوصول إليه';
        } else {
            // التحقق من عدم وجود سجل سابق لنفس اليوم
            $stmt = $db->prepare("SELECT id FROM attendance WHERE employee_id = ? AND attendance_date = ?");
            $stmt->execute([$employee_id, $attendance_date]);
            $existing = $stmt->fetch();
            
            if ($existing) {
                $error_message = 'يوجد سجل حضور سابق لنفس الموظف في هذا التاريخ';
            } else {
                // إدراج سجل الحضور
                $stmt = $db->prepare("
                    INSERT INTO attendance (employee_id, attendance_date, status, check_in_time, check_out_time, notes, created_by, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$employee_id, $attendance_date, $status, $check_in_time, $check_out_time, $notes, $current_user['id']]);
                
                // تسجيل النشاط
                logActivity($current_user['id'], 'attendance_entry', 'attendance', $db->lastInsertId(), '', json_encode($_POST));
                
                $success_message = 'تم إدخال بيانات الحضور بنجاح';
            }
        }
    } catch (Exception $e) {
        logError("Attendance entry error: " . $e->getMessage());
        $error_message = 'حدث خطأ في إدخال البيانات';
    }
}

try {
    $db = new Database();
    
    // الحصول على موظفي المركز
    $stmt = $db->prepare("SELECT id, full_name, employee_id, job_title FROM employees WHERE center_id = ? AND status = 'active' ORDER BY full_name");
    $stmt->execute([$current_user['center_id']]);
    $employees = $stmt->fetchAll();
    
    // الحصول على آخر إدخالات الحضور
    $stmt = $db->prepare("
        SELECT a.*, e.full_name as employee_name, e.employee_id
        FROM attendance a 
        JOIN employees e ON a.employee_id = e.id 
        WHERE e.center_id = ? AND a.created_by = ?
        ORDER BY a.attendance_date DESC, a.created_at DESC
        LIMIT 20
    ");
    $stmt->execute([$current_user['center_id'], $current_user['id']]);
    $recent_entries = $stmt->fetchAll();
    
} catch (Exception $e) {
    logError("Attendance entry page error: " . $e->getMessage());
    $employees = [];
    $recent_entries = [];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدخال الحضور والغياب - <?php echo $current_user['center_name']; ?></title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <h1><i class="fas fa-calendar-check"></i> إدخال الحضور والغياب</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span class="user-name"><?php echo $current_user['full_name']; ?></span>
                        <span class="user-role"><?php echo $current_user['role_display_name']; ?></span>
                    </div>
                    <div class="user-actions">
                        <a href="data_entry_dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
                        </a>
                        <a href="logout.php" class="btn btn-danger">
                            <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- Messages -->
            <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
            </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
            </div>
            <?php endif; ?>

            <!-- Attendance Entry Form -->
            <div class="form-section">
                <h2><i class="fas fa-plus-circle"></i> إدخال بيانات الحضور</h2>
                
                <form method="POST" class="attendance-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="employee_id">الموظف *</label>
                            <select name="employee_id" id="employee_id" required>
                                <option value="">اختر الموظف</option>
                                <?php foreach ($employees as $employee): ?>
                                <option value="<?php echo $employee['id']; ?>">
                                    <?php echo $employee['full_name']; ?> - <?php echo $employee['employee_id']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="attendance_date">التاريخ *</label>
                            <input type="date" name="attendance_date" id="attendance_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="status">الحالة *</label>
                            <select name="status" id="status" required onchange="toggleTimeFields()">
                                <option value="">اختر الحالة</option>
                                <option value="present">حاضر</option>
                                <option value="absent">غائب</option>
                                <option value="late">متأخر</option>
                                <option value="early_leave">انصراف مبكر</option>
                            </select>
                        </div>
                        
                        <div class="form-group" id="check_in_group" style="display: none;">
                            <label for="check_in_time">وقت الحضور</label>
                            <input type="time" name="check_in_time" id="check_in_time">
                        </div>
                        
                        <div class="form-group" id="check_out_group" style="display: none;">
                            <label for="check_out_time">وقت الانصراف</label>
                            <input type="time" name="check_out_time" id="check_out_time">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">ملاحظات</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="أضف ملاحظات حول الحضور..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="submit_attendance" class="btn btn-primary">
                            <i class="fas fa-save"></i> حفظ البيانات
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> إعادة تعيين
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recent Entries -->
            <?php if (!empty($recent_entries)): ?>
            <div class="recent-entries">
                <h2><i class="fas fa-history"></i> آخر إدخالات الحضور</h2>
                
                <div class="table-container">
                    <table class="entries-table">
                        <thead>
                            <tr>
                                <th>الموظف</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>وقت الحضور</th>
                                <th>وقت الانصراف</th>
                                <th>الملاحظات</th>
                                <th>تاريخ الإدخال</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_entries as $entry): ?>
                            <tr>
                                <td>
                                    <div class="employee-info">
                                        <strong><?php echo $entry['employee_name']; ?></strong>
                                        <small><?php echo $entry['employee_id']; ?></small>
                                    </div>
                                </td>
                                <td><?php echo date('Y-m-d', strtotime($entry['attendance_date'])); ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    $status_text = '';
                                    switch ($entry['status']) {
                                        case 'present':
                                            $status_class = 'status-present';
                                            $status_text = 'حاضر';
                                            break;
                                        case 'absent':
                                            $status_class = 'status-absent';
                                            $status_text = 'غائب';
                                            break;
                                        case 'late':
                                            $status_class = 'status-late';
                                            $status_text = 'متأخر';
                                            break;
                                        case 'early_leave':
                                            $status_class = 'status-early';
                                            $status_text = 'انصراف مبكر';
                                            break;
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </td>
                                <td><?php echo $entry['check_in_time'] ?: '-'; ?></td>
                                <td><?php echo $entry['check_out_time'] ?: '-'; ?></td>
                                <td><?php echo $entry['notes'] ?: '-'; ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($entry['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        function toggleTimeFields() {
            const status = document.getElementById('status').value;
            const checkInGroup = document.getElementById('check_in_group');
            const checkOutGroup = document.getElementById('check_out_group');
            
            if (status === 'present' || status === 'late') {
                checkInGroup.style.display = 'block';
                checkOutGroup.style.display = 'block';
            } else {
                checkInGroup.style.display = 'none';
                checkOutGroup.style.display = 'none';
            }
        }
        
        // Set today's date as default
        document.getElementById('attendance_date').value = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>

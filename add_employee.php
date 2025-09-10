<?php
/**
 * صفحة إضافة موظف جديد - مدخل البيانات
 * Add Employee Page - Data Entry
 */

require_once 'config/config.php';
require_once 'config/database.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect('/login.php');
}

$current_user = getCurrentUser();

// التحقق من الصلاحية
if (!hasPermission('add_employee_data')) {
    redirect('/dashboard.php');
}

$success_message = '';
$error_message = '';

// معالجة إضافة الموظف
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_employee'])) {
    $employee_id = sanitize($_POST['employee_id']);
    $full_name = sanitize($_POST['full_name']);
    $job_title = sanitize($_POST['job_title']);
    $department = sanitize($_POST['department'] ?? '');
    $hire_date = $_POST['hire_date'];
    $salary = $_POST['salary'] ? (float)$_POST['salary'] : null;
    $phone = sanitize($_POST['phone'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $national_id = sanitize($_POST['national_id'] ?? '');
    $birth_date = $_POST['birth_date'] ?? null;
    $gender = $_POST['gender'] ?? '';
    $marital_status = $_POST['marital_status'] ?? '';
    $emergency_contact = sanitize($_POST['emergency_contact'] ?? '');
    $emergency_phone = sanitize($_POST['emergency_phone'] ?? '');
    $notes = sanitize($_POST['notes'] ?? '');
    
    try {
        $db = new Database();
        
        // التحقق من عدم وجود موظف بنفس رقم الهوية
        $stmt = $db->prepare("SELECT id FROM employees WHERE employee_id = ?");
        $stmt->execute([$employee_id]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            $error_message = 'يوجد موظف مسجل بنفس رقم الهوية';
        } else {
            // إدراج الموظف الجديد
            $stmt = $db->prepare("
                INSERT INTO employees (
                    employee_id, full_name, job_title, department, hire_date, salary, 
                    phone, email, address, national_id, birth_date, gender, 
                    marital_status, emergency_contact, emergency_phone, notes, 
                    center_id, status, created_by, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?, NOW())
            ");
            
            $stmt->execute([
                $employee_id, $full_name, $job_title, $department, $hire_date, $salary,
                $phone, $email, $address, $national_id, $birth_date, $gender,
                $marital_status, $emergency_contact, $emergency_phone, $notes,
                $current_user['center_id'], $current_user['id']
            ]);
            
            // تسجيل النشاط
            logActivity($current_user['id'], 'add_employee', 'employees', $db->lastInsertId(), '', json_encode($_POST));
            
            $success_message = 'تم إضافة الموظف بنجاح';
            
            // إعادة تعيين النموذج
            $_POST = [];
        }
    } catch (Exception $e) {
        logError("Add employee error: " . $e->getMessage());
        $error_message = 'حدث خطأ في إضافة الموظف';
    }
}

try {
    $db = new Database();
    
    // الحصول على آخر الموظفين المضافين
    $stmt = $db->prepare("
        SELECT * FROM employees 
        WHERE center_id = ? AND created_by = ? 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$current_user['center_id'], $current_user['id']]);
    $recent_employees = $stmt->fetchAll();
    
} catch (Exception $e) {
    logError("Add employee page error: " . $e->getMessage());
    $recent_employees = [];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة موظف جديد - <?php echo $current_user['center_name']; ?></title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <h1><i class="fas fa-user-plus"></i> إضافة موظف جديد</h1>
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

            <!-- Add Employee Form -->
            <div class="form-section">
                <h2><i class="fas fa-user-plus"></i> بيانات الموظف الجديد</h2>
                
                <form method="POST" class="employee-form">
                    <!-- Basic Information -->
                    <div class="form-section-header">
                        <h3><i class="fas fa-info-circle"></i> المعلومات الأساسية</h3>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="employee_id">رقم الهوية/الموظف *</label>
                            <input type="text" name="employee_id" id="employee_id" value="<?php echo $_POST['employee_id'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="full_name">الاسم الكامل *</label>
                            <input type="text" name="full_name" id="full_name" value="<?php echo $_POST['full_name'] ?? ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="job_title">المسمى الوظيفي *</label>
                            <input type="text" name="job_title" id="job_title" value="<?php echo $_POST['job_title'] ?? ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="department">القسم</label>
                            <input type="text" name="department" id="department" value="<?php echo $_POST['department'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="hire_date">تاريخ التعيين *</label>
                            <input type="date" name="hire_date" id="hire_date" value="<?php echo $_POST['hire_date'] ?? date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="salary">الراتب</label>
                            <input type="number" name="salary" id="salary" value="<?php echo $_POST['salary'] ?? ''; ?>" step="0.01" min="0">
                        </div>
                    </div>
                    
                    <!-- Personal Information -->
                    <div class="form-section-header">
                        <h3><i class="fas fa-user"></i> المعلومات الشخصية</h3>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="national_id">رقم الهوية الوطنية</label>
                            <input type="text" name="national_id" id="national_id" value="<?php echo $_POST['national_id'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="birth_date">تاريخ الميلاد</label>
                            <input type="date" name="birth_date" id="birth_date" value="<?php echo $_POST['birth_date'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="gender">الجنس</label>
                            <select name="gender" id="gender">
                                <option value="">اختر الجنس</option>
                                <option value="male" <?php echo ($_POST['gender'] ?? '') == 'male' ? 'selected' : ''; ?>>ذكر</option>
                                <option value="female" <?php echo ($_POST['gender'] ?? '') == 'female' ? 'selected' : ''; ?>>أنثى</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="marital_status">الحالة الاجتماعية</label>
                            <select name="marital_status" id="marital_status">
                                <option value="">اختر الحالة</option>
                                <option value="single" <?php echo ($_POST['marital_status'] ?? '') == 'single' ? 'selected' : ''; ?>>أعزب</option>
                                <option value="married" <?php echo ($_POST['marital_status'] ?? '') == 'married' ? 'selected' : ''; ?>>متزوج</option>
                                <option value="divorced" <?php echo ($_POST['marital_status'] ?? '') == 'divorced' ? 'selected' : ''; ?>>مطلق</option>
                                <option value="widowed" <?php echo ($_POST['marital_status'] ?? '') == 'widowed' ? 'selected' : ''; ?>>أرمل</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="form-section-header">
                        <h3><i class="fas fa-phone"></i> معلومات الاتصال</h3>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">رقم الهاتف</label>
                            <input type="tel" name="phone" id="phone" value="<?php echo $_POST['phone'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">البريد الإلكتروني</label>
                            <input type="email" name="email" id="email" value="<?php echo $_POST['email'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">العنوان</label>
                        <textarea name="address" id="address" rows="2" placeholder="العنوان الكامل..."><?php echo $_POST['address'] ?? ''; ?></textarea>
                    </div>
                    
                    <!-- Emergency Contact -->
                    <div class="form-section-header">
                        <h3><i class="fas fa-ambulance"></i> جهة الاتصال في حالات الطوارئ</h3>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="emergency_contact">اسم جهة الاتصال</label>
                            <input type="text" name="emergency_contact" id="emergency_contact" value="<?php echo $_POST['emergency_contact'] ?? ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="emergency_phone">رقم الهاتف</label>
                            <input type="tel" name="emergency_phone" id="emergency_phone" value="<?php echo $_POST['emergency_phone'] ?? ''; ?>">
                        </div>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="form-group">
                        <label for="notes">ملاحظات إضافية</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="ملاحظات إضافية عن الموظف..."><?php echo $_POST['notes'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="add_employee" class="btn btn-primary">
                            <i class="fas fa-save"></i> إضافة الموظف
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> إعادة تعيين
                        </button>
                    </div>
                </form>
            </div>

            <!-- Recent Employees -->
            <?php if (!empty($recent_employees)): ?>
            <div class="recent-entries">
                <h2><i class="fas fa-history"></i> آخر الموظفين المضافين</h2>
                
                <div class="table-container">
                    <table class="entries-table">
                        <thead>
                            <tr>
                                <th>رقم الهوية</th>
                                <th>الاسم</th>
                                <th>المسمى الوظيفي</th>
                                <th>تاريخ التعيين</th>
                                <th>الهاتف</th>
                                <th>تاريخ الإضافة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_employees as $employee): ?>
                            <tr>
                                <td><?php echo $employee['employee_id']; ?></td>
                                <td>
                                    <div class="employee-info">
                                        <strong><?php echo $employee['full_name']; ?></strong>
                                        <small><?php echo $employee['department']; ?></small>
                                    </div>
                                </td>
                                <td><?php echo $employee['job_title']; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($employee['hire_date'])); ?></td>
                                <td><?php echo $employee['phone'] ?: '-'; ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($employee['created_at'])); ?></td>
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
        // Set today's date as default for hire date
        document.getElementById('hire_date').value = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>

<?php
/**
 * صفحة إدارة المستخدمين
 * User Management Page
 */

require_once 'config/config.php';
require_once 'classes/User.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect('/login.php');
}

$user = new User();
if (!$user->validateSession()) {
    redirect('/login.php?timeout=1');
}

$current_user = getCurrentUser();

// التحقق من الصلاحيات
if (!hasRole(ROLE_SUPER_ADMIN) && !hasRole(ROLE_HOSPITAL_MANAGER)) {
    redirect('/dashboard.php');
}

$message = getMessage();
$users = [];
$roles = [];
$hospitals = [];
$centers = [];

try {
    $db = new Database();
    
    // الحصول على المستخدمين حسب الصلاحيات
    $filters = [];
    if ($current_user['role_id'] == ROLE_HOSPITAL_MANAGER) {
        $filters['hospital_id'] = $current_user['hospital_id'];
    }
    
    $users = $user->getAllUsers($filters);
    
    // الحصول على الأدوار
    $stmt = $db->prepare("SELECT * FROM roles WHERE is_active = 1 ORDER BY id");
    $stmt->execute();
    $roles = $stmt->fetchAll();
    
    // الحصول على المستشفيات
    $hospitals = getUserHospitals();
    
    // الحصول على المراكز
    $centers = getUserCenters();
    
} catch (Exception $e) {
    logError("User management error: " . $e->getMessage());
    $message = ['message' => 'حدث خطأ في تحميل البيانات', 'type' => 'error'];
}

// معالجة العمليات
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'create_user') {
        $user_data = [
            'username' => sanitize($_POST['username']),
            'email' => sanitize($_POST['email']),
            'password' => $_POST['password'],
            'full_name' => sanitize($_POST['full_name']),
            'phone' => sanitize($_POST['phone']),
            'role_id' => (int)$_POST['role_id'],
            'hospital_id' => !empty($_POST['hospital_id']) ? (int)$_POST['hospital_id'] : null,
            'center_id' => !empty($_POST['center_id']) ? (int)$_POST['center_id'] : null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        $result = $user->createUser($user_data);
        if ($result['success']) {
            showMessage('تم إنشاء المستخدم بنجاح', 'success');
        } else {
            showMessage($result['message'], 'error');
        }
        redirect('/user-management.php');
        
    } elseif ($action == 'update_user') {
        $user_id = (int)$_POST['user_id'];
        $user_data = [
            'full_name' => sanitize($_POST['full_name']),
            'email' => sanitize($_POST['email']),
            'phone' => sanitize($_POST['phone']),
            'role_id' => (int)$_POST['role_id'],
            'hospital_id' => !empty($_POST['hospital_id']) ? (int)$_POST['hospital_id'] : null,
            'center_id' => !empty($_POST['center_id']) ? (int)$_POST['center_id'] : null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];
        
        if (!empty($_POST['password'])) {
            $user_data['password'] = $_POST['password'];
        }
        
        $result = $user->updateUser($user_id, $user_data);
        if ($result['success']) {
            showMessage('تم تحديث المستخدم بنجاح', 'success');
        } else {
            showMessage($result['message'], 'error');
        }
        redirect('/user-management.php');
        
    } elseif ($action == 'delete_user') {
        $user_id = (int)$_POST['user_id'];
        $result = $user->deleteUser($user_id);
        if ($result['success']) {
            showMessage('تم حذف المستخدم بنجاح', 'success');
        } else {
            showMessage($result['message'], 'error');
        }
        redirect('/user-management.php');
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المستخدمين - <?php echo SITE_NAME; ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <div class="logo">
                <i class="fas fa-hospital"></i>
                <span>الصحة القابضة</span>
            </div>
            <div class="hospital-info">
                <span class="hospital-name"><?php echo $current_user['hospital_name'] ?? 'جميع المستشفيات'; ?></span>
                <span class="center-name"><?php echo $current_user['center_name'] ?? 'جميع المراكز'; ?></span>
            </div>
        </div>
        <div class="header-right">
            <div class="user-menu">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <span class="user-name"><?php echo $current_user['full_name']; ?></span>
                        <span class="user-role"><?php echo $current_user['role_display_name']; ?></span>
                    </div>
                </div>
                <div class="user-actions">
                    <a href="dashboard.php" class="btn btn-outline">
                        <i class="fas fa-arrow-right"></i>
                        العودة للرئيسية
                    </a>
                    <a href="logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i>
                        خروج
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="nav">
        <div class="nav-container">
            <a href="dashboard.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>الرئيسية</span>
            </a>
            <a href="data-entry.php" class="nav-item">
                <i class="fas fa-plus-circle"></i>
                <span>إدخال البيانات</span>
            </a>
            <a href="approval.php" class="nav-item">
                <i class="fas fa-check-circle"></i>
                <span>الاعتماد</span>
            </a>
            <a href="reports.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>التقارير</span>
            </a>
            <a href="user-management.php" class="nav-item active">
                <i class="fas fa-users"></i>
                <span>إدارة المستخدمين</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-left">
                <h2 class="page-title">
                    <i class="fas fa-users"></i>
                    إدارة المستخدمين
                </h2>
                <p class="page-subtitle">إدارة حسابات المستخدمين وصلاحياتهم</p>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="showCreateUserModal()">
                    <i class="fas fa-plus"></i>
                    إضافة مستخدم جديد
                </button>
                <button class="btn btn-outline" onclick="exportUsers()">
                    <i class="fas fa-download"></i>
                    تصدير
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <div class="filters-container">
                <div class="filter-group">
                    <label>الدور:</label>
                    <select id="role-filter" onchange="filterUsers()">
                        <option value="">جميع الأدوار</option>
                        <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>"><?php echo $role['display_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>المستشفى:</label>
                    <select id="hospital-filter" onchange="filterUsers()">
                        <option value="">جميع المستشفيات</option>
                        <?php foreach ($hospitals as $hospital): ?>
                        <option value="<?php echo $hospital['id']; ?>"><?php echo $hospital['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>المركز:</label>
                    <select id="center-filter" onchange="filterUsers()">
                        <option value="">جميع المراكز</option>
                        <?php foreach ($centers as $center): ?>
                        <option value="<?php echo $center['id']; ?>"><?php echo $center['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label>الحالة:</label>
                    <select id="status-filter" onchange="filterUsers()">
                        <option value="">جميع الحالات</option>
                        <option value="1">نشط</option>
                        <option value="0">غير نشط</option>
                    </select>
                </div>
                <div class="filter-actions">
                    <button class="btn btn-outline" onclick="clearFilters()">
                        <i class="fas fa-times"></i>
                        مسح الفلاتر
                    </button>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="table-section">
            <div class="table-container">
                <table class="data-table" id="usersTable">
                    <thead>
                        <tr>
                            <th>اسم المستخدم</th>
                            <th>الاسم الكامل</th>
                            <th>البريد الإلكتروني</th>
                            <th>الدور</th>
                            <th>المستشفى</th>
                            <th>المركز</th>
                            <th>الحالة</th>
                            <th>آخر دخول</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user_item): ?>
                        <tr data-user-id="<?php echo $user_item['id']; ?>">
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span><?php echo $user_item['username']; ?></span>
                                </div>
                            </td>
                            <td><?php echo $user_item['full_name']; ?></td>
                            <td><?php echo $user_item['email']; ?></td>
                            <td>
                                <span class="role-badge role-<?php echo $user_item['role_name']; ?>">
                                    <?php echo $user_item['role_display_name']; ?>
                                </span>
                            </td>
                            <td><?php echo $user_item['hospital_name'] ?? '-'; ?></td>
                            <td><?php echo $user_item['center_name'] ?? '-'; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $user_item['is_active'] ? 'active' : 'inactive'; ?>">
                                    <?php echo $user_item['is_active'] ? 'نشط' : 'غير نشط'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user_item['last_login']): ?>
                                    <?php echo timeAgo($user_item['last_login']); ?>
                                <?php else: ?>
                                    <span class="text-muted">لم يسجل دخول</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-outline" onclick="editUser(<?php echo $user_item['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline" onclick="resetPassword(<?php echo $user_item['id']; ?>)">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <?php if ($user_item['id'] != $current_user['id']): ?>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user_item['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Create User Modal -->
    <div id="createUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>إضافة مستخدم جديد</h3>
                <button class="modal-close" onclick="hideCreateUserModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="createUserForm" method="POST">
                <input type="hidden" name="action" value="create_user">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="create_username">اسم المستخدم *</label>
                            <input type="text" id="create_username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="create_email">البريد الإلكتروني *</label>
                            <input type="email" id="create_email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="create_full_name">الاسم الكامل *</label>
                            <input type="text" id="create_full_name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="create_phone">رقم الهاتف</label>
                            <input type="tel" id="create_phone" name="phone">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="create_password">كلمة المرور *</label>
                            <input type="password" id="create_password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="create_role">الدور *</label>
                            <select id="create_role" name="role_id" required onchange="updateRoleFields()">
                                <option value="">اختر الدور</option>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>"><?php echo $role['display_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" id="hospital-field" style="display: none;">
                            <label for="create_hospital">المستشفى</label>
                            <select id="create_hospital" name="hospital_id" onchange="updateCenters()">
                                <option value="">اختر المستشفى</option>
                                <?php foreach ($hospitals as $hospital): ?>
                                <option value="<?php echo $hospital['id']; ?>"><?php echo $hospital['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" id="center-field" style="display: none;">
                            <label for="create_center">المركز</label>
                            <select id="create_center" name="center_id">
                                <option value="">اختر المركز</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" checked>
                            <span class="checkmark"></span>
                            حساب نشط
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="hideCreateUserModal()">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة المستخدم</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>تعديل المستخدم</h3>
                <button class="modal-close" onclick="hideEditUserModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="editUserForm" method="POST">
                <input type="hidden" name="action" value="update_user">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_username">اسم المستخدم</label>
                            <input type="text" id="edit_username" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit_email">البريد الإلكتروني *</label>
                            <input type="email" id="edit_email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_full_name">الاسم الكامل *</label>
                            <input type="text" id="edit_full_name" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_phone">رقم الهاتف</label>
                            <input type="tel" id="edit_phone" name="phone">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="edit_password">كلمة المرور الجديدة</label>
                            <input type="password" id="edit_password" name="password" placeholder="اتركها فارغة للحفاظ على كلمة المرور الحالية">
                        </div>
                        <div class="form-group">
                            <label for="edit_role">الدور *</label>
                            <select id="edit_role" name="role_id" required onchange="updateEditRoleFields()">
                                <option value="">اختر الدور</option>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>"><?php echo $role['display_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group" id="edit_hospital_field" style="display: none;">
                            <label for="edit_hospital">المستشفى</label>
                            <select id="edit_hospital" name="hospital_id" onchange="updateEditCenters()">
                                <option value="">اختر المستشفى</option>
                                <?php foreach ($hospitals as $hospital): ?>
                                <option value="<?php echo $hospital['id']; ?>"><?php echo $hospital['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group" id="edit_center_field" style="display: none;">
                            <label for="edit_center">المركز</label>
                            <select id="edit_center" name="center_id">
                                <option value="">اختر المركز</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_active" id="edit_is_active">
                            <span class="checkmark"></span>
                            حساب نشط
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="hideEditUserModal()">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>تأكيد الحذف</h3>
                <button class="modal-close" onclick="hideDeleteModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف هذا المستخدم؟</p>
                <p class="text-danger">هذا الإجراء لا يمكن التراجع عنه.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="hideDeleteModal()">إلغاء</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">حذف</button>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="notification-toast" class="notification-toast">
        <div class="toast-content">
            <div class="toast-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="toast-message">
                <div class="toast-title">تم بنجاح</div>
                <div class="toast-description">تم تنفيذ العملية بنجاح</div>
            </div>
            <button class="toast-close" onclick="hideNotification()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    <script>
        // إظهار رسائل النظام
        <?php if ($message): ?>
        showNotification('<?php echo $message['message']; ?>', '<?php echo $message['type']; ?>');
        <?php endif; ?>

        let usersData = <?php echo json_encode($users); ?>;
        let deleteUserId = null;

        // إظهار/إخفاء النوافذ المنبثقة
        function showCreateUserModal() {
            document.getElementById('createUserModal').style.display = 'flex';
        }

        function hideCreateUserModal() {
            document.getElementById('createUserModal').style.display = 'none';
            document.getElementById('createUserForm').reset();
        }

        function showEditUserModal() {
            document.getElementById('editUserModal').style.display = 'flex';
        }

        function hideEditUserModal() {
            document.getElementById('editUserModal').style.display = 'none';
        }

        function showDeleteModal() {
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            deleteUserId = null;
        }

        // تعديل مستخدم
        function editUser(userId) {
            const user = usersData.find(u => u.id == userId);
            if (!user) return;

            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_full_name').value = user.full_name;
            document.getElementById('edit_phone').value = user.phone || '';
            document.getElementById('edit_role').value = user.role_id;
            document.getElementById('edit_hospital').value = user.hospital_id || '';
            document.getElementById('edit_center').value = user.center_id || '';
            document.getElementById('edit_is_active').checked = user.is_active == 1;

            updateEditRoleFields();
            showEditUserModal();
        }

        // حذف مستخدم
        function deleteUser(userId) {
            deleteUserId = userId;
            showDeleteModal();
        }

        function confirmDelete() {
            if (deleteUserId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" value="${deleteUserId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // تحديث حقول الدور
        function updateRoleFields() {
            const roleId = document.getElementById('create_role').value;
            const hospitalField = document.getElementById('hospital-field');
            const centerField = document.getElementById('center-field');

            if (roleId == 2) { // مشرف المستشفى
                hospitalField.style.display = 'block';
                centerField.style.display = 'none';
            } else if (roleId == 1) { // مدير المركز
                hospitalField.style.display = 'block';
                centerField.style.display = 'block';
            } else {
                hospitalField.style.display = 'none';
                centerField.style.display = 'none';
            }
        }

        function updateEditRoleFields() {
            const roleId = document.getElementById('edit_role').value;
            const hospitalField = document.getElementById('edit_hospital_field');
            const centerField = document.getElementById('edit_center_field');

            if (roleId == 2) { // مشرف المستشفى
                hospitalField.style.display = 'block';
                centerField.style.display = 'none';
            } else if (roleId == 1) { // مدير المركز
                hospitalField.style.display = 'block';
                centerField.style.display = 'block';
            } else {
                hospitalField.style.display = 'none';
                centerField.style.display = 'none';
            }
        }

        // تحديث المراكز عند تغيير المستشفى
        function updateCenters() {
            const hospitalId = document.getElementById('create_hospital').value;
            const centerSelect = document.getElementById('create_center');
            
            centerSelect.innerHTML = '<option value="">اختر المركز</option>';
            
            if (hospitalId) {
                // إرسال طلب AJAX للحصول على المراكز
                fetch(`api/get-centers.php?hospital_id=${hospitalId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(center => {
                            const option = document.createElement('option');
                            option.value = center.id;
                            option.textContent = center.name;
                            centerSelect.appendChild(option);
                        });
                    });
            }
        }

        function updateEditCenters() {
            const hospitalId = document.getElementById('edit_hospital').value;
            const centerSelect = document.getElementById('edit_center');
            
            centerSelect.innerHTML = '<option value="">اختر المركز</option>';
            
            if (hospitalId) {
                // إرسال طلب AJAX للحصول على المراكز
                fetch(`api/get-centers.php?hospital_id=${hospitalId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(center => {
                            const option = document.createElement('option');
                            option.value = center.id;
                            option.textContent = center.name;
                            centerSelect.appendChild(option);
                        });
                    });
            }
        }

        // فلترة المستخدمين
        function filterUsers() {
            const roleFilter = document.getElementById('role-filter').value;
            const hospitalFilter = document.getElementById('hospital-filter').value;
            const centerFilter = document.getElementById('center-filter').value;
            const statusFilter = document.getElementById('status-filter').value;

            const rows = document.querySelectorAll('#usersTable tbody tr');
            
            rows.forEach(row => {
                const roleId = row.querySelector('td:nth-child(4) .role-badge').getAttribute('class').includes('role-center_manager') ? '1' : 
                              row.querySelector('td:nth-child(4) .role-badge').getAttribute('class').includes('role-hospital_supervisor') ? '2' : '3';
                const hospitalId = row.getAttribute('data-hospital-id') || '';
                const centerId = row.getAttribute('data-center-id') || '';
                const isActive = row.querySelector('td:nth-child(7) .status-badge').textContent.includes('نشط') ? '1' : '0';

                let show = true;

                if (roleFilter && roleId !== roleFilter) show = false;
                if (hospitalFilter && hospitalId !== hospitalFilter) show = false;
                if (centerFilter && centerId !== centerFilter) show = false;
                if (statusFilter && isActive !== statusFilter) show = false;

                row.style.display = show ? '' : 'none';
            });
        }

        function clearFilters() {
            document.getElementById('role-filter').value = '';
            document.getElementById('hospital-filter').value = '';
            document.getElementById('center-filter').value = '';
            document.getElementById('status-filter').value = '';
            filterUsers();
        }

        // تصدير المستخدمين
        function exportUsers() {
            // تنفيذ تصدير المستخدمين
            window.open('api/export-users.php', '_blank');
        }

        // إعادة تعيين كلمة المرور
        function resetPassword(userId) {
            if (confirm('هل تريد إعادة تعيين كلمة المرور لهذا المستخدم؟')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="user_id" value="${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // إظهار/إخفاء الإشعارات
        function showNotification(message, type = 'success') {
            const toast = document.getElementById('notification-toast');
            const icon = toast.querySelector('.toast-icon i');
            const title = toast.querySelector('.toast-title');
            const description = toast.querySelector('.toast-description');

            // تحديث المحتوى
            description.textContent = message;

            // تحديث الأيقونة واللون
            toast.className = `notification-toast ${type}`;
            if (type === 'success') {
                icon.className = 'fas fa-check-circle';
                title.textContent = 'تم بنجاح';
            } else if (type === 'error') {
                icon.className = 'fas fa-exclamation-circle';
                title.textContent = 'خطأ';
            } else if (type === 'warning') {
                icon.className = 'fas fa-exclamation-triangle';
                title.textContent = 'تحذير';
            } else {
                icon.className = 'fas fa-info-circle';
                title.textContent = 'معلومات';
            }

            // إظهار الإشعار
            toast.style.display = 'flex';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 5000);
        }

        function hideNotification() {
            document.getElementById('notification-toast').style.display = 'none';
        }

        // إغلاق النوافذ المنبثقة عند النقر خارجها
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>

<?php
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'منذ لحظات';
    if ($time < 3600) return 'منذ ' . floor($time/60) . ' دقيقة';
    if ($time < 86400) return 'منذ ' . floor($time/3600) . ' ساعة';
    if ($time < 2592000) return 'منذ ' . floor($time/86400) . ' يوم';
    if ($time < 31536000) return 'منذ ' . floor($time/2592000) . ' شهر';
    return 'منذ ' . floor($time/31536000) . ' سنة';
}
?>

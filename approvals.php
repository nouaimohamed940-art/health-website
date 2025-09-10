<?php
/**
 * صفحة الموافقات - مدير المركز
 * Approvals Page - Center Manager
 */

require_once 'config/config.php';
require_once 'config/database.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect('/login.php');
}

$current_user = getCurrentUser();

// التحقق من الصلاحية
if (!hasPermission('approve_data_entry')) {
    redirect('/dashboard.php');
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$status = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';

try {
    $db = new Database();
    
    // بناء استعلام البحث
    $where_conditions = ["e.center_id = ?"];
    $params = [$current_user['center_id']];
    
    if ($status != 'all') {
        $where_conditions[] = "m.status = ?";
        $params[] = $status;
    }
    
    if (!empty($search)) {
        $where_conditions[] = "(e.full_name LIKE ? OR e.employee_id LIKE ? OR mt.name LIKE ?)";
        $search_term = "%$search%";
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    $where_clause = "WHERE " . implode(" AND ", $where_conditions);
    
    // الحصول على إجمالي السجلات
    $count_sql = "
        SELECT COUNT(*) as total
        FROM movements m 
        JOIN employees e ON m.employee_id = e.id 
        JOIN movement_types mt ON m.movement_type_id = mt.id
        $where_clause
    ";
    $stmt = $db->prepare($count_sql);
    $stmt->execute($params);
    $total_records = $stmt->fetch()['total'];
    $total_pages = ceil($total_records / $limit);
    
    // الحصول على الطلبات
    $sql = "
        SELECT m.*, e.full_name as employee_name, e.employee_id, e.job_title, 
               mt.name as movement_type_name, mt.display_name as movement_display_name,
               de.full_name as created_by_name
        FROM movements m 
        JOIN employees e ON m.employee_id = e.id 
        JOIN movement_types mt ON m.movement_type_id = mt.id
        LEFT JOIN data_entry_users de ON m.created_by = de.id
        $where_clause
        ORDER BY m.created_at DESC
        LIMIT $limit OFFSET $offset
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $requests = $stmt->fetchAll();
    
    // إحصائيات سريعة
    $stats_sql = "
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
        FROM movements m 
        JOIN employees e ON m.employee_id = e.id 
        WHERE e.center_id = ?
    ";
    $stmt = $db->prepare($stats_sql);
    $stmt->execute([$current_user['center_id']]);
    $stats = $stmt->fetch();
    
} catch (Exception $e) {
    logError("Approvals page error: " . $e->getMessage());
    $requests = [];
    $stats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
    $total_pages = 0;
}

// معالجة الموافقة/الرفض
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['approve']) || isset($_POST['reject'])) {
        $request_id = (int)$_POST['request_id'];
        $action = isset($_POST['approve']) ? 'approve' : 'reject';
        $notes = sanitize($_POST['notes'] ?? '');
        
        try {
            $db = new Database();
            
            // التحقق من أن الطلب يخص مركز المستخدم
            $stmt = $db->prepare("
                SELECT m.*, e.center_id 
                FROM movements m 
                JOIN employees e ON m.employee_id = e.id 
                WHERE m.id = ? AND e.center_id = ?
            ");
            $stmt->execute([$request_id, $current_user['center_id']]);
            $request = $stmt->fetch();
            
            if (!$request) {
                showMessage('الطلب غير موجود أو لا توجد صلاحية للوصول إليه', 'error');
            } else {
                // تحديث حالة الطلب
                $new_status = $action == 'approve' ? 'approved' : 'rejected';
                $stmt = $db->prepare("
                    UPDATE movements 
                    SET status = ?, approved_by = ?, approved_at = NOW(), approval_notes = ?
                    WHERE id = ?
                ");
                $stmt->execute([$new_status, $current_user['id'], $notes, $request_id]);
                
                // تسجيل النشاط
                logActivity($current_user['id'], $action, 'movements', $request_id, $request['status'], $new_status);
                
                showMessage("تم $action الطلب بنجاح", 'success');
                redirect('/approvals.php');
            }
        } catch (Exception $e) {
            logError("Approval error: " . $e->getMessage());
            showMessage('حدث خطأ في معالجة الطلب', 'error');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الموافقات - <?php echo $current_user['center_name']; ?></title>
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-content">
                <div class="header-left">
                    <h1><i class="fas fa-check-circle"></i> إدارة الموافقات</h1>
                </div>
                <div class="header-right">
                    <div class="user-info">
                        <span class="user-name"><?php echo $current_user['full_name']; ?></span>
                        <span class="user-role"><?php echo $current_user['role_display_name']; ?></span>
                    </div>
                    <div class="user-actions">
                        <a href="center_manager_dashboard.php" class="btn btn-secondary">
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
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['total']; ?></h3>
                        <p>إجمالي الطلبات</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['pending']; ?></h3>
                        <p>طلبات معلقة</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['approved']; ?></h3>
                        <p>طلبات معتمدة</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $stats['rejected']; ?></h3>
                        <p>طلبات مرفوضة</p>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters-section">
                <form method="GET" class="filters-form">
                    <div class="filter-group">
                        <label for="status">الحالة:</label>
                        <select name="status" id="status">
                            <option value="all" <?php echo $status == 'all' ? 'selected' : ''; ?>>جميع الحالات</option>
                            <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>معلق</option>
                            <option value="approved" <?php echo $status == 'approved' ? 'selected' : ''; ?>>معتمد</option>
                            <option value="rejected" <?php echo $status == 'rejected' ? 'selected' : ''; ?>>مرفوض</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="search">البحث:</label>
                        <input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="اسم الموظف أو رقم الهوية">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> بحث
                    </button>
                    
                    <a href="approvals.php" class="btn btn-secondary">
                        <i class="fas fa-refresh"></i> إعادة تعيين
                    </a>
                </form>
            </div>

            <!-- Requests Table -->
            <div class="requests-section">
                <h2><i class="fas fa-list-alt"></i> قائمة الطلبات</h2>
                
                <?php if (empty($requests)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>لا توجد طلبات</h3>
                    <p>لا توجد طلبات مطابقة للمعايير المحددة</p>
                </div>
                <?php else: ?>
                
                <div class="table-container">
                    <table class="requests-table">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>الموظف</th>
                                <th>نوع الحركة</th>
                                <th>التاريخ</th>
                                <th>مدخل البيانات</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request): ?>
                            <tr>
                                <td>#<?php echo $request['id']; ?></td>
                                <td>
                                    <div class="employee-info">
                                        <strong><?php echo $request['employee_name']; ?></strong>
                                        <small><?php echo $request['job_title']; ?></small>
                                    </div>
                                </td>
                                <td><?php echo $request['movement_display_name']; ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($request['created_at'])); ?></td>
                                <td><?php echo $request['created_by_name'] ?? 'غير محدد'; ?></td>
                                <td>
                                    <?php
                                    $status_class = '';
                                    $status_text = '';
                                    switch ($request['status']) {
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
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="view_request.php?id=<?php echo $request['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> عرض
                                        </a>
                                        
                                        <?php if ($request['status'] == 'pending'): ?>
                                        <button onclick="showApprovalModal(<?php echo $request['id']; ?>, 'approve')" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> موافقة
                                        </button>
                                        <button onclick="showApprovalModal(<?php echo $request['id']; ?>, 'reject')" class="btn btn-sm btn-danger">
                                            <i class="fas fa-times"></i> رفض
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status; ?>&search=<?php echo urlencode($search); ?>" class="btn btn-secondary">
                        <i class="fas fa-chevron-right"></i> السابق
                    </a>
                    <?php endif; ?>
                    
                    <span class="page-info">
                        صفحة <?php echo $page; ?> من <?php echo $total_pages; ?>
                    </span>
                    
                    <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status; ?>&search=<?php echo urlencode($search); ?>" class="btn btn-secondary">
                        التالي <i class="fas fa-chevron-left"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Approval Modal -->
    <div id="approvalModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">موافقة على الطلب</h3>
                <span class="close">&times;</span>
            </div>
            <form method="POST" id="approvalForm">
                <div class="modal-body">
                    <input type="hidden" name="request_id" id="requestId">
                    <input type="hidden" name="action" id="actionType">
                    
                    <div class="form-group">
                        <label for="notes">ملاحظات (اختياري):</label>
                        <textarea name="notes" id="notes" rows="4" placeholder="أضف ملاحظات حول القرار..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">إلغاء</button>
                    <button type="submit" class="btn" id="submitBtn">تأكيد</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showApprovalModal(requestId, action) {
            document.getElementById('requestId').value = requestId;
            document.getElementById('actionType').value = action;
            
            const modal = document.getElementById('approvalModal');
            const title = document.getElementById('modalTitle');
            const submitBtn = document.getElementById('submitBtn');
            
            if (action === 'approve') {
                title.textContent = 'موافقة على الطلب';
                submitBtn.textContent = 'موافقة';
                submitBtn.className = 'btn btn-success';
            } else {
                title.textContent = 'رفض الطلب';
                submitBtn.textContent = 'رفض';
                submitBtn.className = 'btn btn-danger';
            }
            
            modal.style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('approvalModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('approvalModal');
            if (event.target == modal) {
                closeModal();
            }
        }
        
        // Close modal with X button
        document.querySelector('.close').onclick = closeModal;
    </script>
</body>
</html>

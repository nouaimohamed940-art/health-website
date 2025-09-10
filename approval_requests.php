<?php
/**
 * صفحة موافقة المديرين على طلبات مدخلي البيانات
 * Center Managers Approval Page for Data Entry Requests
 */

require_once 'config/config.php';
require_once 'config/database.php';

// التحقق من تسجيل الدخول
if (!isLoggedIn()) {
    redirect('/login.php');
}

$current_user = getCurrentUser();

// فقط مديري المراكز يمكنهم الوصول لهذه الصفحة
if ($current_user['role_id'] != ROLE_CENTER_MANAGER) {
    redirect('/dashboard.php');
}

$center_id = $current_user['center_id'];
$success_message = '';
$error_message = '';

// معالجة الموافقة أو الرفض
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    $request_id = (int)($_POST['request_id'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');
    
    if ($action && $request_id) {
        try {
            $db = new Database();
            
            // التحقق من أن الطلب يخص مركز المدير
            $stmt = $db->prepare("
                SELECT der.*, deu.full_name as data_entry_user_name 
                FROM data_entry_requests der
                JOIN data_entry_users deu ON der.data_entry_user_id = deu.id
                WHERE der.id = ? AND der.center_id = ? AND der.status = 'pending'
            ");
            $stmt->execute([$request_id, $center_id]);
            $request = $stmt->fetch();
            
            if (!$request) {
                $error_message = 'الطلب غير موجود أو لا يمكن معالجته';
            } else {
                if ($action === 'approve') {
                    // موافقة على الطلب
                    $stmt = $db->prepare("CALL ApproveDataEntryRequest(?, ?, ?)");
                    $stmt->execute([$request_id, $current_user['id'], $notes]);
                    $success_message = 'تمت الموافقة على الطلب بنجاح';
                } elseif ($action === 'reject') {
                    // رفض الطلب
                    $stmt = $db->prepare("CALL RejectDataEntryRequest(?, ?, ?)");
                    $stmt->execute([$request_id, $current_user['id'], $notes]);
                    $success_message = 'تم رفض الطلب بنجاح';
                }
            }
        } catch (Exception $e) {
            error_log("Approval error: " . $e->getMessage());
            $error_message = 'حدث خطأ في معالجة الطلب';
        }
    }
}

try {
    $db = new Database();
    
    // الحصول على طلبات الموافقة
    $stmt = $db->prepare("
        SELECT 
            der.*,
            deu.full_name as data_entry_user_name,
            deu.username as data_entry_username,
            c.name as center_name,
            h.name as hospital_name
        FROM data_entry_requests der
        JOIN data_entry_users deu ON der.data_entry_user_id = deu.id
        JOIN centers c ON der.center_id = c.id
        JOIN hospitals h ON c.hospital_id = h.id
        WHERE der.center_id = ?
        ORDER BY der.created_at DESC
    ");
    $stmt->execute([$center_id]);
    $requests = $stmt->fetchAll();
    
    // إحصائيات الطلبات
    $stats = [
        'total' => count($requests),
        'pending' => count(array_filter($requests, fn($r) => $r['status'] === 'pending')),
        'approved' => count(array_filter($requests, fn($r) => $r['status'] === 'approved')),
        'rejected' => count(array_filter($requests, fn($r) => $r['status'] === 'rejected'))
    ];
    
} catch (Exception $e) {
    error_log("Approval page error: " . $e->getMessage());
    $requests = [];
    $stats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>موافقة الطلبات - <?php echo SITE_NAME; ?></title>
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
            max-width: 1400px;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-200);
            text-align: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--white);
            margin: 0 auto 1rem;
        }

        .stat-icon.blue { background: linear-gradient(135deg, var(--primary-color), var(--primary-light)); }
        .stat-icon.orange { background: linear-gradient(135deg, var(--warning-color), #f59e0b); }
        .stat-icon.green { background: linear-gradient(135deg, var(--success-color), #10b981); }
        .stat-icon.red { background: linear-gradient(135deg, var(--danger-color), #ef4444); }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--gray-600);
            font-weight: 600;
        }

        .requests-section {
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

        .request-card {
            background: var(--gray-50);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            border: 1px solid var(--gray-200);
            transition: var(--transition);
        }

        .request-card:hover {
            background: var(--white);
            box-shadow: var(--shadow);
        }

        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .request-type {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .request-status {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            font-size: 0.875rem;
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

        .request-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-600);
            margin-bottom: 0.25rem;
        }

        .detail-value {
            color: var(--gray-900);
            font-weight: 500;
        }

        .request-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            font-size: 0.875rem;
        }

        .btn-approve {
            background: var(--success-color);
            color: var(--white);
        }

        .btn-approve:hover {
            background: #047857;
            transform: translateY(-2px);
        }

        .btn-reject {
            background: var(--danger-color);
            color: var(--white);
        }

        .btn-reject:hover {
            background: #b91c1c;
            transform: translateY(-2px);
        }

        .btn-view {
            background: var(--info-color);
            color: var(--white);
        }

        .btn-view:hover {
            background: #0e7490;
            transform: translateY(-2px);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            width: 90%;
            max-width: 500px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--gray-400);
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius);
            font-size: 1rem;
            resize: vertical;
            min-height: 100px;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .modal-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
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
            .container {
                padding: 1rem;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .request-details {
                grid-template-columns: 1fr;
            }

            .request-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <div>
                    <h1 class="header-title">موافقة طلبات إدخال البيانات</h1>
                    <p class="header-subtitle">مراجعة وموافقة الطلبات المرسلة من مدخلي البيانات</p>
                </div>
                <a href="center_manager_dashboard.php" class="back-btn">
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

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-value"><?php echo $stats['total']; ?></div>
                <div class="stat-label">إجمالي الطلبات</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value"><?php echo $stats['pending']; ?></div>
                <div class="stat-label">في انتظار الموافقة</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?php echo $stats['approved']; ?></div>
                <div class="stat-label">تمت الموافقة</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-value"><?php echo $stats['rejected']; ?></div>
                <div class="stat-label">تم الرفض</div>
            </div>
        </div>

        <div class="requests-section">
            <h2 class="section-title">
                <i class="fas fa-list"></i>
                طلبات إدخال البيانات
            </h2>

            <?php if (empty($requests)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>لا توجد طلبات</h3>
                <p>لم يتم إرسال أي طلبات من مدخلي البيانات بعد</p>
            </div>
            <?php else: ?>
            <?php foreach ($requests as $request): ?>
            <div class="request-card">
                <div class="request-header">
                    <div class="request-type">
                        <?php
                        $type_names = [
                            'workforce' => 'القوى العاملة',
                            'transfer' => 'النقل والتكليف',
                            'sick_leave' => 'الإجازات المرضية',
                            'leave' => 'الإجازات',
                            'delegation' => 'الإيفاد والابتعاث',
                            'report' => 'التقارير'
                        ];
                        echo $type_names[$request['request_type']] ?? $request['request_type'];
                        ?>
                    </div>
                    <span class="request-status status-<?php echo $request['status']; ?>">
                        <?php
                        $status_names = [
                            'pending' => 'في الانتظار',
                            'approved' => 'تمت الموافقة',
                            'rejected' => 'تم الرفض'
                        ];
                        echo $status_names[$request['status']] ?? $request['status'];
                        ?>
                    </span>
                </div>

                <div class="request-details">
                    <div class="detail-item">
                        <div class="detail-label">مدخل البيانات</div>
                        <div class="detail-value"><?php echo htmlspecialchars($request['data_entry_user_name']); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">تاريخ الإرسال</div>
                        <div class="detail-value"><?php echo date('Y-m-d H:i', strtotime($request['created_at'])); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">المركز</div>
                        <div class="detail-value"><?php echo htmlspecialchars($request['center_name']); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">المستشفى</div>
                        <div class="detail-value"><?php echo htmlspecialchars($request['hospital_name']); ?></div>
                    </div>
                </div>

                <?php if ($request['status'] === 'pending'): ?>
                <div class="request-actions">
                    <button class="btn btn-approve" onclick="openModal(<?php echo $request['id']; ?>, 'approve')">
                        <i class="fas fa-check"></i>
                        موافقة
                    </button>
                    <button class="btn btn-reject" onclick="openModal(<?php echo $request['id']; ?>, 'reject')">
                        <i class="fas fa-times"></i>
                        رفض
                    </button>
                    <button class="btn btn-view" onclick="viewRequest(<?php echo $request['id']; ?>)">
                        <i class="fas fa-eye"></i>
                        عرض التفاصيل
                    </button>
                </div>
                <?php elseif ($request['status'] === 'approved'): ?>
                <div class="request-actions">
                    <span style="color: var(--success-color); font-weight: 600;">
                        <i class="fas fa-check-circle"></i>
                        تمت الموافقة في <?php echo date('Y-m-d H:i', strtotime($request['approved_at'])); ?>
                    </span>
                    <?php if ($request['approval_notes']): ?>
                    <div style="margin-top: 0.5rem; color: var(--gray-600);">
                        <strong>ملاحظات:</strong> <?php echo htmlspecialchars($request['approval_notes']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php elseif ($request['status'] === 'rejected'): ?>
                <div class="request-actions">
                    <span style="color: var(--danger-color); font-weight: 600;">
                        <i class="fas fa-times-circle"></i>
                        تم الرفض في <?php echo date('Y-m-d H:i', strtotime($request['approved_at'])); ?>
                    </span>
                    <?php if ($request['rejected_reason']): ?>
                    <div style="margin-top: 0.5rem; color: var(--gray-600);">
                        <strong>سبب الرفض:</strong> <?php echo htmlspecialchars($request['rejected_reason']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal للموافقة/الرفض -->
    <div id="approvalModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">موافقة على الطلب</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" id="approvalForm">
                <input type="hidden" name="action" id="actionInput">
                <input type="hidden" name="request_id" id="requestIdInput">
                
                <div class="form-group">
                    <label class="form-label" for="notes">
                        <span id="notesLabel">ملاحظات الموافقة</span>
                    </label>
                    <textarea 
                        name="notes" 
                        id="notes" 
                        class="form-textarea" 
                        placeholder="أدخل ملاحظاتك هنا..."
                        required
                    ></textarea>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn" onclick="closeModal()" style="background: var(--gray-400);">
                        إلغاء
                    </button>
                    <button type="submit" class="btn" id="submitBtn">
                        <i class="fas fa-check"></i>
                        تأكيد
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(requestId, action) {
            const modal = document.getElementById('approvalModal');
            const actionInput = document.getElementById('actionInput');
            const requestIdInput = document.getElementById('requestIdInput');
            const modalTitle = document.getElementById('modalTitle');
            const notesLabel = document.getElementById('notesLabel');
            const submitBtn = document.getElementById('submitBtn');
            const notes = document.getElementById('notes');

            actionInput.value = action;
            requestIdInput.value = requestId;

            if (action === 'approve') {
                modalTitle.textContent = 'موافقة على الطلب';
                notesLabel.textContent = 'ملاحظات الموافقة (اختياري)';
                submitBtn.innerHTML = '<i class="fas fa-check"></i> موافقة';
                submitBtn.className = 'btn btn-approve';
                notes.placeholder = 'أدخل ملاحظاتك حول الموافقة...';
                notes.required = false;
            } else {
                modalTitle.textContent = 'رفض الطلب';
                notesLabel.textContent = 'سبب الرفض';
                submitBtn.innerHTML = '<i class="fas fa-times"></i> رفض';
                submitBtn.className = 'btn btn-reject';
                notes.placeholder = 'أدخل سبب رفض الطلب...';
                notes.required = true;
            }

            modal.style.display = 'flex';
            notes.focus();
        }

        function closeModal() {
            document.getElementById('approvalModal').style.display = 'none';
            document.getElementById('notes').value = '';
        }

        function viewRequest(requestId) {
            // يمكن إضافة نافذة منبثقة لعرض تفاصيل الطلب
            alert('عرض تفاصيل الطلب رقم: ' + requestId);
        }

        // إغلاق النافذة عند النقر خارجها
        window.onclick = function(event) {
            const modal = document.getElementById('approvalModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // تأثيرات تفاعلية
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .request-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>

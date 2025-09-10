<?php
session_start();
require_once 'config/database.php';
require_once 'config/config.php';

// التحقق من صلاحيات المستخدم
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != ROLE_SUPER_ADMIN) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// معالجة إرسال النموذج
if ($_POST) {
    try {
        $pdo->beginTransaction();
        
        // إضافة المركز
        $center_name = $_POST['center_name'];
        $center_code = $_POST['center_code'];
        $center_description = $_POST['center_description'];
        $hospital_id = $_POST['hospital_id'];
        
        $stmt = $pdo->prepare("INSERT INTO centers (hospital_id, name, code, description, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$hospital_id, $center_name, $center_code, $center_description]);
        $center_id = $pdo->lastInsertId();
        
        // إضافة مدير المركز
        $manager_username = $_POST['manager_username'];
        $manager_password = password_hash($_POST['manager_password'], PASSWORD_DEFAULT);
        $manager_full_name = $_POST['manager_full_name'];
        $manager_email = $_POST['manager_email'];
        $manager_phone = $_POST['manager_phone'];
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, email, phone, role_id, center_id, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$manager_username, $manager_password, $manager_full_name, $manager_email, $manager_phone, ROLE_CENTER_MANAGER, $center_id]);
        
        // إضافة مدخلي البيانات
        for ($i = 1; $i <= 2; $i++) {
            $data_entry_username = $_POST["data_entry_{$i}_username"];
            $data_entry_password = password_hash($_POST["data_entry_{$i}_password"], PASSWORD_DEFAULT);
            $data_entry_full_name = $_POST["data_entry_{$i}_full_name"];
            $data_entry_email = $_POST["data_entry_{$i}_email"];
            $data_entry_phone = $_POST["data_entry_{$i}_phone"];
            
            if (!empty($data_entry_username)) {
                $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name, email, phone, role_id, center_id, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())");
                $stmt->execute([$data_entry_username, $data_entry_password, $data_entry_full_name, $data_entry_email, $data_entry_phone, ROLE_DATA_ENTRY, $center_id]);
            }
        }
        
        $pdo->commit();
        $message = "تم إضافة المركز والمستخدمين بنجاح!";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "حدث خطأ: " . $e->getMessage();
    }
}

// جلب المستشفيات
$hospitals = $pdo->query("SELECT * FROM hospitals ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة مركز ومستخدمين جدد</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e40af;
            --success-color: #059669;
            --warning-color: #d97706;
            --danger-color: #dc2626;
            --info-color: #0891b2;
            --light-bg: #f8fafc;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-900: #111827;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --border-radius: 12px;
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
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--info-color) 100%);
            color: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            text-align: center;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .header p {
            font-size: 1.25rem;
            opacity: 0.9;
        }

        .form-container {
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            border: 1px solid var(--gray-200);
        }

        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid var(--gray-200);
            border-radius: var(--border-radius);
            background: var(--gray-50);
        }

        .form-section h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.875rem;
            border: 2px solid var(--gray-200);
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            background: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            background: #1e3a8a;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-success {
            background: var(--success-color);
        }

        .btn-success:hover {
            background: #047857;
        }

        .btn-secondary {
            background: var(--gray-500);
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .alert {
            padding: 1rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
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

        .back-btn {
            background: var(--gray-500);
            color: var(--white);
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .back-btn:hover {
            background: #4b5563;
            transform: translateY(-2px);
        }

        .required {
            color: var(--danger-color);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">
            <i class="fas fa-arrow-right"></i>
            العودة للوحة التحكم
        </a>

        <div class="header">
            <h1><i class="fas fa-plus-circle"></i> إضافة مركز ومستخدمين جدد</h1>
            <p>إضافة مركز جديد مع مديره ومدخلي البيانات</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-container">
            <!-- معلومات المركز -->
            <div class="form-section">
                <h3><i class="fas fa-building"></i> معلومات المركز</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="hospital_id">المستشفى <span class="required">*</span></label>
                        <select name="hospital_id" id="hospital_id" required>
                            <option value="">اختر المستشفى</option>
                            <?php foreach ($hospitals as $hospital): ?>
                                <option value="<?php echo $hospital['id']; ?>"><?php echo htmlspecialchars($hospital['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="center_name">اسم المركز <span class="required">*</span></label>
                        <input type="text" name="center_name" id="center_name" required placeholder="مثال: مركز القلب">
                    </div>
                    <div class="form-group">
                        <label for="center_code">رمز المركز <span class="required">*</span></label>
                        <input type="text" name="center_code" id="center_code" required placeholder="مثال: KAMC_CARDIO">
                    </div>
                    <div class="form-group">
                        <label for="center_description">وصف المركز</label>
                        <textarea name="center_description" id="center_description" placeholder="وصف مختصر للمركز"></textarea>
                    </div>
                </div>
            </div>

            <!-- مدير المركز -->
            <div class="form-section">
                <h3><i class="fas fa-user-tie"></i> مدير المركز</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="manager_username">اسم المستخدم <span class="required">*</span></label>
                        <input type="text" name="manager_username" id="manager_username" required placeholder="مثال: center_manager_kamc_cardio">
                    </div>
                    <div class="form-group">
                        <label for="manager_password">كلمة المرور <span class="required">*</span></label>
                        <input type="password" name="manager_password" id="manager_password" required placeholder="كلمة مرور قوية">
                    </div>
                    <div class="form-group">
                        <label for="manager_full_name">الاسم الكامل <span class="required">*</span></label>
                        <input type="text" name="manager_full_name" id="manager_full_name" required placeholder="مثال: مدير مركز القلب">
                    </div>
                    <div class="form-group">
                        <label for="manager_email">البريد الإلكتروني <span class="required">*</span></label>
                        <input type="email" name="manager_email" id="manager_email" required placeholder="مثال: manager@cardio.sa">
                    </div>
                    <div class="form-group">
                        <label for="manager_phone">رقم الهاتف <span class="required">*</span></label>
                        <input type="tel" name="manager_phone" id="manager_phone" required placeholder="مثال: 0501234567">
                    </div>
                </div>
            </div>

            <!-- مدخل البيانات الأول -->
            <div class="form-section">
                <h3><i class="fas fa-keyboard"></i> مدخل البيانات الأول</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="data_entry_1_username">اسم المستخدم <span class="required">*</span></label>
                        <input type="text" name="data_entry_1_username" id="data_entry_1_username" required placeholder="مثال: data_entry_kamc_cardio_1">
                    </div>
                    <div class="form-group">
                        <label for="data_entry_1_password">كلمة المرور <span class="required">*</span></label>
                        <input type="password" name="data_entry_1_password" id="data_entry_1_password" required placeholder="كلمة مرور قوية">
                    </div>
                    <div class="form-group">
                        <label for="data_entry_1_full_name">الاسم الكامل <span class="required">*</span></label>
                        <input type="text" name="data_entry_1_full_name" id="data_entry_1_full_name" required placeholder="مثال: مدخل بيانات أول - القلب">
                    </div>
                    <div class="form-group">
                        <label for="data_entry_1_email">البريد الإلكتروني <span class="required">*</span></label>
                        <input type="email" name="data_entry_1_email" id="data_entry_1_email" required placeholder="مثال: data1@cardio.sa">
                    </div>
                    <div class="form-group">
                        <label for="data_entry_1_phone">رقم الهاتف <span class="required">*</span></label>
                        <input type="tel" name="data_entry_1_phone" id="data_entry_1_phone" required placeholder="مثال: 0501234568">
                    </div>
                </div>
            </div>

            <!-- مدخل البيانات الثاني -->
            <div class="form-section">
                <h3><i class="fas fa-keyboard"></i> مدخل البيانات الثاني</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="data_entry_2_username">اسم المستخدم <span class="required">*</span></label>
                        <input type="text" name="data_entry_2_username" id="data_entry_2_username" required placeholder="مثال: data_entry_kamc_cardio_2">
                    </div>
                    <div class="form-group">
                        <label for="data_entry_2_password">كلمة المرور <span class="required">*</span></label>
                        <input type="password" name="data_entry_2_password" id="data_entry_2_password" required placeholder="كلمة مرور قوية">
                    </div>
                    <div class="form-group">
                        <label for="data_entry_2_full_name">الاسم الكامل <span class="required">*</span></label>
                        <input type="text" name="data_entry_2_full_name" id="data_entry_2_full_name" required placeholder="مثال: مدخل بيانات ثاني - القلب">
                    </div>
                    <div class="form-group">
                        <label for="data_entry_2_email">البريد الإلكتروني <span class="required">*</span></label>
                        <input type="email" name="data_entry_2_email" id="data_entry_2_email" required placeholder="مثال: data2@cardio.sa">
                    </div>
                    <div class="form-group">
                        <label for="data_entry_2_phone">رقم الهاتف <span class="required">*</span></label>
                        <input type="tel" name="data_entry_2_phone" id="data_entry_2_phone" required placeholder="مثال: 0501234569">
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i>
                    حفظ واعتماد
                </button>
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                    إعادة تعيين
                </button>
            </div>
        </form>
    </div>

    <script>
        // إضافة تأثيرات تفاعلية
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input, select, textarea');
            
            // تأثير التركيز
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                    this.parentElement.style.transition = 'transform 0.2s ease';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
            
            // التحقق من صحة البيانات
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.style.borderColor = 'var(--danger-color)';
                        isValid = false;
                    } else {
                        field.style.borderColor = 'var(--gray-200)';
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('يرجى ملء جميع الحقول المطلوبة');
                }
            });
        });
    </script>
</body>
</html>

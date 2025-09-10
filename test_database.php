<?php
/**
 * اختبار الاتصال بقاعدة البيانات
 * Database Connection Test
 */

echo "<h1>اختبار الاتصال بقاعدة البيانات</h1>";

// إعدادات قاعدة البيانات
$host = 'localhost';
$port = '3307';
$db_name = 'health_staff_management';
$username = 'health_staff_user';
$password = 'HealthStaff2024!';

try {
    echo "<p>محاولة الاتصال بقاعدة البيانات...</p>";
    
    $dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<p style='color: green;'>✅ تم الاتصال بقاعدة البيانات بنجاح!</p>";
    
    // اختبار الجداول
    echo "<h2>فحص الجداول:</h2>";
    
    $tables = ['users', 'data_entry_users', 'roles', 'hospitals', 'centers'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $count = $stmt->fetch()['count'];
            echo "<p>✅ جدول $table: $count سجل</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ جدول $table: " . $e->getMessage() . "</p>";
        }
    }
    
    // اختبار المستخدمين
    echo "<h2>اختبار المستخدمين:</h2>";
    
    // المستخدمين العاديين
    $stmt = $pdo->query("SELECT username, full_name, role_id FROM users LIMIT 5");
    $users = $stmt->fetchAll();
    
    echo "<h3>المستخدمين العاديين:</h3>";
    foreach ($users as $user) {
        echo "<p>• {$user['username']} - {$user['full_name']} (دور: {$user['role_id']})</p>";
    }
    
    // مدخلي البيانات
    $stmt = $pdo->query("SELECT username, full_name FROM data_entry_users LIMIT 5");
    $data_entry_users = $stmt->fetchAll();
    
    echo "<h3>مدخلي البيانات:</h3>";
    foreach ($data_entry_users as $user) {
        echo "<p>• {$user['username']} - {$user['full_name']}</p>";
    }
    
    // اختبار تسجيل الدخول
    echo "<h2>اختبار تسجيل الدخول:</h2>";
    
    $test_username = 'SUPER_ADMIN_001';
    $test_password = 'password';
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$test_username]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "<p>✅ تم العثور على المستخدم: {$user['username']}</p>";
        
        if (password_verify($test_password, $user['password_hash'])) {
            echo "<p style='color: green;'>✅ كلمة المرور صحيحة!</p>";
        } else {
            echo "<p style='color: red;'>❌ كلمة المرور غير صحيحة!</p>";
            echo "<p>كلمة المرور المشفرة: " . substr($user['password_hash'], 0, 20) . "...</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ لم يتم العثور على المستخدم: $test_username</p>";
    }
    
    // اختبار مدخلي البيانات
    $test_de_username = 'DE_KAMC_001_A';
    
    $stmt = $pdo->prepare("SELECT * FROM data_entry_users WHERE username = ?");
    $stmt->execute([$test_de_username]);
    $de_user = $stmt->fetch();
    
    if ($de_user) {
        echo "<p>✅ تم العثور على مدخل البيانات: {$de_user['username']}</p>";
        
        if (password_verify($test_password, $de_user['password_hash'])) {
            echo "<p style='color: green;'>✅ كلمة المرور صحيحة!</p>";
        } else {
            echo "<p style='color: red;'>❌ كلمة المرور غير صحيحة!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ لم يتم العثور على مدخل البيانات: $test_de_username</p>";
    }
    
    echo "<h2>✅ جميع الاختبارات مكتملة!</h2>";
    echo "<p><a href='simple_login.php'>جرب تسجيل الدخول الآن</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ خطأ في الاتصال: " . $e->getMessage() . "</p>";
    
    echo "<h2>خطوات استكشاف الأخطاء:</h2>";
    echo "<ol>";
    echo "<li>تأكد من تشغيل XAMPP</li>";
    echo "<li>تأكد من تشغيل MySQL على المنفذ 3307</li>";
    echo "<li>تأكد من وجود قاعدة البيانات 'health_staff_management'</li>";
    echo "<li>تأكد من صحة بيانات الاتصال</li>";
    echo "<li>شغل ملف FIX_LOGIN_PASSWORDS.sql في phpMyAdmin</li>";
    echo "</ol>";
}
?>

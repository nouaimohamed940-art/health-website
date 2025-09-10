<?php
/**
 * اختبار تسجيل الدخول البسيط
 * Simple Login Test
 */

// بدء الجلسة
session_start();

// إعدادات قاعدة البيانات
$host = 'localhost';
$port = '3307';
$db_name = 'health_staff_management';
$username = 'health_staff_user';
$password = 'HealthStaff2024!';

$message = '';
$test_results = [];

echo "<h1>اختبار تسجيل الدخول</h1>";

try {
    // الاتصال بقاعدة البيانات
    $dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "<p style='color: green;'>✅ تم الاتصال بقاعدة البيانات بنجاح</p>";
    
    // اختبار المستخدمين
    $test_users = [
        'SUPER_ADMIN_001' => 'password',
        'HOSP_MGR_KAMC_001' => 'password',
        'CTR_MGR_KAMC_001' => 'password',
        'DE_KAMC_001_A' => 'password'
    ];
    
    echo "<h2>اختبار المستخدمين العاديين:</h2>";
    
    foreach ($test_users as $test_username => $test_password) {
        if ($test_username === 'DE_KAMC_001_A') continue; // تخطي مدخل البيانات في هذا الاختبار
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$test_username]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "<p>✅ تم العثور على المستخدم: {$user['username']}</p>";
            
            if (password_verify($test_password, $user['password_hash'])) {
                echo "<p style='color: green;'>✅ كلمة المرور صحيحة!</p>";
                $test_results[$test_username] = 'SUCCESS';
            } else {
                echo "<p style='color: red;'>❌ كلمة المرور غير صحيحة!</p>";
                $test_results[$test_username] = 'FAILED';
            }
        } else {
            echo "<p style='color: red;'>❌ لم يتم العثور على المستخدم: $test_username</p>";
            $test_results[$test_username] = 'NOT_FOUND';
        }
        echo "<hr>";
    }
    
    // اختبار مدخلي البيانات
    echo "<h2>اختبار مدخلي البيانات:</h2>";
    
    $stmt = $pdo->prepare("SELECT * FROM data_entry_users WHERE username = ?");
    $stmt->execute(['DE_KAMC_001_A']);
    $de_user = $stmt->fetch();
    
    if ($de_user) {
        echo "<p>✅ تم العثور على مدخل البيانات: {$de_user['username']}</p>";
        
        if (password_verify('password', $de_user['password_hash'])) {
            echo "<p style='color: green;'>✅ كلمة المرور صحيحة!</p>";
            $test_results['DE_KAMC_001_A'] = 'SUCCESS';
        } else {
            echo "<p style='color: red;'>❌ كلمة المرور غير صحيحة!</p>";
            $test_results['DE_KAMC_001_A'] = 'FAILED';
        }
    } else {
        echo "<p style='color: red;'>❌ لم يتم العثور على مدخل البيانات: DE_KAMC_001_A</p>";
        $test_results['DE_KAMC_001_A'] = 'NOT_FOUND';
    }
    
    // ملخص النتائج
    echo "<h2>ملخص النتائج:</h2>";
    $success_count = 0;
    $total_count = count($test_results);
    
    foreach ($test_results as $username => $result) {
        $status = '';
        $color = '';
        
        switch ($result) {
            case 'SUCCESS':
                $status = 'نجح';
                $color = 'green';
                $success_count++;
                break;
            case 'FAILED':
                $status = 'فشل - كلمة مرور خاطئة';
                $color = 'red';
                break;
            case 'NOT_FOUND':
                $status = 'فشل - مستخدم غير موجود';
                $color = 'red';
                break;
        }
        
        echo "<p style='color: $color;'>• $username: $status</p>";
    }
    
    echo "<h3>النتيجة النهائية: $success_count من $total_count نجح</h3>";
    
    if ($success_count == $total_count) {
        echo "<p style='color: green; font-size: 1.2em; font-weight: bold;'>🎉 جميع الاختبارات نجحت! يمكنك الآن تسجيل الدخول من صفحة login.php</p>";
        echo "<p><a href='login.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>جرب تسجيل الدخول الآن</a></p>";
    } else {
        echo "<p style='color: red; font-size: 1.2em; font-weight: bold;'>❌ بعض الاختبارات فشلت. يرجى تشغيل ملف FINAL_PASSWORD_FIX.sql أولاً</p>";
        echo "<p><a href='FINAL_PASSWORD_FIX.sql' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>تحميل ملف إصلاح كلمات المرور</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ خطأ في الاتصال: " . $e->getMessage() . "</p>";
    echo "<p>تأكد من:</p>";
    echo "<ul>";
    echo "<li>تشغيل XAMPP</li>";
    echo "<li>تشغيل MySQL على المنفذ 3307</li>";
    echo "<li>وجود قاعدة البيانات 'health_staff_management'</li>";
    echo "<li>تشغيل ملف FINAL_PASSWORD_FIX.sql في phpMyAdmin</li>";
    echo "</ul>";
}
?>

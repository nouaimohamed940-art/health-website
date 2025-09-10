<?php
/**
 * اختبار الاتصال بقاعدة البيانات
 * Database Connection Test
 */

require_once 'config/database.php';

echo "<h2>اختبار الاتصال بقاعدة البيانات</h2>";
echo "<p>البورت المستخدم: 3307</p>";

try {
    $db = new Database();
    echo "<p style='color: green;'>✅ تم الاتصال بقاعدة البيانات بنجاح!</p>";
    
    // اختبار استعلام بسيط
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p>عدد المستخدمين في قاعدة البيانات: " . $result['count'] . "</p>";
    
    // اختبار استعلام الأدوار
    $stmt = $db->query("SELECT id, name, display_name FROM roles ORDER BY id");
    $roles = $stmt->fetchAll();
    
    echo "<h3>الأدوار المتاحة:</h3>";
    echo "<ul>";
    foreach ($roles as $role) {
        echo "<li>ID: {$role['id']} - {$role['display_name']} ({$role['name']})</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ فشل في الاتصال بقاعدة البيانات:</p>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
    
    echo "<h3>خطوات استكشاف الأخطاء:</h3>";
    echo "<ol>";
    echo "<li>تأكد من تشغيل XAMPP</li>";
    echo "<li>تأكد من تشغيل MySQL على البورت 3307</li>";
    echo "<li>تأكد من إنشاء قاعدة البيانات 'health_staff_management'</li>";
    echo "<li>تأكد من إنشاء المستخدم 'health_staff_user'</li>";
    echo "<li>تحقق من كلمة المرور 'HealthStaff2024!'</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<p><a href='login.php'>العودة لصفحة تسجيل الدخول</a></p>";
?>

<?php
/**
 * صفحة تسجيل الخروج
 * Logout Page
 */

require_once 'config/config.php';

// تدمير الجلسة
session_destroy();

// إعادة التوجيه لصفحة تسجيل الدخول
redirect('/login.php?logout=1');
?>
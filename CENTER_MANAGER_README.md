# نظام إدارة مديري المراكز (38 شخص)
## Center Manager Management System (38 People)

---

## 📋 **نظرة عامة على النظام**

هذا النظام مخصص لـ **38 مدير مركز** في نظام إدارة القوى العاملة الصحية. كل مدير مركز لديه صلاحيات محددة لإدارة مركزه فقط.

---

## 🎯 **الوظائف الأساسية لمدير المركز**

### **1. إدارة القوى العاملة**
- تحديث عدد الموظفين في المركز
- تسجيل الموظفين النشطين وغير النشطين
- تتبع المعينين الجدد والمستقيلين
- عرض إحصائيات شاملة للقوى العاملة

### **2. إدارة التنقلات والتكليفات**
- تسجيل التنقلات الوظيفية
- تسجيل التكليفات الخاصة
- تسجيل الترقيات والتنزيلات
- تتبع حالة الطلبات (معلق/معتمد/مرفوض)

### **3. إدارة الغياب المرضي**
- تسجيل تكرار الغياب المرضي لكل موظف
- تتبع عدد أيام الغياب
- إدارة الشهادات الطبية
- إحصائيات شهرية وسنوية

### **4. إدارة الإجازات**
- تسجيل جميع أنواع الإجازات:
  - إجازات استثنائية
  - إجازات أمومة
  - إجازات سنوية
  - إجازات مرضية
  - إجازات طارئة
  - إجازات دراسية
  - إجازات حج وعمرة
- تتبع حالة الإجازات
- إدارة الشهادات الطبية

### **5. إدارة الإيفاد والابتعاث**
- تسجيل طلبات الإيفاد
- تسجيل طلبات الابتعاث
- تسجيل التدريبات والمؤتمرات
- تتبع التكاليف ومصادر التمويل
- إدارة ورش العمل

### **6. إدارة التقارير المركزية**
- إنشاء تقارير يومية
- إنشاء تقارير أسبوعية
- إنشاء تقارير شهرية
- جمع الإحصائيات تلقائياً
- إضافة الإنجازات والتحديات والتوصيات

---

## 🗄️ **قاعدة البيانات**

### **الجداول الرئيسية:**

#### **1. center_workforce** - القوى العاملة
```sql
- id (Primary Key)
- center_id (Foreign Key)
- total_employees (إجمالي الموظفين)
- active_employees (الموظفين النشطين)
- inactive_employees (الموظفين غير النشطين)
- new_hires_this_month (المعينين هذا الشهر)
- resignations_this_month (المستقيلين هذا الشهر)
- last_updated (آخر تحديث)
- updated_by (تم التحديث بواسطة)
```

#### **2. employee_transfers** - التنقلات والتكليفات
```sql
- id (Primary Key)
- center_id (Foreign Key)
- employee_name (اسم الموظف)
- employee_id (رقم الموظف)
- current_position (المنصب الحالي)
- new_position (المنصب الجديد)
- transfer_type (نوع التنقل)
- transfer_date (تاريخ التنقل)
- reason (السبب)
- status (الحالة)
- created_by (تم الإنشاء بواسطة)
```

#### **3. sick_leave_records** - سجلات الغياب المرضي
```sql
- id (Primary Key)
- center_id (Foreign Key)
- employee_name (اسم الموظف)
- sick_leave_days (أيام الغياب)
- sick_leave_occurrences (عدد المرات)
- medical_certificate_required (يتطلب شهادة طبية)
- medical_certificate_provided (تم تقديم الشهادة)
- last_sick_leave_date (تاريخ آخر غياب)
- total_sick_days_this_year (إجمالي الأيام هذا العام)
```

#### **4. detailed_leaves** - الإجازات التفصيلية
```sql
- id (Primary Key)
- center_id (Foreign Key)
- employee_name (اسم الموظف)
- leave_type (نوع الإجازة)
- start_date (تاريخ البداية)
- end_date (تاريخ النهاية)
- total_days (إجمالي الأيام)
- remaining_leave_days (الأيام المتبقية)
- status (الحالة)
- medical_certificate_required (يتطلب شهادة طبية)
- approval_required (يتطلب موافقة)
```

#### **5. delegations_scholarships** - الإيفاد والابتعاث
```sql
- id (Primary Key)
- center_id (Foreign Key)
- employee_name (اسم الموظف)
- type (النوع: إيفاد/ابتعاث/تدريب/مؤتمر/ورشة)
- destination (الوجهة)
- purpose (الغرض)
- start_date (تاريخ البداية)
- end_date (تاريخ النهاية)
- duration_days (مدة الأيام)
- cost (التكلفة)
- funding_source (مصدر التمويل)
- status (الحالة)
```

#### **6. center_reports** - التقارير المركزية
```sql
- id (Primary Key)
- center_id (Foreign Key)
- report_type (نوع التقرير: يومي/أسبوعي/شهري)
- report_date (تاريخ التقرير)
- report_title (عنوان التقرير)
- report_content (محتوى التقرير)
- workforce_summary (ملخص القوى العاملة - JSON)
- transfers_summary (ملخص التنقلات - JSON)
- sick_leave_summary (ملخص الغياب المرضي - JSON)
- leaves_summary (ملخص الإجازات - JSON)
- delegations_summary (ملخص الإيفاد والابتعاث - JSON)
- achievements (الإنجازات)
- challenges (التحديات)
- recommendations (التوصيات)
- status (الحالة)
```

#### **7. center_monthly_stats** - الإحصائيات الشهرية
```sql
- id (Primary Key)
- center_id (Foreign Key)
- year (السنة)
- month (الشهر)
- total_workforce (إجمالي القوى العاملة)
- new_hires (المعينين الجدد)
- resignations (المستقيلين)
- sick_leave_days (أيام الغياب المرضي)
- exceptional_leaves (الإجازات الاستثنائية)
- maternity_leaves (إجازات الأمومة)
- annual_leaves (الإجازات السنوية)
- delegations_count (عدد الإيفادات)
- scholarships_count (عدد الابتعاثات)
- attendance_rate (معدل الحضور)
- productivity_score (درجة الإنتاجية)
```

#### **8. center_attachments** - المرفقات
```sql
- id (Primary Key)
- center_id (Foreign Key)
- related_table (الجدول المرتبط)
- related_id (معرف السجل المرتبط)
- file_name (اسم الملف)
- file_path (مسار الملف)
- file_type (نوع الملف)
- file_size (حجم الملف)
- description (الوصف)
- uploaded_by (تم الرفع بواسطة)
```

---

## 🔧 **إعداد النظام**

### **1. إنشاء قاعدة البيانات**
```bash
# تشغيل ملف SQL
mysql -u root -p < center_manager_tables.sql
```

### **2. إعداد المستخدمين**
```sql
-- إنشاء مستخدم مديري المراكز
CREATE USER 'center_manager_user'@'localhost' IDENTIFIED BY 'CenterManager2024!';

-- منح الصلاحيات
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.center_workforce TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.employee_transfers TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.sick_leave_records TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.detailed_leaves TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.delegations_scholarships TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.center_reports TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.center_monthly_stats TO 'center_manager_user'@'localhost';
GRANT SELECT, INSERT, UPDATE, DELETE ON health_staff_management.center_attachments TO 'center_manager_user'@'localhost';
```

### **3. إعداد الملفات**
- `center_manager_dashboard.php` - لوحة التحكم الرئيسية
- `workforce_management.php` - إدارة القوى العاملة
- `transfers_management.php` - إدارة التنقلات
- `sick_leave_management.php` - إدارة الغياب المرضي
- `leaves_management.php` - إدارة الإجازات
- `delegations_management.php` - إدارة الإيفاد والابتعاث
- `reports_management.php` - إدارة التقارير

---

## 🚀 **كيفية الاستخدام**

### **1. تسجيل الدخول**
```
http://localhost/mostaqil/login.php
```

### **2. الوصول للوحة التحكم**
- بعد تسجيل الدخول، سيتم توجيه مديري المراكز تلقائياً إلى:
```
http://localhost/mostaqil/center_manager_dashboard.php
```

### **3. المهام المتاحة**
1. **إدارة القوى العاملة** - تحديث أعداد الموظفين
2. **التنقلات والتكليفات** - تسجيل التنقلات والترقيات
3. **الغياب المرضي** - تتبع الغياب المرضي
4. **الإجازات** - إدارة جميع أنواع الإجازات
5. **الإيفاد والابتعاث** - تسجيل طلبات الإيفاد والابتعاث
6. **التقارير** - إنشاء التقارير المركزية

---

## 📊 **الإحصائيات المتاحة**

### **في لوحة التحكم الرئيسية:**
- إجمالي القوى العاملة
- الموظفين النشطين
- التنقلات المعلقة
- الإجازات المعلقة
- أيام الغياب المرضي
- طلبات الإيفاد والابتعاث
- الإجازات الاستثنائية
- إجازات الأمومة

### **في كل صفحة إدارة:**
- إحصائيات مفصلة حسب النوع
- رسوم بيانية تفاعلية
- تقارير قابلة للتصدير

---

## 🔒 **الأمان والصلاحيات**

### **صلاحيات مدير المركز:**
- ✅ عرض بيانات مركزه فقط
- ✅ إدخال وتعديل بيانات مركزه
- ✅ إنشاء التقارير لمركزه
- ✅ تصدير البيانات
- ❌ عرض بيانات مراكز أخرى
- ❌ تعديل إعدادات النظام
- ❌ إدارة المستخدمين

### **حماية البيانات:**
- تشفير كلمات المرور
- التحقق من الصلاحيات
- حماية من SQL Injection
- تشفير البيانات الحساسة

---

## 📱 **التصميم المتجاوب**

- متوافق مع جميع الأجهزة
- تصميم احترافي للشركات الدولية
- دعم اللغة العربية (RTL)
- ألوان متناسقة ومهنية
- تأثيرات بصرية متقدمة

---

## 🛠️ **التقنيات المستخدمة**

### **الواجهة الأمامية:**
- HTML5
- CSS3 (متقدم مع متغيرات)
- JavaScript (ES6+)
- Font Awesome (الأيقونات)
- تصميم متجاوب

### **الخلفية:**
- PHP 7.4+
- MySQL 8.0+
- PDO (قاعدة البيانات)
- JSON (تخزين البيانات المعقدة)

### **الأمان:**
- تشفير كلمات المرور
- حماية من SQL Injection
- التحقق من الصلاحيات
- تنظيف البيانات

---

## 📈 **المميزات المتقدمة**

### **1. جمع البيانات التلقائي**
- يتم جمع الإحصائيات تلقائياً من البيانات المسجلة
- تحديث فوري للتقارير
- دقة عالية في البيانات

### **2. التقارير الذكية**
- تقارير يومية/أسبوعية/شهرية
- جمع تلقائي للإحصائيات
- تصدير بصيغ مختلفة

### **3. واجهة مستخدم متقدمة**
- تصميم احترافي
- تأثيرات بصرية
- سهولة الاستخدام
- دعم كامل للعربية

### **4. نظام إشعارات**
- تنبيهات للطلبات المعلقة
- تذكيرات للمهام المهمة
- إشعارات النظام

---

## 🔄 **التحديثات المستقبلية**

### **المخطط إضافتها:**
- نظام إشعارات متقدم
- تقارير تفاعلية أكثر
- دعم المرفقات
- نظام الموافقات
- تطبيق جوال
- API للتكامل

---

## 📞 **الدعم الفني**

### **للمساعدة:**
- مراجعة هذا الدليل
- فحص ملفات السجل
- التواصل مع فريق التطوير

### **ملفات السجل:**
- `error.log` - أخطاء النظام
- `access.log` - سجل الوصول
- `database.log` - أخطاء قاعدة البيانات

---

## ✅ **قائمة التحقق**

### **قبل التشغيل:**
- [ ] إنشاء قاعدة البيانات
- [ ] تشغيل ملف SQL
- [ ] إعداد المستخدمين
- [ ] رفع الملفات
- [ ] اختبار الاتصال
- [ ] اختبار تسجيل الدخول

### **بعد التشغيل:**
- [ ] اختبار جميع الوظائف
- [ ] فحص الأمان
- [ ] اختبار الأداء
- [ ] تدريب المستخدمين
- [ ] النسخ الاحتياطي

---

## 🎉 **الخلاصة**

هذا النظام يوفر حلاً شاملاً ومتطوراً لإدارة مديري المراكز (38 شخص) في نظام إدارة القوى العاملة الصحية. النظام مصمم ليكون:

- **دقيق جداً** - كما طلبت
- **احترافي** - للشركات الدولية
- **آمن** - مع حماية كاملة للبيانات
- **سهل الاستخدام** - واجهة بديهية
- **متكامل** - مع النظام الرئيسي

**النظام جاهز للاستخدام فوراً!** 🚀

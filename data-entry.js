// Data Entry JavaScript - نظام إدخال البيانات
class DataEntry {
    constructor() {
        this.recentEntries = [];
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeForms();
        this.loadRecentEntries();
        this.hideLoadingScreen();
    }

    setupEventListeners() {
        // Employee form
        const employeeForm = document.getElementById('employeeForm');
        if (employeeForm) {
            employeeForm.addEventListener('submit', (e) => this.handleEmployeeSubmit(e));
        }

        // Movement form
        const movementForm = document.getElementById('movementForm');
        if (movementForm) {
            movementForm.addEventListener('submit', (e) => this.handleMovementSubmit(e));
        }

        // File upload
        const uploadArea = document.getElementById('uploadArea');
        const bulkFile = document.getElementById('bulkFile');
        
        if (uploadArea && bulkFile) {
            uploadArea.addEventListener('click', () => bulkFile.click());
            uploadArea.addEventListener('dragover', (e) => this.handleDragOver(e));
            uploadArea.addEventListener('drop', (e) => this.handleDrop(e));
            bulkFile.addEventListener('change', (e) => this.handleFileSelect(e));
        }

        // Form validation
        this.setupFormValidation();
    }

    initializeForms() {
        // Set current date
        const today = new Date().toISOString().split('T')[0];
        const dateInputs = ['movementDate', 'leaveStartDate', 'leaveEndDate'];
        dateInputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.value = today;
            }
        });

        // Set default department
        const fromDepartment = document.getElementById('fromDepartment');
        if (fromDepartment) {
            fromDepartment.value = 'مركز الطب الباطني';
        }
    }

    setupFormValidation() {
        // Real-time validation
        const inputs = document.querySelectorAll('input[required], select[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const isValid = value !== '';
        
        if (!isValid) {
            this.showFieldError(field, 'هذا الحقل مطلوب');
        } else {
            this.clearFieldError(field);
        }
        
        return isValid;
    }

    showFieldError(field, message) {
        this.clearFieldError(field);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        
        field.parentNode.appendChild(errorDiv);
        field.classList.add('error');
    }

    clearFieldError(field) {
        const errorDiv = field.parentNode.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
        field.classList.remove('error');
    }

    handleEmployeeSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        // Validate form
        if (!this.validateEmployeeForm(data)) {
            return;
        }
        
        // Simulate saving employee data
        this.showNotification('جاري حفظ بيانات الموظف...', 'info');
        
        setTimeout(() => {
            // Add to recent entries
            this.addRecentEntry({
                type: 'employee',
                date: new Date().toLocaleDateString('ar-SA'),
                employee: data.fullName,
                movement: 'إضافة موظف جديد',
                status: 'saved'
            });
            
            this.showNotification('تم حفظ بيانات الموظف بنجاح', 'success');
            e.target.reset();
        }, 1500);
    }

    validateEmployeeForm(data) {
        const requiredFields = ['employeeId', 'fullName', 'jobTitle', 'department'];
        
        for (const field of requiredFields) {
            if (!data[field] || data[field].trim() === '') {
                this.showNotification(`يرجى ملء حقل ${this.getFieldLabel(field)}`, 'error');
                return false;
            }
        }
        
        // Validate employee ID format
        if (!/^\d{6}$/.test(data.employeeId)) {
            this.showNotification('رقم الموظف يجب أن يكون 6 أرقام', 'error');
            return false;
        }
        
        // Validate email if provided
        if (data.email && !this.isValidEmail(data.email)) {
            this.showNotification('البريد الإلكتروني غير صحيح', 'error');
            return false;
        }
        
        return true;
    }

    handleMovementSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        // Validate form
        if (!this.validateMovementForm(data)) {
            return;
        }
        
        // Simulate saving movement data
        this.showNotification('جاري حفظ بيانات الحركة...', 'info');
        
        setTimeout(() => {
            // Add to recent entries
            this.addRecentEntry({
                type: 'movement',
                date: new Date().toLocaleDateString('ar-SA'),
                employee: data.employeeId || 'غير محدد',
                movement: this.getMovementTypeLabel(data.movementType),
                status: 'pending'
            });
            
            this.showNotification('تم حفظ بيانات الحركة بنجاح', 'success');
            e.target.reset();
            this.toggleMovementFields();
        }, 1500);
    }

    validateMovementForm(data) {
        const requiredFields = ['movementType', 'movementDate'];
        
        for (const field of requiredFields) {
            if (!data[field] || data[field].trim() === '') {
                this.showNotification(`يرجى ملء حقل ${this.getFieldLabel(field)}`, 'error');
                return false;
            }
        }
        
        // Validate specific fields based on movement type
        if (data.movementType === 'leave') {
            if (!data.leaveType || !data.leaveDuration) {
                this.showNotification('يرجى ملء جميع حقول الإجازة', 'error');
                return false;
            }
        }
        
        if (data.movementType === 'transfer') {
            if (!data.toDepartment) {
                this.showNotification('يرجى اختيار القسم الجديد', 'error');
                return false;
            }
        }
        
        if (data.movementType === 'delegation') {
            if (!data.delegationType || !data.delegationDestination) {
                this.showNotification('يرجى ملء جميع حقول الإيفاد', 'error');
                return false;
            }
        }
        
        return true;
    }

    getFieldLabel(fieldName) {
        const labels = {
            employeeId: 'رقم الموظف',
            fullName: 'الاسم الكامل',
            jobTitle: 'المسمى الوظيفي',
            department: 'القسم',
            movementType: 'نوع الحركة',
            movementDate: 'تاريخ الحركة',
            leaveType: 'نوع الإجازة',
            leaveDuration: 'مدة الإجازة',
            toDepartment: 'القسم الجديد',
            delegationType: 'نوع الإيفاد',
            delegationDestination: 'الوجهة'
        };
        return labels[fieldName] || fieldName;
    }

    getMovementTypeLabel(type) {
        const types = {
            attendance: 'حضور',
            absence: 'غياب',
            leave: 'إجازة',
            transfer: 'تنقل',
            delegation: 'إيفاد',
            assignment: 'تكليف',
            scholarship: 'ابتعاث'
        };
        return types[type] || type;
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    toggleMovementFields() {
        const movementType = document.getElementById('movementType').value;
        const fields = document.querySelectorAll('.movement-fields');
        
        // Hide all fields
        fields.forEach(field => {
            field.style.display = 'none';
        });
        
        // Show relevant fields
        switch (movementType) {
            case 'leave':
                document.getElementById('leaveFields').style.display = 'block';
                break;
            case 'transfer':
                document.getElementById('transferFields').style.display = 'block';
                break;
            case 'delegation':
                document.getElementById('delegationFields').style.display = 'block';
                break;
        }
    }

    handleDragOver(e) {
        e.preventDefault();
        e.currentTarget.classList.add('drag-over');
    }

    handleDrop(e) {
        e.preventDefault();
        e.currentTarget.classList.remove('drag-over');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            this.processFile(files[0]);
        }
    }

    handleFileSelect(e) {
        const file = e.target.files[0];
        if (file) {
            this.processFile(file);
        }
    }

    processFile(file) {
        // Validate file type
        const allowedTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
            'text/csv'
        ];
        
        if (!allowedTypes.includes(file.type)) {
            this.showNotification('نوع الملف غير مدعوم. يرجى اختيار ملف Excel أو CSV', 'error');
            return;
        }
        
        // Show progress
        this.showUploadProgress();
        
        // Simulate file processing
        this.simulateFileProcessing(file);
    }

    showUploadProgress() {
        const progress = document.getElementById('uploadProgress');
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        
        progress.style.display = 'block';
        
        let percent = 0;
        const interval = setInterval(() => {
            percent += Math.random() * 15;
            if (percent > 100) percent = 100;
            
            progressFill.style.width = percent + '%';
            progressText.textContent = Math.round(percent) + '%';
            
            if (percent >= 100) {
                clearInterval(interval);
                setTimeout(() => {
                    progress.style.display = 'none';
                    this.showNotification('تم رفع الملف بنجاح', 'success');
                }, 500);
            }
        }, 200);
    }

    simulateFileProcessing(file) {
        // In a real application, this would process the actual file
        // For now, we'll just simulate the process
        console.log('Processing file:', file.name);
    }

    downloadTemplate() {
        // Create a sample Excel template
        const templateData = [
            ['رقم الموظف', 'الاسم الكامل', 'المسمى الوظيفي', 'القسم', 'نوع الحركة', 'التاريخ', 'ملاحظات'],
            ['123456', 'أحمد محمد علي', 'طبيب', 'الطب الباطني', 'حضور', '2024-01-15', ''],
            ['123457', 'فاطمة أحمد', 'ممرضة', 'الطوارئ', 'إجازة', '2024-01-15', 'إجازة سنوية']
        ];
        
        // Convert to CSV
        const csvContent = templateData.map(row => row.join(',')).join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', 'data-entry-template.csv');
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        this.showNotification('تم تحميل القالب بنجاح', 'success');
    }

    addRecentEntry(entry) {
        this.recentEntries.unshift(entry);
        
        // Keep only last 10 entries
        if (this.recentEntries.length > 10) {
            this.recentEntries = this.recentEntries.slice(0, 10);
        }
        
        this.updateRecentEntriesTable();
    }

    loadRecentEntries() {
        // Load sample data
        this.recentEntries = [
            {
                type: 'movement',
                date: '2024-01-15',
                employee: 'أحمد محمد علي',
                movement: 'إجازة استثنائية',
                status: 'pending'
            },
            {
                type: 'employee',
                date: '2024-01-15',
                employee: 'فاطمة أحمد',
                movement: 'إضافة موظف جديد',
                status: 'saved'
            },
            {
                type: 'movement',
                date: '2024-01-14',
                employee: 'محمد عبدالله',
                movement: 'تنقل',
                status: 'approved'
            }
        ];
        
        this.updateRecentEntriesTable();
    }

    updateRecentEntriesTable() {
        const tbody = document.getElementById('recentEntriesTable');
        if (!tbody) return;
        
        tbody.innerHTML = this.recentEntries.map(entry => `
            <tr>
                <td>${entry.date}</td>
                <td>${entry.employee}</td>
                <td>${entry.movement}</td>
                <td>
                    <span class="status-badge ${entry.status}">
                        ${this.getStatusLabel(entry.status)}
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-primary" onclick="editEntry('${entry.type}')">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="deleteEntry('${entry.type}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    getStatusLabel(status) {
        const labels = {
            saved: 'محفوظ',
            pending: 'معلق',
            approved: 'معتمد',
            rejected: 'مرفوض'
        };
        return labels[status] || status;
    }

    clearForm(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.reset();
            this.toggleMovementFields();
            
            // Clear any error states
            const errorFields = form.querySelectorAll('.error');
            errorFields.forEach(field => {
                field.classList.remove('error');
            });
            
            const errorMessages = form.querySelectorAll('.field-error');
            errorMessages.forEach(message => {
                message.remove();
            });
        }
    }

    showNotification(message, type = 'info') {
        const notification = document.getElementById('notification');
        if (!notification) return;
        
        const icon = notification.querySelector('.notification-icon');
        const messageEl = notification.querySelector('.notification-message');
        
        messageEl.textContent = message;
        
        const typeConfig = {
            success: { icon: '✓', color: '#10b981' },
            error: { icon: '✗', color: '#ef4444' },
            warning: { icon: '⚠', color: '#f59e0b' },
            info: { icon: 'ℹ', color: '#06b6d4' }
        };
        
        const config = typeConfig[type] || typeConfig.info;
        icon.textContent = config.icon;
        icon.style.background = config.color;
        notification.style.borderRightColor = config.color;
        
        notification.classList.add('show');
        
        setTimeout(() => {
            notification.classList.remove('show');
        }, 5000);
    }

    hideLoadingScreen() {
        setTimeout(() => {
            const loadingScreen = document.getElementById('loading-screen');
            if (loadingScreen) {
                loadingScreen.classList.add('hidden');
                setTimeout(() => {
                    loadingScreen.style.display = 'none';
                }, 500);
            }
        }, 1000);
    }
}

// Global functions
function toggleMovementFields() {
    if (window.dataEntry) {
        window.dataEntry.toggleMovementFields();
    }
}

function clearForm(formId) {
    if (window.dataEntry) {
        window.dataEntry.clearForm(formId);
    }
}

function downloadTemplate() {
    if (window.dataEntry) {
        window.dataEntry.downloadTemplate();
    }
}

function editEntry(type) {
    if (window.dataEntry) {
        window.dataEntry.showNotification('تعديل الإدخال', 'info');
    }
}

function deleteEntry(type) {
    if (confirm('هل أنت متأكد من حذف هذا الإدخال؟')) {
        if (window.dataEntry) {
            window.dataEntry.showNotification('تم حذف الإدخال', 'success');
        }
    }
}

// Initialize data entry when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.dataEntry = new DataEntry();
});

// Handle drag and drop events
document.addEventListener('dragover', (e) => {
    e.preventDefault();
});

document.addEventListener('drop', (e) => {
    e.preventDefault();
});

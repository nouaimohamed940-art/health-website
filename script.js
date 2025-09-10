// Health Staff Management System - JavaScript
class HealthStaffSystem {
    constructor() {
        this.currentPage = 'dashboard';
        this.notificationTimeout = null;
        this.dataUpdateInterval = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeData();
        this.startDataUpdates();
        this.hideLoadingScreen();
    }

    setupEventListeners() {
        // Navigation
        document.querySelectorAll('.nav-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const page = item.dataset.page;
                this.showPage(page);
                this.updateNavigation(item);
            });
        });

        // Data Entry Form
        const dataForm = document.getElementById('dataEntryForm');
        if (dataForm) {
            dataForm.addEventListener('submit', (e) => this.handleDataEntry(e));
        }

        // Reports Form
        const reportsForm = document.getElementById('reportsForm');
        if (reportsForm) {
            reportsForm.addEventListener('submit', (e) => this.handleReports(e));
        }

        // Widget buttons
        document.querySelectorAll('.widget-button').forEach(button => {
            button.addEventListener('click', (e) => this.handleWidgetAction(e));
        });

        // Export buttons
        document.querySelectorAll('.export-btn').forEach(button => {
            button.addEventListener('click', (e) => this.handleExport(e));
        });

        // View all activities button
        const viewAllBtn = document.querySelector('.view-all-btn');
        if (viewAllBtn) {
            viewAllBtn.addEventListener('click', () => this.showAllActivities());
        }

        // Set current date for date inputs
        this.setCurrentDate();
    }

    initializeData() {
        // Initialize with sample data
        this.sampleData = {
            totalEmployees: 245,
            attendance: 230,
            absence: 15,
            transfers: 5,
            sickLeave: 7,
            activeDelegations: 12,
            newScholarships: 3,
            exceptionalLeaves: 8,
            maternityLeaves: 3,
            annualLeaves: 12,
            activities: [
                {
                    id: 1,
                    name: 'أحمد محمد علي',
                    type: 'إجازة استثنائية',
                    status: 'pending',
                    time: 'منذ ساعتين',
                    avatar: 'أ'
                },
                {
                    id: 2,
                    name: 'فاطمة أحمد',
                    type: 'تنقل إلى قسم الطوارئ',
                    status: 'approved',
                    time: 'منذ 3 ساعات',
                    avatar: 'ف'
                },
                {
                    id: 3,
                    name: 'محمد عبدالله',
                    type: 'غياب مرضي',
                    status: 'approved',
                    time: 'اليوم',
                    avatar: 'م'
                }
            ]
        };
    }

    setCurrentDate() {
        const today = new Date().toISOString().split('T')[0];
        const dateInputs = ['date', 'dateFrom', 'dateTo'];
        dateInputs.forEach(id => {
            const input = document.getElementById(id);
            if (input) {
                input.value = today;
            }
        });
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
        }, 2000);
    }

    showPage(pageId) {
        // Hide all pages
        document.querySelectorAll('.page').forEach(page => {
            page.classList.remove('active');
        });

        // Show selected page
        const targetPage = document.getElementById(pageId);
        if (targetPage) {
            targetPage.classList.add('active');
            this.currentPage = pageId;
            
            // Page-specific initialization
            this.initializePage(pageId);
        }
    }

    updateNavigation(activeItem) {
        // Remove active class from all nav items
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });

        // Add active class to clicked item
        activeItem.classList.add('active');
    }

    initializePage(pageId) {
        switch (pageId) {
            case 'dashboard':
                this.updateDashboardData();
                break;
            case 'data-entry':
                this.initializeDataEntry();
                break;
            case 'approval':
                this.initializeApproval();
                break;
            case 'reports':
                this.initializeReports();
                break;
            case 'users':
                this.initializeUsers();
                break;
        }
    }

    updateDashboardData() {
        // Update summary cards with real-time data
        this.updateSummaryCards();
        this.updateWidgets();
        this.updateActivities();
    }

    updateSummaryCards() {
        const data = this.sampleData;
        
        // Update attendance percentage
        const attendancePercentage = ((data.attendance / data.totalEmployees) * 100).toFixed(1);
        const absencePercentage = ((data.absence / data.totalEmployees) * 100).toFixed(1);
        
        // Update DOM elements
        this.updateElement('.summary-card:nth-child(2) .card-number', data.attendance);
        this.updateElement('.summary-card:nth-child(2) .card-subtitle', `${attendancePercentage}% من إجمالي الموظفين`);
        this.updateElement('.summary-card:nth-child(3) .card-number', data.absence);
        this.updateElement('.summary-card:nth-child(3) .card-subtitle', `${absencePercentage}% من إجمالي الموظفين`);
    }

    updateWidgets() {
        const data = this.sampleData;
        
        // Update sick leave widget
        this.updateElement('.sick-leave-widget .widget-number', data.sickLeave);
        
        // Update delegation widget
        this.updateElement('.delegation-widget .widget-number', data.activeDelegations);
        this.updateElement('.delegation-widget .detail-item:first-child .detail-number', data.activeDelegations);
        this.updateElement('.delegation-widget .detail-item:last-child .detail-number', data.newScholarships);
        
        // Update leaves widget
        this.updateElement('.leaves-widget .leave-type:nth-child(1) .leave-number', data.exceptionalLeaves);
        this.updateElement('.leaves-widget .leave-type:nth-child(2) .leave-number', data.maternityLeaves);
        this.updateElement('.leaves-widget .leave-type:nth-child(3) .leave-number', data.annualLeaves);
    }

    updateActivities() {
        const activitiesList = document.querySelector('.activities-list');
        if (!activitiesList) return;

        activitiesList.innerHTML = '';
        
        this.sampleData.activities.forEach(activity => {
            const activityElement = this.createActivityElement(activity);
            activitiesList.appendChild(activityElement);
        });
    }

    createActivityElement(activity) {
        const div = document.createElement('div');
        div.className = 'activity-item';
        div.innerHTML = `
            <div class="activity-avatar">
                <span>${activity.avatar}</span>
            </div>
            <div class="activity-content">
                <div class="activity-name">${activity.name}</div>
                <div class="activity-type">${activity.type}</div>
            </div>
            <div class="activity-status">
                <span class="status-badge ${activity.status}">
                    <i class="fas fa-${activity.status === 'pending' ? 'clock' : 'check'}"></i>
                    ${activity.status === 'pending' ? 'معلق' : 'معتمد'}
                </span>
                <span class="activity-time">${activity.time}</span>
            </div>
        `;
        return div;
    }

    updateElement(selector, value) {
        const element = document.querySelector(selector);
        if (element) {
            element.textContent = value;
        }
    }

    startDataUpdates() {
        // Update data every 30 seconds
        this.dataUpdateInterval = setInterval(() => {
            if (this.currentPage === 'dashboard') {
                this.simulateDataChange();
                this.updateDashboardData();
            }
        }, 30000);
    }

    simulateDataChange() {
        // Simulate small changes in data
        const data = this.sampleData;
        
        // Randomly change attendance by ±2
        const attendanceChange = Math.floor(Math.random() * 5) - 2;
        data.attendance = Math.max(220, Math.min(250, data.attendance + attendanceChange));
        data.absence = data.totalEmployees - data.attendance;
        
        // Randomly change sick leave by ±1
        const sickLeaveChange = Math.floor(Math.random() * 3) - 1;
        data.sickLeave = Math.max(5, Math.min(10, data.sickLeave + sickLeaveChange));
        
        // Randomly change transfers by ±1
        const transferChange = Math.floor(Math.random() * 3) - 1;
        data.transfers = Math.max(3, Math.min(8, data.transfers + transferChange));
    }

    handleDataEntry(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        // Validate form
        if (!this.validateForm(data)) {
            return;
        }
        
        // Simulate saving data
        this.showNotification('تم حفظ البيانات بنجاح! سيتم مراجعتها من قبل المشرف.', 'success');
        
        // Add to activities
        this.addNewActivity(data);
        
        // Reset form
        e.target.reset();
        this.setCurrentDate();
    }

    validateForm(data) {
        const requiredFields = ['fullName', 'jobTitle', 'movementType', 'date'];
        
        for (const field of requiredFields) {
            if (!data[field] || data[field].trim() === '') {
                this.showNotification(`يرجى ملء حقل ${this.getFieldLabel(field)}`, 'error');
                return false;
            }
        }
        
        return true;
    }

    getFieldLabel(fieldName) {
        const labels = {
            fullName: 'الاسم الكامل',
            jobTitle: 'المسمى الوظيفي',
            movementType: 'نوع الحركة',
            date: 'التاريخ'
        };
        return labels[fieldName] || fieldName;
    }

    addNewActivity(data) {
        const newActivity = {
            id: Date.now(),
            name: data.fullName,
            type: this.getMovementTypeLabel(data.movementType),
            status: 'pending',
            time: 'الآن',
            avatar: data.fullName.charAt(0)
        };
        
        this.sampleData.activities.unshift(newActivity);
        
        // Keep only last 10 activities
        if (this.sampleData.activities.length > 10) {
            this.sampleData.activities = this.sampleData.activities.slice(0, 10);
        }
        
        if (this.currentPage === 'dashboard') {
            this.updateActivities();
        }
    }

    getMovementTypeLabel(type) {
        const types = {
            absence: 'غياب',
            leave: 'إجازة',
            delegation: 'إيفاد',
            assignment: 'تكليف',
            scholarship: 'ابتعاث'
        };
        return types[type] || type;
    }

    handleReports(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        this.showNotification('تم إنشاء التقرير بنجاح!', 'success');
        
        // Simulate report generation
        setTimeout(() => {
            this.showNotification('التقرير جاهز للتحميل', 'info');
        }, 2000);
    }

    handleWidgetAction(e) {
        const button = e.target.closest('.widget-button');
        if (!button) return;
        
        const widget = button.closest('.widget');
        const widgetTitle = widget.querySelector('.widget-title').textContent;
        
        this.showNotification(`عرض تفاصيل ${widgetTitle}`, 'info');
    }

    handleExport(e) {
        const button = e.target.closest('.export-btn');
        if (!button) return;
        
        const reportType = button.textContent.trim();
        this.showNotification(`جاري تصدير ${reportType}...`, 'info');
        
        // Simulate export process
        setTimeout(() => {
            this.showNotification(`تم تصدير ${reportType} بنجاح!`, 'success');
        }, 2000);
    }

    showAllActivities() {
        this.showNotification('عرض جميع الأنشطة', 'info');
        // Here you would typically navigate to a detailed activities page
    }

    // Approval functions
    approveRequest(id) {
        if (confirm('هل أنت متأكد من اعتماد هذا الطلب؟')) {
            this.updateRequestStatus(id, 'approved');
            this.showNotification('تم اعتماد الطلب بنجاح!', 'success');
        }
    }

    rejectRequest(id) {
        const comment = prompt('يرجى إدخال سبب الرفض:');
        if (comment && comment.trim() !== '') {
            this.updateRequestStatus(id, 'rejected');
            this.showNotification('تم رفض الطلب', 'warning');
        }
    }

    updateRequestStatus(id, status) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (!row) return;
        
        const statusCell = row.querySelector('.status-badge');
        const actionCell = row.querySelector('.action-buttons');
        
        if (statusCell) {
            statusCell.className = `status-badge ${status}`;
            statusCell.innerHTML = `
                <i class="fas fa-${status === 'approved' ? 'check' : 'times'}"></i>
                ${status === 'approved' ? 'معتمد' : 'مرفوض'}
            `;
        }
        
        if (actionCell) {
            actionCell.innerHTML = `
                <span class="status-badge ${status}">
                    ${status === 'approved' ? 'تم الاعتماد' : 'تم الرفض'}
                </span>
            `;
        }
    }

    // Export functions
    exportToExcel() {
        this.showNotification('جاري تصدير التقرير إلى Excel...', 'info');
        setTimeout(() => {
            this.showNotification('تم تصدير التقرير إلى Excel بنجاح!', 'success');
        }, 2000);
    }

    exportToPDF() {
        this.showNotification('جاري تصدير التقرير إلى PDF...', 'info');
        setTimeout(() => {
            this.showNotification('تم تصدير التقرير إلى PDF بنجاح!', 'success');
        }, 2000);
    }

    // Notification system
    showNotification(message, type = 'info') {
        const notification = document.getElementById('notification');
        if (!notification) return;
        
        const icon = notification.querySelector('.notification-icon');
        const messageEl = notification.querySelector('.notification-message');
        
        // Set message
        messageEl.textContent = message;
        
        // Set icon and color based on type
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
        
        // Show notification
        notification.classList.add('show');
        
        // Hide after 5 seconds
        clearTimeout(this.notificationTimeout);
        this.notificationTimeout = setTimeout(() => {
            notification.classList.remove('show');
        }, 5000);
    }

    // Page-specific initialization methods
    initializeDataEntry() {
        // Set focus to first input
        const firstInput = document.querySelector('#data-entry input');
        if (firstInput) {
            firstInput.focus();
        }
    }

    initializeApproval() {
        // Add data attributes to table rows for easier identification
        const rows = document.querySelectorAll('#approval tbody tr');
        rows.forEach((row, index) => {
            row.setAttribute('data-id', index + 1);
        });
    }

    initializeReports() {
        // Set default date range (last 30 days)
        const today = new Date();
        const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
        
        const dateFrom = document.getElementById('dateFrom');
        const dateTo = document.getElementById('dateTo');
        
        if (dateFrom) dateFrom.value = thirtyDaysAgo.toISOString().split('T')[0];
        if (dateTo) dateTo.value = today.toISOString().split('T')[0];
    }

    initializeUsers() {
        // Add any user-specific initialization
        console.log('Users page initialized');
    }

    // Utility methods
    formatDate(date) {
        return new Date(date).toLocaleDateString('ar-SA');
    }

    formatTime(date) {
        return new Date(date).toLocaleTimeString('ar-SA', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Cleanup method
    destroy() {
        if (this.dataUpdateInterval) {
            clearInterval(this.dataUpdateInterval);
        }
        if (this.notificationTimeout) {
            clearTimeout(this.notificationTimeout);
        }
    }
}

// Global functions for HTML onclick handlers
function showPage(pageId) {
    if (window.healthStaffSystem) {
        window.healthStaffSystem.showPage(pageId);
    }
}

function approveRequest(id) {
    if (window.healthStaffSystem) {
        window.healthStaffSystem.approveRequest(id);
    }
}

function rejectRequest(id) {
    if (window.healthStaffSystem) {
        window.healthStaffSystem.rejectRequest(id);
    }
}

function exportToExcel() {
    if (window.healthStaffSystem) {
        window.healthStaffSystem.exportToExcel();
    }
}

function exportToPDF() {
    if (window.healthStaffSystem) {
        window.healthStaffSystem.exportToPDF();
    }
}

function exportReport(type) {
    if (window.healthStaffSystem) {
        window.healthStaffSystem.handleExport({ target: { closest: () => ({ textContent: `تقرير ${type}` }) } });
    }
}

// Initialize the system when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.healthStaffSystem = new HealthStaffSystem();
});

// Handle page visibility change to pause/resume updates
document.addEventListener('visibilitychange', () => {
    if (window.healthStaffSystem) {
        if (document.hidden) {
            // Page is hidden, pause updates
            if (window.healthStaffSystem.dataUpdateInterval) {
                clearInterval(window.healthStaffSystem.dataUpdateInterval);
            }
        } else {
            // Page is visible, resume updates
            window.healthStaffSystem.startDataUpdates();
        }
    }
});

// Handle window resize for responsive adjustments
window.addEventListener('resize', () => {
    // Add any responsive adjustments here
    const isMobile = window.innerWidth < 768;
    document.body.classList.toggle('mobile', isMobile);
});

// Handle keyboard shortcuts
document.addEventListener('keydown', (e) => {
    // Ctrl/Cmd + number keys for quick navigation
    if ((e.ctrlKey || e.metaKey) && e.key >= '1' && e.key <= '5') {
        e.preventDefault();
        const pages = ['dashboard', 'data-entry', 'approval', 'reports', 'users'];
        const pageIndex = parseInt(e.key) - 1;
        if (pages[pageIndex] && window.healthStaffSystem) {
            window.healthStaffSystem.showPage(pages[pageIndex]);
        }
    }
    
    // Escape key to close notifications
    if (e.key === 'Escape') {
        const notification = document.getElementById('notification');
        if (notification) {
            notification.classList.remove('show');
        }
    }
});

// Service Worker registration for offline support (optional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = HealthStaffSystem;
}

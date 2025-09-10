// Dashboard JavaScript - نظام لوحة التحكم
class Dashboard {
    constructor() {
        this.charts = {};
        this.autoRefresh = true;
        this.refreshInterval = null;
        this.data = {
            totalWorkforce: 245,
            exceptionalLeaves: 8,
            maternityLeaves: 3,
            assignments: 12,
            sickLeave: 7,
            delegations: 15,
            attendance: {
                present: 230,
                absent: 15,
                late: 5
            }
        };
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeCharts();
        this.loadDashboardData();
        this.startAutoRefresh();
        this.hideLoadingScreen();
    }

    setupEventListeners() {
        // Chart period change
        const chartPeriod = document.getElementById('chart-period');
        if (chartPeriod) {
            chartPeriod.addEventListener('change', () => this.updateChart());
        }

        // Window resize for charts
        window.addEventListener('resize', () => this.resizeCharts());

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));
    }

    initializeCharts() {
        this.initAttendanceChart();
        this.initLeavesChart();
        this.initCentersChart();
    }

    initAttendanceChart() {
        const element = document.getElementById('attendanceChart');
        if (!element) return;

        const options = {
            series: [{
                name: 'الحضور',
                data: [95, 97, 93, 98, 96, 94, 92]
            }, {
                name: 'الغياب',
                data: [5, 3, 7, 2, 4, 6, 8]
            }],
            chart: {
                type: 'area',
                height: 300,
                fontFamily: 'Cairo, Inter, sans-serif',
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#10b981', '#ef4444'],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1,
                    stops: [0, 100]
                }
            },
            xaxis: {
                categories: ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
                labels: {
                    style: {
                        fontFamily: 'Cairo, Inter, sans-serif',
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                min: 0,
                max: 100,
                labels: {
                    style: {
                        fontFamily: 'Cairo, Inter, sans-serif',
                        fontSize: '12px'
                    },
                    formatter: function(value) {
                        return value + '%';
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontFamily: 'Cairo, Inter, sans-serif',
                fontSize: '14px',
                fontWeight: 600
            },
            tooltip: {
                theme: 'light',
                style: {
                    fontFamily: 'Cairo, Inter, sans-serif'
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4
            }
        };

        this.charts.attendance = new ApexCharts(element, options);
        this.charts.attendance.render();
    }

    initLeavesChart() {
        const element = document.getElementById('leavesChart');
        if (!element) return;

        const options = {
            series: [8, 3, 12, 5],
            chart: {
                type: 'donut',
                height: 250,
                fontFamily: 'Cairo, Inter, sans-serif',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            labels: ['إجازات استثنائية', 'إجازات أمومة', 'إجازات سنوية', 'إجازات مرضية'],
            colors: ['#f59e0b', '#ec4899', '#10b981', '#ef4444'],
            dataLabels: {
                enabled: true,
                style: {
                    fontFamily: 'Cairo, Inter, sans-serif',
                    fontSize: '12px',
                    fontWeight: 600
                },
                formatter: function(val, opts) {
                    return opts.w.config.series[opts.seriesIndex] + ' (' + val.toFixed(1) + '%)';
                }
            },
            legend: {
                position: 'bottom',
                fontFamily: 'Cairo, Inter, sans-serif',
                fontSize: '12px',
                fontWeight: 500
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'إجمالي الإجازات',
                                fontFamily: 'Cairo, Inter, sans-serif',
                                fontSize: '14px',
                                fontWeight: 600,
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            tooltip: {
                theme: 'light',
                style: {
                    fontFamily: 'Cairo, Inter, sans-serif'
                }
            }
        };

        this.charts.leaves = new ApexCharts(element, options);
        this.charts.leaves.render();
    }

    initCentersChart() {
        const element = document.getElementById('centersChart');
        if (!element) return;

        const options = {
            series: [{
                name: 'معدل الحضور',
                data: [94, 96, 89, 92, 88, 95, 91, 93, 87, 90]
            }],
            chart: {
                type: 'bar',
                height: 250,
                fontFamily: 'Cairo, Inter, sans-serif',
                toolbar: {
                    show: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800
                }
            },
            colors: ['#3b82f6'],
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return val + '%';
                },
                style: {
                    fontFamily: 'Cairo, Inter, sans-serif',
                    fontSize: '11px',
                    fontWeight: 600
                }
            },
            xaxis: {
                categories: [
                    'مركز الطب الباطني', 'مركز الطوارئ', 'مركز الجراحة', 
                    'مركز طب الأطفال', 'مركز أمراض القلب', 'مركز الأشعة',
                    'مركز المختبر', 'مركز الصيدلة', 'مركز التمريض', 'مركز الإدارة'
                ],
                labels: {
                    style: {
                        fontFamily: 'Cairo, Inter, sans-serif',
                        fontSize: '10px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontFamily: 'Cairo, Inter, sans-serif',
                        fontSize: '10px'
                    }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4
            },
            tooltip: {
                theme: 'light',
                style: {
                    fontFamily: 'Cairo, Inter, sans-serif'
                }
            }
        };

        this.charts.centers = new ApexCharts(element, options);
        this.charts.centers.render();
    }

    loadDashboardData() {
        this.updateKPICards();
        this.loadActivityFeed();
        this.updateSystemStatus();
    }

    updateKPICards() {
        // Update KPI numbers with animation
        this.animateNumber('total-workforce', this.data.totalWorkforce);
        this.animateNumber('exceptional-leaves', this.data.exceptionalLeaves);
        this.animateNumber('maternity-leaves', this.data.maternityLeaves);
        this.animateNumber('assignments', this.data.assignments);
        this.animateNumber('sick-leave', this.data.sickLeave);
        this.animateNumber('delegations', this.data.delegations);
    }

    animateNumber(elementId, targetValue) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const startValue = parseInt(element.textContent) || 0;
        const duration = 1000;
        const startTime = performance.now();

        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const currentValue = Math.floor(startValue + (targetValue - startValue) * progress);
            element.textContent = currentValue;

            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };

        requestAnimationFrame(animate);
    }

    loadActivityFeed() {
        const feed = document.getElementById('activity-feed');
        if (!feed) return;

        const activities = [
            {
                id: 1,
                type: 'leave',
                user: 'أحمد محمد علي',
                action: 'طلب إجازة استثنائية',
                time: 'منذ 5 دقائق',
                status: 'pending',
                avatar: 'أ'
            },
            {
                id: 2,
                type: 'transfer',
                user: 'فاطمة أحمد',
                action: 'تم اعتماد طلب التنقل',
                time: 'منذ 15 دقيقة',
                status: 'approved',
                avatar: 'ف'
            },
            {
                id: 3,
                type: 'delegation',
                user: 'محمد عبدالله',
                action: 'طلب إيفاد جديد',
                time: 'منذ 30 دقيقة',
                status: 'pending',
                avatar: 'م'
            },
            {
                id: 4,
                type: 'sick',
                user: 'سارة أحمد',
                action: 'تقرير غياب مرضي',
                time: 'منذ ساعة',
                status: 'approved',
                avatar: 'س'
            }
        ];

        feed.innerHTML = activities.map(activity => this.createActivityItem(activity)).join('');
    }

    createActivityItem(activity) {
        const statusClass = activity.status === 'approved' ? 'approved' : 'pending';
        const typeIcon = this.getActivityIcon(activity.type);
        
        return `
            <div class="activity-item ${statusClass}">
                <div class="activity-avatar">
                    <span>${activity.avatar}</span>
                </div>
                <div class="activity-content">
                    <div class="activity-user">${activity.user}</div>
                    <div class="activity-action">
                        <i class="${typeIcon}"></i>
                        ${activity.action}
                    </div>
                </div>
                <div class="activity-time">${activity.time}</div>
            </div>
        `;
    }

    getActivityIcon(type) {
        const icons = {
            leave: 'fas fa-calendar-alt',
            transfer: 'fas fa-exchange-alt',
            delegation: 'fas fa-paper-plane',
            sick: 'fas fa-heartbeat'
        };
        return icons[type] || 'fas fa-info-circle';
    }

    updateSystemStatus() {
        // Simulate system status checks
        const statusItems = document.querySelectorAll('.status-indicator');
        statusItems.forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('pulse');
                setTimeout(() => {
                    item.classList.remove('pulse');
                }, 1000);
            }, index * 200);
        });
    }

    updateChart() {
        const period = document.getElementById('chart-period').value;
        
        // Simulate data update based on period
        let newData;
        switch (period) {
            case 'week':
                newData = {
                    categories: ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'],
                    attendance: [95, 97, 93, 98, 96, 94, 92],
                    absence: [5, 3, 7, 2, 4, 6, 8]
                };
                break;
            case 'month':
                newData = {
                    categories: ['الأسبوع 1', 'الأسبوع 2', 'الأسبوع 3', 'الأسبوع 4'],
                    attendance: [94, 96, 93, 95],
                    absence: [6, 4, 7, 5]
                };
                break;
            case 'quarter':
                newData = {
                    categories: ['الشهر 1', 'الشهر 2', 'الشهر 3'],
                    attendance: [95, 94, 96],
                    absence: [5, 6, 4]
                };
                break;
        }

        if (this.charts.attendance && newData) {
            this.charts.attendance.updateOptions({
                xaxis: {
                    categories: newData.categories
                },
                series: [{
                    name: 'الحضور',
                    data: newData.attendance
                }, {
                    name: 'الغياب',
                    data: newData.absence
                }]
            });
        }
    }

    updateKPIPeriod() {
        const period = document.querySelector('.period-selector').value;
        this.showNotification(`تم تحديث المؤشرات للفترة: ${period}`, 'info');
        // Here you would typically update the KPI data based on the selected period
    }

    toggleChartType() {
        if (this.charts.attendance) {
            const currentType = this.charts.attendance.w.config.chart.type;
            const newType = currentType === 'area' ? 'line' : 'area';
            this.charts.attendance.updateOptions({
                chart: {
                    type: newType
                }
            });
        }
    }

    exportChart() {
        if (this.charts.attendance) {
            this.charts.attendance.dataURI().then((uri) => {
                const link = document.createElement('a');
                link.href = uri.imgURI;
                link.download = 'attendance-chart.png';
                link.click();
            });
        }
    }

    refreshCentersChart() {
        if (this.charts.centers) {
            // Simulate data refresh
            const newData = Array.from({length: 10}, () => Math.floor(Math.random() * 20) + 80);
            this.charts.centers.updateSeries([{
                name: 'معدل الحضور',
                data: newData
            }]);
            this.showNotification('تم تحديث بيانات المراكز', 'success');
        }
    }

    toggleLeavesChart() {
        if (this.charts.leaves) {
            const currentType = this.charts.leaves.config.type;
            const newType = currentType === 'doughnut' ? 'pie' : 'doughnut';
            this.charts.leaves.config.type = newType;
            this.charts.leaves.update();
        }
    }

    startAutoRefresh() {
        if (this.autoRefresh) {
            this.refreshInterval = setInterval(() => {
                this.refreshData();
            }, 30000); // Refresh every 30 seconds
        }
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
    }

    refreshData() {
        // Simulate data refresh
        this.data.totalWorkforce += Math.floor(Math.random() * 3) - 1;
        this.data.exceptionalLeaves += Math.floor(Math.random() * 3) - 1;
        this.data.assignments += Math.floor(Math.random() * 3) - 1;
        
        this.updateKPICards();
        this.loadActivityFeed();
        
        this.showNotification('تم تحديث البيانات', 'success');
    }

    refreshDashboard() {
        this.showNotification('جاري تحديث البيانات...', 'info');
        
        setTimeout(() => {
            this.refreshData();
            this.showNotification('تم تحديث لوحة التحكم بنجاح', 'success');
        }, 1500);
    }

    exportDashboard() {
        this.showNotification('جاري تصدير التقرير...', 'info');
        
        setTimeout(() => {
            // Simulate export process
            const data = {
                timestamp: new Date().toISOString(),
                kpis: this.data,
                charts: 'exported'
            };
            
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `dashboard-report-${new Date().toISOString().split('T')[0]}.json`;
            a.click();
            URL.revokeObjectURL(url);
            
            this.showNotification('تم تصدير التقرير بنجاح', 'success');
        }, 2000);
    }

    toggleAutoRefresh() {
        this.autoRefresh = !this.autoRefresh;
        const icon = document.getElementById('refresh-icon');
        
        if (this.autoRefresh) {
            this.startAutoRefresh();
            icon.classList.remove('fa-pause');
            icon.classList.add('fa-sync-alt');
            this.showNotification('تم تفعيل التحديث التلقائي', 'success');
        } else {
            this.stopAutoRefresh();
            icon.classList.remove('fa-sync-alt');
            icon.classList.add('fa-pause');
            this.showNotification('تم إيقاف التحديث التلقائي', 'warning');
        }
    }

    showAllActivities() {
        this.showNotification('عرض جميع النشاطات', 'info');
        // Here you would typically navigate to a detailed activities page
    }

    quickAction(action) {
        const modal = document.getElementById('quick-action-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');
        
        const actions = {
            'add-employee': {
                title: 'إضافة موظف جديد',
                content: this.getAddEmployeeForm()
            },
            'add-leave': {
                title: 'طلب إجازة',
                content: this.getLeaveRequestForm()
            },
            'add-transfer': {
                title: 'طلب تنقل',
                content: this.getTransferRequestForm()
            },
            'add-delegation': {
                title: 'طلب إيفاد',
                content: this.getDelegationRequestForm()
            },
            'generate-report': {
                title: 'تقرير سريع',
                content: this.getQuickReportForm()
            },
            'emergency-contact': {
                title: 'اتصال طوارئ',
                content: this.getEmergencyContactForm()
            }
        };
        
        const actionData = actions[action];
        if (actionData) {
            modalTitle.textContent = actionData.title;
            modalBody.innerHTML = actionData.content;
            modal.style.display = 'flex';
        }
    }

    getAddEmployeeForm() {
        return `
            <form class="quick-form">
                <div class="form-group">
                    <label>الاسم الكامل</label>
                    <input type="text" required>
                </div>
                <div class="form-group">
                    <label>المسمى الوظيفي</label>
                    <input type="text" required>
                </div>
                <div class="form-group">
                    <label>المركز</label>
                    <select required>
                        <option value="">اختر المركز</option>
                        <option value="internal">مركز الطب الباطني</option>
                        <option value="emergency">مركز الطوارئ</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">إضافة</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">إلغاء</button>
                </div>
            </form>
        `;
    }

    getLeaveRequestForm() {
        return `
            <form class="quick-form">
                <div class="form-group">
                    <label>نوع الإجازة</label>
                    <select required>
                        <option value="">اختر نوع الإجازة</option>
                        <option value="annual">سنوية</option>
                        <option value="exceptional">استثنائية</option>
                        <option value="maternity">أمومة</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>من تاريخ</label>
                    <input type="date" required>
                </div>
                <div class="form-group">
                    <label>إلى تاريخ</label>
                    <input type="date" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">إرسال الطلب</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">إلغاء</button>
                </div>
            </form>
        `;
    }

    getTransferRequestForm() {
        return `
            <form class="quick-form">
                <div class="form-group">
                    <label>المركز الحالي</label>
                    <input type="text" readonly value="مركز الطب الباطني">
                </div>
                <div class="form-group">
                    <label>المركز المطلوب</label>
                    <select required>
                        <option value="">اختر المركز</option>
                        <option value="emergency">مركز الطوارئ</option>
                        <option value="surgery">مركز الجراحة</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>سبب التنقل</label>
                    <textarea required></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">إرسال الطلب</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">إلغاء</button>
                </div>
            </form>
        `;
    }

    getDelegationRequestForm() {
        return `
            <form class="quick-form">
                <div class="form-group">
                    <label>نوع الإيفاد</label>
                    <select required>
                        <option value="">اختر نوع الإيفاد</option>
                        <option value="training">تدريب</option>
                        <option value="conference">مؤتمر</option>
                        <option value="scholarship">ابتعاث</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>الوجهة</label>
                    <input type="text" required>
                </div>
                <div class="form-group">
                    <label>مدة الإيفاد</label>
                    <input type="number" min="1" max="365" required>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">إرسال الطلب</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">إلغاء</button>
                </div>
            </form>
        `;
    }

    getQuickReportForm() {
        return `
            <form class="quick-form">
                <div class="form-group">
                    <label>نوع التقرير</label>
                    <select required>
                        <option value="">اختر نوع التقرير</option>
                        <option value="attendance">تقرير الحضور</option>
                        <option value="leaves">تقرير الإجازات</option>
                        <option value="transfers">تقرير التنقلات</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>الفترة</label>
                    <select required>
                        <option value="today">اليوم</option>
                        <option value="week">هذا الأسبوع</option>
                        <option value="month">هذا الشهر</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">إنشاء التقرير</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">إلغاء</button>
                </div>
            </form>
        `;
    }

    getEmergencyContactForm() {
        return `
            <div class="emergency-contacts">
                <h4>أرقام الطوارئ</h4>
                <div class="contact-list">
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>الطوارئ: 911</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-user-md"></i>
                        <span>مدير المركز: 0501234567</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-building"></i>
                        <span>إدارة المستشفى: 0112345678</span>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-primary" onclick="closeModal()">إغلاق</button>
                </div>
            </div>
        `;
    }

    resizeCharts() {
        Object.values(this.charts).forEach(chart => {
            if (chart) {
                chart.resize();
            }
        });
    }

    handleKeyboard(e) {
        // Keyboard shortcuts
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case 'r':
                    e.preventDefault();
                    this.refreshDashboard();
                    break;
                case 'e':
                    e.preventDefault();
                    this.exportDashboard();
                    break;
                case 'a':
                    e.preventDefault();
                    this.toggleAutoRefresh();
                    break;
            }
        }
        
        // Escape key to close modal
        if (e.key === 'Escape') {
            this.closeModal();
        }
    }

    closeModal() {
        const modal = document.getElementById('quick-action-modal');
        modal.style.display = 'none';
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
        }, 2000);
    }
}

// Global functions
function refreshDashboard() {
    if (window.dashboard) {
        window.dashboard.refreshDashboard();
    }
}

function exportDashboard() {
    if (window.dashboard) {
        window.dashboard.exportDashboard();
    }
}

function toggleAutoRefresh() {
    if (window.dashboard) {
        window.dashboard.toggleAutoRefresh();
    }
}

function showAllActivities() {
    if (window.dashboard) {
        window.dashboard.showAllActivities();
    }
}

function quickAction(action) {
    if (window.dashboard) {
        window.dashboard.quickAction(action);
    }
}

function closeModal() {
    if (window.dashboard) {
        window.dashboard.closeModal();
    }
}

function updateChart() {
    if (window.dashboard) {
        window.dashboard.updateChart();
    }
}

function toggleLeavesChart() {
    if (window.dashboard) {
        window.dashboard.toggleLeavesChart();
    }
}

function updateKPIPeriod() {
    if (window.dashboard) {
        window.dashboard.updateKPIPeriod();
    }
}

function toggleChartType() {
    if (window.dashboard) {
        window.dashboard.toggleChartType();
    }
}

function exportChart() {
    if (window.dashboard) {
        window.dashboard.exportChart();
    }
}

function refreshCentersChart() {
    if (window.dashboard) {
        window.dashboard.refreshCentersChart();
    }
}

// Initialize dashboard when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.dashboard = new Dashboard();
});

// Handle page visibility change
document.addEventListener('visibilitychange', () => {
    if (window.dashboard) {
        if (document.hidden) {
            window.dashboard.stopAutoRefresh();
        } else {
            window.dashboard.startAutoRefresh();
        }
    }
});

// Handle window resize
window.addEventListener('resize', () => {
    if (window.dashboard) {
        window.dashboard.resizeCharts();
    }
});

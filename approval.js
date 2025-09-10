// Approval JavaScript - نظام الاعتماد
class ApprovalSystem {
    constructor() {
        this.currentPage = 1;
        this.pageSize = 25;
        this.totalPages = 1;
        this.selectedItems = new Set();
        this.currentRequest = null;
        this.requests = [];
        this.filters = {
            status: '',
            type: '',
            center: '',
            dateFrom: '',
            dateTo: '',
            search: ''
        };
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadApprovalData();
        this.hideLoadingScreen();
    }

    setupEventListeners() {
        // Set current date for date filters
        const today = new Date().toISOString().split('T')[0];
        const dateTo = document.getElementById('date-to');
        if (dateTo) {
            dateTo.value = today;
        }

        // Set date from to 30 days ago
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        const dateFrom = document.getElementById('date-from');
        if (dateFrom) {
            dateFrom.value = thirtyDaysAgo.toISOString().split('T')[0];
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));
    }

    loadApprovalData() {
        // Sample data - in real application, this would come from API
        this.requests = [
            {
                id: 1,
                requestNumber: 'REQ-2024-001',
                employee: {
                    id: 123456,
                    name: 'أحمد محمد علي',
                    department: 'الطب الباطني',
                    center: 'مركز الطب الباطني'
                },
                type: 'leave',
                typeLabel: 'إجازة استثنائية',
                date: '2024-01-15',
                status: 'pending',
                details: {
                    leaveType: 'استثنائية',
                    startDate: '2024-01-20',
                    endDate: '2024-01-22',
                    duration: 2,
                    reason: 'حالة طارئة في العائلة'
                },
                createdBy: 'مدير المركز',
                createdAt: '2024-01-15 10:30:00'
            },
            {
                id: 2,
                requestNumber: 'REQ-2024-002',
                employee: {
                    id: 123457,
                    name: 'فاطمة أحمد محمد',
                    department: 'الطوارئ',
                    center: 'مركز الطوارئ'
                },
                type: 'transfer',
                typeLabel: 'تنقل',
                date: '2024-01-16',
                status: 'pending',
                details: {
                    fromDepartment: 'الطوارئ',
                    toDepartment: 'الجراحة',
                    reason: 'تطوير المهارات المهنية'
                },
                createdBy: 'مدير المركز',
                createdAt: '2024-01-16 14:20:00'
            },
            {
                id: 3,
                requestNumber: 'REQ-2024-003',
                employee: {
                    id: 123458,
                    name: 'محمد عبدالله السعد',
                    department: 'الجراحة',
                    center: 'مركز الجراحة'
                },
                type: 'delegation',
                typeLabel: 'إيفاد',
                date: '2024-01-17',
                status: 'approved',
                details: {
                    delegationType: 'مؤتمر طبي',
                    destination: 'دبي، الإمارات العربية المتحدة',
                    startDate: '2024-02-01',
                    endDate: '2024-02-05',
                    duration: 4,
                    cost: 15000
                },
                createdBy: 'مدير المركز',
                createdAt: '2024-01-17 09:15:00',
                approvedBy: 'مدير المستشفى',
                approvedAt: '2024-01-18 11:30:00'
            },
            {
                id: 4,
                requestNumber: 'REQ-2024-004',
                employee: {
                    id: 123459,
                    name: 'سارة أحمد حسن',
                    department: 'طب الأطفال',
                    center: 'مركز طب الأطفال'
                },
                type: 'leave',
                typeLabel: 'إجازة أمومة',
                date: '2024-01-18',
                status: 'rejected',
                details: {
                    leaveType: 'أمومة',
                    startDate: '2024-02-01',
                    endDate: '2024-05-01',
                    duration: 90,
                    reason: 'إجازة أمومة'
                },
                createdBy: 'مدير المركز',
                createdAt: '2024-01-18 16:45:00',
                rejectedBy: 'مدير المستشفى',
                rejectedAt: '2024-01-19 10:20:00',
                rejectionReason: 'عدم توفر بديل مناسب في الوقت الحالي'
            },
            {
                id: 5,
                requestNumber: 'REQ-2024-005',
                employee: {
                    id: 123460,
                    name: 'خالد محمد العلي',
                    department: 'أمراض القلب',
                    center: 'مركز أمراض القلب'
                },
                type: 'assignment',
                typeLabel: 'تكليف',
                date: '2024-01-19',
                status: 'pending',
                details: {
                    assignmentType: 'مهمة خاصة',
                    description: 'مشاركة في لجنة تقييم الأداء',
                    startDate: '2024-01-25',
                    endDate: '2024-01-30',
                    duration: 5
                },
                createdBy: 'مدير المركز',
                createdAt: '2024-01-19 13:10:00'
            }
        ];

        this.updateStatistics();
        this.renderTable();
    }

    updateStatistics() {
        const stats = {
            pending: this.requests.filter(r => r.status === 'pending').length,
            approved: this.requests.filter(r => r.status === 'approved').length,
            rejected: this.requests.filter(r => r.status === 'rejected').length,
            total: this.requests.length
        };

        this.updateElement('pending-count', stats.pending);
        this.updateElement('approved-count', stats.approved);
        this.updateElement('rejected-count', stats.rejected);
        this.updateElement('total-count', stats.total);
    }

    renderTable() {
        const tbody = document.getElementById('approvals-tbody');
        if (!tbody) return;

        const filteredRequests = this.getFilteredRequests();
        const paginatedRequests = this.getPaginatedRequests(filteredRequests);

        tbody.innerHTML = paginatedRequests.map(request => this.createRequestRow(request)).join('');

        this.updatePagination(filteredRequests.length);
        this.updateBulkActions();
    }

    createRequestRow(request) {
        const statusClass = request.status === 'pending' ? 'pending' : 
                           request.status === 'approved' ? 'approved' : 'rejected';
        
        const statusLabel = request.status === 'pending' ? 'في انتظار الاعتماد' :
                           request.status === 'approved' ? 'معتمد' : 'مرفوض';

        return `
            <tr class="request-row ${statusClass}" data-id="${request.id}">
                <td>
                    <input type="checkbox" class="request-checkbox" value="${request.id}" 
                           onchange="toggleRequestSelection(${request.id})" 
                           ${request.status !== 'pending' ? 'disabled' : ''}>
                </td>
                <td>
                    <span class="request-number">${request.requestNumber}</span>
                </td>
                <td>
                    <div class="employee-info">
                        <div class="employee-avatar">${request.employee.name.charAt(0)}</div>
                        <div class="employee-details">
                            <div class="employee-name">${request.employee.name}</div>
                            <div class="employee-id">ID: ${request.employee.id}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="request-type ${request.type}">${request.typeLabel}</span>
                </td>
                <td>${request.employee.center}</td>
                <td>${this.formatDate(request.date)}</td>
                <td>
                    <span class="status-badge ${statusClass}">
                        <i class="fas fa-${this.getStatusIcon(request.status)}"></i>
                        ${statusLabel}
                    </span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-info" onclick="viewRequest(${request.id})" title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </button>
                        ${request.status === 'pending' ? `
                            <button class="btn btn-sm btn-success" onclick="quickApprove(${request.id})" title="اعتماد سريع">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="quickReject(${request.id})" title="رفض سريع">
                                <i class="fas fa-times"></i>
                            </button>
                        ` : ''}
                    </div>
                </td>
            </tr>
        `;
    }

    getStatusIcon(status) {
        const icons = {
            pending: 'clock',
            approved: 'check',
            rejected: 'times'
        };
        return icons[status] || 'question';
    }

    getFilteredRequests() {
        return this.requests.filter(request => {
            // Status filter
            if (this.filters.status && request.status !== this.filters.status) {
                return false;
            }

            // Type filter
            if (this.filters.type && request.type !== this.filters.type) {
                return false;
            }

            // Center filter
            if (this.filters.center && request.employee.center !== this.filters.center) {
                return false;
            }

            // Date filters
            if (this.filters.dateFrom && request.date < this.filters.dateFrom) {
                return false;
            }
            if (this.filters.dateTo && request.date > this.filters.dateTo) {
                return false;
            }

            // Search filter
            if (this.filters.search) {
                const searchTerm = this.filters.search.toLowerCase();
                const searchableText = [
                    request.requestNumber,
                    request.employee.name,
                    request.employee.id.toString(),
                    request.typeLabel
                ].join(' ').toLowerCase();
                
                if (!searchableText.includes(searchTerm)) {
                    return false;
                }
            }

            return true;
        });
    }

    getPaginatedRequests(requests) {
        const startIndex = (this.currentPage - 1) * this.pageSize;
        const endIndex = startIndex + this.pageSize;
        return requests.slice(startIndex, endIndex);
    }

    updatePagination(totalItems) {
        this.totalPages = Math.ceil(totalItems / this.pageSize);
        const pagination = document.getElementById('pagination');
        if (!pagination) return;

        if (this.totalPages <= 1) {
            pagination.innerHTML = '';
            return;
        }

        let paginationHTML = '<div class="pagination-controls">';
        
        // Previous button
        if (this.currentPage > 1) {
            paginationHTML += `<button class="btn btn-sm btn-secondary" onclick="goToPage(${this.currentPage - 1})">
                <i class="fas fa-chevron-right"></i>
                السابق
            </button>`;
        }

        // Page numbers
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(this.totalPages, this.currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === this.currentPage ? 'active' : '';
            paginationHTML += `<button class="btn btn-sm ${isActive}" onclick="goToPage(${i})">${i}</button>`;
        }

        // Next button
        if (this.currentPage < this.totalPages) {
            paginationHTML += `<button class="btn btn-sm btn-secondary" onclick="goToPage(${this.currentPage + 1})">
                التالي
                <i class="fas fa-chevron-left"></i>
            </button>`;
        }

        paginationHTML += '</div>';
        pagination.innerHTML = paginationHTML;
    }

    applyFilters() {
        this.filters.status = document.getElementById('status-filter').value;
        this.filters.type = document.getElementById('type-filter').value;
        this.filters.center = document.getElementById('center-filter').value;
        this.filters.dateFrom = document.getElementById('date-from').value;
        this.filters.dateTo = document.getElementById('date-to').value;
        this.filters.search = document.getElementById('search-input').value;

        this.currentPage = 1;
        this.renderTable();
    }

    clearFilters() {
        document.getElementById('status-filter').value = '';
        document.getElementById('type-filter').value = '';
        document.getElementById('center-filter').value = '';
        document.getElementById('date-from').value = '';
        document.getElementById('date-to').value = '';
        document.getElementById('search-input').value = '';

        this.filters = {
            status: '',
            type: '',
            center: '',
            dateFrom: '',
            dateTo: '',
            search: ''
        };

        this.currentPage = 1;
        this.renderTable();
    }

    toggleSelectAll() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.request-checkbox:not(:disabled)');
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
            this.toggleRequestSelection(parseInt(checkbox.value));
        });
    }

    toggleRequestSelection(requestId) {
        if (this.selectedItems.has(requestId)) {
            this.selectedItems.delete(requestId);
        } else {
            this.selectedItems.add(requestId);
        }
        this.updateBulkActions();
    }

    updateBulkActions() {
        const selectedCount = this.selectedItems.size;
        const bulkApproveBtn = document.getElementById('bulk-approve-btn');
        const bulkRejectBtn = document.getElementById('bulk-reject-btn');
        const selectedCountSpan = document.getElementById('selected-count');

        if (bulkApproveBtn) bulkApproveBtn.disabled = selectedCount === 0;
        if (bulkRejectBtn) bulkRejectBtn.disabled = selectedCount === 0;
        if (selectedCountSpan) selectedCountSpan.textContent = `${selectedCount} عنصر محدد`;
    }

    bulkApprove() {
        if (this.selectedItems.size === 0) return;

        if (confirm(`هل أنت متأكد من اعتماد ${this.selectedItems.size} طلب؟`)) {
            this.selectedItems.forEach(requestId => {
                this.approveRequest(requestId, 'تم الاعتماد بالجملة');
            });
            this.selectedItems.clear();
            this.updateBulkActions();
            this.renderTable();
            this.updateStatistics();
        }
    }

    bulkReject() {
        if (this.selectedItems.size === 0) return;

        const reason = prompt('يرجى إدخال سبب الرفض:');
        if (reason && reason.trim() !== '') {
            this.selectedItems.forEach(requestId => {
                this.rejectRequest(requestId, reason);
            });
            this.selectedItems.clear();
            this.updateBulkActions();
            this.renderTable();
            this.updateStatistics();
        }
    }

    viewRequest(requestId) {
        const request = this.requests.find(r => r.id === requestId);
        if (!request) return;

        this.currentRequest = request;
        this.showRequestDetails(request);
    }

    showRequestDetails(request) {
        const modal = document.getElementById('approval-modal');
        const modalTitle = document.getElementById('modal-title');
        const requestDetails = document.getElementById('request-details');

        modalTitle.textContent = `مراجعة الطلب - ${request.requestNumber}`;
        
        requestDetails.innerHTML = this.createRequestDetailsHTML(request);
        
        modal.style.display = 'flex';
    }

    createRequestDetailsHTML(request) {
        return `
            <div class="request-info">
                <div class="info-section">
                    <h4>معلومات الموظف</h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>الاسم:</label>
                            <span>${request.employee.name}</span>
                        </div>
                        <div class="info-item">
                            <label>رقم الموظف:</label>
                            <span>${request.employee.id}</span>
                        </div>
                        <div class="info-item">
                            <label>القسم:</label>
                            <span>${request.employee.department}</span>
                        </div>
                        <div class="info-item">
                            <label>المركز:</label>
                            <span>${request.employee.center}</span>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h4>تفاصيل الطلب</h4>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>نوع الطلب:</label>
                            <span>${request.typeLabel}</span>
                        </div>
                        <div class="info-item">
                            <label>تاريخ الطلب:</label>
                            <span>${this.formatDate(request.date)}</span>
                        </div>
                        <div class="info-item">
                            <label>الحالة:</label>
                            <span class="status-badge ${request.status}">${this.getStatusLabel(request.status)}</span>
                        </div>
                        <div class="info-item">
                            <label>مرسل من:</label>
                            <span>${request.createdBy}</span>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h4>التفاصيل الإضافية</h4>
                    <div class="details-content">
                        ${this.createDetailsContent(request.details, request.type)}
                    </div>
                </div>

                ${request.status === 'approved' ? `
                    <div class="info-section">
                        <h4>معلومات الاعتماد</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>اعتمد من:</label>
                                <span>${request.approvedBy}</span>
                            </div>
                            <div class="info-item">
                                <label>تاريخ الاعتماد:</label>
                                <span>${this.formatDateTime(request.approvedAt)}</span>
                            </div>
                        </div>
                    </div>
                ` : ''}

                ${request.status === 'rejected' ? `
                    <div class="info-section">
                        <h4>معلومات الرفض</h4>
                        <div class="info-grid">
                            <div class="info-item">
                                <label>رفض من:</label>
                                <span>${request.rejectedBy}</span>
                            </div>
                            <div class="info-item">
                                <label>تاريخ الرفض:</label>
                                <span>${this.formatDateTime(request.rejectedAt)}</span>
                            </div>
                            <div class="info-item">
                                <label>سبب الرفض:</label>
                                <span>${request.rejectionReason}</span>
                            </div>
                        </div>
                    </div>
                ` : ''}
            </div>
        `;
    }

    createDetailsContent(details, type) {
        switch (type) {
            case 'leave':
                return `
                    <div class="info-grid">
                        <div class="info-item">
                            <label>نوع الإجازة:</label>
                            <span>${details.leaveType}</span>
                        </div>
                        <div class="info-item">
                            <label>تاريخ البداية:</label>
                            <span>${this.formatDate(details.startDate)}</span>
                        </div>
                        <div class="info-item">
                            <label>تاريخ النهاية:</label>
                            <span>${this.formatDate(details.endDate)}</span>
                        </div>
                        <div class="info-item">
                            <label>المدة:</label>
                            <span>${details.duration} يوم</span>
                        </div>
                        <div class="info-item full-width">
                            <label>السبب:</label>
                            <span>${details.reason}</span>
                        </div>
                    </div>
                `;
            case 'transfer':
                return `
                    <div class="info-grid">
                        <div class="info-item">
                            <label>من القسم:</label>
                            <span>${details.fromDepartment}</span>
                        </div>
                        <div class="info-item">
                            <label>إلى القسم:</label>
                            <span>${details.toDepartment}</span>
                        </div>
                        <div class="info-item full-width">
                            <label>السبب:</label>
                            <span>${details.reason}</span>
                        </div>
                    </div>
                `;
            case 'delegation':
                return `
                    <div class="info-grid">
                        <div class="info-item">
                            <label>نوع الإيفاد:</label>
                            <span>${details.delegationType}</span>
                        </div>
                        <div class="info-item">
                            <label>الوجهة:</label>
                            <span>${details.destination}</span>
                        </div>
                        <div class="info-item">
                            <label>تاريخ البداية:</label>
                            <span>${this.formatDate(details.startDate)}</span>
                        </div>
                        <div class="info-item">
                            <label>تاريخ النهاية:</label>
                            <span>${this.formatDate(details.endDate)}</span>
                        </div>
                        <div class="info-item">
                            <label>المدة:</label>
                            <span>${details.duration} يوم</span>
                        </div>
                        <div class="info-item">
                            <label>التكلفة:</label>
                            <span>${details.cost ? details.cost.toLocaleString() + ' ريال' : 'غير محدد'}</span>
                        </div>
                    </div>
                `;
            case 'assignment':
                return `
                    <div class="info-grid">
                        <div class="info-item">
                            <label>نوع التكليف:</label>
                            <span>${details.assignmentType}</span>
                        </div>
                        <div class="info-item">
                            <label>الوصف:</label>
                            <span>${details.description}</span>
                        </div>
                        <div class="info-item">
                            <label>تاريخ البداية:</label>
                            <span>${this.formatDate(details.startDate)}</span>
                        </div>
                        <div class="info-item">
                            <label>تاريخ النهاية:</label>
                            <span>${this.formatDate(details.endDate)}</span>
                        </div>
                        <div class="info-item">
                            <label>المدة:</label>
                            <span>${details.duration} يوم</span>
                        </div>
                    </div>
                `;
            default:
                return '<p>لا توجد تفاصيل إضافية</p>';
        }
    }

    approveRequest(requestId, comment = '') {
        const request = this.requests.find(r => r.id === requestId);
        if (!request) return;

        request.status = 'approved';
        request.approvedBy = 'مدير المستشفى';
        request.approvedAt = new Date().toISOString();
        request.approvalComment = comment;

        this.showNotification(`تم اعتماد الطلب ${request.requestNumber} بنجاح`, 'success');
    }

    rejectRequest(requestId, reason = '') {
        const request = this.requests.find(r => r.id === requestId);
        if (!request) return;

        request.status = 'rejected';
        request.rejectedBy = 'مدير المستشفى';
        request.rejectedAt = new Date().toISOString();
        request.rejectionReason = reason;

        this.showNotification(`تم رفض الطلب ${request.requestNumber}`, 'warning');
    }

    quickApprove(requestId) {
        if (confirm('هل أنت متأكد من اعتماد هذا الطلب؟')) {
            this.approveRequest(requestId, 'اعتماد سريع');
            this.renderTable();
            this.updateStatistics();
        }
    }

    quickReject(requestId) {
        const reason = prompt('يرجى إدخال سبب الرفض:');
        if (reason && reason.trim() !== '') {
            this.rejectRequest(requestId, reason);
            this.renderTable();
            this.updateStatistics();
        }
    }

    closeApprovalModal() {
        const modal = document.getElementById('approval-modal');
        modal.style.display = 'none';
        document.getElementById('approval-comment').value = '';
    }

    changePageSize() {
        this.pageSize = parseInt(document.getElementById('page-size').value);
        this.currentPage = 1;
        this.renderTable();
    }

    goToPage(page) {
        this.currentPage = page;
        this.renderTable();
    }

    refreshApprovals() {
        this.showNotification('جاري تحديث البيانات...', 'info');
        setTimeout(() => {
            this.loadApprovalData();
            this.renderTable();
            this.showNotification('تم تحديث البيانات بنجاح', 'success');
        }, 1000);
    }

    exportApprovals() {
        this.showNotification('جاري تصدير البيانات...', 'info');
        
        setTimeout(() => {
            const filteredRequests = this.getFilteredRequests();
            const csvContent = this.convertToCSV(filteredRequests);
            this.downloadCSV(csvContent, 'approvals-export.csv');
            this.showNotification('تم تصدير البيانات بنجاح', 'success');
        }, 1500);
    }

    convertToCSV(requests) {
        const headers = ['رقم الطلب', 'الموظف', 'نوع الطلب', 'المركز', 'التاريخ', 'الحالة'];
        const rows = requests.map(request => [
            request.requestNumber,
            request.employee.name,
            request.typeLabel,
            request.employee.center,
            request.date,
            this.getStatusLabel(request.status)
        ]);
        
        return [headers, ...rows].map(row => row.join(',')).join('\n');
    }

    downloadCSV(content, filename) {
        const blob = new Blob([content], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('ar-SA');
    }

    formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('ar-SA');
    }

    getStatusLabel(status) {
        const labels = {
            pending: 'في انتظار الاعتماد',
            approved: 'معتمد',
            rejected: 'مرفوض'
        };
        return labels[status] || status;
    }

    updateElement(id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    }

    handleKeyboard(e) {
        if (e.ctrlKey || e.metaKey) {
            switch (e.key) {
                case 'a':
                    e.preventDefault();
                    document.getElementById('select-all').click();
                    break;
                case 'r':
                    e.preventDefault();
                    this.refreshApprovals();
                    break;
            }
        }
        
        if (e.key === 'Escape') {
            this.closeApprovalModal();
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
function applyFilters() {
    if (window.approvalSystem) {
        window.approvalSystem.applyFilters();
    }
}

function clearFilters() {
    if (window.approvalSystem) {
        window.approvalSystem.clearFilters();
    }
}

function toggleSelectAll() {
    if (window.approvalSystem) {
        window.approvalSystem.toggleSelectAll();
    }
}

function toggleRequestSelection(requestId) {
    if (window.approvalSystem) {
        window.approvalSystem.toggleRequestSelection(requestId);
    }
}

function bulkApprove() {
    if (window.approvalSystem) {
        window.approvalSystem.bulkApprove();
    }
}

function bulkReject() {
    if (window.approvalSystem) {
        window.approvalSystem.bulkReject();
    }
}

function viewRequest(requestId) {
    if (window.approvalSystem) {
        window.approvalSystem.viewRequest(requestId);
    }
}

function quickApprove(requestId) {
    if (window.approvalSystem) {
        window.approvalSystem.quickApprove(requestId);
    }
}

function quickReject(requestId) {
    if (window.approvalSystem) {
        window.approvalSystem.quickReject(requestId);
    }
}

function closeApprovalModal() {
    if (window.approvalSystem) {
        window.approvalSystem.closeApprovalModal();
    }
}

function approveRequest() {
    if (window.approvalSystem && window.approvalSystem.currentRequest) {
        const comment = document.getElementById('approval-comment').value;
        window.approvalSystem.approveRequest(window.approvalSystem.currentRequest.id, comment);
        window.approvalSystem.closeApprovalModal();
        window.approvalSystem.renderTable();
        window.approvalSystem.updateStatistics();
    }
}

function rejectRequest() {
    if (window.approvalSystem && window.approvalSystem.currentRequest) {
        const comment = document.getElementById('approval-comment').value;
        if (comment.trim() === '') {
            alert('يرجى إدخال سبب الرفض');
            return;
        }
        window.approvalSystem.rejectRequest(window.approvalSystem.currentRequest.id, comment);
        window.approvalSystem.closeApprovalModal();
        window.approvalSystem.renderTable();
        window.approvalSystem.updateStatistics();
    }
}

function changePageSize() {
    if (window.approvalSystem) {
        window.approvalSystem.changePageSize();
    }
}

function goToPage(page) {
    if (window.approvalSystem) {
        window.approvalSystem.goToPage(page);
    }
}

function refreshApprovals() {
    if (window.approvalSystem) {
        window.approvalSystem.refreshApprovals();
    }
}

function exportApprovals() {
    if (window.approvalSystem) {
        window.approvalSystem.exportApprovals();
    }
}

// Initialize approval system when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.approvalSystem = new ApprovalSystem();
});

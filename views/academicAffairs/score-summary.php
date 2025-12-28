<?php
$title = 'Tổng hợp Điểm - Giáo vụ';
require __DIR__ . '/../layouts/header.php';
?>

<div id="score-summary-app">
    <h2 class="mb-4"><i class="bi bi-clipboard-data"></i> Tổng hợp Điểm</h2>
    
    <!-- Main View: List of Classes -->
    <div id="class-list-view">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Bộ lọc</h5>
            </div>
            <div class="card-body">
                <form id="filter-form" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Năm học</label>
                        <select class="form-select" id="year-select">
                            <option value="">-- Tất cả --</option>
                            <option value="2024-2025">2024-2025</option>
                            <option value="2023-2024">2023-2024</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Học kỳ</label>
                        <select class="form-select" id="semester-select">
                            <option value="">-- Tất cả --</option>
                            <option value="HK1">Học kỳ 1</option>
                            <option value="HK2">Học kỳ 2</option>
                        </select>
                    </div>
                     <div class="col-md-3">
                        <label class="form-label">Trạng thái</label>
                        <select class="form-select" id="status-select">
                            <option value="">-- Tất cả --</option>
                            <option value="unlocked">Chưa chốt</option>
                            <option value="locked">Đã chốt</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Lọc
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-list-task"></i> Danh sách Lớp học phần</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="classes-table" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Mã lớp</th>
                                <th>Học phần</th>
                                <th>Năm học / HK</th>
                                <th>Tín chỉ</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail View: Class Scores (Hidden by default) -->
    <div id="class-detail-view" style="display: none;">
        <div class="mb-3">
            <button class="btn btn-outline-secondary" onclick="showClassList()">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </button>
        </div>

        <div class="card shadow-sm mb-4 border-top border-4 border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="card-title text-primary fw-bold" id="detail-class-name">Loading...</h4>
                        <p class="text-muted mb-0" id="detail-subject-name">Loading...</p>
                        <span class="badge bg-light text-dark border mt-2" id="detail-semester"></span>
                    </div>
                    <div class="text-end">
                        <div id="detail-actions" class="mb-2">
                            <!-- Buttons injected via JS -->
                        </div>
                        <div id="detail-status"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-people"></i> Danh sách Sinh viên</h5>
                <input type="text" id="score-search" class="form-control form-control-sm w-auto" placeholder="Tìm sinh viên...">
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0" id="scores-table">
                        <thead class="table-light">
                            <tr>
                                <th>Mã SV</th>
                                <th>Họ tên</th>
                                <th class="text-center">X1</th>
                                <th class="text-center">X2</th>
                                <th class="text-center">X3</th>
                                <th class="text-center">Y</th>
                                <th class="text-center">CC</th>
                                <th class="text-center table-active">Z</th>
                                <th class="text-center">GPA</th>
                                <th class="text-center">Điểm chữ</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const API_CLASSES_URL = '<?= url("/academic/scores/api/classes") ?>';
    const API_SCORES_URL = '<?= url("/academic/scores/api/detail") ?>';
    const API_LOCK_URL = '<?= url("/academic/scores/lock") ?>';
    
    let classesTable;
    let currentClassId = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Init Classes Table
        classesTable = $('#classes-table').DataTable({
            language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json' },
            columns: [
                { data: 'class_code', className: 'fw-bold text-primary' },
                { 
                    data: null,
                    render: function(data) {
                        return `<div>${data.subject_name}</div><small class="text-muted">${data.subject_code}</small>`;
                    }
                },
                { 
                    data: null,
                    render: function(data) {
                        return `${data.academic_year} <br> <span class="badge bg-light text-dark border">${data.semester}</span>`;
                    }
                },
                { data: 'credits', className: 'text-center' },
                { 
                    data: 'is_locked',
                    className: 'text-center',
                    render: function(data) {
                        return data == 1 
                            ? '<span class="badge bg-danger"><i class="bi bi-lock-fill"></i> Đã chốt</span>' 
                            : '<span class="badge bg-success"><i class="bi bi-unlock-fill"></i> Chưa chốt</span>';
                    }
                },
                {
                    data: null,
                    className: 'text-end',
                    orderable: false,
                    render: function(data) {
                        return `<button class="btn btn-sm btn-outline-primary" onclick="loadClassDetail(${data.id})">
                                    <i class="bi bi-eye"></i> Xem điểm
                                </button>`;
                    }
                }
            ],
            dom: 'rtip',
            pageLength: 10
        });

        // Load initial list
        loadClasses();

        // Handle Filter
        document.getElementById('filter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            loadClasses();
        });
        
        // Search in scores table
        document.getElementById('score-search').addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            $("#scores-table tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    async function loadClasses() {
        const params = {
            academic_year: document.getElementById('year-select').value,
            semester: document.getElementById('semester-select').value,
            status: document.getElementById('status-select').value
        };

        try {
            const response = await fetch(API_CLASSES_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(params)
            });
            const json = await response.json();
            if (json.success) {
                classesTable.clear().rows.add(json.data).draw();
            }
        } catch (e) {
            showToast('error', 'Lỗi tải danh sách lớp');
        }
    }

    async function loadClassDetail(classId) {
        currentClassId = classId;
        
        // Show loader
        $('#scores-table tbody').html('<tr><td colspan="10" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>');
        
        // Switch Views
        document.getElementById('class-list-view').style.display = 'none';
        document.getElementById('class-detail-view').style.display = 'block';
        document.getElementById('class-detail-view').classList.add('animate__animated', 'animate__fadeInRight');

        try {
            const separator = API_SCORES_URL.includes('?') ? '&' : '?';
            const response = await fetch(`${API_SCORES_URL}${separator}class_id=${classId}`);
            const json = await response.json();
            
            if (json.success) {
                const cls = json.data.class;
                const scores = json.data.scores;
                
                // Update Header
                document.getElementById('detail-class-name').textContent = cls.class_code;
                document.getElementById('detail-subject-name').textContent = `${cls.subject_name} (${cls.subject_code})`;
                document.getElementById('detail-semester').textContent = `${cls.semester} - ${cls.academic_year}`;
                
                // Update Actions
                const actionsDiv = document.getElementById('detail-actions');
                const exportUrl = '<?= url("/academic/scores/export?class_id=") ?>' + classId;
                
                let html = `<a href="${exportUrl}" class="btn btn-success me-2"><i class="bi bi-file-earmark-excel"></i> Xuất Excel</a>`;
                
                if (cls.is_locked == 0) {
                    html += `<button class="btn btn-danger" onclick="confirmLock(${classId})"><i class="bi bi-lock"></i> Chốt điểm</button>`;
                }
                actionsDiv.innerHTML = html;
                
                document.getElementById('detail-status').innerHTML = cls.is_locked == 1 
                    ? '<span class="badge bg-danger fs-6">Đã chốt điểm</span>' 
                    : '<span class="badge bg-success fs-6">Đang nhập điểm</span>';

                // Render Table
                const tbody = document.querySelector('#scores-table tbody');
                tbody.innerHTML = '';
                
                if (scores.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="10" class="text-center py-4 text-muted">Chưa có dữ liệu điểm.</td></tr>';
                } else {
                    scores.forEach(s => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${s.student_code}</td>
                            <td>${s.full_name}</td>
                            <td class="text-center">${s.x1 || '-'}</td>
                            <td class="text-center">${s.x2 || '-'}</td>
                            <td class="text-center">${s.x3 || '-'}</td>
                            <td class="text-center">${s.y || '-'}</td>
                            <td class="text-center">${s.cc || '-'}</td>
                            <td class="text-center table-active fw-bold">${s.z || '-'}</td>
                            <td class="text-center">${s.gpa || '-'}</td>
                            <td class="text-center"><span class="badge bg-${getGradeColor(s.letter)}">${s.letter || '-'}</span></td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            }
        } catch (e) {
            showToast('error', 'Lỗi tải chi tiết lớp học');
        }
    }

    function showClassList() {
        document.getElementById('class-detail-view').style.display = 'none';
        document.getElementById('class-list-view').style.display = 'block';
        document.getElementById('class-list-view').classList.add('animate__animated', 'animate__fadeInLeft');
    }
    
    function getGradeColor(grade) {
         const colors = { 'A': 'success', 'B': 'primary', 'C': 'info', 'D': 'warning', 'F': 'danger' };
         return colors[grade] || 'secondary';
    }

    function confirmLock(classId) {
        Swal.fire({
            title: 'Xác nhận chốt điểm?',
            text: "Sau khi chốt, giảng viên sẽ không thể chỉnh sửa điểm số nữa!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Chốt ngay',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                performLock(classId);
            }
        });
    }

    async function performLock(classId) {
        try {
            const response = await fetch(API_LOCK_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ class_room_id: classId })
            });
            const json = await response.json();
            
            if (json.success) {
                Swal.fire('Thành công', json.message, 'success');
                loadClassDetail(classId); // Reload details
                loadClasses(); // Reload background list
            } else {
                showToast('error', json.message);
            }
        } catch (e) {
            showToast('error', 'Lỗi kết nối');
        }
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
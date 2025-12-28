<?php
$title = 'Dashboard - Root';
require __DIR__ . '/../layouts/header.php';
?>

<div id="root-dashboard-app">
    <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard - Root</h2>
    
    <!-- Loader -->
    <div id="dashboard-loader" class="text-center py-5">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
    </div>

    <div id="dashboard-content" style="display: none;">
        <!-- Thống kê nhanh -->
        <div class="row mb-4">
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card text-center border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <i class="bi bi-building fs-1 text-primary"></i>
                        <h3 class="mt-2 text-primary" id="stat-faculties-count">-</h3>
                        <p class="text-muted mb-0">Khoa</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card text-center border-start border-success border-4 h-100">
                    <div class="card-body">
                        <i class="bi bi-people fs-1 text-success"></i>
                        <h3 class="mt-2 text-success" id="stat-students-count">-</h3>
                        <p class="text-muted mb-0">Sinh viên</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card text-center border-start border-info border-4 h-100">
                    <div class="card-body">
                        <i class="bi bi-book fs-1 text-info"></i>
                        <h3 class="mt-2 text-info" id="stat-departments-count">-</h3>
                        <p class="text-muted mb-0">Bộ môn</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card text-center border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <i class="bi bi-clipboard-data fs-1 text-warning"></i>
                        <h3 class="mt-2 text-warning" id="stat-scores-count">-</h3>
                        <p class="text-muted mb-0">Tổng điểm</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quản lý Chốt điểm -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0 text-danger"><i class="bi bi-lock-fill"></i> Quản lý Chốt điểm (Giảng viên)</h5>
                        <button class="btn btn-sm btn-outline-secondary" onclick="loadLocks()">
                            <i class="bi bi-arrow-clockwise"></i> Làm mới
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="locks-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Lớp học</th>
                                        <th>Môn học</th>
                                        <th>Giảng viên</th>
                                        <th>Thời gian chốt</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Đang tải danh sách khóa...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Biểu đồ thống kê -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Phân bố Điểm số (Toàn trường)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="gradeDistChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Tỷ lệ Đậu/Rớt</h5>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div style="max-height: 250px; margin: 0 auto; width: 100%;">
                            <canvas id="passFailChart"></canvas>
                        </div>
                        <div class="mt-4 text-center">
                            <div class="row">
                                <div class="col-6">
                                    <h4 class="text-success mb-0" id="stat-pass-count">-</h4>
                                    <small class="text-muted">Đậu (<span id="stat-pass-rate">-</span>%)</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="text-danger mb-0" id="stat-fail-count">-</h4>
                                    <small class="text-muted">Rớt (<span id="stat-fail-rate">-</span>%)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Thống kê theo Khoa -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Thống kê theo Khoa</h5>
            </div>
            <div class="card-body">
                <canvas id="facultyChart" style="max-height: 400px;"></canvas>
            </div>
        </div>

        <!-- Danh sách Khoa -->
        <div class="row" id="faculty-list-container">
            <!-- Faculty items will be injected here -->
        </div>
    </div>
</div>

<script>
    // Constants
    const API_STATS_URL = '<?= url("/root/api/stats") ?>';
    const API_LOCKS_URL = '<?= url("/root/api/locks") ?>';
    const API_UNLOCK_URL = '<?= url("/root/api/unlock") ?>';
    
    // Charts instances
    let gradeDistChart = null;
    let passFailChart = null;
    let facultyChart = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Initial load
        Promise.all([
            loadStats(),
            loadLocks()
        ]).finally(() => {
            document.getElementById('dashboard-loader').style.display = 'none';
            document.getElementById('dashboard-content').style.display = 'block';
            document.getElementById('dashboard-content').classList.add('animate__animated', 'animate__fadeIn');
        });
    });

    /**
     * Load Dashboard Statistics
     */
    async function loadStats() {
        try {
            const response = await fetch(API_STATS_URL);
            const json = await response.json();
            
            if (!json.success) throw new Error(json.message || 'Lỗi tải dữ liệu');
            
            const data = json.data;
            
            // Update Summary Cards
            document.getElementById('stat-faculties-count').textContent = data.summary.total_faculties;
            document.getElementById('stat-students-count').textContent = data.summary.total_students;
            document.getElementById('stat-departments-count').textContent = data.summary.total_departments;
            document.getElementById('stat-scores-count').textContent = data.summary.total_scores;
            
            // Update Pass/Fail details
            document.getElementById('stat-pass-count').textContent = data.passFail.passed || 0;
            document.getElementById('stat-pass-rate').textContent = data.passFail.pass_rate || 0;
            document.getElementById('stat-fail-count').textContent = data.passFail.failed || 0;
            document.getElementById('stat-fail-rate').textContent = data.passFail.fail_rate || 0;
            
            // Render Charts
            renderGradeDistChart(data.gradeDist);
            renderPassFailChart(data.passFail);
            renderFacultyChart(data.faculties);
            
            // Render Faculty List
            renderFacultyList(data.faculties);
            
        } catch (error) {
            console.error('Stats error:', error);
            showToast('error', 'Không thể tải thống kê dashboard: ' + error.message);
        }
    }

    /**
     * Load Score Locks
     */
    async function loadLocks() {
        const tbody = document.querySelector('#locks-table tbody');
        
        try {
            const response = await fetch(API_LOCKS_URL);
            const json = await response.json();
            
            if (!json.success) throw new Error(json.message || 'Lỗi tải dữ liệu khóa điểm');
            
            const locks = json.data;
            
            tbody.innerHTML = '';
            
            if (locks.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="bi bi-check-circle fs-3 d-block mb-2"></i>Không có lớp học nào đang bị khóa điểm.</td></tr>';
                return;
            }
            
            locks.forEach(lock => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>
                        <span class="fw-bold text-primary">${lock.class_name}</span>
                        <div class="small text-muted">ID: ${lock.class_room_id}</div>
                    </td>
                    <td>
                        <div class="fw-bold">${lock.subject_name}</div>
                        <div class="small text-muted">${lock.subject_code}</div>
                    </td>
                    <td>
                        <div class="fw-bold">${lock.lecturer_name}</div>
                        <div class="small text-muted">${lock.lecturer_code}</div>
                    </td>
                    <td>
                        <i class="bi bi-clock"></i> ${moment(lock.locked_at).format('DD/MM/YYYY HH:mm')}
                    </td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-danger" onclick="confirmUnlock(${lock.class_room_id}, '${lock.class_name}')">
                            <i class="bi bi-unlock"></i> Mở khóa
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            
        } catch (error) {
            console.error('Locks error:', error);
            tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-3">Lỗi tải dữ liệu: ${error.message}</td></tr>`;
        }
    }

    /**
     * Unlock Action
     */
    window.confirmUnlock = function(classId, className) {
        Swal.fire({
            title: 'Xác nhận mở khóa',
            text: `Bạn có chắc chắn muốn mở khóa nhập điểm cho lớp ${className}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Mở khóa ngay',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                performUnlock(classId);
            }
        });
    }
    
    async function performUnlock(classId) {
        try {
            const response = await fetch(API_UNLOCK_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ class_id: classId })
            });
            
            const json = await response.json();
            
            if (json.success) {
                showToast('success', json.message);
                loadLocks(); // Reload the list
            } else {
                throw new Error(json.message);
            }
        } catch (error) {
            showToast('error', error.message);
        }
    }

    /**
     * Chart Rendering Functions
     */
    function renderGradeDistChart(data) {
        const ctx = document.getElementById('gradeDistChart').getContext('2d');
        if (gradeDistChart) gradeDistChart.destroy();
        
        // Define colors based on grade
        const getGradeColor = (grade) => {
            const colors = {
                'A': '#28a745', // Green
                'B': '#0d6efd', // Blue
                'C': '#0dcaf0', // Cyan
                'D': '#ffc107', // Yellow
                'F': '#dc3545'  // Red
            };
            return colors[grade] || '#6c757d'; // Default Gray
        };

        const bgColors = data.map(d => getGradeColor(d.letter));

        gradeDistChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.letter),
                datasets: [{
                    label: 'Số lượng điểm',
                    data: data.map(d => parseInt(d.count)),
                    backgroundColor: bgColors,
                    borderRadius: 5,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Số lượng: ${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        title: {
                            display: true,
                            text: 'Số lượng sinh viên đạt điểm'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Điểm chữ'
                        }
                    }
                }
            }
        });
    }

    function renderPassFailChart(data) {
        const ctx = document.getElementById('passFailChart').getContext('2d');
        if (passFailChart) passFailChart.destroy();
        
        passFailChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Đậu', 'Rớt'],
                datasets: [{
                    data: [data.passed || 0, data.failed || 0],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    function renderFacultyChart(faculties) {
        const ctx = document.getElementById('facultyChart').getContext('2d');
        if (facultyChart) facultyChart.destroy();
        
        facultyChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: faculties.map(f => f.name),
                datasets: [
                    {
                        label: 'Sinh viên',
                        data: faculties.map(f => f.total_students),
                        backgroundColor: '#6f42c1',
                        borderRadius: 4
                    },
                    {
                        label: 'Bộ môn',
                        data: faculties.map(f => f.total_departments),
                        backgroundColor: '#17a2b8',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    function renderFacultyList(faculties) {
        const container = document.getElementById('faculty-list-container');
        container.innerHTML = '';
        
        faculties.forEach(faculty => {
            const col = document.createElement('div');
            col.className = 'col-md-4 mb-3';
            col.innerHTML = `
                <div class="card h-100 hover-shadow transition-all">
                    <div class="card-body">
                        <h5 class="card-title text-truncate" title="${faculty.name}">
                            <i class="bi bi-building text-primary"></i> ${faculty.name}
                        </h5>
                        <hr>
                        <p class="card-text">
                            <div class="d-flex justify-content-between mb-2">
                                <span><i class="bi bi-people text-success"></i> Sinh viên:</span>
                                <span class="fw-bold">${faculty.total_students}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span><i class="bi bi-book text-info"></i> Bộ môn:</span>
                                <span class="fw-bold">${faculty.total_departments}</span>
                            </div>
                        </p>
                    </div>
                </div>
            `;
            container.appendChild(col);
        });
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
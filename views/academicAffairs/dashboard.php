<?php
$title = 'Dashboard - Giáo vụ';
require __DIR__ . '/../layouts/header.php';
?>

<div id="academic-dashboard-app">
    <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard - Giáo vụ</h2>
    
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
                        <i class="bi bi-journal-text fs-1 text-primary"></i>
                        <h3 class="mt-2 text-primary" id="stat-classes-count">-</h3>
                        <p class="text-muted mb-0">Lớp học</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card text-center border-start border-success border-4 h-100">
                    <div class="card-body">
                        <i class="bi bi-check-circle fs-1 text-success"></i>
                        <h3 class="mt-2 text-success" id="stat-locked-count">-</h3>
                        <p class="text-muted mb-0">Lớp đã chốt</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card text-center border-start border-info border-4 h-100">
                    <div class="card-body">
                        <i class="bi bi-clipboard-data fs-1 text-info"></i>
                        <h3 class="mt-2 text-info" id="stat-scores-count">-</h3>
                        <p class="text-muted mb-0">Tổng điểm</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3 mb-3">
                <div class="card text-center border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                        <h3 class="mt-2 text-warning" id="stat-warnings-count">-</h3>
                        <p class="text-muted mb-0">Cảnh báo học tập</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Biểu đồ -->
        <div class="row mb-4">
            <div class="col-lg-8 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Phân bố Điểm số (Khoa)</h5>
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
        
        <!-- Danh sách Lớp mới nhất -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Các lớp học gần đây</h5>
                <a href="<?= url('/academic/scores') ?>" class="btn btn-sm btn-outline-primary">Xem tất cả</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="recent-classes-table">
                        <thead class="table-light">
                            <tr>
                                <th>Mã lớp</th>
                                <th>Học phần</th>
                                <th>Học kỳ</th>
                                <th>Năm học</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data loaded via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const API_STATS_URL = '<?= url("/academic/api/stats") ?>';
    let gradeDistChart = null;
    let passFailChart = null;

    document.addEventListener('DOMContentLoaded', function() {
        loadStats();
    });

    async function loadStats() {
        try {
            const response = await fetch(API_STATS_URL);
            const json = await response.json();
            
            if (!json.success) throw new Error(json.message);
            
            const data = json.data;
            
            // 1. Update Summary
            document.getElementById('stat-classes-count').textContent = data.summary.total_classes;
            document.getElementById('stat-locked-count').textContent = data.summary.locked_classes;
            document.getElementById('stat-scores-count').textContent = data.summary.total_scores;
            document.getElementById('stat-warnings-count').textContent = data.summary.warnings;
            
            // 2. Update Pass/Fail details
            document.getElementById('stat-pass-count').textContent = data.passFail.passed || 0;
            document.getElementById('stat-pass-rate').textContent = data.passFail.pass_rate || 0;
            document.getElementById('stat-fail-count').textContent = data.passFail.failed || 0;
            document.getElementById('stat-fail-rate').textContent = data.passFail.fail_rate || 0;
            
            // 3. Render Charts
            renderGradeDistChart(data.gradeDist);
            renderPassFailChart(data.passFail);
            
            // 4. Render Recent Classes
            renderRecentClasses(data.recentClasses);
            
            // Show Content
            document.getElementById('dashboard-loader').style.display = 'none';
            document.getElementById('dashboard-content').style.display = 'block';
            document.getElementById('dashboard-content').classList.add('animate__animated', 'animate__fadeIn');
            
        } catch (error) {
            console.error(error);
            showToast('error', 'Lỗi tải dữ liệu dashboard');
        }
    }

    function renderGradeDistChart(data) {
        const ctx = document.getElementById('gradeDistChart').getContext('2d');
        if (gradeDistChart) gradeDistChart.destroy();
        
        const getGradeColor = (grade) => {
            const colors = { 'A': '#28a745', 'B': '#0d6efd', 'C': '#0dcaf0', 'D': '#ffc107', 'F': '#dc3545' };
            return colors[grade] || '#6c757d';
        };

        gradeDistChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.letter),
                datasets: [{
                    label: 'Số lượng',
                    data: data.map(d => d.count),
                    backgroundColor: data.map(d => getGradeColor(d.letter)),
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
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
                plugins: { legend: { display: false } }
            }
        });
    }

    function renderRecentClasses(classes) {
        const tbody = document.querySelector('#recent-classes-table tbody');
        tbody.innerHTML = '';
        
        if (!classes || classes.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Chưa có lớp học nào.</td></tr>';
            return;
        }
        
        classes.forEach(cls => {
            const statusBadge = cls.is_locked == 1 
                ? '<span class="badge bg-danger"><i class="bi bi-lock-fill"></i> Đã chốt</span>' 
                : '<span class="badge bg-success"><i class="bi bi-unlock-fill"></i> Chưa chốt</span>';
                
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><span class="fw-bold text-primary">${cls.class_code}</span></td>
                <td>${cls.subject_name}</td>
                <td>${cls.semester}</td>
                <td>${cls.academic_year}</td>
                <td>${statusBadge}</td>
                <td class="text-end">
                    <a href="<?= url('/academic/scores?class_id=') ?>${cls.id}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> Xem
                    </a>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
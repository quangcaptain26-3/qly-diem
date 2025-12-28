<?php
$title = 'Dashboard - Trưởng khoa';
require __DIR__ . '/../layouts/header.php';
?>

            <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h2>
            
            <!-- Thống kê nhanh -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card text-center border-start border-primary border-4">
                        <div class="card-body">
                            <i class="bi bi-journal-text fs-1 text-primary"></i>
                            <h3 class="mt-2 text-primary"><?= count($classes) ?></h3>
                            <p class="text-muted mb-0">Lớp học</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center border-start border-success border-4">
                        <div class="card-body">
                            <i class="bi bi-award fs-1 text-success"></i>
                            <h3 class="mt-2 text-success"><?= count($scholarships) ?></h3>
                            <p class="text-muted mb-0">Học bổng</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center border-start border-warning border-4">
                        <div class="card-body">
                            <i class="bi bi-exclamation-triangle fs-1 text-warning"></i>
                            <h3 class="mt-2 text-warning"><?= count($warnings) ?></h3>
                            <p class="text-muted mb-0">Cảnh báo</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center border-start border-info border-4">
                        <div class="card-body">
                            <i class="bi bi-graph-up fs-1 text-info"></i>
                            <h3 class="mt-2 text-info"><?= number_format($passFail['pass_rate'] ?? 0, 1) ?>%</h3>
                            <p class="text-muted mb-0">Tỷ lệ đậu</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Biểu đồ -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="bi bi-graph-up"></i> Điểm trung bình theo Học kỳ</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="avgScoreChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="bi bi-pie-chart"></i> Tỷ lệ Đậu/Rớt</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="passFailChart"></canvas>
                            <div class="mt-3 text-center">
                                <p class="mb-1"><strong><?= $passFail['pass_rate'] ?? 0 ?>%</strong> Đậu</p>
                                <p class="mb-0"><strong><?= $passFail['fail_rate'] ?? 0 ?>%</strong> Rớt</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Học bổng và Cảnh báo -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-award"></i> Học bổng (Top 5)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($scholarships)): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Mã SV</th>
                                                <th>Họ tên</th>
                                                <th>GPA</th>
                                                <th>Loại</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($scholarships, 0, 5) as $scholarship): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($scholarship['student_code']) ?></td>
                                                    <td><?= htmlspecialchars($scholarship['full_name']) ?></td>
                                                    <td><strong><?= number_format($scholarship['gpa'], 2) ?></strong></td>
                                                    <td><span class="badge bg-success"><?= htmlspecialchars($scholarship['type']) ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
                                <a href="<?= url('/dean/scholarship') ?>" class="btn btn-sm btn-success w-100">Xem tất cả</a>
                            <?php else: ?>
                                <p class="text-muted text-center">Chưa có học bổng</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Cảnh báo (Top 5)</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($warnings)): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Mã SV</th>
                                                <th>Họ tên</th>
                                                <th>GPA</th>
                                                <th>Trạng thái</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($warnings, 0, 5) as $warning): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($warning['student_code']) ?></td>
                                                    <td><?= htmlspecialchars($warning['full_name']) ?></td>
                                                    <td><strong class="text-danger"><?= number_format($warning['gpa'], 2) ?></strong></td>
                                                    <td><span class="badge bg-danger">Cảnh báo</span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
                                <a href="<?= url('/academic/warning') ?>" class="btn btn-sm btn-warning w-100">Xem tất cả</a>
                            <?php else: ?>
                                <p class="text-muted text-center">Không có cảnh báo</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

<script>
    // Điểm trung bình theo học kỳ
    const avgScoreData = <?= json_encode($avgScores) ?>;
    if (avgScoreData.length > 0) {
        const avgScoreCtx = document.getElementById('avgScoreChart').getContext('2d');
        new Chart(avgScoreCtx, {
            type: 'line',
            data: {
                labels: avgScoreData.map(d => d.academic_year + ' ' + d.semester),
                datasets: [{
                    label: 'Điểm trung bình',
                    data: avgScoreData.map(d => parseFloat(d.avg_score || 0)),
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.1)',
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 0,
                        max: 10
                    }
                }
            }
        });
    }
    
    // Tỷ lệ Đậu/Rớt
    const passFailCtx = document.getElementById('passFailChart').getContext('2d');
    new Chart(passFailCtx, {
        type: 'doughnut',
        data: {
            labels: ['Đậu', 'Rớt'],
            datasets: [{
                data: [<?= $passFail['passed'] ?? 0 ?>, <?= $passFail['failed'] ?? 0 ?>],
                backgroundColor: ['#28a745', '#dc3545'],
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
        }
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

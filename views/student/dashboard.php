<?php
$title = 'Dashboard - Sinh viên';
require __DIR__ . '/../layouts/header.php';
?>

            <h2 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h2>
            
            <!-- Thống kê cá nhân -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card text-center border-start border-primary border-4">
                        <div class="card-body">
                            <i class="bi bi-star-fill fs-1 text-primary"></i>
                            <h3 class="mt-2 text-primary"><?= $gpa['gpa'] !== null ? number_format($gpa['gpa'], 2) : 'N/A' ?></h3>
                            <p class="text-muted mb-0">GPA</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center border-start border-success border-4">
                        <div class="card-body">
                            <i class="bi bi-book fs-1 text-success"></i>
                            <h3 class="mt-2 text-success"><?= $gpa['total_credits'] ?? 0 ?></h3>
                            <p class="text-muted mb-0">Tín chỉ</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center border-start border-info border-4">
                        <div class="card-body">
                            <i class="bi bi-journal-text fs-1 text-info"></i>
                            <h3 class="mt-2 text-info"><?= count($enrollments) ?></h3>
                            <p class="text-muted mb-0">Lớp học</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card text-center border-start border-warning border-4">
                        <div class="card-body">
                            <i class="bi bi-check-circle fs-1 text-warning"></i>
                            <h3 class="mt-2 text-warning"><?= count(array_filter($enrollments, fn($e) => !empty($e['score_id']))) ?></h3>
                            <p class="text-muted mb-0">Đã có điểm</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Biểu đồ GPA -->
            <?php if ($gpa['gpa'] !== null): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="bi bi-graph-up"></i> Thống kê GPA</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="gpaChart"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h6>Phân loại GPA:</h6>
                                <?php 
                                $gpaValue = $gpa['gpa'];
                                $grade = 'D';
                                $color = 'danger';
                                if ($gpaValue >= 3.5) {
                                    $grade = 'A';
                                    $color = 'success';
                                } elseif ($gpaValue >= 3.0) {
                                    $grade = 'B';
                                    $color = 'primary';
                                } elseif ($gpaValue >= 2.0) {
                                    $grade = 'C';
                                    $color = 'warning';
                                }
                                ?>
                                <div class="alert alert-<?= $color ?>">
                                    <h4 class="mb-0">GPA: <?= number_format($gpaValue, 2) ?> - Xếp loại: <strong><?= $grade ?></strong></h4>
                                </div>
                                <p><strong>Tín chỉ đã tích lũy:</strong> <?= $gpa['total_credits'] ?></p>
                                <p><strong>Tổng điểm có trọng số:</strong> <?= number_format($gpa['weighted_sum'], 2) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-list-ul"></i> Lớp học của tôi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover datatable">
                            <thead>
                                <tr>
                                    <th>Mã lớp</th>
                                    <th>Học phần</th>
                                    <th>Tín chỉ</th>
                                    <th>Học kỳ</th>
                                    <th>Năm học</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrollments as $enrollment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($enrollment['class_code']) ?></td>
                                        <td><?= htmlspecialchars($enrollment['subject_name']) ?></td>
                                        <td><?= $enrollment['credits'] ?? '-' ?></td>
                                        <td><?= htmlspecialchars($enrollment['semester']) ?></td>
                                        <td><?= htmlspecialchars($enrollment['academic_year']) ?></td>
                                        <td>
                                            <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
                                            <a href="<?= url('/student/scores?academic_year=' . $enrollment['academic_year'] . '&semester=' . $enrollment['semester']) ?>" class="btn btn-sm btn-primary">
                                                <i class="bi bi-eye"></i> Xem điểm
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <script>
                <?php if ($gpa['gpa'] !== null): ?>
                // Biểu đồ GPA
                const gpaCtx = document.getElementById('gpaChart').getContext('2d');
                const gpaValue = <?= $gpa['gpa'] ?>;
                new Chart(gpaCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['GPA hiện tại', 'Còn lại'],
                        datasets: [{
                            data: [gpaValue, 4.0 - gpaValue],
                            backgroundColor: ['#6f42c1', '#e9ecef'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                display: true,
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'GPA: ' + gpaValue.toFixed(2) + '/4.0';
                                    }
                                }
                            }
                        }
                    }
                });
                <?php endif; ?>
            </script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


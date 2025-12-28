<?php
$title = 'Chi tiết Học sinh - Root';
require __DIR__ . '/../layouts/header.php';
?>

<div class="mb-4">
    <a href="<?= url('/root/students') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
</div>

<div class="row">
    <!-- Student Info & Stats -->
    <div class="col-md-4 mb-4">
        <!-- Info Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary"><i class="bi bi-person-badge"></i> Thông tin Sinh viên</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 2rem;">
                        <?= strtoupper(substr($student['full_name'] ?? 'S', 0, 1)) ?>
                    </div>
                    <h5 class="mt-3 mb-1"><?= htmlspecialchars($student['full_name']) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($student['student_code']) ?></p>
                </div>
                
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted"><i class="bi bi-envelope"></i> Email</span>
                        <span><?= htmlspecialchars($student['email']) ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-muted"><i class="bi bi-building"></i> Khoa</span>
                        <span><?= htmlspecialchars($student['faculty_name'] ?? 'N/A') ?></span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-success"><i class="bi bi-bar-chart"></i> Thống kê Học tập</h5>
            </div>
            <div class="card-body">
                <div class="row text-center g-3">
                    <div class="col-6">
                        <div class="p-3 border rounded bg-light h-100">
                            <h2 class="text-primary mb-0"><?= $gpaData['gpa'] ?? '-' ?></h2>
                            <small class="text-muted">GPA Tích lũy</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 border rounded bg-light h-100">
                            <h2 class="text-info mb-0"><?= $gpaData['total_credits'] ?></h2>
                            <small class="text-muted">Tín chỉ tích lũy</small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 border rounded bg-light">
                            <h4 class="mb-0 text-<?= $perfLevel['color'] ?>"><?= $perfLevel['label'] ?></h4>
                            <small class="text-muted">Xếp loại học lực</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Score History -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-journal-text"></i> Lịch sử Điểm thi</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Năm học</th>
                                <th>Học kỳ</th>
                                <th>Môn học</th>
                                <th class="text-center">Tín chỉ</th>
                                <th class="text-center">Điểm TK</th>
                                <th class="text-center">GPA</th>
                                <th class="text-center">Điểm chữ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($scores)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Chưa có dữ liệu điểm.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($scores as $score): ?>
                                    <tr>
                                        <td><?= $score['academic_year'] ?></td>
                                        <td><?= $score['semester'] ?></td>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($score['subject_name']) ?></div>
                                            <small class="text-muted"><?= $score['subject_code'] ?></small>
                                        </td>
                                        <td class="text-center"><?= $score['credits'] ?></td>
                                        <td class="text-center fw-bold text-primary"><?= $score['z'] ?></td>
                                        <td class="text-center fw-bold"><?= $score['gpa'] ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-<?= $score['letter'] === 'F' ? 'danger' : ($score['letter'] === 'A' ? 'success' : 'secondary') ?>">
                                                <?= $score['letter'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

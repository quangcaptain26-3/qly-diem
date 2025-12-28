<?php
$title = 'Hồ sơ Sinh viên - Giáo vụ';
require __DIR__ . '/../layouts/header.php';
?>

<div class="mb-4">
    <a href="<?= url('/academic/students') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại tìm kiếm
    </a>
</div>

<div class="row">
    <!-- Student Info -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-body text-center">
                <div class="avatar bg-info text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                    <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
                </div>
                <h4><?= htmlspecialchars($student['full_name']) ?></h4>
                <p class="text-muted"><?= htmlspecialchars($student['student_code']) ?></p>
                <span class="badge bg-primary mb-3"><?= htmlspecialchars($student['faculty_name']) ?></span>
                
                <hr>
                
                <div class="row text-center mt-3">
                    <div class="col-6">
                        <h3 class="fw-bold text-primary"><?= $gpaData['gpa'] ?? '-' ?></h3>
                        <small class="text-muted">GPA</small>
                    </div>
                    <div class="col-6">
                        <h3 class="fw-bold text-info"><?= $gpaData['total_credits'] ?></h3>
                        <small class="text-muted">Tín chỉ</small>
                    </div>
                </div>
                 <div class="mt-3">
                    <span class="badge bg-<?= $perfLevel['color'] ?> p-2"><?= $perfLevel['label'] ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Score History -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><i class="bi bi-journal-check"></i> Bảng điểm chi tiết</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>HK / Năm học</th>
                                <th>Môn học</th>
                                <th class="text-center">TC</th>
                                <th class="text-center">Điểm chữ</th>
                                <th class="text-center">Điểm hệ 4</th>
                                <th class="text-center">Điểm hệ 10</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($scores)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Chưa có dữ liệu điểm.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($scores as $score): ?>
                                    <tr>
                                        <td><?= $score['semester'] ?> <br> <small class="text-muted"><?= $score['academic_year'] ?></small></td>
                                        <td>
                                            <div class="fw-bold"><?= htmlspecialchars($score['subject_name']) ?></div>
                                            <small class="text-muted"><?= $score['subject_code'] ?></small>
                                        </td>
                                        <td class="text-center"><?= $score['credits'] ?></td>
                                        <td class="text-center">
                                             <span class="badge bg-<?= $score['letter'] === 'F' ? 'danger' : ($score['letter'] === 'A' ? 'success' : 'secondary') ?>">
                                                <?= $score['letter'] ?>
                                            </span>
                                        </td>
                                        <td class="text-center fw-bold"><?= $score['gpa'] ?></td>
                                        <td class="text-center text-primary fw-bold"><?= $score['z'] ?></td>
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

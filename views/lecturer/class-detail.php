<?php
$title = 'Chi tiết Lớp học - Giảng viên';
require __DIR__ . '/../layouts/header.php';
?>

            <h2 class="mb-4"><i class="bi bi-journal-text"></i> <?= htmlspecialchars($class['class_code']) ?></h2>
            
            <div class="card mb-3">
                <div class="card-body">
                    <h5><?= htmlspecialchars($class['subject_name']) ?></h5>
                    <p class="text-muted">
                        <strong>Học kỳ:</strong> <?= htmlspecialchars($class['semester']) ?> | 
                        <strong>Năm học:</strong> <?= htmlspecialchars($class['academic_year']) ?>
                    </p>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5>Danh sách Sinh viên (<?= count($enrollments) ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã SV</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrollments as $enrollment): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($enrollment['student_code']) ?></td>
                                        <td><?= htmlspecialchars($enrollment['full_name']) ?></td>
                                        <td><?= htmlspecialchars($enrollment['email'] ?? '-') ?></td>
                                        <td>
                                            <span class="badge bg-<?= ($enrollment['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                                <?= ($enrollment['status'] ?? 'active') === 'active' ? 'Đang học' : htmlspecialchars($enrollment['status'] ?? '') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


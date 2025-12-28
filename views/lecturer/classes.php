<?php
$title = 'Lớp học - Giảng viên';
require __DIR__ . '/../layouts/header.php';
?>

            <h2 class="mb-4"><i class="bi bi-journal-text"></i> Lớp học của tôi</h2>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã lớp</th>
                                    <th>Học phần</th>
                                    <th>Học kỳ</th>
                                    <th>Năm học</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($classes as $class): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($class['class_code']) ?></td>
                                        <td><?= htmlspecialchars($class['subject_name']) ?></td>
                                        <td><?= htmlspecialchars($class['semester']) ?></td>
                                        <td><?= htmlspecialchars($class['academic_year']) ?></td>
                                        <td>
                                            <?php if ($class['is_locked']): ?>
                                                <span class="badge bg-danger">Đã chốt</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Chưa chốt</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
<a href="<?= url('/lecturer/class?id=' . $class['id']) ?>" class="btn btn-primary" title="Chi tiết">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= url('/lecturer/scores?class_id=' . $class['id']) ?>" class="btn btn-success" title="Nhập điểm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="<?= url('/lecturer/attendance?class_id=' . $class['id']) ?>" class="btn btn-info" title="Điểm danh">
                                                    <i class="bi bi-calendar-check"></i>
                                                </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


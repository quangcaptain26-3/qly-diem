<?php
$title = 'Dashboard - Giảng viên';
require __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../../models/Score.php';
require_once __DIR__ . '/../../models/Enrollment.php';

// Các biến đã được truyền từ Controller: $classes (current), $academicYear, $semester

// Thống kê trên dữ liệu hiện tại
$totalClasses = count($classes);
$lockedClasses = count(array_filter($classes, fn($c) => $c['is_locked']));
$unlockedClasses = $totalClasses - $lockedClasses;

// Tính tổng sinh viên
$totalStudents = 0;
foreach ($classes as $class) {
    // Lưu ý: Đây là ước tính nhanh dựa trên max_students hoặc query enrollment thực tế nếu cần chính xác tuyệt đối
    // Ở đây ta dùng model Enrollment để đếm chính xác
    $totalStudents += count((new Enrollment())->findByClassRoom($class['id']));
}

// Lấy điểm cần nhập (lớp chưa chốt và chưa có điểm đầy đủ)
$scoreModel = new Score();
$classesNeedInput = [];
foreach ($classes as $class) {
    if (!$class['is_locked']) {
        $scores = $scoreModel->findByClassRoom($class['id']);
        $enrollments = (new Enrollment())->findByClassRoom($class['id']);
        // Nếu số lượng điểm ít hơn số lượng sinh viên -> Cần nhập thêm
        $hasIncompleteScores = count($scores) < count($enrollments);
        if ($hasIncompleteScores) {
            $classesNeedInput[] = $class;
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1"><i class="bi bi-speedometer2"></i> Tổng quan</h2>
        <p class="text-muted mb-0">Học kỳ <strong><?= $semester ?></strong> - Năm học <strong><?= $academicYear ?></strong></p>
    </div>
    <!-- Nút xem lịch sử nếu cần -->
    <a href="<?= url('/lecturer/classes') ?>" class="btn btn-outline-primary">
        <i class="bi bi-archive"></i> Xem tất cả các kỳ
    </a>
</div>

<!-- Thống kê nhanh -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-journal-text fs-3 text-primary"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $totalClasses ?></h3>
                    <span class="text-muted small">Lớp đang dạy</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                    <i class="bi bi-people fs-3 text-info"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $totalStudents ?></h3>
                    <span class="text-muted small">Tổng sinh viên</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                    <i class="bi bi-pencil-square fs-3 text-warning"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= count($classesNeedInput) ?></h3>
                    <span class="text-muted small">Lớp cần nhập điểm</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-check-circle fs-3 text-success"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold"><?= $lockedClasses ?></h3>
                    <span class="text-muted small">Lớp đã hoàn tất</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Danh sách lớp học hiện tại -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-week"></i> Danh sách lớp học phần kỳ này</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Mã lớp</th>
                        <th>Học phần</th>
                        <th class="text-center">Sĩ số</th>
                        <th class="text-center">Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($classes)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                Hiện tại không có lớp học phần nào trong học kỳ này.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($classes as $class): ?>
                            <?php 
                                // Đếm lại sĩ số cho chắc chắn nếu cần hiển thị
                                // $countStudents = count((new Enrollment())->findByClassRoom($class['id'])); 
                                // Để tối ưu query, có thể dùng max_students hoặc query riêng nếu cần chính xác
                            ?>
                            <tr>
                                <td class="ps-4 fw-bold text-primary"><?= htmlspecialchars($class['class_code']) ?></td>
                                <td>
                                    <div class="fw-medium"><?= htmlspecialchars($class['subject_name']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($class['subject_code']) ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary rounded-pill">
                                        <?= $class['max_students'] ?? 'N/A' ?> SV
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($class['is_locked']): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3">
                                            <i class="bi bi-lock-fill me-1"></i> Đã chốt
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                                            <i class="bi bi-unlock-fill me-1"></i> Đang mở
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group">
                                        <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
                                        
                                        <!-- Nút Nhập điểm (Quan trọng nhất) -->
                                        <a href="<?= url('/lecturer/scores?class_id=' . $class['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" 
                                           title="Nhập điểm">
                                            <i class="bi bi-pencil-square"></i> Điểm
                                        </a>

                                        <!-- Nút Điểm danh -->
                                        <a href="<?= url('/lecturer/attendance?class_id=' . $class['id']) ?>" 
                                           class="btn btn-sm btn-outline-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Điểm danh">
                                            <i class="bi bi-clipboard-check"></i> Điểm danh
                                        </a>

                                        <!-- Nút Chi tiết -->
                                        <a href="<?= url('/lecturer/class?id=' . $class['id']) ?>" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           data-bs-toggle="tooltip" 
                                           title="Xem danh sách SV">
                                            <i class="bi bi-people"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Kích hoạt tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
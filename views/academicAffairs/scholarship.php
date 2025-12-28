<?php
$title = 'Xét Học bổng - Giáo vụ';
require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-award-fill text-warning"></i> Danh sách Học bổng Dự kiến</h2>
    <button onclick="window.print()" class="btn btn-outline-primary">
        <i class="bi bi-printer"></i> In Danh sách
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 text-primary fw-bold">Kết quả xét duyệt</h5>
    </div>
    <div class="card-body">
        <?php if (empty($scholarships)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1 d-block mb-3"></i>
                <p>Chưa có sinh viên nào đủ điều kiện nhận học bổng trong danh sách hiện tại.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle datatable">
                    <thead class="bg-light">
                        <tr>
                            <th>Mã SV</th>
                            <th>Họ và Tên</th>
                            <th class="text-center">GPA</th>
                            <th class="text-center">Tín chỉ tích lũy</th>
                            <th class="text-center">Xếp loại</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($scholarships as $student): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($student['student_code']) ?></td>
                                <td><?= htmlspecialchars($student['full_name']) ?></td>
                                <td class="text-center fw-bold text-dark"><?= number_format($student['gpa'], 2) ?></td>
                                <td class="text-center"><?= $student['credits'] ?></td>
                                <td class="text-center">
                                    <?php 
                                        $badgeColor = 'success';
                                        if (strpos(strtolower($student['type']), 'xuất sắc') !== false) $badgeColor = 'warning text-dark';
                                        elseif (strpos(strtolower($student['type']), 'giỏi') !== false) $badgeColor = 'success';
                                        else $badgeColor = 'info';
                                    ?>
                                    <span class="badge bg-<?= $badgeColor ?> rounded-pill px-3">
                                        <?= htmlspecialchars($student['type']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

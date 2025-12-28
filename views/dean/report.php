<?php
$title = 'Báo cáo - Trưởng khoa';
require __DIR__ . '/../layouts/header.php';
?>

<h2 class="mb-4"><i class="bi bi-file-earmark-text"></i> Báo cáo</h2>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Năm học</label>
                <input type="text" name="academic_year" class="form-control"
                    value="<?= htmlspecialchars($academicYear) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Học kỳ</label>
                <select name="semester" class="form-select">
                    <option value="HK1" <?= $semester === 'HK1' ? 'selected' : '' ?>>Học kỳ 1</option>
                    <option value="HK2" <?= $semester === 'HK2' ? 'selected' : '' ?>>Học kỳ 2</option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-filter"></i> Xem báo
                        cáo</button>
                    <a href="<?= url('/dean/report/export?academic_year=' . $academicYear . '&semester=' . $semester) ?>"
                        class="btn btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Xuất Báo cáo
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

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
                                <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
                                <a href="<?= url('/academic/scores?class_id=' . $class['id']) ?>"
                                    class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Xem
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
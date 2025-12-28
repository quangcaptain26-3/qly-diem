<?php
$title = 'Xem Điểm - Sinh viên';
require __DIR__ . '/../layouts/header.php';
?>

            <h2 class="mb-4"><i class="bi bi-clipboard-check"></i> Điểm của tôi</h2>
            
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <input type="hidden" name="route" value="/student/scores">
                        <div class="col-md-4">
                            <select name="academic_year" class="form-select">
                                <option value="">Tất cả năm học</option>
                                <?php foreach ($academicYears as $year): ?>
                                    <option value="<?= htmlspecialchars($year) ?>" <?= ($academicYear ?? '') === $year ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($year) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="semester" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="HK1" <?= ($semester ?? '') === 'HK1' ? 'selected' : '' ?>>HK1</option>
                                <option value="HK2" <?= ($semester ?? '') === 'HK2' ? 'selected' : '' ?>>HK2</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">Lọc</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if ($gpa['gpa'] !== null): ?>
                <div class="alert alert-info">
                    <strong>GPA:</strong> <?= $gpa['gpa'] ?> | 
                    <strong>Tín chỉ:</strong> <?= $gpa['total_credits'] ?>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Học phần</th>
                                    <th>X1</th>
                                    <th>X2</th>
                                    <th>X3</th>
                                    <th>CC</th>
                                    <th>Y</th>
                                    <th>Z</th>
                                    <th>GPA</th>
                                    <th>Letter</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($scores)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            Chưa có dữ liệu điểm cho học kỳ này
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($scores as $score): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($score['subject_name']) ?></td>
                                            <td><?= $score['x1'] ?? '-' ?></td>
                                            <td><?= $score['x2'] ?? '-' ?></td>
                                            <td><?= $score['x3'] ?? '-' ?></td>
                                            <td><?= $score['cc'] ?? '-' ?></td>
                                            <td><?= $score['y'] ?? '-' ?></td>
                                            <td><strong><?= $score['z'] ?? '-' ?></strong></td>
                                            <td><?= $score['gpa'] ?? '-' ?></td>
                                            <td><span class="badge bg-primary"><?= $score['letter'] ?? '-' ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


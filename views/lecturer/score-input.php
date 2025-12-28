<?php
$title = 'Nhập Điểm - Giảng viên';
require __DIR__ . '/../layouts/header.php';
?>

            <h2 class="mb-4"><i class="bi bi-pencil-square"></i> Nhập Điểm - <?= htmlspecialchars($class['class_code']) ?></h2>
            
            
            <?php if ($class['is_locked']): ?>
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="bi bi-lock"></i> Lớp này đã được chốt điểm. Chỉ root mới có thể mở lại.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (!$class['is_locked']): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
                    <a href="<?= url('/lecturer/import-score?class_id=' . $class['id']) ?>" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Import từ Excel
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
            <form method="POST" action="<?= url('/lecturer/scores/save') ?>">
                <input type="hidden" name="class_room_id" value="<?= $class['id'] ?>">
                
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã SV</th>
                                        <th>Họ tên</th>
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
                                    <?php foreach ($enrollments as $enrollment): ?>
                                        <?php $score = $scores[$enrollment['id']] ?? null; ?>
                                        <tr>
                                            <td><?= htmlspecialchars($enrollment['student_code']) ?></td>
                                            <td><?= htmlspecialchars($enrollment['full_name']) ?></td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="10" 
                                                       name="scores[<?= $enrollment['id'] ?>][x1]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?= $score['x1'] ?? '' ?>" 
                                                       <?= $class['is_locked'] ? 'readonly' : '' ?>>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="10" 
                                                       name="scores[<?= $enrollment['id'] ?>][x2]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?= $score['x2'] ?? '' ?>" 
                                                       <?= $class['is_locked'] ? 'readonly' : '' ?>>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="10" 
                                                       name="scores[<?= $enrollment['id'] ?>][x3]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?= $score['x3'] ?? '' ?>" 
                                                       <?= $class['is_locked'] ? 'readonly' : '' ?>>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="10" 
                                                       name="scores[<?= $enrollment['id'] ?>][cc]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?= $score['cc'] ?? '' ?>" 
                                                       <?= $class['is_locked'] ? 'readonly' : '' ?>>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0" max="10" 
                                                       name="scores[<?= $enrollment['id'] ?>][y]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?= $score['y'] ?? '' ?>" 
                                                       <?= $class['is_locked'] ? 'readonly' : '' ?>>
                                            </td>
                                            <td><strong><?= $score['z'] ?? '-' ?></strong></td>
                                            <td><?= $score['gpa'] ?? '-' ?></td>
                                            <td><span class="badge bg-primary"><?= $score['letter'] ?? '-' ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (!$class['is_locked']): ?>
                            <button type="submit" class="btn btn-primary mt-3">
                                <i class="bi bi-save"></i> Lưu điểm
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


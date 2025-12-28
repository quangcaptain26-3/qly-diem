<?php
$title = 'Điểm danh - Giảng viên';
require __DIR__ . '/../layouts/header.php';
?>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-calendar-check"></i> Điểm danh Lớp học</h2>
            </div>
            
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5><?= htmlspecialchars($class['class_code']) ?> - <?= htmlspecialchars($class['subject_name']) ?></h5>
                            <p class="mb-0 text-muted">
                                <i class="bi bi-clock"></i> Học kỳ: <?= htmlspecialchars($class['semester']) ?> | 
                                <i class="bi bi-calendar"></i> Năm học: <?= htmlspecialchars($class['academic_year']) ?>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <form method="GET" action="<?= url('/lecturer/attendance') ?>" class="d-flex gap-2">
                                <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($currentDate) ?>" onchange="this.form.submit()">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-filter"></i> Xem</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="<?= url('/lecturer/attendance/save') ?>">
                <input type="hidden" name="class_room_id" value="<?= $class['id'] ?>">
                <input type="hidden" name="attendance_date" value="<?= htmlspecialchars($currentDate) ?>">
                
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped align-middle mb-0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th style="width: 120px;">Mã SV</th>
                                        <th style="width: 200px;">Họ tên</th>
                                        <th class="text-center">Trạng thái điểm danh</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($students)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4">Chưa có sinh viên nào trong lớp này.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($students as $index => $student): ?>
                                            <?php 
                                            // Mặc định là 'present' nếu chưa có dữ liệu
                                            $status = $student['status'] ?? 'present'; 
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= $index + 1 ?></td>
                                                <td class="fw-bold"><?= htmlspecialchars($student['student_code']) ?></td>
                                                <td><?= htmlspecialchars($student['full_name']) ?></td>
                                                <td>
                                                    <div class="d-flex justify-content-center gap-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="attendance[<?= $student['enrollment_id'] ?>]" 
                                                                   id="present_<?= $student['enrollment_id'] ?>" value="present" 
                                                                   <?= $status === 'present' ? 'checked' : '' ?>>
                                                            <label class="form-check-label text-success fw-bold" for="present_<?= $student['enrollment_id'] ?>">Có mặt</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="attendance[<?= $student['enrollment_id'] ?>]" 
                                                                   id="late_<?= $student['enrollment_id'] ?>" value="late"
                                                                   <?= $status === 'late' ? 'checked' : '' ?>>
                                                            <label class="form-check-label text-warning fw-bold" for="late_<?= $student['enrollment_id'] ?>">Đi muộn</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="attendance[<?= $student['enrollment_id'] ?>]" 
                                                                   id="excused_<?= $student['enrollment_id'] ?>" value="excused"
                                                                   <?= $status === 'excused' ? 'checked' : '' ?>>
                                                            <label class="form-check-label text-info fw-bold" for="excused_<?= $student['enrollment_id'] ?>">Có phép</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="attendance[<?= $student['enrollment_id'] ?>]" 
                                                                   id="absent_<?= $student['enrollment_id'] ?>" value="absent"
                                                                   <?= $status === 'absent' ? 'checked' : '' ?>>
                                                            <label class="form-check-label text-danger fw-bold" for="absent_<?= $student['enrollment_id'] ?>">K.Phép</label>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm" name="note[<?= $student['enrollment_id'] ?>]" 
                                                           value="<?= htmlspecialchars($student['notes'] ?? '') ?>" placeholder="Ghi chú...">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small">
                                <i class="bi bi-info-circle"></i> Điểm chuyên cần sẽ được tự động cập nhật khi lưu.
                            </div>
                            <div>
                                <a href="<?= url('/lecturer/classes') ?>" class="btn btn-secondary me-2">Hủy bỏ</a>
                                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save"></i> Lưu điểm danh</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


<?php
$title = 'Học bổng - Trưởng khoa';
require __DIR__ . '/../layouts/header.php';
?>

            <h2 class="mb-4"><i class="bi bi-award"></i> Danh sách Học bổng</h2>
            
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã SV</th>
                                    <th>Họ tên</th>
                                    <th>GPA</th>
                                    <th>Tín chỉ</th>
                                    <th>Loại học bổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($scholarships as $scholarship): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($scholarship['student_code']) ?></td>
                                        <td><?= htmlspecialchars($scholarship['full_name']) ?></td>
                                        <td><strong><?= number_format($scholarship['gpa'], 2) ?></strong></td>
                                        <td><?= $scholarship['credits'] ?></td>
                                        <td><span class="badge bg-success"><?= htmlspecialchars($scholarship['type']) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


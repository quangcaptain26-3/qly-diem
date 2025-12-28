<?php
$title = 'Import Điểm - Giảng viên';
require __DIR__ . '/../layouts/header.php';
?>

            <h2 class="mb-4"><i class="bi bi-upload"></i> Import Điểm từ Excel</h2>
            
            <div class="card mb-3">
                <div class="card-body">
                    <h5><i class="bi bi-info-circle"></i> Hướng dẫn</h5>
                    <p>Để nhập điểm chính xác, vui lòng thực hiện theo quy trình sau:</p>
                    <ol>
                        <li>Tải file mẫu (chứa danh sách sinh viên lớp hiện tại).</li>
                        <li>Nhập điểm vào các cột: <strong>X1, X2, X3, CC, Y</strong>.</li>
                        <li><strong>Không sửa đổi</strong> Mã SV và Họ tên.</li>
                        <li>Upload file đã nhập điểm lên hệ thống.</li>
                    </ol>
                    
                    <a href="<?= url('/lecturer/import-score/template?class_id=' . htmlspecialchars($class_room_id ?? '')) ?>" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Tải file mẫu nhập điểm
                    </a>
                </div>
            </div>
            
            <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
            <form method="POST" action="<?= url('/lecturer/import-score') ?>" enctype="multipart/form-data">
                <input type="hidden" name="class_room_id" value="<?= htmlspecialchars($class_room_id ?? '') ?>">
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Upload bảng điểm</h5>
                        <div class="mb-4">
                            <label for="file" class="form-label">Chọn file Excel (.xlsx, .xls)</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls" required>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Import dữ liệu
                            </button>
                            <a href="<?= url('/lecturer/scores?class_id=' . htmlspecialchars($class_room_id ?? '')) ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>
            </form>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


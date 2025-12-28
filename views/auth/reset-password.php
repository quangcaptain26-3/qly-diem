<?php
$title = 'Đặt lại mật khẩu';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 200px);">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4" style="color: var(--primary-color);">
                        <i class="bi bi-shield-lock"></i> Đặt lại mật khẩu
                    </h2>
                    
                    
                    <form method="POST" action="<?= isset($_GET['route']) ? '?route=/reset-password&token=' : '/?route=/reset-password&token=' ?><?= htmlspecialchars($token ?? '') ?>">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Đặt lại mật khẩu
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
                        <a href="<?= url('/login') ?>" class="text-decoration-none" style="color: var(--primary-color);">Quay lại đăng nhập</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


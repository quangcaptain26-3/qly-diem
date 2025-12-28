<?php
$title = 'Quên mật khẩu';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 200px);">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4" style="color: var(--primary-color);">
                        <i class="bi bi-key"></i> Quên mật khẩu
                    </h2>
                    
                    
                    <form method="POST" action="<?= isset($_GET['route']) ? '?route=/forgot-password' : '/?route=/forgot-password' ?>">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-send"></i> Gửi yêu cầu
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


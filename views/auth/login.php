<?php
$title = 'Đăng nhập';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 200px);">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4" style="color: var(--primary-color);">
                        <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                    </h2>
                    
                    
                    <form method="POST" action="<?= isset($_GET['route']) ? '?route=/login' : '/?route=/login' ?>">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập / Email</label>
                            <input type="text" class="form-control" id="username" name="username" required autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3">
                            <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
                            <a href="<?= url('/forgot-password') ?>" class="text-decoration-none" style="color: var(--primary-color);">Quên mật khẩu?</a>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


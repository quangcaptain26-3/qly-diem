<?php
$title = 'Kiểm tra Email';
require __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: calc(100vh - 200px);">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="bi bi-envelope-check text-primary" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="mb-3" style="color: var(--primary-color);">Kiểm tra Email của bạn</h2>
                    
                    <p class="text-muted mb-4">
                        Chúng tôi đã gửi hướng dẫn đặt lại mật khẩu đến email: <strong><?= htmlspecialchars($email ?? '') ?></strong>
                    </p>
                    
                    <p class="mb-4">
                        Vui lòng kiểm tra hộp thư đến (và cả mục Spam) để hoàn tất quá trình.
                    </p>

                    <div class="alert alert-info border-info">
                        <h5><i class="bi bi-link-45deg"></i> Link đặt lại mật khẩu</h5>
                        <p class="mb-3">Vui lòng sử dụng link bên dưới để đặt lại mật khẩu của bạn:</p>
                        
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="resetLink" value="<?= htmlspecialchars($resetLink ?? '') ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyResetLink()">
                                <i class="bi bi-clipboard"></i> Copy
                            </button>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="<?= htmlspecialchars($resetLink ?? '#') ?>" class="btn btn-success btn-lg">
                                <i class="bi bi-arrow-right-circle"></i> Đặt lại mật khẩu ngay
                            </a>
                        </div>
                        
                        <small class="text-muted d-block mt-2">
                            <i class="bi bi-info-circle"></i> Link này có hiệu lực trong 30 phút
                        </small>
                    </div>
                    
                    <div class="mt-4 border-top pt-3">
                        <a href="<?= url('/login') ?>" class="text-decoration-none text-muted">
                            <i class="bi bi-arrow-left"></i> Quay lại đăng nhập
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyResetLink(event) {
    const input = document.getElementById('resetLink');
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Show success message
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check-circle"></i> Đã copy!';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    } catch (err) {
        // Fallback: use modern Clipboard API
        if (navigator.clipboard) {
            navigator.clipboard.writeText(input.value).then(() => {
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-circle"></i> Đã copy!';
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-success');
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-secondary');
                }, 2000);
            }).catch(() => {
                alert('Không thể copy. Vui lòng copy thủ công.');
            });
        } else {
            alert('Không thể copy. Vui lòng copy thủ công.');
        }
    }
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

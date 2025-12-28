<?php
$title = 'Quản lý Hệ thống - Root';
require __DIR__ . '/../layouts/header.php';
?>

            <h2 class="mb-4"><i class="bi bi-gear"></i> Quản lý Hệ thống</h2>
            
            
            <?php require_once __DIR__ . '/../../helpers/url_helper.php'; ?>
            <form method="POST" action="<?= url('/root/system') ?>">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>Reset mật khẩu hàng loạt</h5>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input type="text" class="form-control" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới">
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5>Chọn người dùng</h5>
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th>Username</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="user_ids[]" value="<?= $user['id'] ?>" class="user-checkbox">
                                            </td>
                                            <td><?= htmlspecialchars($user['username']) ?></td>
                                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td><span class="badge bg-primary"><?= htmlspecialchars($user['role']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="bi bi-arrow-clockwise"></i> Reset mật khẩu
                        </button>
                    </div>
                </div>
            </form>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>


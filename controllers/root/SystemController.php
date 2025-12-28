<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/User.php';

class SystemController extends Controller {
    public function resetPasswords() {
        RoleMiddleware::handle(['root']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userIds = $_POST['user_ids'] ?? [];
            $newPassword = $_POST['new_password'] ?? '123456';
            
            if (empty($userIds)) {
                $_SESSION['error'] = 'Vui lòng chọn ít nhất một người dùng';
                $this->redirect('/root/system');
            }
            
            $userModel = new User();
            $success = 0;
            $failed = 0;
            
            foreach ($userIds as $userId) {
                if ($userModel->resetPassword($userId, $newPassword)) {
                    $success++;
                } else {
                    $failed++;
                }
            }
            
            $_SESSION['success'] = "Đã reset mật khẩu cho {$success} người dùng. Thất bại: {$failed}";
            $this->redirect('/root/system');
        }
        
        $userModel = new User();
        $users = $userModel->findAll(['is_active' => 1], 'role, full_name');
        
        $this->view('root/system', ['users' => $users]);
    }
}


<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../models/User.php';

Auth::start();

class ResetPasswordController extends Controller {
    public function show() {
        $this->view('auth/forgot-password');
    }

    public function request() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/forgot-password');
        }
        
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            $_SESSION['error'] = 'Vui lòng nhập email';
            $this->redirect('/forgot-password');
        }
        
        $userModel = new User();
        $user = $userModel->findAll(['email' => $email])[0] ?? null;
        
        if (!$user) {
            $_SESSION['error'] = 'Email không tồn tại trong hệ thống';
            $this->redirect('/forgot-password');
        }
        
        // Generate token
        $token = bin2hex(random_bytes(32));
        
        $db = Database::getInstance()->getConnection();
        // Use MySQL time function to ensure consistency with the check query later
        $stmt = $db->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))");
        $stmt->execute([$user['id'], $token]);
        
        // Generate correct link using Router helper với full URL
        $resetLink = Router::url('/reset-password?token=' . $token, true);
        
        // Render the verify email page instead of redirecting
        $this->view('auth/verify-email', [
            'email' => $email,
            'resetLink' => $resetLink
        ]);
    }

    public function reset() {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $_SESSION['error'] = 'Token không hợp lệ';
            $this->redirect('/forgot-password');
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM password_reset_tokens WHERE token = ? AND expires_at > NOW() AND used_at IS NULL");
        $stmt->execute([$token]);
        $tokenData = $stmt->fetch();
        
        if (!$tokenData) {
            $_SESSION['error'] = 'Token không hợp lệ hoặc đã hết hạn';
            $this->redirect('/forgot-password');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            if (empty($password) || $password !== $confirmPassword) {
                $_SESSION['error'] = 'Mật khẩu không khớp';
                $this->view('auth/reset-password', ['token' => $token]);
                return;
            }
            
            // Update password
            $userModel = new User();
            $userModel->resetPassword($tokenData['user_id'], $password);
            
            // Mark token as used
            $stmt = $db->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE id = ?");
            $stmt->execute([$tokenData['id']]);
            
            $_SESSION['success'] = 'Đặt lại mật khẩu thành công';
            $this->redirect('/login');
        } else {
            $this->view('auth/reset-password', ['token' => $token]);
        }
    }
}


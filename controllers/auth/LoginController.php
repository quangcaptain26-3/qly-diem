<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';

Auth::start();

class LoginController extends Controller {
    public function show() {
        if (Auth::check()) {
            $this->redirect('/dashboard');
        }
        
        $this->view('auth/login');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/login');
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
            $this->redirect('/login');
        }
        
        if (Auth::login($username, $password)) {
            $this->redirect('/dashboard');
        } else {
            $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng';
            $this->redirect('/login');
        }
    }
}


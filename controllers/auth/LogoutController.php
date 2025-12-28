<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';

Auth::start();

class LogoutController extends Controller {
    public function logout() {
        Auth::logout();
        $this->redirect('/login');
    }
}


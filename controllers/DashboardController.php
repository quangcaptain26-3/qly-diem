<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class DashboardController extends Controller {
    public function index() {
        AuthMiddleware::handle();
        
        $role = Auth::role();
        
        switch ($role) {
            case 'root':
                $this->redirect('/root/dashboard');
                break;
            case 'dean':
                $this->redirect('/dean/dashboard');
                break;
            case 'academic_affairs':
                $this->redirect('/academic/dashboard');
                break;
            case 'lecturer':
                $this->redirect('/lecturer/dashboard');
                break;
            case 'student':
                $this->redirect('/student/dashboard');
                break;
            default:
                $this->redirect('/login');
        }
    }
}


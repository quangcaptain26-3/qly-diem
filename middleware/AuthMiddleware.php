<?php
/**
 * Authentication Middleware
 */

require_once __DIR__ . '/../core/Auth.php';

class AuthMiddleware {
    public static function handle() {
        if (!Auth::check()) {
            require_once __DIR__ . '/../core/Router.php';
            header('Location: ' . Router::url('/login'));
            exit;
        }
    }
}

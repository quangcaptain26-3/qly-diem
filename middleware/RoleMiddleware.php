<?php
/**
 * Role-based Access Control Middleware
 */

require_once __DIR__ . '/AuthMiddleware.php';
require_once __DIR__ . '/../core/Auth.php';

class RoleMiddleware {
    public static function handle($allowedRoles = []) {
        AuthMiddleware::handle();
        
        $userRole = Auth::role();
        
        if (!in_array($userRole, $allowedRoles)) {
            http_response_code(403);
            die("403 - Access Denied");
        }
    }
}

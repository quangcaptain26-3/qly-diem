<?php
/**
 * Authentication Class
 */

class Auth {
    private static $user = null;

    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            $config = require __DIR__ . '/../config/app.php';
            session_name($config['session_name']);
            session_start();
        }
    }

    public static function login($username, $password) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && self::verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_faculty_id'] = $user['faculty_id'];
            $_SESSION['user_department_id'] = $user['department_id'];
            self::$user = $user;
            return true;
        }

        return false;
    }

    public static function logout() {
        session_destroy();
        self::$user = null;
    }

    public static function check() {
        return isset($_SESSION['user_id']);
    }

    public static function user() {
        if (self::$user === null && self::check()) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            self::$user = $stmt->fetch();
        }
        return self::$user;
    }

    public static function id() {
        return $_SESSION['user_id'] ?? null;
    }

    public static function role() {
        return $_SESSION['user_role'] ?? null;
    }

    public static function name() {
        return $_SESSION['user_name'] ?? null;
    }

    public static function facultyId() {
        return $_SESSION['user_faculty_id'] ?? null;
    }

    public static function departmentId() {
        return $_SESSION['user_department_id'] ?? null;
    }

    public static function hashPassword($password) {
        return hash('sha256', $password);
    }

    public static function verifyPassword($password, $hash) {
        return self::hashPassword($password) === $hash;
    }

    public static function hasPermission($permission) {
        if (!self::check()) return false;
        
        $role = self::role();
        $roles = require __DIR__ . '/../config/roles.php';
        
        return in_array($permission, $roles['permissions'][$role] ?? []);
    }
}


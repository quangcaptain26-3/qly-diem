<?php
/**
 * Score Lock Middleware - Prevent editing locked scores
 */

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Auth.php';

class ScoreLockMiddleware {
    public static function handle($classRoomId) {
        $db = Database::getInstance()->getConnection();
        
        // Check if class is locked
        $stmt = $db->prepare("SELECT is_locked FROM class_rooms WHERE id = ?");
        $stmt->execute([$classRoomId]);
        $class = $stmt->fetch();
        
        if ($class && $class['is_locked']) {
            // Only root can unlock
            if (Auth::role() !== 'root') {
                http_response_code(403);
                die("403 - Điểm đã được chốt và không thể chỉnh sửa");
            }
        }
    }
}

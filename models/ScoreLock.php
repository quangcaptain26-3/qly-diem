<?php
require_once __DIR__ . '/../core/Model.php';

class ScoreLock extends Model {
    protected $table = 'score_locks';

    public function findByClassRoom($classRoomId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE class_room_id = ?");
        $stmt->execute([$classRoomId]);
        return $stmt->fetch();
    }

    public function isLocked($classRoomId) {
        $lock = $this->findByClassRoom($classRoomId);
        return $lock && $lock['is_locked'] == 1;
    }

    public function getLockedClasses() {
        $sql = "SELECT sl.*, cr.class_code as class_name, s.name as subject_name, s.code as subject_code, 
                u.full_name as lecturer_name, u.username as lecturer_code
                FROM {$this->table} sl
                JOIN class_rooms cr ON cr.id = sl.class_room_id
                JOIN subjects s ON s.id = cr.subject_id
                JOIN users u ON u.id = cr.lecturer_id
                WHERE sl.is_locked = 1
                ORDER BY sl.locked_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}


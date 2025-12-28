<?php
require_once __DIR__ . '/../core/Model.php';

class Enrollment extends Model {
    protected $table = 'enrollments';

    public function findByStudent($studentId, $academicYear = null, $semester = null) {
        $sql = "SELECT e.*, cr.class_code, cr.semester, cr.academic_year,
                s.name as subject_name, s.code as subject_code, s.credits
                FROM {$this->table} e
                JOIN class_rooms cr ON cr.id = e.class_room_id
                JOIN subjects s ON s.id = cr.subject_id
                WHERE e.student_id = ? AND e.status = 'active'";
        $params = [$studentId];
        
        if ($academicYear) {
            $sql .= " AND cr.academic_year = ?";
            $params[] = $academicYear;
        }
        
        if ($semester) {
            $sql .= " AND cr.semester = ?";
            $params[] = $semester;
        }
        
        $sql .= " ORDER BY cr.academic_year DESC, cr.semester DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findByClassRoom($classRoomId) {
        $sql = "SELECT e.*, u.full_name, u.student_code, u.id as student_id
                FROM {$this->table} e
                JOIN users u ON u.id = e.student_id
                WHERE e.class_room_id = ? AND e.status = 'active'
                ORDER BY u.student_code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$classRoomId]);
        return $stmt->fetchAll();
    }
}


<?php
require_once __DIR__ . '/../core/Model.php';

class Faculty extends Model {
    protected $table = 'faculties';

    public function getWithStats() {
        $sql = "SELECT f.*, 
                COUNT(DISTINCT d.id) as total_departments,
                (
                    SELECT COUNT(DISTINCT u.id)
                    FROM users u
                    WHERE u.role = 'student' 
                    AND u.is_active = 1
                    AND (
                        u.faculty_id = f.id
                        OR EXISTS (
                            SELECT 1
                            FROM enrollments e
                            JOIN class_rooms cr ON cr.id = e.class_room_id
                            JOIN subjects s ON s.id = cr.subject_id
                            JOIN departments d2 ON d2.id = s.department_id
                            WHERE e.student_id = u.id
                            AND d2.faculty_id = f.id
                        )
                    )
                ) as total_students
                FROM {$this->table} f
                LEFT JOIN departments d ON d.faculty_id = f.id
                GROUP BY f.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}


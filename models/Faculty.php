<?php
require_once __DIR__ . '/../core/Model.php';

class Faculty extends Model {
    protected $table = 'faculties';

    public function getWithStats() {
        $sql = "SELECT f.*, 
                COUNT(DISTINCT u.id) as total_students,
                COUNT(DISTINCT d.id) as total_departments
                FROM {$this->table} f
                LEFT JOIN users u ON u.faculty_id = f.id AND u.role = 'student'
                LEFT JOIN departments d ON d.faculty_id = f.id
                GROUP BY f.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}


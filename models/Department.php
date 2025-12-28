<?php
require_once __DIR__ . '/../core/Model.php';

class Department extends Model {
    protected $table = 'departments';

    public function findByFaculty($facultyId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE faculty_id = ? ORDER BY name");
        $stmt->execute([$facultyId]);
        return $stmt->fetchAll();
    }
}


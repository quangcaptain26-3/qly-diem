<?php
require_once __DIR__ . '/../core/Model.php';

class Major extends Model {
    protected $table = 'majors';

    public function findByFaculty($facultyId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE faculty_id = ? ORDER BY code");
        $stmt->execute([$facultyId]);
        return $stmt->fetchAll();
    }
}


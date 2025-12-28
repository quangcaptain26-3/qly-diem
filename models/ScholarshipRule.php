<?php
require_once __DIR__ . '/../core/Model.php';

class ScholarshipRule extends Model {
    protected $table = 'scholarship_rules';

    public function findByFaculty($facultyId) {
        $sql = "SELECT * FROM {$this->table} WHERE (faculty_id = ? OR faculty_id IS NULL) AND is_active = 1 ORDER BY min_gpa DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$facultyId]);
        return $stmt->fetchAll();
    }

    public function getAllActive() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY min_gpa DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}


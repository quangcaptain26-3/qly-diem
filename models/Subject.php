<?php
require_once __DIR__ . '/../core/Model.php';

class Subject extends Model {
    protected $table = 'subjects';

    public function findByDepartment($departmentId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE department_id = ? ORDER BY code");
        $stmt->execute([$departmentId]);
        return $stmt->fetchAll();
    }

    public function findByCode($code) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE code = ?");
        $stmt->execute([$code]);
        return $stmt->fetch();
    }
}


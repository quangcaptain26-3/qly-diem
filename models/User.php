<?php
require_once __DIR__ . '/../core/Model.php';

class User extends Model {
    protected $table = 'users';

    public function findByUsernameOrEmail($username) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE (username = ? OR email = ?) AND is_active = 1");
        $stmt->execute([$username, $username]);
        return $stmt->fetch();
    }

    public function findByRole($role, $facultyId = null) {
        $sql = "SELECT * FROM {$this->table} WHERE role = ? AND is_active = 1";
        $params = [$role];
        
        if ($facultyId) {
            $sql .= " AND faculty_id = ?";
            $params[] = $facultyId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findByDepartment($departmentId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE department_id = ? AND is_active = 1");
        $stmt->execute([$departmentId]);
        return $stmt->fetchAll();
    }

    public function resetPassword($userId, $newPassword) {
        $hashedPassword = Auth::hashPassword($newPassword);
        return $this->update($userId, ['password' => $hashedPassword]);
    }
}


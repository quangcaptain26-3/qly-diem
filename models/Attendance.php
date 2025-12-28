<?php
require_once __DIR__ . '/../core/Model.php';

class Attendance extends Model {
    protected $table = 'attendances';

    // Lấy danh sách điểm danh của một lớp vào ngày cụ thể
    public function getByClassAndDate($classRoomId, $date) {
        $sql = "SELECT e.id as enrollment_id, u.student_code, u.full_name, a.status, a.notes, a.created_at
                FROM enrollments e
                JOIN users u ON u.id = e.student_id
                LEFT JOIN {$this->table} a ON a.enrollment_id = e.id AND a.attendance_date = ?
                WHERE e.class_room_id = ? AND e.status = 'active'
                ORDER BY u.student_code ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$date, $classRoomId]);
        return $stmt->fetchAll();
    }

    public function getByEnrollment($enrollmentId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE enrollment_id = ? ORDER BY attendance_date DESC");
        $stmt->execute([$enrollmentId]);
        return $stmt->fetchAll();
    }

    // Lưu hoặc Cập nhật trạng thái điểm danh
    public function saveAttendance($enrollmentId, $date, $status, $note = '') {
        // Kiểm tra xem đã có bản ghi chưa
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE enrollment_id = ? AND attendance_date = ?");
        $stmt->execute([$enrollmentId, $date]);
        $exists = $stmt->fetch();

        if ($exists) {
            $sql = "UPDATE {$this->table} SET status = ?, notes = ? WHERE id = ?";
            $updateStmt = $this->db->prepare($sql);
            return $updateStmt->execute([$status, $note, $exists['id']]);
        } else {
            $sql = "INSERT INTO {$this->table} (enrollment_id, attendance_date, status, notes, created_at) VALUES (?, ?, ?, ?, NOW())";
            $insertStmt = $this->db->prepare($sql);
            return $insertStmt->execute([$enrollmentId, $date, $status, $note]);
        }
    }

    public function getSummary($enrollmentId) {
        $stmt = $this->db->prepare("SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent, -- Vắng không phép
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused -- Vắng có phép
                FROM {$this->table} WHERE enrollment_id = ?");
        $stmt->execute([$enrollmentId]);
        return $stmt->fetch();
    }

    // Tính điểm CC dựa trên quy tắc trừ điểm
    // Mặc định 10. Vắng K.Phép -2, Vắng C.Phép -1, Muộn -0.5
    public function calculateAttendanceScore($enrollmentId, $baseScore = 10) {
        $summary = $this->getSummary($enrollmentId);
        
        if (!$summary) return $baseScore;

        $deduction = 0;
        $deduction += ($summary['absent'] * 2);      // Vắng không phép trừ 2
        $deduction += ($summary['excused'] * 1);     // Vắng có phép trừ 1
        $deduction += ($summary['late'] * 0.5);      // Đi muộn trừ 0.5

        $finalScore = $baseScore - $deduction;
        return max(0, $finalScore); // Không thấp hơn 0
    }

    // Đồng bộ điểm CC sang bảng scores
    public function syncScoreToMainTable($enrollmentId, $scoreValue) {
        // Kiểm tra xem đã có bản ghi score chưa
        $stmt = $this->db->prepare("SELECT id FROM scores WHERE enrollment_id = ?");
        $stmt->execute([$enrollmentId]);
        $scoreRecord = $stmt->fetch();

        if ($scoreRecord) {
            $sql = "UPDATE scores SET cc = ? WHERE id = ?";
            $this->db->prepare($sql)->execute([$scoreValue, $scoreRecord['id']]);
        } else {
            $sql = "INSERT INTO scores (enrollment_id, cc) VALUES (?, ?)";
            $this->db->prepare($sql)->execute([$enrollmentId, $scoreValue]);
        }
    }
}


<?php
require_once __DIR__ . '/../core/Model.php';

class Score extends Model {
    protected $table = 'scores';

    public function findByEnrollment($enrollmentId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE enrollment_id = ?");
        $stmt->execute([$enrollmentId]);
        return $stmt->fetch();
    }

    public function findByClassRoom($classRoomId) {
        $sql = "SELECT s.*, e.student_id, u.full_name, u.student_code
                FROM enrollments e
                JOIN users u ON u.id = e.student_id
                LEFT JOIN scores s ON s.enrollment_id = e.id
                WHERE e.class_room_id = ? AND e.status = 'active'
                ORDER BY u.student_code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$classRoomId]);
        return $stmt->fetchAll();
    }

    public function findByStudent($studentId, $academicYear = null, $semester = null) {
        // Change: Select from enrollments first, then LEFT JOIN scores
        // This ensures subjects appear even if no score record exists yet
        $sql = "SELECT s.*, cr.class_code, cr.semester, cr.academic_year,
                sub.name as subject_name, sub.code as subject_code, sub.credits
                FROM enrollments e
                JOIN class_rooms cr ON cr.id = e.class_room_id
                JOIN subjects sub ON sub.id = cr.subject_id
                LEFT JOIN {$this->table} s ON s.enrollment_id = e.id
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

    public function createOrUpdate($enrollmentId, $data, $inputBy) {
        $existing = $this->findByEnrollment($enrollmentId);
        
        $scoreData = [
            'x1' => $data['x1'] ?? null,
            'x2' => $data['x2'] ?? null,
            'x3' => $data['x3'] ?? null,
            'y' => $data['y'] ?? null,
            'cc' => $data['cc'] ?? null,
            'input_by' => $inputBy,
            'input_at' => date('Y-m-d H:i:s'),
        ];
        
        // Calculate Z, GPA, Letter, is_qualified
        require_once __DIR__ . '/../services/ScoreCalculator.php';
        $calculated = ScoreCalculator::calculate($scoreData);
        $scoreData = array_merge($scoreData, $calculated);
        
        if ($existing) {
            return $this->update($existing['id'], $scoreData);
        } else {
            $scoreData['enrollment_id'] = $enrollmentId;
            return $this->create($scoreData);
        }
    }
}


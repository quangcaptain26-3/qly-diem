<?php
require_once __DIR__ . '/../core/Model.php';

class ClassRoom extends Model {
    protected $table = 'class_rooms';

    public function find($id) {
        $sql = "SELECT cr.*, s.name as subject_name, s.code as subject_code, s.credits 
                FROM {$this->table} cr
                JOIN subjects s ON s.id = cr.subject_id
                WHERE cr.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function findByLecturer($lecturerId, $academicYear = null, $semester = null) {
        $sql = "SELECT cr.*, s.name as subject_name, s.code as subject_code, s.credits
                FROM {$this->table} cr
                JOIN subjects s ON s.id = cr.subject_id
                WHERE cr.lecturer_id = ?";
        $params = [$lecturerId];
        
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

    public function findByFaculty($facultyId, $academicYear = null, $semester = null) {
        $sql = "SELECT cr.*, s.name as subject_name, s.code as subject_code, s.credits,
                d.faculty_id
                FROM {$this->table} cr
                JOIN subjects s ON s.id = cr.subject_id
                JOIN departments d ON d.id = s.department_id
                WHERE d.faculty_id = ?";
        $params = [$facultyId];
        
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

    public function getStudents($classRoomId) {
        $sql = "SELECT u.*, e.id as enrollment_id, e.status as enrollment_status
                FROM enrollments e
                JOIN users u ON u.id = e.student_id
                WHERE e.class_room_id = ? AND e.status = 'active'
                ORDER BY u.student_code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$classRoomId]);
        return $stmt->fetchAll();
    }

    public function lock($classRoomId, $lockedBy) {
        $this->db->beginTransaction();
        try {
            // Update class_rooms
            $this->update($classRoomId, [
                'is_locked' => 1,
                'locked_at' => date('Y-m-d H:i:s'),
                'locked_by' => $lockedBy
            ]);
            
            // Update score_locks
            $stmt = $this->db->prepare("SELECT id FROM score_locks WHERE class_room_id = ?");
            $stmt->execute([$classRoomId]);
            $lock = $stmt->fetch();
            
            if ($lock) {
                $stmt = $this->db->prepare("UPDATE score_locks SET is_locked = 1, locked_at = NOW(), locked_by = ? WHERE class_room_id = ?");
                $stmt->execute([$lockedBy, $classRoomId]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO score_locks (class_room_id, is_locked, locked_at, locked_by) VALUES (?, 1, NOW(), ?)");
                $stmt->execute([$classRoomId, $lockedBy]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function unlock($classRoomId, $unlockedBy) {
        $this->db->beginTransaction();
        try {
            // Update class_rooms
            $this->update($classRoomId, [
                'is_locked' => 0,
                'locked_at' => null,
                'locked_by' => null
            ]);
            
            // Update score_locks
            $stmt = $this->db->prepare("UPDATE score_locks SET is_locked = 0, unlocked_at = NOW(), unlocked_by = ? WHERE class_room_id = ?");
            $stmt->execute([$unlockedBy, $classRoomId]);
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getAcademicYears() {
        $stmt = $this->db->prepare("SELECT DISTINCT academic_year FROM {$this->table} ORDER BY academic_year DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}


<?php
/**
 * Statistics Service
 */

require_once __DIR__ . '/GPAService.php';

class StatisticService {
    public static function getAverageScoreBySemester($facultyId = null, $academicYear = null) {
        require_once __DIR__ . '/../core/Database.php';
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT cr.semester, cr.academic_year, AVG(s.z) as avg_score, COUNT(s.id) as total_scores
                FROM scores s
                JOIN enrollments e ON e.id = s.enrollment_id
                JOIN class_rooms cr ON cr.id = e.class_room_id
                JOIN subjects sub ON sub.id = cr.subject_id
                JOIN departments d ON d.id = sub.department_id
                WHERE s.z IS NOT NULL";
        $params = [];
        
        if ($facultyId) {
            $sql .= " AND d.faculty_id = ?";
            $params[] = $facultyId;
        }
        
        if ($academicYear) {
            $sql .= " AND cr.academic_year = ?";
            $params[] = $academicYear;
        }
        
        $sql .= " GROUP BY cr.semester, cr.academic_year ORDER BY cr.academic_year DESC, cr.semester DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getPassFailRate($facultyId = null, $academicYear = null, $semester = null) {
        require_once __DIR__ . '/../core/Database.php';
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN s.z >= 5.5 THEN 1 ELSE 0 END) as passed,
                SUM(CASE WHEN s.z < 5.5 THEN 1 ELSE 0 END) as failed
                FROM scores s
                JOIN enrollments e ON e.id = s.enrollment_id
                JOIN class_rooms cr ON cr.id = e.class_room_id
                JOIN subjects sub ON sub.id = cr.subject_id
                JOIN departments d ON d.id = sub.department_id
                WHERE s.z IS NOT NULL";
        $params = [];
        
        if ($facultyId) {
            $sql .= " AND d.faculty_id = ?";
            $params[] = $facultyId;
        }
        
        if ($academicYear) {
            $sql .= " AND cr.academic_year = ?";
            $params[] = $academicYear;
        }
        
        if ($semester) {
            $sql .= " AND cr.semester = ?";
            $params[] = $semester;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        if ($result['total'] > 0) {
            $result['pass_rate'] = round(($result['passed'] / $result['total']) * 100, 2);
            $result['fail_rate'] = round(($result['failed'] / $result['total']) * 100, 2);
        } else {
            $result['pass_rate'] = 0;
            $result['fail_rate'] = 0;
        }
        
        return $result;
    }

    public static function getGradeDistribution($facultyId = null, $academicYear = null, $semester = null) {
        require_once __DIR__ . '/../core/Database.php';
        $db = Database::getInstance()->getConnection();

        $sql = "SELECT s.letter, COUNT(*) as count
                FROM scores s
                JOIN enrollments e ON e.id = s.enrollment_id
                JOIN class_rooms cr ON cr.id = e.class_room_id
                JOIN subjects sub ON sub.id = cr.subject_id
                JOIN departments d ON d.id = sub.department_id
                WHERE s.letter IS NOT NULL";
        
        $params = [];
        
        if ($facultyId) {
            $sql .= " AND d.faculty_id = ?";
            $params[] = $facultyId;
        }
        
        if ($academicYear) {
            $sql .= " AND cr.academic_year = ?";
            $params[] = $academicYear;
        }
        
        if ($semester) {
            $sql .= " AND cr.semester = ?";
            $params[] = $semester;
        }
        
        $sql .= " GROUP BY s.letter ORDER BY s.letter";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function getWarningStudents($facultyId = null, $minGPA = 2.0) {
        require_once __DIR__ . '/../core/Database.php';
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT u.id, u.full_name, u.student_code, u.faculty_id
                FROM users u
                WHERE u.role = 'student' AND u.is_active = 1";
        $params = [];
        
        if ($facultyId) {
            $sql .= " AND u.faculty_id = ?";
            $params[] = $facultyId;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $students = $stmt->fetchAll();
        
        $warnings = [];
        $gpaService = new GPAService();
        
        foreach ($students as $student) {
            $gpaData = $gpaService->calculateStudentGPA($student['id']);
            if ($gpaData['gpa'] !== null && $gpaData['gpa'] < $minGPA) {
                $warnings[] = array_merge($student, $gpaData);
            }
        }
        
        return $warnings;
    }
}


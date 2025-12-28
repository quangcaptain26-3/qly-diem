<?php
/**
 * Scholarship Service
 */

require_once __DIR__ . '/GPAService.php';
require_once __DIR__ . '/../models/ScholarshipRule.php';
require_once __DIR__ . '/../core/Database.php';

class ScholarshipService {
    public static function checkEligibility($studentId, $facultyId = null) {
        $gpaService = new GPAService();
        $gpaData = $gpaService->calculateStudentGPA($studentId);
        
        if ($gpaData['gpa'] === null) {
            return null;
        }
        
        $scholarshipModel = new ScholarshipRule();
        $rules = $scholarshipModel->findByFaculty($facultyId);
        
        foreach ($rules as $rule) {
            if ($gpaData['gpa'] >= $rule['min_gpa'] && 
                $gpaData['total_credits'] >= ($rule['min_credits'] ?? 0)) {
                return [
                    'type' => $rule['scholarship_type'],
                    'gpa' => $gpaData['gpa'],
                    'credits' => $gpaData['total_credits'],
                ];
            }
        }
        
        return null;
    }

    public static function getEligibleStudents($facultyId = null) {
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
        
        $eligible = [];
        foreach ($students as $student) {
            $scholarship = self::checkEligibility($student['id'], $student['faculty_id']);
            if ($scholarship) {
                $eligible[] = array_merge($student, $scholarship);
            }
        }
        
        return $eligible;
    }
}

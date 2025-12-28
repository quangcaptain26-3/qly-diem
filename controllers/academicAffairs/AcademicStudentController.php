<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Faculty.php';
require_once __DIR__ . '/../../services/GPAService.php';

class AcademicStudentController extends Controller {
    public function index() {
        RoleMiddleware::handle(['academic_affairs']);
        // Only get current user's faculty
        $facultyId = Auth::facultyId();
        
        $facultyModel = new Faculty();
        $faculty = $facultyModel->find($facultyId);
        
        $this->view('academicAffairs/student_list', ['faculty' => $faculty]);
    }

    public function detail() {
        RoleMiddleware::handle(['academic_affairs']);
        $studentId = $_GET['id'] ?? null;
        
        if (!$studentId) {
            $this->redirect('/academic/students');
        }

        $userModel = new User();
        // Check if student belongs to the same faculty or inferred faculty
        $facultyId = Auth::facultyId();
        
        // Slightly modified query to ensure faculty restriction
        $sql = "SELECT u.*, COALESCE(f.name, f_inferred.name) as faculty_name, COALESCE(u.faculty_id, f_inferred.id) as real_faculty_id
                FROM users u 
                LEFT JOIN faculties f ON f.id = u.faculty_id
                LEFT JOIN (
                    SELECT e.student_id, fac.id, fac.name
                    FROM enrollments e
                    JOIN class_rooms cr ON cr.id = e.class_room_id
                    JOIN subjects s ON s.id = cr.subject_id
                    JOIN departments d ON d.id = s.department_id
                    JOIN faculties fac ON fac.id = d.faculty_id
                    GROUP BY e.student_id
                ) f_inferred ON f_inferred.student_id = u.id
                WHERE u.id = ? AND u.role = 'student'
                HAVING real_faculty_id = ?";
                
        $stmt = $userModel->query($sql, [$studentId, $facultyId]);
        $student = $stmt->fetch();
        
        if (!$student) {
            // Student not found or not in this faculty
            // Flash message here would be nice
            $this->redirect('/academic/students');
        }

        // Get Stats
        require_once __DIR__ . '/../../services/GPAService.php';
        $gpaData = GPAService::calculateStudentGPA($studentId);
        $perfLevel = $this->getPerformanceLevel($gpaData['gpa']);

        // Get Scores
        require_once __DIR__ . '/../../models/Score.php';
        $scoreModel = new Score();
        $scores = $scoreModel->findByStudent($studentId);

        $this->view('academicAffairs/student_detail', [
            'student' => $student,
            'gpaData' => $gpaData,
            'perfLevel' => $perfLevel,
            'scores' => $scores
        ]);
    }

    public function apiList() {
        RoleMiddleware::handle(['academic_affairs']);
        
        $facultyId = Auth::facultyId();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_GET;
        $search = $input['search'] ?? '';
        
        $userModel = new User();
        
        // Query scoped to current faculty
        $sql = "SELECT u.id, u.student_code, u.full_name, u.email, 
                COALESCE(f.name, f_inferred.name) as faculty_name,
                COALESCE(u.faculty_id, f_inferred.id) as real_faculty_id
                FROM users u 
                LEFT JOIN faculties f ON f.id = u.faculty_id
                LEFT JOIN (
                    SELECT e.student_id, fac.id, fac.name
                    FROM enrollments e
                    JOIN class_rooms cr ON cr.id = e.class_room_id
                    JOIN subjects s ON s.id = cr.subject_id
                    JOIN departments d ON d.id = s.department_id
                    JOIN faculties fac ON fac.id = d.faculty_id
                    GROUP BY e.student_id
                ) f_inferred ON f_inferred.student_id = u.id
                WHERE u.role = 'student' AND u.is_active = 1
                HAVING real_faculty_id = ?";
        
        $params = [$facultyId];
        
        // Add search condition to HAVING clause or rewrite WHERE
        // Since we used HAVING for faculty check, we can filter result set or move logic to WHERE.
        // Moving logic to WHERE is cleaner for search.
        
        // Optimized query:
         $sql = "SELECT u.id, u.student_code, u.full_name, u.email, 
                COALESCE(f.name, f_inferred.name) as faculty_name
                FROM users u 
                LEFT JOIN faculties f ON f.id = u.faculty_id
                LEFT JOIN (
                    SELECT e.student_id, fac.id, fac.name
                    FROM enrollments e
                    JOIN class_rooms cr ON cr.id = e.class_room_id
                    JOIN subjects s ON s.id = cr.subject_id
                    JOIN departments d ON d.id = s.department_id
                    JOIN faculties fac ON fac.id = d.faculty_id
                    GROUP BY e.student_id
                ) f_inferred ON f_inferred.student_id = u.id
                WHERE u.role = 'student' AND u.is_active = 1
                AND COALESCE(u.faculty_id, f_inferred.id) = ?";
        
        $params = [$facultyId];

        if ($search) {
            $sql .= " AND (u.full_name LIKE ? OR u.student_code LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $stmt = $userModel->query($sql, $params);
        $students = $stmt->fetchAll();
        
        $results = [];
        foreach ($students as $student) {
            $gpaData = GPAService::calculateStudentGPA($student['id']);
            $results[] = array_merge($student, [
                'gpa' => $gpaData['gpa']
            ]);
        }
        
        $this->json(['success' => true, 'data' => $results]);
    }
    
    private function getPerformanceLevel($gpa) {
        if ($gpa === null) return ['key' => 'none', 'label' => 'Chưa có điểm', 'color' => 'secondary'];
        if ($gpa >= 3.6) return ['key' => 'excellent', 'label' => 'Xuất sắc', 'color' => 'success'];
        if ($gpa >= 3.2) return ['key' => 'good', 'label' => 'Giỏi', 'color' => 'primary'];
        if ($gpa >= 2.5) return ['key' => 'average', 'label' => 'Khá', 'color' => 'info'];
        if ($gpa >= 2.0) return ['key' => 'weak', 'label' => 'Trung bình', 'color' => 'warning'];
        return ['key' => 'poor', 'label' => 'Kém', 'color' => 'danger'];
    }
}

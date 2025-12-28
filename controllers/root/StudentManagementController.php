<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Faculty.php';
require_once __DIR__ . '/../../models/Score.php';
require_once __DIR__ . '/../../services/GPAService.php';

class StudentManagementController extends Controller {
    public function index() {
        RoleMiddleware::handle(['root']);
        $facultyModel = new Faculty();
        $faculties = $facultyModel->findAll();
        $this->view('root/student_management', ['faculties' => $faculties]);
    }

    public function detail() {
        RoleMiddleware::handle(['root']);
        $studentId = $_GET['id'] ?? null;
        
        if (!$studentId) {
            $this->redirect('/root/students');
        }

        // Get Student Info
        $userModel = new User();
        $sql = "SELECT u.*, COALESCE(f.name, f_inferred.name) as faculty_name 
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
                WHERE u.id = ? AND u.role = 'student'";
        $stmt = $userModel->query($sql, [$studentId]);
        $student = $stmt->fetch();
        
        if (!$student) {
            // Student not found
            $this->redirect('/root/students');
        }

        // Get Stats
        $gpaData = GPAService::calculateStudentGPA($studentId);
        $perfLevel = $this->getPerformanceLevel($gpaData['gpa']);

        // Get Scores
        $scoreModel = new Score();
        $scores = $scoreModel->findByStudent($studentId);

        $this->view('root/student_detail', [
            'student' => $student,
            'gpaData' => $gpaData,
            'perfLevel' => $perfLevel,
            'scores' => $scores
        ]);
    }

    public function apiList() {
        RoleMiddleware::handle(['root']);
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_GET;
        
        $search = $input['search'] ?? '';
        $facultyId = $input['faculty_id'] ?? '';
        $performance = $input['performance'] ?? ''; // excellent, good, average, weak, poor
        $minGpa = $input['min_gpa'] ?? '';
        $maxGpa = $input['max_gpa'] ?? '';
        
        $userModel = new User();
        // Custom query to find students, inferring faculty from enrollments if missing
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
                WHERE u.role = 'student' AND u.is_active = 1";
        
        $params = [];
        
        if ($search) {
            $sql .= " AND (u.full_name LIKE ? OR u.student_code LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($facultyId) {
            $sql .= " AND COALESCE(u.faculty_id, f_inferred.id) = ?";
            $params[] = $facultyId;
        }
        
        $stmt = $userModel->query($sql, $params);
        $students = $stmt->fetchAll();
        
        $results = [];
        foreach ($students as $student) {
            // Calculate CPA (Cumulative GPA)
            $gpaData = GPAService::calculateStudentGPA($student['id']);
            $gpa = $gpaData['gpa'];
            
            // Filter by GPA Range
            if ($minGpa !== '' && ($gpa === null || $gpa < $minGpa)) continue;
            if ($maxGpa !== '' && ($gpa === null || $gpa > $maxGpa)) continue;
            
            // Filter by Performance
            $perfLevel = $this->getPerformanceLevel($gpa);
            if ($performance && $performance !== $perfLevel['key']) continue;
            
            $results[] = array_merge($student, [
                'gpa' => $gpa,
                'performance' => $perfLevel['label'],
                'performance_color' => $perfLevel['color']
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

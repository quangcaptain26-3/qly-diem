<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Faculty.php';
require_once __DIR__ . '/../../models/Department.php';

class LecturerManagementController extends Controller {
    public function index() {
        RoleMiddleware::handle(['root']);
        $facultyModel = new Faculty();
        $faculties = $facultyModel->findAll();
        $this->view('root/lecturer_management', ['faculties' => $faculties]);
    }

    public function apiList() {
        RoleMiddleware::handle(['root']);
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_GET;
        $search = $input['search'] ?? '';
        $facultyId = $input['faculty_id'] ?? '';

        $userModel = new User();
        
        // Complex query to get lecturer stats
        $sql = "SELECT u.id, u.username, u.full_name, u.email, 
                       f.name as faculty_name, d.name as department_name,
                       COUNT(DISTINCT cr.id) as total_classes,
                       COUNT(DISTINCT e.student_id) as total_students,
                       AVG(s.z) as avg_grade_given
                FROM users u
                LEFT JOIN departments d ON d.id = u.department_id
                LEFT JOIN faculties f ON f.id = d.faculty_id
                LEFT JOIN class_rooms cr ON cr.lecturer_id = u.id
                LEFT JOIN enrollments e ON e.class_room_id = cr.id
                LEFT JOIN scores s ON s.enrollment_id = e.id
                WHERE u.role = 'lecturer' AND u.is_active = 1";
        
        $params = [];
        
        if ($search) {
            $sql .= " AND (u.full_name LIKE ? OR u.username LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($facultyId) {
            $sql .= " AND d.faculty_id = ?";
            $params[] = $facultyId;
        }
        
        $sql .= " GROUP BY u.id ORDER BY u.full_name ASC";
        
        $stmt = $userModel->query($sql, $params);
        $lecturers = $stmt->fetchAll();
        
        // Add "Style" logic
        foreach ($lecturers as &$l) {
            $l['avg_grade_given'] = $l['avg_grade_given'] ? round($l['avg_grade_given'], 2) : null;
            $l['grading_style'] = $this->determineGradingStyle($l['avg_grade_given']);
        }

        $this->json(['success' => true, 'data' => $lecturers]);
    }

    private function determineGradingStyle($avgGrade) {
        if ($avgGrade === null) return ['label' => 'Chưa có dữ liệu', 'color' => 'secondary'];
        if ($avgGrade >= 8.0) return ['label' => 'Phóng khoáng', 'color' => 'success'];
        if ($avgGrade >= 6.5) return ['label' => 'Cân bằng', 'color' => 'primary'];
        if ($avgGrade >= 5.0) return ['label' => 'Khắt khe', 'color' => 'warning'];
        return ['label' => 'Rất nghiêm khắc', 'color' => 'danger'];
    }
}

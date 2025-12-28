<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/ClassRoom.php';
require_once __DIR__ . '/../../models/Enrollment.php';
require_once __DIR__ . '/../../models/Score.php';
require_once __DIR__ . '/../../models/Attendance.php';

class ClassController extends Controller {
    public function index() {
        RoleMiddleware::handle(['lecturer']);
        
        $lecturerId = Auth::id();
        $classRoomModel = new ClassRoom();
        $classes = $classRoomModel->findByLecturer($lecturerId);
        
        $this->view('lecturer/classes', ['classes' => $classes]);
    }
    
    public function dashboard() {
        RoleMiddleware::handle(['lecturer']);
        
        $lecturerId = Auth::id();
        $classRoomModel = new ClassRoom();
        $allClasses = $classRoomModel->findByLecturer($lecturerId);
        
        // Find latest academic year and semester
        $currentAcademicYear = '';
        $currentSemester = '';
        
        if (!empty($allClasses)) {
            // Sort mainly by academic year desc, then semester desc (assuming HK2 > HK1 alphabetically works or specific logic needed)
            // Since data is already sorted from Model, we can take the first one
            $currentAcademicYear = $allClasses[0]['academic_year'];
            $currentSemester = $allClasses[0]['semester'];
        }
        
        // Filter classes for current semester only
        $currentClasses = array_filter($allClasses, function($c) use ($currentAcademicYear, $currentSemester) {
            return $c['academic_year'] === $currentAcademicYear && $c['semester'] === $currentSemester;
        });
        
        $this->view('lecturer/dashboard', [
            'classes' => $currentClasses,
            'academicYear' => $currentAcademicYear,
            'semester' => $currentSemester
        ]);
    }

    public function show() {
        RoleMiddleware::handle(['lecturer']);
        
        $classRoomId = $_GET['id'] ?? null;
        
        if (!$classRoomId) {
            $this->redirect('/lecturer/classes');
        }
        
        $classRoomModel = new ClassRoom();
        $class = $classRoomModel->find($classRoomId);
        
        // Verify lecturer owns this class
        if ($class['lecturer_id'] != Auth::id()) {
            http_response_code(403);
            die("403 - Access Denied");
        }
        
        $enrollmentModel = new Enrollment();
        $enrollments = $enrollmentModel->findByClassRoom($classRoomId);
        
        // Get full user info for enrollments
        $db = Database::getInstance()->getConnection();
        foreach ($enrollments as &$enrollment) {
            $stmt = $db->prepare("SELECT email FROM users WHERE id = ?");
            $stmt->execute([$enrollment['student_id']]);
            $user = $stmt->fetch();
            $enrollment['email'] = $user['email'] ?? null;
        }
        
        $this->view('lecturer/class-detail', [
            'class' => $class,
            'enrollments' => $enrollments,
        ]);
    }
}


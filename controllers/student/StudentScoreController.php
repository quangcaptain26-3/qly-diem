<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/Score.php';
require_once __DIR__ . '/../../models/ClassRoom.php';
require_once __DIR__ . '/../../services/GPAService.php';

class StudentScoreController extends Controller {
    public function index() {
        RoleMiddleware::handle(['student']);
        
        $studentId = Auth::id();
        $academicYear = $_GET['academic_year'] ?? null;
        $semester = $_GET['semester'] ?? null;
        
        $scoreModel = new Score();
        $scores = $scoreModel->findByStudent($studentId, $academicYear, $semester);

        $classRoomModel = new ClassRoom();
        $academicYears = $classRoomModel->getAcademicYears();
        
        $gpaService = new GPAService();
        $gpaData = $gpaService->calculateStudentGPA($studentId, $academicYear, $semester);
        
        $this->view('student/scores', [
            'scores' => $scores,
            'gpa' => $gpaData,
            'academicYear' => $academicYear,
            'semester' => $semester,
            'academicYears' => $academicYears,
        ]);
    }
}


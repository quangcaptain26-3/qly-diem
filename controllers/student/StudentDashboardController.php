<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/Enrollment.php';
require_once __DIR__ . '/../../models/Score.php';
require_once __DIR__ . '/../../services/GPAService.php';

class StudentDashboardController extends Controller {
    public function dashboard() {
        RoleMiddleware::handle(['student']);
        
        $studentId = Auth::id();
        $enrollmentModel = new Enrollment();
        $enrollments = $enrollmentModel->findByStudent($studentId);
        
        // Get scores for enrollments
        $scoreModel = new Score();
        foreach ($enrollments as &$enrollment) {
            $score = $scoreModel->findByEnrollment($enrollment['id']);
            $enrollment['score_id'] = $score['id'] ?? null;
        }
        
        $gpaService = new GPAService();
        $gpaData = $gpaService->calculateStudentGPA($studentId);
        
        $this->view('student/dashboard', [
            'enrollments' => $enrollments,
            'gpa' => $gpaData,
        ]);
    }
}

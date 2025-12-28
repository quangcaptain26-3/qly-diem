<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/ClassRoom.php';
require_once __DIR__ . '/../../services/StatisticService.php';
require_once __DIR__ . '/../../services/ScholarshipService.php';

class DeanDashboardController extends Controller {
    public function dashboard() {
        RoleMiddleware::handle(['dean']);
        
        $facultyId = Auth::facultyId();
        $classRoomModel = new ClassRoom();
        $classes = $classRoomModel->findByFaculty($facultyId);
        
        $statService = new StatisticService();
        $avgScores = $statService->getAverageScoreBySemester($facultyId);
        $passFail = $statService->getPassFailRate($facultyId);
        $scholarships = ScholarshipService::getEligibleStudents($facultyId);
        $warnings = $statService->getWarningStudents($facultyId);
        
        $this->view('dean/dashboard', [
            'classes' => $classes,
            'avgScores' => $avgScores,
            'passFail' => $passFail,
            'scholarships' => $scholarships,
            'warnings' => $warnings,
        ]);
    }
}


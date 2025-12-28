<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/ClassRoom.php';
require_once __DIR__ . '/../../services/StatisticService.php';

class AcademicDashboardController extends Controller {
    public function dashboard() {
        RoleMiddleware::handle(['academic_affairs']);
        $this->view('academicAffairs/dashboard');
    }

    public function apiStats() {
        RoleMiddleware::handle(['academic_affairs']);
        
        $facultyId = Auth::facultyId();
        
        // 1. Class Stats
        $classRoomModel = new ClassRoom();
        $classes = $classRoomModel->findByFaculty($facultyId);
        $totalClasses = count($classes);
        $lockedClasses = count(array_filter($classes, fn($c) => $c['is_locked']));
        
        // 2. Score Stats
        $statService = new StatisticService();
        $passFail = $statService->getPassFailRate($facultyId);
        $warnings = $statService->getWarningStudents($facultyId);
        $gradeDist = $statService->getGradeDistribution($facultyId);
        
        // 3. Return JSON
        $this->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_classes' => $totalClasses,
                    'locked_classes' => $lockedClasses,
                    'unlocked_classes' => $totalClasses - $lockedClasses,
                    'total_scores' => $passFail['total'] ?? 0,
                    'warnings' => count($warnings)
                ],
                'passFail' => $passFail,
                'gradeDist' => $gradeDist,
                'recentClasses' => array_slice($classes, 0, 5) // Last 5 classes
            ]
        ]);
    }
}
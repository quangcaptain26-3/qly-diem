<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/Faculty.php';
require_once __DIR__ . '/../../services/StatisticService.php';
require_once __DIR__ . '/../../models/ClassRoom.php';
require_once __DIR__ . '/../../models/ScoreLock.php';

class RootDashboardController extends Controller {
    public function dashboard() {
        RoleMiddleware::handle(['root']);
        $this->view('root/dashboard');
    }

    public function apiStats() {
        RoleMiddleware::handle(['root']);
        
        $facultyModel = new Faculty();
        $faculties = $facultyModel->getWithStats();
        
        $statService = new StatisticService();
        $gradeDist = $statService->getGradeDistribution();
        $passFail = $statService->getPassFailRate();

        // Count totals for summary cards
        $totalStudents = array_sum(array_column($faculties, 'total_students'));
        $totalDepartments = array_sum(array_column($faculties, 'total_departments'));
        
        $this->json([
            'success' => true,
            'data' => [
                'faculties' => $faculties,
                'gradeDist' => $gradeDist,
                'passFail' => $passFail,
                'summary' => [
                    'total_faculties' => count($faculties),
                    'total_students' => $totalStudents,
                    'total_departments' => $totalDepartments,
                    'total_scores' => $passFail['total'] ?? 0
                ]
            ]
        ]);
    }

    public function apiLocks() {
        RoleMiddleware::handle(['root']);
        $scoreLockModel = new ScoreLock();
        $locks = $scoreLockModel->getLockedClasses();
        $this->json(['success' => true, 'data' => $locks]);
    }

    public function apiUnlock() {
        RoleMiddleware::handle(['root']);
        
        $input = json_decode(file_get_contents('php://input'), true);
        $classId = $input['class_id'] ?? null;
        
        if (!$classId) {
            $this->json(['success' => false, 'message' => 'Missing class ID'], 400);
            return;
        }

        $classRoomModel = new ClassRoom();
        if ($classRoomModel->unlock($classId, Auth::id())) {
             $this->json(['success' => true, 'message' => 'Mở khóa thành công']);
        } else {
             $this->json(['success' => false, 'message' => 'Mở khóa thất bại'], 500);
        }
    }
}
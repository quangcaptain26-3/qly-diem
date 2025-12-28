<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/Faculty.php';
require_once __DIR__ . '/../../services/StatisticService.php';
require_once __DIR__ . '/../../models/ClassRoom.php';
require_once __DIR__ . '/../../models/ScoreLock.php';

class RootDashboardController extends Controller
{
    public function dashboard()
    {
        RoleMiddleware::handle(['root']);
        $this->view('root/dashboard');
    }

    public function apiStats()
    {
        RoleMiddleware::handle(['root']);

        try {
            $facultyModel = new Faculty();
            $faculties = $facultyModel->getWithStats();

            $gradeDist = StatisticService::getGradeDistribution();
            $passFail = StatisticService::getPassFailRate();

            // Ensure faculties is an array
            if (!is_array($faculties)) {
                $faculties = [];
            }

            // Count totals for summary cards - ensure numeric values
            $totalStudents = 0;
            $totalDepartments = 0;

            foreach ($faculties as $faculty) {
                $totalStudents += (int) ($faculty['total_students'] ?? 0);
                $totalDepartments += (int) ($faculty['total_departments'] ?? 0);
            }

            // Ensure gradeDist is an array
            if (!is_array($gradeDist)) {
                $gradeDist = [];
            }

            // Ensure passFail has all required fields
            if (!is_array($passFail)) {
                $passFail = [
                    'total' => 0,
                    'passed' => 0,
                    'failed' => 0,
                    'pass_rate' => 0,
                    'fail_rate' => 0
                ];
            } else {
                $passFail['total'] = (int) ($passFail['total'] ?? 0);
                $passFail['passed'] = (int) ($passFail['passed'] ?? 0);
                $passFail['failed'] = (int) ($passFail['failed'] ?? 0);
                $passFail['pass_rate'] = (float) ($passFail['pass_rate'] ?? 0);
                $passFail['fail_rate'] = (float) ($passFail['fail_rate'] ?? 0);
            }

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
                        'total_scores' => $passFail['total']
                    ]
                ]
            ]);
        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'message' => 'Lỗi khi tải dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiLocks()
    {
        RoleMiddleware::handle(['root']);
        $scoreLockModel = new ScoreLock();
        $locks = $scoreLockModel->getLockedClasses();
        $this->json(['success' => true, 'data' => $locks]);
    }

    public function apiUnlock()
    {
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
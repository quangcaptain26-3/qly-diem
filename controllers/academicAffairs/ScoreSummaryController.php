<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/ClassRoom.php';
require_once __DIR__ . '/../../models/Score.php';
require_once __DIR__ . '/../../export/excel/ScoreExportXlsx.php';

class ScoreSummaryController extends Controller {
    public function index() {
        RoleMiddleware::handle(['academic_affairs']);
        $this->view('academicAffairs/score-summary');
    }

    public function apiListClasses() {
        RoleMiddleware::handle(['academic_affairs']);
        $facultyId = Auth::facultyId();
        
        $input = json_decode(file_get_contents('php://input'), true) ?? $_GET;
        $year = $input['academic_year'] ?? '';
        $semester = $input['semester'] ?? '';
        $status = $input['status'] ?? ''; // locked, unlocked
        
        $classRoomModel = new ClassRoom();
        $classes = $classRoomModel->findByFaculty($facultyId, $year, $semester);

        // Filter by status if provided
        if ($status !== '') {
            $classes = array_filter($classes, function($c) use ($status) {
                return $status === 'locked' ? $c['is_locked'] == 1 : $c['is_locked'] == 0;
            });
            $classes = array_values($classes); // Re-index array
        }
        
        $this->json(['success' => true, 'data' => $classes]);
    }

    public function apiClassScores() {
        RoleMiddleware::handle(['academic_affairs']);
        $classId = $_GET['class_id'] ?? null;
        
        if (!$classId) {
            $this->json(['success' => false, 'message' => 'Missing Class ID']);
            return;
        }

        $scores = (new Score())->findByClassRoom($classId);
        $class = (new ClassRoom())->find($classId);
        
        if (!$class) {
            $this->json(['success' => false, 'message' => 'Class not found']);
            return;
        }

        $this->json([
            'success' => true, 
            'data' => [
                'class' => $class,
                'scores' => $scores
            ]
        ]);
    }

    public function export() {
        RoleMiddleware::handle(['academic_affairs']);
        
        $classRoomId = $_GET['class_id'] ?? null;
        
        if (!$classRoomId) {
            $_SESSION['error'] = 'Lớp học không hợp lệ';
            $this->redirect('/academic/scores');
        }
        
        $exporter = new ScoreExportXlsx();
        $exporter->export($classRoomId);
    }

    public function lock() {
        RoleMiddleware::handle(['academic_affairs']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/academic/scores');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $classRoomId = $input['class_room_id'] ?? null;
        
        if (!$classRoomId) {
            $this->json(['success' => false, 'message' => 'Lớp học không hợp lệ']);
            return;
        }
        
        $classRoomModel = new ClassRoom();
        if ($classRoomModel->lock($classRoomId, Auth::id())) {
             $this->json(['success' => true, 'message' => 'Đã chốt điểm thành công']);
        } else {
             $this->json(['success' => false, 'message' => 'Chốt điểm thất bại']);
        }
    }
}
<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../middleware/ScoreLockMiddleware.php';
require_once __DIR__ . '/../../models/ClassRoom.php';
require_once __DIR__ . '/../../models/Enrollment.php';
require_once __DIR__ . '/../../models/Score.php';

class ScoreInputController extends Controller {
    public function show() {
        RoleMiddleware::handle(['lecturer']);
        
        $classRoomId = $_GET['class_id'] ?? null;
        
        if (!$classRoomId) {
            $this->redirect('/lecturer/classes');
        }
        
        // Removed ScoreLockMiddleware check here to allow read-only access
        
        $classRoomModel = new ClassRoom();
        $class = $classRoomModel->find($classRoomId);
        
        // Verify lecturer owns this class
        if ($class['lecturer_id'] != Auth::id()) {
            http_response_code(403);
            die("403 - Access Denied");
        }
        
        $enrollmentModel = new Enrollment();
        $enrollments = $enrollmentModel->findByClassRoom($classRoomId);
        
        $scoreModel = new Score();
        $scores = [];
        foreach ($enrollments as $enrollment) {
            $score = $scoreModel->findByEnrollment($enrollment['id']);
            if ($score) {
                $scores[$enrollment['id']] = $score;
            }
        }
        
        $this->view('lecturer/score-input', [
            'class' => $class,
            'enrollments' => $enrollments,
            'scores' => $scores,
        ]);
    }

    public function save() {
        RoleMiddleware::handle(['lecturer']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/lecturer/classes');
        }
        
        $classRoomId = $_POST['class_room_id'] ?? null;
        $scores = $_POST['scores'] ?? [];
        
        if (!$classRoomId) {
            $_SESSION['error'] = 'Lớp học không hợp lệ';
            $this->redirect('/lecturer/classes');
        }
        
        ScoreLockMiddleware::handle($classRoomId);
        
        $classRoomModel = new ClassRoom();
        $class = $classRoomModel->find($classRoomId);
        
        // Verify lecturer owns this class
        if ($class['lecturer_id'] != Auth::id()) {
            http_response_code(403);
            die("403 - Access Denied");
        }
        
        $scoreModel = new Score();
        $success = 0;
        $failed = 0;
        
        foreach ($scores as $enrollmentId => $scoreData) {
            if ($scoreModel->createOrUpdate($enrollmentId, $scoreData, Auth::id())) {
                $success++;
            } else {
                $failed++;
            }
        }
        
        $_SESSION['success'] = "Đã lưu điểm cho {$success} sinh viên. Thất bại: {$failed}";
        $this->redirect('/lecturer/scores?class_id=' . $classRoomId);
    }
}


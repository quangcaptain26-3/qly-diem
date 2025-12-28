<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../import/excel/ScoreImportXlsx.php';
require_once __DIR__ . '/../../export/excel/ScoreExportXlsx.php';

class ImportScoreController extends Controller {
    public function show() {
        RoleMiddleware::handle(['lecturer']);
        
        $classRoomId = $_GET['class_id'] ?? null;
        $this->view('lecturer/import-score', ['class_room_id' => $classRoomId]);
    }

    public function downloadTemplate() {
        RoleMiddleware::handle(['lecturer']);
        
        $classRoomId = $_GET['class_id'] ?? null;
        
        if (!$classRoomId) {
            $_SESSION['error'] = 'Lớp học không hợp lệ';
            $this->redirect('/lecturer/classes');
        }
        
        $exporter = new ScoreExportXlsx();
        $exporter->exportTemplate($classRoomId);
    }

    public function import() {
        RoleMiddleware::handle(['lecturer']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
            $_SESSION['error'] = 'Vui lòng chọn file';
            $this->redirect('/lecturer/import-score');
        }
        
        $classRoomId = $_POST['class_room_id'] ?? null;
        
        if (!$classRoomId) {
            $_SESSION['error'] = 'Lớp học không hợp lệ';
            $this->redirect('/lecturer/import-score');
        }
        
        $importer = new ScoreImportXlsx();
        $result = $importer->import($_FILES['file']['tmp_name'], $classRoomId, Auth::id());
        
        if ($result['success']) {
            $_SESSION['success'] = "Đã import điểm cho {$result['count']} sinh viên";
        } else {
            $_SESSION['error'] = $result['message'];
        }
        
        $this->redirect('/lecturer/scores?class_id=' . $classRoomId);
    }
}


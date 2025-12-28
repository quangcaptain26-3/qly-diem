<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/ClassRoom.php';
require_once __DIR__ . '/../../models/Attendance.php';

class AttendanceController extends Controller {
    public function show() {
        RoleMiddleware::handle(['lecturer']);
        
        $classRoomId = $_GET['class_id'] ?? null;
        $date = $_GET['date'] ?? date('Y-m-d'); // Mặc định hôm nay
        
        if (!$classRoomId) {
            $this->redirect('/lecturer/classes');
        }
        
        $classRoomModel = new ClassRoom();
        $class = $classRoomModel->find($classRoomId);
        
        // Verify lecturer owns this class
        if (!$class || $class['lecturer_id'] != Auth::id()) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập lớp học này';
            $this->redirect('/lecturer/classes');
        }
        
        $attendanceModel = new Attendance();
        // Lấy danh sách sinh viên kèm trạng thái điểm danh của ngày đó
        $students = $attendanceModel->getByClassAndDate($classRoomId, $date);
        
        $this->view('lecturer/attendance', [
            'class' => $class,
            'students' => $students,
            'currentDate' => $date
        ]);
    }

    public function save() {
        RoleMiddleware::handle(['lecturer']);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/lecturer/classes');
        }
        
        $classRoomId = $_POST['class_room_id'] ?? null;
        $date = $_POST['attendance_date'] ?? date('Y-m-d');
        $attendances = $_POST['attendance'] ?? []; // enrollment_id => status
        $notes = $_POST['note'] ?? []; // enrollment_id => note
        
        if (!$classRoomId) {
            $_SESSION['error'] = 'Dữ liệu không hợp lệ';
            $this->redirect('/lecturer/classes');
        }
        
        $attendanceModel = new Attendance();
        $count = 0;
        
        foreach ($attendances as $enrollmentId => $status) {
            $note = $notes[$enrollmentId] ?? '';
            
            // 1. Lưu điểm danh
            if ($attendanceModel->saveAttendance($enrollmentId, $date, $status, $note)) {
                
                // 2. Tự động tính lại điểm CC
                $newCCScore = $attendanceModel->calculateAttendanceScore($enrollmentId);
                
                // 3. Đồng bộ sang bảng điểm
                $attendanceModel->syncScoreToMainTable($enrollmentId, $newCCScore);
                
                $count++;
            }
        }
        
        $_SESSION['success'] = "Đã lưu điểm danh cho {$count} sinh viên. Điểm chuyên cần đã được cập nhật.";
        $this->redirect('/lecturer/attendance?class_id=' . $classRoomId . '&date=' . $date);
    }
}


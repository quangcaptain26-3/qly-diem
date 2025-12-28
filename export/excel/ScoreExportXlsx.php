<?php
/**
 * Score Export to Excel
 */

use Shuchkin\SimpleXLSXGen;

require_once __DIR__ . '/../../libs/simplexlsxgen/src/SimpleXLSXGen.php';
require_once __DIR__ . '/../../models/ClassRoom.php';
require_once __DIR__ . '/../../models/Score.php';
require_once __DIR__ . '/../../services/StatisticService.php';

class ScoreExportXlsx {
    
    // Xuất bảng điểm chi tiết của lớp (Cho Giáo vụ, Giảng viên lưu trữ)
    public function export($classRoomId) {
        $classRoomModel = new ClassRoom();
        $class = $classRoomModel->find($classRoomId);
        
        if (!$class) {
            return false;
        }
        
        $scoreModel = new Score();
        $scores = $scoreModel->findByClassRoom($classRoomId);
        
        $data = [
            ['Mã SV', 'Họ tên', 'X1', 'X2', 'X3', 'CC', 'Y', 'Z', 'GPA', 'Letter']
        ];
        
        foreach ($scores as $score) {
            $data[] = [
                $score['student_code'],
                $score['full_name'],
                $score['x1'] ?? '',
                $score['x2'] ?? '',
                $score['x3'] ?? '',
                $score['cc'] ?? '',
                $score['y'] ?? '',
                $score['z'] ?? '',
                $score['gpa'] ?? '',
                $score['letter'] ?? '',
            ];
        }
        
        $xlsx = SimpleXLSXGen::fromArray($data);
        $filename = $class['class_code'] . '_Scores_' . date('Y-m-d') . '.xlsx';
        
        $this->sendDownloadHeaders($filename, $xlsx);
    }

    // Xuất file mẫu để nhập điểm (Cho Giảng viên)
    public function exportTemplate($classRoomId) {
        $classRoomModel = new ClassRoom();
        $class = $classRoomModel->find($classRoomId);
        $enrollments = $classRoomModel->getStudents($classRoomId);

        if (!$class) return false;

        $data = [
            ['Mã SV', 'Họ tên', 'X1', 'X2', 'X3', 'CC', 'Y'] // Không có Z, GPA vì hệ thống tự tính
        ];

        foreach ($enrollments as $student) {
            $data[] = [
                $student['student_code'],
                $student['full_name'],
                '', '', '', '', '' // Để trống cho GV nhập
            ];
        }

        $xlsx = SimpleXLSXGen::fromArray($data);
        $filename = $class['class_code'] . '_Template_' . date('Y-m-d') . '.xlsx';
        
        $this->sendDownloadHeaders($filename, $xlsx);
    }

    // Xuất báo cáo tổng hợp (Cho Trưởng khoa)
    public function exportFacultyReport($facultyId, $academicYear, $semester) {
        // Sheet 1: Tổng quan
        $passFail = StatisticService::getPassFailRate($facultyId, $academicYear, $semester);
        $overviewData = [
            ['Báo cáo Tổng hợp Khoa', $academicYear, $semester],
            [],
            ['Thống kê', 'Giá trị'],
            ['Tổng số điểm', $passFail['total']],
            ['Đậu', $passFail['passed']],
            ['Rớt', $passFail['failed']],
            ['Tỷ lệ Đậu', $passFail['pass_rate'] . '%'],
            ['Tỷ lệ Rớt', $passFail['fail_rate'] . '%']
        ];

        // Sheet 2: Danh sách Lớp học
        $classRoomModel = new ClassRoom();
        $classes = $classRoomModel->findByFaculty($facultyId, $academicYear, $semester);
        $classesData = [
            ['STT', 'Mã Lớp', 'Học Phần', 'Giảng viên', 'Sĩ số', 'Trạng thái']
        ];
        foreach ($classes as $index => $c) {
            // Cần lấy tên giảng viên (query thêm hoặc lấy từ user model nếu cần, tạm thời để ID hoặc skip nếu chưa có join)
            // Model findByFaculty hiện tại join subjects, departments. 
            // Ta cần thêm join users để lấy tên GV nếu muốn đẹp. Tạm thời dùng thông tin có sẵn.
            $classesData[] = [
                $index + 1,
                $c['class_code'],
                $c['subject_name'],
                $c['lecturer_id'], // Cần cải thiện hiển thị tên GV sau
                'N/A', // Cần count enrollment
                $c['is_locked'] ? 'Đã chốt' : 'Mở'
            ];
        }

        // Sheet 3: Cảnh báo học vụ
        $warnings = StatisticService::getWarningStudents($facultyId);
        $warningData = [
            ['Mã SV', 'Họ tên', 'GPA', 'Tín chỉ tích lũy']
        ];
        foreach ($warnings as $w) {
            $warningData[] = [
                $w['student_code'],
                $w['full_name'],
                $w['gpa'],
                $w['total_credits']
            ];
        }

        $xlsx = new SimpleXLSXGen();
        $xlsx->addSheet($overviewData, 'Tong Quan');
        $xlsx->addSheet($classesData, 'Danh sach Lop');
        $xlsx->addSheet($warningData, 'Canh bao Hoc vu');

        $filename = 'FacultyReport_' . date('Y-m-d') . '.xlsx';
        $this->sendDownloadHeaders($filename, $xlsx);
    }

    private function sendDownloadHeaders($filename, $xlsx) {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $xlsx->downloadAs($filename);
        exit;
    }
}


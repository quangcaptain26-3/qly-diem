<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../services/ScholarshipService.php';

class AcademicScholarshipController extends Controller {
    public function index() {
        RoleMiddleware::handle(['academic_affairs']);
        
        // Giáo vụ chỉ xem được sinh viên thuộc khoa mình quản lý
        $facultyId = Auth::facultyId();
        
        // Lấy danh sách sinh viên đủ điều kiện học bổng
        // Lưu ý: Hàm này đang tính GPA tích lũy toàn khóa hoặc logic mặc định trong Service
        // Nếu muốn lọc theo kỳ, cần nâng cấp Service (như tôi sẽ làm ở bước sau)
        $scholarships = ScholarshipService::getEligibleStudents($facultyId);
        
        $this->view('academicAffairs/scholarship', ['scholarships' => $scholarships]);
    }
}

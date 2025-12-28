<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../services/StatisticService.php';
require_once __DIR__ . '/../../services/GPAService.php';

class WarningController extends Controller {
    public function index() {
        RoleMiddleware::handle(['academic_affairs']);
        $this->view('academicAffairs/warning');
    }

    public function apiList() {
        RoleMiddleware::handle(['academic_affairs']);
        
        $facultyId = Auth::facultyId();
        $input = json_decode(file_get_contents('php://input'), true) ?? $_GET;
        $threshold = $input['threshold'] ?? 2.0; // Default threshold < 2.0
        
        $statService = new StatisticService();
        $warnings = $statService->getWarningStudents($facultyId, $threshold);
        
        // Add "Risk Level" logic
        foreach ($warnings as &$w) {
            $w['risk_level'] = $this->determineRiskLevel($w['gpa']);
        }

        $this->json(['success' => true, 'data' => $warnings]);
    }
    
    private function determineRiskLevel($gpa) {
        if ($gpa < 1.0) return ['label' => 'Nguy cơ Buộc thôi học', 'color' => 'danger'];
        if ($gpa < 1.5) return ['label' => 'Cảnh báo Học vụ Cấp 2', 'color' => 'warning'];
        return ['label' => 'Cảnh báo Học vụ Cấp 1', 'color' => 'secondary'];
    }
}
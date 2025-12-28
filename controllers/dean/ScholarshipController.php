<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../services/ScholarshipService.php';

class ScholarshipController extends Controller {
    public function index() {
        RoleMiddleware::handle(['dean']);
        
        $facultyId = Auth::facultyId();
        $scholarships = ScholarshipService::getEligibleStudents($facultyId);
        
        $this->view('dean/scholarship', ['scholarships' => $scholarships]);
    }
}


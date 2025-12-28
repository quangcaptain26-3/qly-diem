<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/../../middleware/RoleMiddleware.php';
require_once __DIR__ . '/../../models/ClassRoom.php';
require_once __DIR__ . '/../../models/Score.php';
require_once __DIR__ . '/../../export/excel/ScoreExportXlsx.php';

class FacultyReportController extends Controller {
    public function index() {
        RoleMiddleware::handle(['dean']);
        
        $facultyId = Auth::facultyId();
        $academicYear = $_GET['academic_year'] ?? date('Y') . '-' . (date('Y') + 1);
        $semester = $_GET['semester'] ?? 'HK1';
        
        $classRoomModel = new ClassRoom();
        $classes = $classRoomModel->findByFaculty($facultyId, $academicYear, $semester);
        
        $this->view('dean/report', [
            'classes' => $classes,
            'academicYear' => $academicYear,
            'semester' => $semester,
        ]);
    }

    public function export() {
        RoleMiddleware::handle(['dean']);
        
        $facultyId = Auth::facultyId();
        $academicYear = $_GET['academic_year'] ?? date('Y') . '-' . (date('Y') + 1);
        $semester = $_GET['semester'] ?? 'HK1';
        
        $exporter = new ScoreExportXlsx();
        $exporter->exportFacultyReport($facultyId, $academicYear, $semester);
    }
}


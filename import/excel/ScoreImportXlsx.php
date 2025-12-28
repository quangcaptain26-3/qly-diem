<?php
/**
 * Score Import from Excel
 */

require_once __DIR__ . '/../../libs/simplexlsx/src/SimpleXLSX.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/../../models/Enrollment.php';
require_once __DIR__ . '/../../models/Score.php';

use Shuchkin\SimpleXLSX;

class ScoreImportXlsx {
    public function import($filePath, $classRoomId, $inputBy) {
        try {
            if (!file_exists($filePath)) {
                return ['success' => false, 'message' => 'File không tồn tại'];
            }
            
            $xlsx = SimpleXLSX::parse($filePath);
            if (!$xlsx) {
                return ['success' => false, 'message' => 'Không thể đọc file Excel'];
            }
            
            $rows = $xlsx->rows();
            if (count($rows) < 2) {
                return ['success' => false, 'message' => 'File không có dữ liệu'];
            }
            
            // Skip header row
            array_shift($rows);
            
            $enrollmentModel = new Enrollment();
            $scoreModel = new Score();
            $count = 0;
            
            foreach ($rows as $row) {
                if (count($row) < 7) continue;
                
                $studentCode = trim($row[0] ?? '');
                $x1 = !empty($row[1]) ? floatval($row[1]) : null;
                $x2 = !empty($row[2]) ? floatval($row[2]) : null;
                $x3 = !empty($row[3]) ? floatval($row[3]) : null;
                $cc = !empty($row[4]) ? floatval($row[4]) : null;
                $y = !empty($row[5]) ? floatval($row[5]) : null;
                
                if (empty($studentCode)) continue;
                
                // Find enrollment by student code
                $db = Database::getInstance()->getConnection();
                $stmt = $db->prepare("SELECT e.id FROM enrollments e 
                                     JOIN users u ON u.id = e.student_id 
                                     WHERE e.class_room_id = ? AND u.student_code = ?");
                $stmt->execute([$classRoomId, $studentCode]);
                $enrollment = $stmt->fetch();
                
                if ($enrollment) {
                    $scoreData = [
                        'x1' => $x1,
                        'x2' => $x2,
                        'x3' => $x3,
                        'cc' => $cc,
                        'y' => $y,
                    ];
                    
                    if ($scoreModel->createOrUpdate($enrollment['id'], $scoreData, $inputBy)) {
                        $count++;
                    }
                }
            }
            
            return ['success' => true, 'count' => $count];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}


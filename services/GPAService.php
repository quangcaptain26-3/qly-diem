<?php
/**
 * GPA Calculation Service
 */

require_once __DIR__ . '/../models/Score.php';

class GPAService {
    public static function calculateStudentGPA($studentId, $academicYear = null, $semester = null) {
        $scoreModel = new Score();
        $scores = $scoreModel->findByStudent($studentId, $academicYear, $semester);
        
        if (empty($scores)) {
            return ['gpa' => null, 'total_credits' => 0, 'weighted_sum' => 0];
        }
        
        $weightedSum = 0;
        $totalCredits = 0;
        
        foreach ($scores as $score) {
            if ($score['gpa'] !== null && $score['credits'] !== null) {
                $weightedSum += $score['gpa'] * $score['credits'];
                $totalCredits += $score['credits'];
            }
        }
        
        $gpa = $totalCredits > 0 ? round($weightedSum / $totalCredits, 2) : null;
        
        return [
            'gpa' => $gpa,
            'total_credits' => $totalCredits,
            'weighted_sum' => $weightedSum,
        ];
    }
}


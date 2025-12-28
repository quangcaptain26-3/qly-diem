<?php
/**
 * Score Calculation Service
 */

class ScoreCalculator {
    public static function calculate($data) {
        $config = require __DIR__ . '/../config/app.php';
        $formula = $config['score_formula'];
        $gpaThresholds = $config['gpa_thresholds'];
        $qualThreshold = $config['qualification_threshold'];
        
        $x1 = $data['x1'] ?? null;
        $x2 = $data['x2'] ?? null;
        $x3 = $data['x3'] ?? null;
        $y = $data['y'] ?? null;
        $cc = $data['cc'] ?? null;
        
        $result = [
            'z' => null,
            'gpa' => null,
            'letter' => null,
            'is_qualified' => null,
        ];
        
        // Check if all required scores are present
        if ($x1 !== null && $x2 !== null && $x3 !== null && $y !== null && $cc !== null) {
            // Check qualification (any X < threshold)
            $isQualified = ($x1 >= $qualThreshold && $x2 >= $qualThreshold && $x3 >= $qualThreshold);
            $result['is_qualified'] = $isQualified ? 1 : 0;
            
            // Calculate Z: Z = 0.2*(X1+X2+X3)/3 + 0.1*CC + 0.7*Y
            $xAvg = ($x1 + $x2 + $x3) / 3;
            $z = ($formula['x_weight'] * $xAvg) + ($formula['cc_weight'] * $cc) + ($formula['y_weight'] * $y);
            $result['z'] = round($z, 2);
            
            // Calculate GPA and Letter
            if ($z >= $gpaThresholds['A']['min']) {
                $result['gpa'] = $gpaThresholds['A']['gpa'];
                $result['letter'] = 'A';
            } elseif ($z >= $gpaThresholds['B']['min'] && $z <= $gpaThresholds['B']['max']) {
                $result['gpa'] = $gpaThresholds['B']['gpa'];
                $result['letter'] = 'B';
            } elseif ($z >= $gpaThresholds['C']['min'] && $z <= $gpaThresholds['C']['max']) {
                $result['gpa'] = $gpaThresholds['C']['gpa'];
                $result['letter'] = 'C';
            } else {
                $result['gpa'] = $gpaThresholds['D']['gpa'];
                $result['letter'] = 'D';
            }
        }
        
        return $result;
    }
}


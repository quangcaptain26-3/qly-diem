<?php
/**
 * Score Helper Functions
 */

if (!function_exists('format_score')) {
    function format_score($score) {
        return $score !== null ? number_format($score, 2) : '-';
    }
}

if (!function_exists('get_letter_color')) {
    function get_letter_color($letter) {
        $colors = [
            'A' => 'success',
            'B' => 'primary',
            'C' => 'warning',
            'D' => 'danger',
        ];
        return $colors[$letter] ?? 'secondary';
    }
}


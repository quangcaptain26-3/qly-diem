<?php
/**
 * Attendance Helper Functions
 */

if (!function_exists('get_attendance_status_label')) {
    function get_attendance_status_label($status) {
        $labels = [
            'present' => 'Có mặt',
            'absent' => 'Vắng',
            'late' => 'Muộn',
            'excused' => 'Có phép',
        ];
        return $labels[$status] ?? $status;
    }
}

if (!function_exists('get_attendance_status_badge')) {
    function get_attendance_status_badge($status) {
        $badges = [
            'present' => 'success',
            'absent' => 'danger',
            'late' => 'warning',
            'excused' => 'info',
        ];
        return $badges[$status] ?? 'secondary';
    }
}


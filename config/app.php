<?php
/**
 * Application Configuration
 */

return [
    'name' => 'Hệ thống Quản lý Điểm Đại học',
    'version' => '1.0.0',
    'timezone' => 'Asia/Ho_Chi_Minh',
    'base_url' => '/',
    'site_url' => 'https://qly-dev26.free.nf', // URL đầy đủ của website
    'session_name' => 'qly_diem_session',
    'session_lifetime' => 7200, // 2 hours
    'password_reset_token_expiry' => 1800, // 30 minutes
    'score_formula' => [
        'x_weight' => 0.2, // (X1+X2+X3)/3
        'cc_weight' => 0.1, // Chuyên cần
        'y_weight' => 0.7, // Cuối kỳ
    ],
    'gpa_thresholds' => [
        'A' => ['min' => 8.5, 'gpa' => 4.0],
        'B' => ['min' => 7.0, 'max' => 8.4, 'gpa' => 3.0],
        'C' => ['min' => 5.5, 'max' => 6.9, 'gpa' => 2.0],
        'D' => ['min' => 0, 'max' => 5.4, 'gpa' => 1.0],
    ],
    'qualification_threshold' => 4.0, // Điểm tư cách tối thiểu
];


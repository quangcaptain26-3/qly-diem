<?php
/**
 * Role & Permission Configuration
 */

return [
    'roles' => [
        'root' => 'Root',
        'dean' => 'Trưởng khoa',
        'academic_affairs' => 'Giáo vụ',
        'lecturer' => 'Giảng viên',
        'student' => 'Sinh viên',
    ],

    'permissions' => [
        'root' => [
            'login',
            'logout',
            'reset_password_system',
            'view_statistics_all',
            'unlock_scores',
        ],
        'dean' => [
            'login',
            'logout',
            'view_statistics_faculty',
            'export_pdf',
            'export_excel',
            'view_scores_faculty',
        ],
        'academic_affairs' => [
            'login',
            'logout',
            'view_statistics_faculty',
            'export_pdf',
            'export_excel',
            'view_scores_faculty',
            'lock_scores',
        ],
        'lecturer' => [
            'login',
            'logout',
            'import_scores',
            'input_scores',
            'view_scores_class',
            'view_attendance_class',
        ],
        'student' => [
            'login',
            'logout',
            'view_own_scores',
        ],
    ],
];


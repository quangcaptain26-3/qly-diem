<?php
/**
 * Scholarship Configuration
 */

return [
    'default_rules' => [
        [
            'type' => 'Xuất sắc',
            'min_gpa' => 3.50,
            'min_credits' => 15,
        ],
        [
            'type' => 'Giỏi',
            'min_gpa' => 3.20,
            'min_credits' => 15,
        ],
        [
            'type' => 'Khá',
            'min_gpa' => 2.50,
            'min_credits' => 12,
        ],
    ],

    'faculty_rules' => [
        1 => [ // CNTT
            ['type' => 'Xuất sắc CNTT', 'min_gpa' => 3.60, 'min_credits' => 15],
            ['type' => 'Giỏi CNTT', 'min_gpa' => 3.30, 'min_credits' => 15],
        ],
        2 => [ // Ngoại Ngữ
            ['type' => 'Xuất sắc NN', 'min_gpa' => 3.55, 'min_credits' => 15],
            ['type' => 'Giỏi NN', 'min_gpa' => 3.25, 'min_credits' => 15],
        ],
        3 => [ // Kinh tế
            ['type' => 'Xuất sắc KT', 'min_gpa' => 3.58, 'min_credits' => 15],
            ['type' => 'Giỏi KT', 'min_gpa' => 3.28, 'min_credits' => 15],
        ],
    ],
];


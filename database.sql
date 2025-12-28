-- ============================================
-- DATABASE: QUẢN LÝ ĐIỂM ĐẠI HỌC
-- Host: InfinityFree (MySQL 5.7+ / 8.0)
-- Charset: UTF8MB4
-- Engine: InnoDB
-- ============================================

-- Tạo database
CREATE DATABASE IF NOT EXISTS `qly_diem` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `qly_diem`;

-- ============================================
-- 1. BẢNG NGƯỜI DÙNG (USERS)
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Tên đăng nhập',
  `email` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email',
  `password` VARCHAR(64) NOT NULL COMMENT 'SHA2-256 hash',
  `full_name` VARCHAR(100) NOT NULL COMMENT 'Họ và tên',
  `role` ENUM('root', 'dean', 'academic_affairs', 'lecturer', 'student') NOT NULL COMMENT 'Vai trò',
  `faculty_id` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'FK: Khoa (cho dean, academic_affairs)',
  `department_id` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'FK: Bộ môn (cho lecturer)',
  `student_code` VARCHAR(20) NULL DEFAULT NULL COMMENT 'Mã sinh viên (cho student)',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_role` (`role`),
  INDEX `idx_faculty` (`faculty_id`),
  INDEX `idx_department` (`department_id`),
  INDEX `idx_student_code` (`student_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng người dùng';

-- ============================================
-- 2. BẢNG KHOA (FACULTIES)
-- ============================================
CREATE TABLE IF NOT EXISTS `faculties` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(10) NOT NULL UNIQUE COMMENT 'Mã khoa (VD: CNTT, NN, KT)',
  `name` VARCHAR(100) NOT NULL COMMENT 'Tên khoa',
  `description` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng khoa';

-- ============================================
-- 3. BẢNG CHUYÊN NGÀNH (MAJORS)
-- ============================================
CREATE TABLE IF NOT EXISTS `majors` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `faculty_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK: Khoa',
  `code` VARCHAR(10) NOT NULL COMMENT 'Mã chuyên ngành (VD: CNT, KPM, NNA)',
  `name` VARCHAR(100) NOT NULL COMMENT 'Tên chuyên ngành',
  `description` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_faculty_major` (`faculty_id`, `code`),
  INDEX `idx_faculty` (`faculty_id`),
  CONSTRAINT `fk_majors_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng chuyên ngành';

-- ============================================
-- 4. BẢNG BỘ MÔN (DEPARTMENTS)
-- ============================================
CREATE TABLE IF NOT EXISTS `departments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `faculty_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK: Khoa',
  `name` VARCHAR(100) NOT NULL COMMENT 'Tên bộ môn',
  `description` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_faculty` (`faculty_id`),
  CONSTRAINT `fk_departments_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng bộ môn';

-- ============================================
-- 5. BẢNG HỌC PHẦN (SUBJECTS)
-- ============================================
CREATE TABLE IF NOT EXISTS `subjects` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK: Bộ môn',
  `code` VARCHAR(20) NOT NULL COMMENT 'Mã học phần (VD: LTC, NHE1)',
  `name` VARCHAR(200) NOT NULL COMMENT 'Tên học phần',
  `credits` INT(2) NOT NULL DEFAULT 3 COMMENT 'Số tín chỉ',
  `description` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_department` (`department_id`),
  INDEX `idx_code` (`code`),
  CONSTRAINT `fk_subjects_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng học phần';

-- ============================================
-- 6. BẢNG LỚP HỌC PHẦN (CLASS_ROOMS)
-- ============================================
CREATE TABLE IF NOT EXISTS `class_rooms` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subject_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK: Học phần',
  `lecturer_id` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'FK: Giảng viên phụ trách',
  `class_code` VARCHAR(20) NOT NULL COMMENT 'Mã lớp (VD: LTC_N01, LTC_N02)',
  `semester` ENUM('HK1', 'HK2') NOT NULL COMMENT 'Học kỳ',
  `academic_year` VARCHAR(9) NOT NULL COMMENT 'Năm học (VD: 2024-2025)',
  `max_students` INT(4) NULL DEFAULT NULL COMMENT 'Số lượng SV tối đa',
  `is_locked` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=đã chốt điểm, 0=chưa chốt',
  `locked_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Thời điểm chốt điểm',
  `locked_by` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'FK: Người chốt điểm',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_class_code_year` (`class_code`, `academic_year`, `semester`),
  INDEX `idx_subject` (`subject_id`),
  INDEX `idx_lecturer` (`lecturer_id`),
  INDEX `idx_academic_year` (`academic_year`, `semester`),
  CONSTRAINT `fk_class_rooms_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_class_rooms_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_class_rooms_locked_by` FOREIGN KEY (`locked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lớp học phần';

-- ============================================
-- 7. BẢNG ĐĂNG KÝ LỚP (ENROLLMENTS)
-- ============================================
CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `class_room_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK: Lớp học phần',
  `student_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK: Sinh viên',
  `enrolled_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời điểm đăng ký',
  `status` ENUM('active', 'dropped', 'completed') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_student_class` (`class_room_id`, `student_id`),
  INDEX `idx_student` (`student_id`),
  INDEX `idx_class_room` (`class_room_id`),
  CONSTRAINT `fk_enrollments_class_room` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_enrollments_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng đăng ký lớp (SV - Lớp)';

-- ============================================
-- 8. BẢNG ĐIỂM DANH/CHUYÊN CẦN (ATTENDANCES)
-- ============================================
CREATE TABLE IF NOT EXISTS `attendances` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enrollment_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK: Đăng ký lớp',
  `attendance_date` DATE NOT NULL COMMENT 'Ngày điểm danh',
  `status` ENUM('present', 'absent', 'late', 'excused') NOT NULL DEFAULT 'present' COMMENT 'Trạng thái',
  `notes` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_enrollment` (`enrollment_id`),
  INDEX `idx_date` (`attendance_date`),
  CONSTRAINT `fk_attendances_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng điểm danh/chuyên cần';

-- ============================================
-- 9. BẢNG ĐIỂM (SCORES)
-- ============================================
CREATE TABLE IF NOT EXISTS `scores` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enrollment_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK: Đăng ký lớp',
  `x1` DECIMAL(4,2) NULL DEFAULT NULL COMMENT 'Điểm tư cách 1 (0-10)',
  `x2` DECIMAL(4,2) NULL DEFAULT NULL COMMENT 'Điểm tư cách 2 (0-10)',
  `x3` DECIMAL(4,2) NULL DEFAULT NULL COMMENT 'Điểm tư cách 3 (0-10)',
  `y` DECIMAL(4,2) NULL DEFAULT NULL COMMENT 'Điểm cuối kỳ (0-10)',
  `cc` DECIMAL(4,2) NULL DEFAULT NULL COMMENT 'Điểm chuyên cần (0-10)',
  `z` DECIMAL(4,2) NULL DEFAULT NULL COMMENT 'Điểm tổng kết (tính toán)',
  `gpa` DECIMAL(3,2) NULL DEFAULT NULL COMMENT 'GPA (0-4.0)',
  `letter` VARCHAR(2) NULL DEFAULT NULL COMMENT 'Letter grade (A, B, C, D)',
  `is_qualified` TINYINT(1) NULL DEFAULT NULL COMMENT '1=đủ tư cách, 0=mất tư cách',
  `notes` TEXT NULL DEFAULT NULL,
  `input_by` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'FK: Người nhập điểm',
  `input_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Thời điểm nhập điểm',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_enrollment_score` (`enrollment_id`),
  INDEX `idx_input_by` (`input_by`),
  CONSTRAINT `fk_scores_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_scores_input_by` FOREIGN KEY (`input_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng điểm số';

-- ============================================
-- 10. BẢNG CHỐT ĐIỂM (SCORE_LOCKS)
-- ============================================
CREATE TABLE IF NOT EXISTS `score_locks` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `class_room_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK: Lớp học phần',
  `is_locked` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1=đã chốt, 0=chưa chốt',
  `locked_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Thời điểm chốt',
  `locked_by` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'FK: Người chốt',
  `unlocked_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Thời điểm mở lại (root)',
  `unlocked_by` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'FK: Người mở lại (root)',
  `notes` TEXT NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_class_lock` (`class_room_id`),
  INDEX `idx_locked_by` (`locked_by`),
  INDEX `idx_unlocked_by` (`unlocked_by`),
  CONSTRAINT `fk_score_locks_class_room` FOREIGN KEY (`class_room_id`) REFERENCES `class_rooms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_score_locks_locked_by` FOREIGN KEY (`locked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_score_locks_unlocked_by` FOREIGN KEY (`unlocked_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng chốt điểm (chi tiết)';

-- ============================================
-- 11. BẢNG QUY TẮC HỌC BỔNG (SCHOLARSHIP_RULES)
-- ============================================
CREATE TABLE IF NOT EXISTS `scholarship_rules` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `faculty_id` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'FK: Khoa (NULL = toàn trường)',
  `scholarship_type` VARCHAR(50) NOT NULL COMMENT 'Loại học bổng (VD: Xuất sắc, Giỏi)',
  `min_gpa` DECIMAL(3,2) NOT NULL COMMENT 'GPA tối thiểu',
  `min_credits` INT(3) NULL DEFAULT NULL COMMENT 'Số tín chỉ tối thiểu',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_faculty` (`faculty_id`),
  CONSTRAINT `fk_scholarship_rules_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quy tắc học bổng';

-- ============================================
-- 12. BẢNG RESET PASSWORD TOKEN
-- ============================================
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT 'FK: Người dùng',
  `token` VARCHAR(64) NOT NULL UNIQUE COMMENT 'Token reset',
  `expires_at` TIMESTAMP NOT NULL COMMENT 'Thời hạn (15-30 phút)',
  `used_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Đã sử dụng',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_token` (`token`),
  INDEX `idx_user` (`user_id`),
  INDEX `idx_expires` (`expires_at`),
  CONSTRAINT `fk_password_reset_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng token reset password';

-- ============================================
-- 13. DỮ LIỆU MẪU (SAMPLE DATA)
-- ============================================

-- 3 Khoa
INSERT INTO `faculties` (`code`, `name`, `description`) VALUES
('CNTT', 'Khoa Công nghệ Thông tin', 'Khoa CNTT'),
('NN', 'Khoa Ngoại Ngữ', 'Khoa Ngoại Ngữ'),
('KT', 'Khoa Kinh tế', 'Khoa Kinh tế');

-- Chuyên ngành
INSERT INTO `majors` (`faculty_id`, `code`, `name`) VALUES
(1, 'CNT', 'Công nghệ Thông tin'),
(1, 'KPM', 'Kỹ thuật Phần mềm'),
(1, 'TTM', 'Truyền thông Mạng'),
(2, 'NNA', 'Ngôn ngữ Anh'),
(2, 'ATM', 'Anh Thương mại'),
(3, 'KTB', 'Kinh doanh'),
(3, 'KTT', 'Tài chính'),
(3, 'KTN', 'Nghiên cứu Thị trường');

-- Bộ môn
INSERT INTO `departments` (`faculty_id`, `name`) VALUES
-- CNTT
(1, 'Khoa học máy tính'),
(1, 'Hệ thống thông tin'),
(1, 'An toàn thông tin'),
-- Ngoại Ngữ
(2, 'Nghe 1'),
(2, 'Nghe 2'),
(2, 'Nghe 3'),
(2, 'Nói 1'),
(2, 'Nói 2'),
(2, 'Nói 3'),
(2, 'Đọc 1'),
(2, 'Đọc 2'),
(2, 'Đọc 3'),
(2, 'Viết 1'),
(2, 'Viết 2'),
(2, 'Viết 3'),
-- Kinh tế
(3, 'Kinh doanh quốc tế'),
(3, 'Tài chính – tiền tệ'),
(3, 'Nghiên cứu thị trường');


-- User Root (mật khẩu: 123456 - SHA2-256)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`) VALUES
('root', 'root@university.edu.vn', SHA2('123456', 256), 'Administrator', 'root');

-- ============================================
-- USERS - KHOA CNTT (faculty_id = 1)
-- ============================================
-- Trưởng khoa CNTT
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `faculty_id`) VALUES
('DEAN_CNTT', 'dean.cntt@university.edu.vn', SHA2('123456', 256), 'Trưởng khoa CNTT', 'dean', 1);

-- Giáo vụ CNTT
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `faculty_id`) VALUES
('GV_CNTT', 'gv.cntt@university.edu.vn', SHA2('123456', 256), 'Giáo vụ Khoa CNTT', 'academic_affairs', 1);

-- Giảng viên CNTT (department_id: 1=Khoa học máy tính, 2=Hệ thống thông tin, 3=An toàn thông tin)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `department_id`) VALUES
('GV001', 'gv001@university.edu.vn', SHA2('123456', 256), 'Giảng viên 001', 'lecturer', 1),
('GV002', 'gv002@university.edu.vn', SHA2('123456', 256), 'Giảng viên 002', 'lecturer', 1),
('GV003', 'gv003@university.edu.vn', SHA2('123456', 256), 'Giảng viên 003', 'lecturer', 2),
('GV004', 'gv004@university.edu.vn', SHA2('123456', 256), 'Giảng viên 004', 'lecturer', 3);

-- Sinh viên CNTT (SV001 - SV025)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `student_code`) VALUES
('SV001', 'sv001@university.edu.vn', SHA2('123456', 256), 'Nguyễn Văn An', 'student', 'SV001'),
('SV002', 'sv002@university.edu.vn', SHA2('123456', 256), 'Trần Thị Bình', 'student', 'SV002'),
('SV003', 'sv003@university.edu.vn', SHA2('123456', 256), 'Lê Văn Cường', 'student', 'SV003'),
('SV004', 'sv004@university.edu.vn', SHA2('123456', 256), 'Phạm Thị Dung', 'student', 'SV004'),
('SV005', 'sv005@university.edu.vn', SHA2('123456', 256), 'Hoàng Văn Em', 'student', 'SV005'),
('SV006', 'sv006@university.edu.vn', SHA2('123456', 256), 'Vũ Thị Phương', 'student', 'SV006'),
('SV007', 'sv007@university.edu.vn', SHA2('123456', 256), 'Đỗ Văn Giang', 'student', 'SV007'),
('SV008', 'sv008@university.edu.vn', SHA2('123456', 256), 'Bùi Thị Hoa', 'student', 'SV008'),
('SV009', 'sv009@university.edu.vn', SHA2('123456', 256), 'Ngô Văn Hùng', 'student', 'SV009'),
('SV010', 'sv010@university.edu.vn', SHA2('123456', 256), 'Đinh Thị Lan', 'student', 'SV010'),
('SV011', 'sv011@university.edu.vn', SHA2('123456', 256), 'Lý Văn Minh', 'student', 'SV011'),
('SV012', 'sv012@university.edu.vn', SHA2('123456', 256), 'Võ Thị Nga', 'student', 'SV012'),
('SV013', 'sv013@university.edu.vn', SHA2('123456', 256), 'Trương Văn Oanh', 'student', 'SV013'),
('SV014', 'sv014@university.edu.vn', SHA2('123456', 256), 'Đặng Thị Phượng', 'student', 'SV014'),
('SV015', 'sv015@university.edu.vn', SHA2('123456', 256), 'Phan Văn Quang', 'student', 'SV015'),
('SV016', 'sv016@university.edu.vn', SHA2('123456', 256), 'Lương Thị Quyên', 'student', 'SV016'),
('SV017', 'sv017@university.edu.vn', SHA2('123456', 256), 'Tạ Văn Sơn', 'student', 'SV017'),
('SV018', 'sv018@university.edu.vn', SHA2('123456', 256), 'Chu Thị Tâm', 'student', 'SV018'),
('SV019', 'sv019@university.edu.vn', SHA2('123456', 256), 'Dương Văn Tuấn', 'student', 'SV019'),
('SV020', 'sv020@university.edu.vn', SHA2('123456', 256), 'Hồ Thị Uyên', 'student', 'SV020'),
('SV021', 'sv021@university.edu.vn', SHA2('123456', 256), 'Lưu Văn Việt', 'student', 'SV021'),
('SV022', 'sv022@university.edu.vn', SHA2('123456', 256), 'Mai Thị Xuân', 'student', 'SV022'),
('SV023', 'sv023@university.edu.vn', SHA2('123456', 256), 'Vương Văn Yên', 'student', 'SV023'),
('SV024', 'sv024@university.edu.vn', SHA2('123456', 256), 'Tô Thị Zin', 'student', 'SV024'),
('SV025', 'sv025@university.edu.vn', SHA2('123456', 256), 'Cao Văn Anh', 'student', 'SV025');

-- ============================================
-- USERS - KHOA NGOẠI NGỮ (faculty_id = 2)
-- ============================================
-- Trưởng khoa Ngoại Ngữ
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `faculty_id`) VALUES
('DEAN_NN', 'dean.nn@university.edu.vn', SHA2('123456', 256), 'Trưởng khoa Ngoại Ngữ', 'dean', 2);

-- Giáo vụ Ngoại Ngữ
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `faculty_id`) VALUES
('GV_NN', 'gv.nn@university.edu.vn', SHA2('123456', 256), 'Giáo vụ Khoa Ngoại Ngữ', 'academic_affairs', 2);

-- Giảng viên Ngoại Ngữ (department_id: 4=Nghe 1, 5=Nghe 2, 6=Nghe 3, 7=Nói 1, 8=Nói 2, 9=Nói 3)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `department_id`) VALUES
('GV005', 'gv005@university.edu.vn', SHA2('123456', 256), 'Giảng viên 005', 'lecturer', 4),
('GV006', 'gv006@university.edu.vn', SHA2('123456', 256), 'Giảng viên 006', 'lecturer', 5),
('GV007', 'gv007@university.edu.vn', SHA2('123456', 256), 'Giảng viên 007', 'lecturer', 7),
('GV008', 'gv008@university.edu.vn', SHA2('123456', 256), 'Giảng viên 008', 'lecturer', 10);

-- Sinh viên Ngoại Ngữ (SV026 - SV050)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `student_code`) VALUES
('SV026', 'sv026@university.edu.vn', SHA2('123456', 256), 'Nguyễn Thị Bảo', 'student', 'SV026'),
('SV027', 'sv027@university.edu.vn', SHA2('123456', 256), 'Trần Văn Cảnh', 'student', 'SV027'),
('SV028', 'sv028@university.edu.vn', SHA2('123456', 256), 'Lê Thị Duyên', 'student', 'SV028'),
('SV029', 'sv029@university.edu.vn', SHA2('123456', 256), 'Phạm Văn Đức', 'student', 'SV029'),
('SV030', 'sv030@university.edu.vn', SHA2('123456', 256), 'Hoàng Thị Hạnh', 'student', 'SV030'),
('SV031', 'sv031@university.edu.vn', SHA2('123456', 256), 'Vũ Văn Huy', 'student', 'SV031'),
('SV032', 'sv032@university.edu.vn', SHA2('123456', 256), 'Đỗ Thị Kiều', 'student', 'SV032'),
('SV033', 'sv033@university.edu.vn', SHA2('123456', 256), 'Bùi Văn Long', 'student', 'SV033'),
('SV034', 'sv034@university.edu.vn', SHA2('123456', 256), 'Ngô Thị Mai', 'student', 'SV034'),
('SV035', 'sv035@university.edu.vn', SHA2('123456', 256), 'Đinh Văn Nam', 'student', 'SV035'),
('SV036', 'sv036@university.edu.vn', SHA2('123456', 256), 'Lý Thị Oanh', 'student', 'SV036'),
('SV037', 'sv037@university.edu.vn', SHA2('123456', 256), 'Võ Văn Phong', 'student', 'SV037'),
('SV038', 'sv038@university.edu.vn', SHA2('123456', 256), 'Trương Thị Quỳnh', 'student', 'SV038'),
('SV039', 'sv039@university.edu.vn', SHA2('123456', 256), 'Đặng Văn Rạng', 'student', 'SV039'),
('SV040', 'sv040@university.edu.vn', SHA2('123456', 256), 'Phan Thị Sương', 'student', 'SV040'),
('SV041', 'sv041@university.edu.vn', SHA2('123456', 256), 'Lương Văn Thành', 'student', 'SV041'),
('SV042', 'sv042@university.edu.vn', SHA2('123456', 256), 'Tạ Thị Thảo', 'student', 'SV042'),
('SV043', 'sv043@university.edu.vn', SHA2('123456', 256), 'Chu Văn Thắng', 'student', 'SV043'),
('SV044', 'sv044@university.edu.vn', SHA2('123456', 256), 'Dương Thị Trang', 'student', 'SV044'),
('SV045', 'sv045@university.edu.vn', SHA2('123456', 256), 'Hồ Văn Trung', 'student', 'SV045'),
('SV046', 'sv046@university.edu.vn', SHA2('123456', 256), 'Lưu Thị Vân', 'student', 'SV046'),
('SV047', 'sv047@university.edu.vn', SHA2('123456', 256), 'Mai Văn Vinh', 'student', 'SV047'),
('SV048', 'sv048@university.edu.vn', SHA2('123456', 256), 'Vương Thị Yến', 'student', 'SV048'),
('SV049', 'sv049@university.edu.vn', SHA2('123456', 256), 'Tô Văn Zũng', 'student', 'SV049'),
('SV050', 'sv050@university.edu.vn', SHA2('123456', 256), 'Cao Thị Ánh', 'student', 'SV050');

-- ============================================
-- USERS - KHOA KINH TẾ (faculty_id = 3)
-- ============================================
-- Trưởng khoa Kinh tế
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `faculty_id`) VALUES
('DEAN_KT', 'dean.kt@university.edu.vn', SHA2('123456', 256), 'Trưởng khoa Kinh tế', 'dean', 3);

-- Giáo vụ Kinh tế
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `faculty_id`) VALUES
('GV_KT', 'gv.kt@university.edu.vn', SHA2('123456', 256), 'Giáo vụ Khoa Kinh tế', 'academic_affairs', 3);

-- Giảng viên Kinh tế (department_id: 16=Kinh doanh quốc tế, 17=Tài chính – tiền tệ, 18=Nghiên cứu thị trường)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `department_id`) VALUES
('GV009', 'gv009@university.edu.vn', SHA2('123456', 256), 'Giảng viên 009', 'lecturer', 16),
('GV010', 'gv010@university.edu.vn', SHA2('123456', 256), 'Giảng viên 010', 'lecturer', 17),
('GV011', 'gv011@university.edu.vn', SHA2('123456', 256), 'Giảng viên 011', 'lecturer', 18),
('GV012', 'gv012@university.edu.vn', SHA2('123456', 256), 'Giảng viên 012', 'lecturer', 16);

-- Sinh viên Kinh tế (SV051 - SV075)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`, `student_code`) VALUES
('SV051', 'sv051@university.edu.vn', SHA2('123456', 256), 'Nguyễn Văn Bảo', 'student', 'SV051'),
('SV052', 'sv052@university.edu.vn', SHA2('123456', 256), 'Trần Thị Cẩm', 'student', 'SV052'),
('SV053', 'sv053@university.edu.vn', SHA2('123456', 256), 'Lê Văn Dũng', 'student', 'SV053'),
('SV054', 'sv054@university.edu.vn', SHA2('123456', 256), 'Phạm Thị Hương', 'student', 'SV054'),
('SV055', 'sv055@university.edu.vn', SHA2('123456', 256), 'Hoàng Văn Khánh', 'student', 'SV055'),
('SV056', 'sv056@university.edu.vn', SHA2('123456', 256), 'Vũ Thị Linh', 'student', 'SV056'),
('SV057', 'sv057@university.edu.vn', SHA2('123456', 256), 'Đỗ Văn Mạnh', 'student', 'SV057'),
('SV058', 'sv058@university.edu.vn', SHA2('123456', 256), 'Bùi Thị Nhung', 'student', 'SV058'),
('SV059', 'sv059@university.edu.vn', SHA2('123456', 256), 'Ngô Văn Phúc', 'student', 'SV059'),
('SV060', 'sv060@university.edu.vn', SHA2('123456', 256), 'Đinh Thị Quyên', 'student', 'SV060'),
('SV061', 'sv061@university.edu.vn', SHA2('123456', 256), 'Lý Văn Sinh', 'student', 'SV061'),
('SV062', 'sv062@university.edu.vn', SHA2('123456', 256), 'Võ Thị Thanh', 'student', 'SV062'),
('SV063', 'sv063@university.edu.vn', SHA2('123456', 256), 'Trương Văn Thắng', 'student', 'SV063'),
('SV064', 'sv064@university.edu.vn', SHA2('123456', 256), 'Đặng Thị Trang', 'student', 'SV064'),
('SV065', 'sv065@university.edu.vn', SHA2('123456', 256), 'Phan Văn Tuấn', 'student', 'SV065'),
('SV066', 'sv066@university.edu.vn', SHA2('123456', 256), 'Lương Thị Uyên', 'student', 'SV066'),
('SV067', 'sv067@university.edu.vn', SHA2('123456', 256), 'Tạ Văn Việt', 'student', 'SV067'),
('SV068', 'sv068@university.edu.vn', SHA2('123456', 256), 'Chu Thị Xuân', 'student', 'SV068'),
('SV069', 'sv069@university.edu.vn', SHA2('123456', 256), 'Dương Văn Yên', 'student', 'SV069'),
('SV070', 'sv070@university.edu.vn', SHA2('123456', 256), 'Hồ Thị Zin', 'student', 'SV070'),
('SV071', 'sv071@university.edu.vn', SHA2('123456', 256), 'Lưu Văn Anh', 'student', 'SV071'),
('SV072', 'sv072@university.edu.vn', SHA2('123456', 256), 'Mai Thị Bích', 'student', 'SV072'),
('SV073', 'sv073@university.edu.vn', SHA2('123456', 256), 'Vương Văn Cường', 'student', 'SV073'),
('SV074', 'sv074@university.edu.vn', SHA2('123456', 256), 'Tô Thị Dung', 'student', 'SV074'),
('SV075', 'sv075@university.edu.vn', SHA2('123456', 256), 'Cao Văn Em', 'student', 'SV075');

-- ============================================
-- HỌC PHẦN (SUBJECTS)
-- ============================================
-- Học phần CNTT (department_id: 1=Khoa học máy tính, 2=Hệ thống thông tin, 3=An toàn thông tin)
INSERT INTO `subjects` (`department_id`, `code`, `name`, `credits`) VALUES
(1, 'LTC', 'Lập trình C', 3),
(1, 'LTJ', 'Lập trình Java', 3),
(1, 'CSDL', 'Cơ sở dữ liệu', 3),
(1, 'CTDL', 'Cấu trúc dữ liệu', 3),
(2, 'HTTT', 'Hệ thống thông tin', 3),
(2, 'PMQL', 'Phần mềm quản lý', 3),
(3, 'ATTT', 'An toàn thông tin', 3),
(3, 'MMT', 'Mật mã học', 3);

-- Học phần Ngoại Ngữ (department_id: 4=Nghe 1, 5=Nghe 2, 6=Nghe 3, 7=Nói 1, 8=Nói 2, 9=Nói 3, 10=Đọc 1, 11=Đọc 2, 12=Đọc 3, 13=Viết 1, 14=Viết 2, 15=Viết 3)
INSERT INTO `subjects` (`department_id`, `code`, `name`, `credits`) VALUES
(4, 'NHE1', 'Nghe 1', 2),
(5, 'NHE2', 'Nghe 2', 2),
(6, 'NHE3', 'Nghe 3', 2),
(7, 'NOI1', 'Nói 1', 2),
(8, 'NOI2', 'Nói 2', 2),
(9, 'NOI3', 'Nói 3', 2),
(10, 'DOC1', 'Đọc 1', 2),
(11, 'DOC2', 'Đọc 2', 2),
(12, 'DOC3', 'Đọc 3', 2),
(13, 'VIE1', 'Viết 1', 2),
(14, 'VIE2', 'Viết 2', 2),
(15, 'VIE3', 'Viết 3', 2);

-- Học phần Kinh tế (department_id: 16=Kinh doanh quốc tế, 17=Tài chính – tiền tệ, 18=Nghiên cứu thị trường)
INSERT INTO `subjects` (`department_id`, `code`, `name`, `credits`) VALUES
(16, 'KDQT', 'Kinh doanh quốc tế', 3),
(16, 'QTKD', 'Quản trị kinh doanh', 3),
(17, 'TCDN', 'Tài chính doanh nghiệp', 3),
(17, 'TTTM', 'Tiền tệ và thị trường', 3),
(18, 'NCTM', 'Nghiên cứu thị trường', 3),
(18, 'MKT', 'Marketing', 3);

-- ============================================
-- LỚP HỌC PHẦN (CLASS_ROOMS)
-- ============================================
-- Lớp CNTT - HK1 2024-2025
-- Giảng viên: GV001 (id=4), GV002 (id=5), GV003 (id=6), GV004 (id=7)
INSERT INTO `class_rooms` (`subject_id`, `lecturer_id`, `class_code`, `semester`, `academic_year`, `max_students`) VALUES
-- Lập trình C - GV001
(1, 4, 'LTC_N01', 'HK1', '2024-2025', 30),
(1, 4, 'LTC_N02', 'HK1', '2024-2025', 30),
-- Lập trình Java - GV002
(2, 5, 'LTJ_N01', 'HK1', '2024-2025', 30),
-- Cơ sở dữ liệu - GV003
(3, 6, 'CSDL_N01', 'HK1', '2024-2025', 30),
-- Cấu trúc dữ liệu - GV004
(4, 7, 'CTDL_N01', 'HK1', '2024-2025', 30);

-- Lớp Ngoại Ngữ - HK1 2024-2025
-- Giảng viên: GV005 (id=35), GV006 (id=36), GV007 (id=37), GV008 (id=38)
INSERT INTO `class_rooms` (`subject_id`, `lecturer_id`, `class_code`, `semester`, `academic_year`, `max_students`) VALUES
-- Nghe 1 - GV005
(9, 35, 'NHE1_N01', 'HK1', '2024-2025', 30),
(9, 35, 'NHE1_N02', 'HK1', '2024-2025', 30),
-- Nghe 2 - GV006
(10, 36, 'NHE2_N01', 'HK1', '2024-2025', 30),
-- Nói 1 - GV007
(12, 37, 'NOI1_N01', 'HK1', '2024-2025', 30),
-- Viết 1 - GV008
(19, 38, 'VIE1_N01', 'HK1', '2024-2025', 30);

-- Lớp Kinh tế - HK1 2024-2025
-- Giảng viên: GV009 (id=66), GV010 (id=67), GV011 (id=68), GV012 (id=69)
INSERT INTO `class_rooms` (`subject_id`, `lecturer_id`, `class_code`, `semester`, `academic_year`, `max_students`) VALUES
-- Kinh doanh quốc tế - GV009
(21, 66, 'KDQT_N01', 'HK1', '2024-2025', 30),
(21, 66, 'KDQT_N02', 'HK1', '2024-2025', 30),
-- Tài chính doanh nghiệp - GV010
(23, 67, 'TCDN_N01', 'HK1', '2024-2025', 30),
-- Nghiên cứu thị trường - GV011
(25, 68, 'NCTM_N01', 'HK1', '2024-2025', 30),
-- Marketing - GV012
(26, 69, 'MKT_N01', 'HK1', '2024-2025', 30);

-- ============================================
-- PHÂN SINH VIÊN VÀO LỚP (ENROLLMENTS)
-- ============================================
-- Lớp LTC_N01 (id=1) - Sinh viên CNTT: SV001-SV015 (id=8-22)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(1, 8), (1, 9), (1, 10), (1, 11), (1, 12), (1, 13), (1, 14), (1, 15), (1, 16), (1, 17),
(1, 18), (1, 19), (1, 20), (1, 21), (1, 22);

-- Lớp LTC_N02 (id=2) - Sinh viên CNTT: SV016-SV025 (id=23-32)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(2, 23), (2, 24), (2, 25), (2, 26), (2, 27), (2, 28), (2, 29), (2, 30), (2, 31), (2, 32);

-- Lớp LTJ_N01 (id=3) - Sinh viên CNTT: SV001-SV010 (id=8-17)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(3, 8), (3, 9), (3, 10), (3, 11), (3, 12), (3, 13), (3, 14), (3, 15), (3, 16), (3, 17);

-- Lớp CSDL_N01 (id=4) - Sinh viên CNTT: SV011-SV020 (id=18-27)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(4, 18), (4, 19), (4, 20), (4, 21), (4, 22), (4, 23), (4, 24), (4, 25), (4, 26), (4, 27);

-- Lớp CTDL_N01 (id=5) - Sinh viên CNTT: SV021-SV025 (id=28-32)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(5, 28), (5, 29), (5, 30), (5, 31), (5, 32);

-- Lớp NHE1_N01 (id=6) - Sinh viên NN: SV026-SV040 (id=39-53)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(6, 39), (6, 40), (6, 41), (6, 42), (6, 43), (6, 44), (6, 45), (6, 46), (6, 47), (6, 48),
(6, 49), (6, 50), (6, 51), (6, 52), (6, 53);

-- Lớp NHE1_N02 (id=7) - Sinh viên NN: SV041-SV050 (id=54-63)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(7, 54), (7, 55), (7, 56), (7, 57), (7, 58), (7, 59), (7, 60), (7, 61), (7, 62), (7, 63);

-- Lớp NHE2_N01 (id=8) - Sinh viên NN: SV026-SV035 (id=39-48)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(8, 39), (8, 40), (8, 41), (8, 42), (8, 43), (8, 44), (8, 45), (8, 46), (8, 47), (8, 48);

-- Lớp NOI1_N01 (id=9) - Sinh viên NN: SV036-SV045 (id=49-58)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(9, 49), (9, 50), (9, 51), (9, 52), (9, 53), (9, 54), (9, 55), (9, 56), (9, 57), (9, 58);

-- Lớp VIE1_N01 (id=10) - Sinh viên NN: SV046-SV050 (id=59-63)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(10, 59), (10, 60), (10, 61), (10, 62), (10, 63);

-- Lớp KDQT_N01 (id=11) - Sinh viên KT: SV051-SV065 (id=70-84)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(11, 70), (11, 71), (11, 72), (11, 73), (11, 74), (11, 75), (11, 76), (11, 77), (11, 78), (11, 79),
(11, 80), (11, 81), (11, 82), (11, 83), (11, 84);

-- Lớp KDQT_N02 (id=12) - Sinh viên KT: SV066-SV075 (id=85-94)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(12, 85), (12, 86), (12, 87), (12, 88), (12, 89), (12, 90), (12, 91), (12, 92), (12, 93), (12, 94);

-- Lớp TCDN_N01 (id=13) - Sinh viên KT: SV051-SV060 (id=70-79)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(13, 70), (13, 71), (13, 72), (13, 73), (13, 74), (13, 75), (13, 76), (13, 77), (13, 78), (13, 79);

-- Lớp NCTM_N01 (id=14) - Sinh viên KT: SV061-SV070 (id=80-89)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(14, 80), (14, 81), (14, 82), (14, 83), (14, 84), (14, 85), (14, 86), (14, 87), (14, 88), (14, 89);

-- Lớp MKT_N01 (id=15) - Sinh viên KT: SV071-SV075 (id=90-94)
INSERT INTO `enrollments` (`class_room_id`, `student_id`) VALUES
(15, 90), (15, 91), (15, 92), (15, 93), (15, 94);

-- ============================================
-- ĐIỂM DANH/CHUYÊN CẦN (ATTENDANCES) - MẪU
-- ============================================
-- Điểm danh mẫu cho một số sinh viên trong lớp LTC_N01 (enrollment_id: 1-15)
-- Buổi 1: 2024-09-05
INSERT INTO `attendances` (`enrollment_id`, `attendance_date`, `status`) VALUES
(1, '2024-09-05', 'present'), (2, '2024-09-05', 'present'), (3, '2024-09-05', 'late'),
(4, '2024-09-05', 'present'), (5, '2024-09-05', 'absent'), (6, '2024-09-05', 'present'),
(7, '2024-09-05', 'present'), (8, '2024-09-05', 'present'), (9, '2024-09-05', 'excused'),
(10, '2024-09-05', 'present'), (11, '2024-09-05', 'present'), (12, '2024-09-05', 'present'),
(13, '2024-09-05', 'late'), (14, '2024-09-05', 'present'), (15, '2024-09-05', 'present');

-- Buổi 2: 2024-09-12
INSERT INTO `attendances` (`enrollment_id`, `attendance_date`, `status`) VALUES
(1, '2024-09-12', 'present'), (2, '2024-09-12', 'present'), (3, '2024-09-12', 'present'),
(4, '2024-09-12', 'late'), (5, '2024-09-12', 'present'), (6, '2024-09-12', 'present'),
(7, '2024-09-12', 'absent'), (8, '2024-09-12', 'present'), (9, '2024-09-12', 'present'),
(10, '2024-09-12', 'present'), (11, '2024-09-12', 'present'), (12, '2024-09-12', 'late'),
(13, '2024-09-12', 'present'), (14, '2024-09-12', 'present'), (15, '2024-09-12', 'present');

-- Buổi 3: 2024-09-19
INSERT INTO `attendances` (`enrollment_id`, `attendance_date`, `status`) VALUES
(1, '2024-09-19', 'present'), (2, '2024-09-19', 'present'), (3, '2024-09-19', 'present'),
(4, '2024-09-19', 'present'), (5, '2024-09-19', 'present'), (6, '2024-09-19', 'late'),
(7, '2024-09-19', 'present'), (8, '2024-09-19', 'present'), (9, '2024-09-19', 'present'),
(10, '2024-09-19', 'absent'), (11, '2024-09-19', 'present'), (12, '2024-09-19', 'present'),
(13, '2024-09-19', 'present'), (14, '2024-09-19', 'present'), (15, '2024-09-19', 'late');

-- ============================================
-- ĐIỂM SỐ (SCORES) - MẪU
-- ============================================
-- Điểm mẫu cho một số sinh viên trong lớp LTC_N01 (enrollment_id: 1-15)
-- Công thức: Z = 0.2*(X1+X2+X3)/3 + 0.1*CC + 0.7*Y
-- GPA: Z >= 8.5 = 4.0 (A), 7.0-8.4 = 3.0 (B), 5.5-6.9 = 2.0 (C), < 5.5 = 1.0 (D)

-- Sinh viên 1 (enrollment_id=1): Điểm tốt - A
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(1, 8.5, 9.0, 8.5, 9.0, 9.5, 8.85, 4.0, 'A', 1, 4, NOW());

-- Sinh viên 2 (enrollment_id=2): Điểm khá - B
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(2, 7.5, 8.0, 7.0, 7.5, 8.0, 7.55, 3.0, 'B', 1, 4, NOW());

-- Sinh viên 3 (enrollment_id=3): Điểm trung bình - C
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(3, 6.0, 6.5, 5.5, 6.0, 7.0, 6.05, 2.0, 'C', 1, 4, NOW());

-- Sinh viên 4 (enrollment_id=4): Điểm yếu - D
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(4, 4.5, 5.0, 4.0, 5.0, 6.0, 4.85, 1.0, 'D', 0, 4, NOW());

-- Sinh viên 5 (enrollment_id=5): Mất tư cách (X1 < 4) - D
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(5, 3.5, 5.0, 4.5, 4.0, 5.0, 4.15, 1.0, 'D', 0, 4, NOW());

-- Điểm mẫu cho lớp Ngoại Ngữ - NHE1_N01 (enrollment_id: 51-65)
-- Sinh viên 6 (enrollment_id=51): Điểm tốt - A
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(51, 9.0, 8.5, 9.0, 8.5, 9.0, 8.75, 4.0, 'A', 1, 35, NOW());

-- Sinh viên 7 (enrollment_id=52): Điểm khá - B
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(52, 7.0, 7.5, 8.0, 7.0, 8.0, 7.25, 3.0, 'B', 1, 35, NOW());

-- Điểm mẫu cho lớp Kinh tế - KDQT_N01 (enrollment_id: 101-115)
-- Sinh viên 8 (enrollment_id=101): Điểm tốt - B
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(101, 8.0, 8.5, 8.0, 8.5, 9.0, 8.40, 3.0, 'B', 1, 66, NOW());

-- Sinh viên 9 (enrollment_id=102): Điểm khá - B
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(102, 7.5, 7.0, 7.5, 7.0, 8.0, 7.20, 3.0, 'B', 1, 66, NOW());

`y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(66, 9.0, 8.5, 9.0, 8.5, 9.0, 8.75, 4.0, 'A', 1, 35, NOW()),
(67, 7.0, 7.5, 7.0, 7.5, 8.0, 7.35, 3.0, 'B', 1, 35, NOW()),
(68, 8.5, 8.0, 8.5, 8.0, 8.5, 8.20, 3.0, 'B', 1, 35, NOW()),
(69, 6.5, 6.0, 6.5, 6.0, 6.5, 6.20, 2.0, 'C', 1, 35, NOW()),
(70, 7.5, 7.0, 7.5, 7.0, 7.5, 7.20, 3.0, 'B', 1, 35, NOW()),
(71, 8.0, 8.5, 8.0, 8.5, 9.0, 8.40, 4.0, 'A', 1, 35, NOW()),
(72, 5.5, 6.0, 5.5, 6.0, 6.5, 5.85, 2.0, 'C', 1, 35, NOW()),
(73, 9.5, 9.0, 9.5, 9.0, 9.5, 9.20, 4.0, 'A', 1, 35, NOW()),
(74, 6.0, 6.5, 6.0, 6.5, 7.0, 6.35, 2.0, 'C', 1, 35, NOW()),
(75, 4.5, 5.0, 4.5, 5.0, 5.5, 4.85, 1.0, 'D', 1, 35, NOW());

-- Điểm cho lớp NHE2_N01 (enrollment_id: 76-85)
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(76, 8.5, 9.0, 8.5, 9.0, 9.5, 8.85, 4.0, 'A', 1, 36, NOW()),
(77, 7.0, 7.5, 7.0, 7.5, 8.0, 7.35, 3.0, 'B', 1, 36, NOW()),
(78, 6.5, 7.0, 6.5, 7.0, 7.5, 6.85, 2.0, 'C', 1, 36, NOW()),
(79, 8.0, 8.5, 8.0, 8.5, 9.0, 8.40, 4.0, 'A', 1, 36, NOW()),
(80, 5.0, 5.5, 5.0, 5.5, 6.0, 5.35, 1.0, 'D', 1, 36, NOW()),
(81, 9.0, 9.5, 9.0, 9.5, 10.0, 9.40, 4.0, 'A', 1, 36, NOW()),
(82, 7.5, 8.0, 7.5, 8.0, 8.5, 7.85, 3.0, 'B', 1, 36, NOW()),
(83, 6.0, 6.5, 6.0, 6.5, 7.0, 6.35, 2.0, 'C', 1, 36, NOW()),
(84, 8.5, 8.0, 8.5, 8.0, 8.5, 8.20, 3.0, 'B', 1, 36, NOW()),
(85, 5.5, 6.0, 5.5, 6.0, 6.5, 5.85, 2.0, 'C', 1, 36, NOW());

-- Điểm cho lớp NOI1_N01 (enrollment_id: 86-95)
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(86, 9.0, 8.5, 9.0, 8.5, 9.0, 8.75, 4.0, 'A', 1, 37, NOW()),
(87, 7.5, 7.0, 7.5, 7.0, 7.5, 7.20, 3.0, 'B', 1, 37, NOW()),
(88, 8.0, 8.5, 8.0, 8.5, 9.0, 8.40, 4.0, 'A', 1, 37, NOW()),
(89, 6.5, 6.0, 6.5, 6.0, 6.5, 6.20, 2.0, 'C', 1, 37, NOW()),
(90, 7.0, 7.5, 7.0, 7.5, 8.0, 7.35, 3.0, 'B', 1, 37, NOW()),
(91, 8.5, 9.0, 8.5, 9.0, 9.5, 8.85, 4.0, 'A', 1, 37, NOW()),
(92, 5.5, 6.0, 5.5, 6.0, 6.5, 5.85, 2.0, 'C', 1, 37, NOW()),
(93, 9.5, 9.0, 9.5, 9.0, 9.5, 9.20, 4.0, 'A', 1, 37, NOW()),
(94, 6.0, 6.5, 6.0, 6.5, 7.0, 6.35, 2.0, 'C', 1, 37, NOW()),
(95, 4.0, 4.5, 4.0, 4.5, 5.0, 4.35, 1.0, 'D', 0, 37, NOW());

-- Điểm cho lớp VIE1_N01 (enrollment_id: 96-100)
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(96, 8.5, 9.0, 8.5, 9.0, 9.5, 8.85, 4.0, 'A', 1, 38, NOW()),
(97, 7.0, 7.5, 7.0, 7.5, 8.0, 7.35, 3.0, 'B', 1, 38, NOW()),
(98, 6.5, 7.0, 6.5, 7.0, 7.5, 6.85, 2.0, 'C', 1, 38, NOW()),
(99, 8.0, 8.5, 8.0, 8.5, 9.0, 8.40, 4.0, 'A', 1, 38, NOW()),
(100, 5.5, 6.0, 5.5, 6.0, 6.5, 5.85, 2.0, 'C', 1, 38, NOW());

-- Điểm cho các sinh viên còn lại trong lớp LTC_N01 (enrollment_id: 6-15)
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(6, 8.0, 8.5, 8.0, 8.5, 9.0, 8.40, 4.0, 'A', 1, 4, NOW()),
(7, 7.0, 7.5, 7.0, 7.5, 8.0, 7.35, 3.0, 'B', 1, 4, NOW()),
(8, 9.0, 9.5, 9.0, 9.5, 10.0, 9.40, 4.0, 'A', 1, 4, NOW()),
(9, 6.5, 7.0, 6.5, 7.0, 7.5, 6.85, 2.0, 'C', 1, 4, NOW()),
(10, 5.5, 6.0, 5.5, 6.0, 6.5, 5.85, 2.0, 'C', 1, 4, NOW());

INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(11, 8.5, 8.0, 8.5, 8.0, 8.5, 8.20, 3.0, 'B', 1, 4, NOW()),
(12, 7.5, 8.0, 7.5, 8.0, 8.5, 7.85, 3.0, 'B', 1, 4, NOW()),
(13, 6.0, 6.5, 6.0, 6.5, 7.0, 6.35, 2.0, 'C', 1, 4, NOW()),
(14, 9.5, 9.0, 9.5, 9.0, 9.5, 9.20, 4.0, 'A', 1, 4, NOW()),
(15, 7.0, 7.5, 7.0, 7.5, 8.0, 7.35, 3.0, 'B', 1, 4, NOW());

-- Điểm cho các sinh viên còn lại trong lớp KDQT_N01 (enrollment_id: 103-115)
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(103, 8.5, 8.0, 8.5, 8.0, 8.5, 8.20, 3.0, 'B', 1, 66, NOW()),
(104, 6.5, 7.0, 6.5, 7.0, 7.5, 6.85, 2.0, 'C', 1, 66, NOW()),
(105, 9.0, 9.5, 9.0, 9.5, 10.0, 9.40, 4.0, 'A', 1, 66, NOW()),
(106, 7.0, 7.5, 7.0, 7.5, 8.0, 7.35, 3.0, 'B', 1, 66, NOW()),
(107, 5.5, 6.0, 5.5, 6.0, 6.5, 5.85, 2.0, 'C', 1, 66, NOW()),
(108, 8.0, 8.5, 8.0, 8.5, 9.0, 8.40, 4.0, 'A', 1, 66, NOW()),
(109, 7.5, 8.0, 7.5, 8.0, 8.5, 7.85, 3.0, 'B', 1, 66, NOW()),
(110, 6.0, 6.5, 6.0, 6.5, 7.0, 6.35, 2.0, 'C', 1, 66, NOW()),
(111, 9.5, 9.0, 9.5, 9.0, 9.5, 9.20, 4.0, 'A', 1, 66, NOW()),
(112, 5.0, 5.5, 5.0, 5.5, 6.0, 5.35, 1.0, 'D', 1, 66, NOW()),
(113, 8.5, 9.0, 8.5, 9.0, 9.5, 8.85, 4.0, 'A', 1, 66, NOW()),
(114, 7.0, 7.5, 7.0, 7.5, 8.0, 7.35, 3.0, 'B', 1, 66, NOW()),
(115, 6.5, 7.0, 6.5, 7.0, 7.5, 6.85, 2.0, 'C', 1, 66, NOW());

-- Điểm cho lớp KDQT_N02 (enrollment_id: 116-125)
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(116, 9.0, 8.5, 9.0, 8.5, 9.0, 8.75, 4.0, 'A', 1, 66, NOW()),
(117, 7.5, 7.0, 7.5, 7.0, 7.5, 7.20, 3.0, 'B', 1, 66, NOW()),
(118, 8.0, 8.5, 8.0, 8.5, 9.0, 8.40, 4.0, 'A', 1, 66, NOW()),
(119, 6.5, 6.0, 6.5, 6.0, 6.5, 6.20, 2.0, 'C', 1, 66, NOW()),
(120, 7.0, 7.5, 7.0, 7.5, 8.0, 7.35, 3.0, 'B', 1, 66, NOW()),
(121, 8.5, 9.0, 8.5, 9.0, 9.5, 8.85, 4.0, 'A', 1, 66, NOW()),
(122, 5.5, 6.0, 5.5, 6.0, 6.5, 5.85, 2.0, 'C', 1, 66, NOW()),
(123, 9.5, 9.0, 9.5, 9.0, 9.5, 9.20, 4.0, 'A', 1, 66, NOW()),
(124, 6.0, 6.5, 6.0, 6.5, 7.0, 6.35, 2.0, 'C', 1, 66, NOW()),
(125, 4.5, 5.0, 4.5, 5.0, 5.5, 4.85, 1.0, 'D', 1, 66, NOW());

-- Điểm cho lớp TCDN_N01 (enrollment_id: 126-135)
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(126, 8.5, 9.0, 8.5, 9.0, 9.5, 8.85, 4.0, 'A', 1, 67, NOW()),
(127, 7.0, 7.5, 7.0, 7.5, 8.0, 7.35, 3.0, 'B', 1, 67, NOW()),
(128, 6.5, 7.0, 6.5, 7.0, 7.5, 6.85, 2.0, 'C', 1, 67, NOW()),
(129, 8.0, 8.5, 8.0, 8.5, 9.0, 8.40, 4.0, 'A', 1, 67, NOW()),
(130, 5.0, 5.5, 5.0, 5.5, 6.0, 5.35, 1.0, 'D', 1, 67, NOW()),
(131, 9.0, 9.5, 9.0, 9.5, 10.0, 9.40, 4.0, 'A', 1, 67, NOW()),
(132, 7.5, 8.0, 7.5, 8.0, 8.5, 7.85, 3.0, 'B', 1, 67, NOW()),
(133, 6.0, 6.5, 6.0, 6.5, 7.0, 6.35, 2.0, 'C', 1, 67, NOW()),
(134, 8.5, 8.0, 8.5, 8.0, 8.5, 8.20, 3.0, 'B', 1, 67, NOW()),
(135, 5.5, 6.0, 5.5, 6.0, 6.5, 5.85, 2.0, 'C', 1, 67, NOW());

-- Điểm cho lớp NCTM_N01 (enrollment_id: 136-145)
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(136, 9.0, 8.5, 9.0, 8.5, 9.0, 8.75, 4.0, 'A', 1, 68, NOW()),
(137, 7.5, 7.0, 7.5, 7.0, 7.5, 7.20, 3.0, 'B', 1, 68, NOW()),
(138, 8.0, 8.5, 8.0, 8.5, 9.0, 8.40, 4.0, 'A', 1, 68, NOW()),
(139, 6.5, 6.0, 6.5, 6.0, 6.5, 6.20, 2.0, 'C', 1, 68, NOW()),
(140, 7.0, 7.5, 7.0, 7.5, 8.0, 7.35, 3.0, 'B', 1, 68, NOW()),
(141, 8.5, 9.0, 8.5, 9.0, 9.5, 8.85, 4.0, 'A', 1, 68, NOW()),
(142, 5.5, 6.0, 5.5, 6.0, 6.5, 5.85, 2.0, 'C', 1, 68, NOW()),
(143, 9.5, 9.0, 9.5, 9.0, 9.5, 9.20, 4.0, 'A', 1, 68, NOW()),
(144, 6.0, 6.5, 6.0, 6.5, 7.0, 6.35, 2.0, 'C', 1, 68, NOW()),
(145, 4.0, 4.5, 4.0, 4.5, 5.0, 4.35, 1.0, 'D', 0, 68, NOW());

-- Điểm cho lớp MKT_N01 (enrollment_id: 146-150)
INSERT INTO `scores` (`enrollment_id`, `x1`, `x2`, `x3`, `y`, `cc`, `z`, `gpa`, `letter`, `is_qualified`, `input_by`, `input_at`) VALUES
(146, 8.5, 9.0, 8.5, 9.0, 9.5, 8.85, 4.0, 'A', 1, 69, NOW()),
(147, 7.0, 7.5, 7.0, 7.5, 8.0, 7.35, 3.0, 'B', 1, 69, NOW()),
(148, 6.5, 7.0, 6.5, 7.0, 7.5, 6.85, 2.0, 'C', 1, 69, NOW()),
(149, 8.0, 8.5, 8.0, 8.5, 9.0, 8.40, 4.0, 'A', 1, 69, NOW()),
(150, 5.5, 6.0, 5.5, 6.0, 6.5, 5.85, 2.0, 'C', 1, 69, NOW());

-- ============================================
-- CHỐT ĐIỂM (SCORE_LOCKS) - MẪU
-- ============================================
-- Chốt điểm cho một số lớp đã hoàn thành nhập điểm
-- Lớp LTC_N01 (class_room_id=1) - Đã chốt bởi giáo vụ CNTT (id=3)
INSERT INTO `score_locks` (`class_room_id`, `is_locked`, `locked_at`, `locked_by`, `notes`) VALUES
(1, 1, NOW(), 3, 'Đã chốt điểm lớp LTC_N01 - HK1 2024-2025');

-- Lớp LTJ_N01 (class_room_id=3) - Đã chốt bởi giáo vụ CNTT (id=3)
INSERT INTO `score_locks` (`class_room_id`, `is_locked`, `locked_at`, `locked_by`, `notes`) VALUES
(3, 1, NOW(), 3, 'Đã chốt điểm lớp LTJ_N01 - HK1 2024-2025');

-- Lớp NHE1_N01 (class_room_id=6) - Đã chốt bởi giáo vụ NN (id=34)
INSERT INTO `score_locks` (`class_room_id`, `is_locked`, `locked_at`, `locked_by`, `notes`) VALUES
(6, 1, NOW(), 34, 'Đã chốt điểm lớp NHE1_N01 - HK1 2024-2025');

-- Lớp KDQT_N01 (class_room_id=11) - Đã chốt bởi giáo vụ KT (id=65)
INSERT INTO `score_locks` (`class_room_id`, `is_locked`, `locked_at`, `locked_by`, `notes`) VALUES
(11, 1, NOW(), 65, 'Đã chốt điểm lớp KDQT_N01 - HK1 2024-2025');

-- Cập nhật trạng thái is_locked trong class_rooms
UPDATE `class_rooms` SET `is_locked` = 1, `locked_at` = NOW(), `locked_by` = 3 WHERE `id` IN (1, 3);
UPDATE `class_rooms` SET `is_locked` = 1, `locked_at` = NOW(), `locked_by` = 34 WHERE `id` = 6;
UPDATE `class_rooms` SET `is_locked` = 1, `locked_at` = NOW(), `locked_by` = 65 WHERE `id` = 11;

-- ============================================
-- QUY TẮC HỌC BỔNG (SCHOLARSHIP_RULES) - MẪU
-- ============================================
-- Quy tắc học bổng toàn trường
INSERT INTO `scholarship_rules` (`faculty_id`, `scholarship_type`, `min_gpa`, `min_credits`, `is_active`) VALUES
(NULL, 'Xuất sắc', 3.50, 15, 1),
(NULL, 'Giỏi', 3.20, 15, 1),
(NULL, 'Khá', 2.50, 12, 1);

-- Quy tắc học bổng theo khoa CNTT
INSERT INTO `scholarship_rules` (`faculty_id`, `scholarship_type`, `min_gpa`, `min_credits`, `is_active`) VALUES
(1, 'Xuất sắc CNTT', 3.60, 15, 1),
(1, 'Giỏi CNTT', 3.30, 15, 1);

-- Quy tắc học bổng theo khoa Ngoại Ngữ
INSERT INTO `scholarship_rules` (`faculty_id`, `scholarship_type`, `min_gpa`, `min_credits`, `is_active`) VALUES
(2, 'Xuất sắc NN', 3.55, 15, 1),
(2, 'Giỏi NN', 3.25, 15, 1);

-- Quy tắc học bổng theo khoa Kinh tế
INSERT INTO `scholarship_rules` (`faculty_id`, `scholarship_type`, `min_gpa`, `min_credits`, `is_active`) VALUES
(3, 'Xuất sắc KT', 3.58, 15, 1),
(3, 'Giỏi KT', 3.28, 15, 1);

-- ============================================
-- GHI CHÚ QUAN TRỌNG
-- ============================================
-- 1. Password mặc định: 123456 (SHA2-256)
-- 2. Foreign Keys: ON DELETE RESTRICT/CASCADE tùy nghiệp vụ
-- 3. Indexes: Đã tối ưu cho các truy vấn thường dùng
-- 4. Charset: UTF8MB4 hỗ trợ đầy đủ tiếng Việt
-- 5. Engine: InnoDB hỗ trợ transactions và foreign keys
-- 6. InfinityFree: Tương thích MySQL 5.7+ và 8.0
-- 7. Các truy vấn phức tạp sẽ được xử lý trong PHP files
-- ============================================


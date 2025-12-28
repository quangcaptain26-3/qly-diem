<?php
$role = Auth::role();
$currentPath = $_GET['route'] ?? '/';
require_once __DIR__ . '/../../helpers/url_helper.php';

// Extract the primary segment for better active state matching
$pathSegments = explode('/', trim(strtok($currentPath, '?'), '/'));
$primarySegment = $pathSegments[1] ?? ''; // e.g., 'dashboard', 'classes', 'attendance'
$fullPath = strtok($currentPath, '?'); // Full path without query string
?>

<div class="nav flex-column">
    <?php if ($role === 'root'): ?>
        <a class="nav-link <?= ($primarySegment === 'dashboard' && count($pathSegments) <= 2) ? 'active' : '' ?>" href="<?= url('/root/dashboard') ?>">
            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'students') ? 'active' : '' ?>" href="<?= url('/root/students') ?>">
            <i class="bi bi-people-fill"></i> <span>Quản lý Học sinh</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'lecturers') ? 'active' : '' ?>" href="<?= url('/root/lecturers') ?>">
            <i class="bi bi-person-workspace"></i> <span>Quản lý Giảng viên</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'system') ? 'active' : '' ?>" href="<?= url('/root/system') ?>">
            <i class="bi bi-gear"></i> <span>Quản lý Hệ thống</span>
        </a>

    <?php elseif ($role === 'dean'): ?>
        <a class="nav-link <?= ($primarySegment === 'dashboard' && count($pathSegments) <= 2) ? 'active' : '' ?>" href="<?= url('/dean/dashboard') ?>">
            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'report') ? 'active' : '' ?>" href="<?= url('/dean/report') ?>">
            <i class="bi bi-file-earmark-text"></i> <span>Báo cáo</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'scholarship') ? 'active' : '' ?>" href="<?= url('/dean/scholarship') ?>">
            <i class="bi bi-award"></i> <span>Học bổng</span>
        </a>

    <?php elseif ($role === 'academic_affairs'): ?>
        <a class="nav-link <?= ($primarySegment === 'dashboard' && count($pathSegments) <= 2) ? 'active' : '' ?>" href="<?= url('/academic/dashboard') ?>">
            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'students') ? 'active' : '' ?>" href="<?= url('/academic/students') ?>">
            <i class="bi bi-search"></i> <span>Tra cứu Sinh viên</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'scores') ? 'active' : '' ?>" href="<?= url('/academic/scores') ?>">
            <i class="bi bi-clipboard-data"></i> <span>Tổng hợp Điểm</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'warning') ? 'active' : '' ?>" href="<?= url('/academic/warning') ?>">
            <i class="bi bi-exclamation-triangle"></i> <span>Cảnh báo</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'scholarship') ? 'active' : '' ?>" href="<?= url('/academic/scholarship') ?>">
            <i class="bi bi-award"></i> <span>Học bổng</span>
        </a>

    <?php elseif ($role === 'lecturer'): ?>
        <?php 
            // Get current class context if available
            $currentClassId = $_GET['class_id'] ?? $_GET['id'] ?? null;
            $classParam = $currentClassId ? '?class_id=' . $currentClassId : '';
        ?>
        <a class="nav-link <?= ($primarySegment === 'dashboard' && count($pathSegments) <= 2) ? 'active' : '' ?>" href="<?= url('/lecturer/dashboard') ?>">
            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'classes' || $primarySegment === 'class') ? 'active' : '' ?>" href="<?= url('/lecturer/classes') ?>">
            <i class="bi bi-journal-text"></i> <span>Lớp học</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'scores' || $primarySegment === 'import-score') ? 'active' : '' ?>" href="<?= url('/lecturer/scores' . $classParam) ?>">
            <i class="bi bi-pencil-square"></i> <span>Nhập Điểm</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'attendance') ? 'active' : '' ?>" href="<?= url('/lecturer/attendance' . $classParam) ?>">
            <i class="bi bi-calendar-check"></i> <span>Điểm danh</span>
        </a>

    <?php elseif ($role === 'student'): ?>
        <a class="nav-link <?= ($primarySegment === 'dashboard' && count($pathSegments) <= 2) ? 'active' : '' ?>" href="<?= url('/student/dashboard') ?>">
            <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
        </a>
        <a class="nav-link <?= ($primarySegment === 'scores') ? 'active' : '' ?>" href="<?= url('/student/scores') ?>">
            <i class="bi bi-clipboard-check"></i> <span>Xem Điểm</span>
        </a>
    <?php endif; ?>
</div>

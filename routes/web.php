<?php
/**
 * Web Routes
 */

// Auth routes
Router::get('/', function() {
    if (Auth::check()) {
        header('Location: ' . Router::url('/dashboard'));
    } else {
        header('Location: ' . Router::url('/login'));
    }
    exit;
});

Router::get('/login', 'LoginController@show');
Router::post('/login', 'LoginController@login');
Router::get('/logout', 'LogoutController@logout');
Router::get('/forgot-password', 'ResetPasswordController@show');
Router::post('/forgot-password', 'ResetPasswordController@request');
Router::get('/reset-password', 'ResetPasswordController@reset');
Router::post('/reset-password', 'ResetPasswordController@reset');

// Dashboard
Router::get('/dashboard', 'DashboardController@index');

// Root routes
Router::get('/root/dashboard', 'RootDashboardController@dashboard');
Router::get('/root/api/stats', 'RootDashboardController@apiStats');
Router::get('/root/api/locks', 'RootDashboardController@apiLocks');
Router::post('/root/api/unlock', 'RootDashboardController@apiUnlock');
Router::get('/root/students', 'StudentManagementController@index');
Router::get('/root/students/detail', 'StudentManagementController@detail');
Router::post('/root/students/api', 'StudentManagementController@apiList');
Router::get('/root/lecturers', 'LecturerManagementController@index');
Router::post('/root/lecturers/api', 'LecturerManagementController@apiList');
Router::get('/root/system', 'SystemController@resetPasswords');
Router::post('/root/system', 'SystemController@resetPasswords');

// Dean routes
Router::get('/dean/dashboard', 'DeanDashboardController@dashboard');
Router::get('/dean/report', 'FacultyReportController@index');
Router::get('/dean/report/export', 'FacultyReportController@export');
Router::get('/dean/scholarship', 'ScholarshipController@index');

// Academic Affairs routes
Router::get('/academic/dashboard', 'AcademicDashboardController@dashboard');
Router::get('/academic/api/stats', 'AcademicDashboardController@apiStats');
Router::get('/academic/students', 'AcademicStudentController@index');
Router::get('/academic/students/detail', 'AcademicStudentController@detail');
Router::post('/academic/students/api', 'AcademicStudentController@apiList');
Router::get('/academic/scores', 'ScoreSummaryController@index');
Router::post('/academic/scores/api/classes', 'ScoreSummaryController@apiListClasses');
Router::get('/academic/scores/api/detail', 'ScoreSummaryController@apiClassScores');
Router::get('/academic/scores/export', 'ScoreSummaryController@export');
Router::post('/academic/scores/lock', 'ScoreSummaryController@lock');
Router::get('/academic/warning', 'WarningController@index');
Router::post('/academic/warning/api', 'WarningController@apiList');
Router::get('/academic/scholarship', 'AcademicScholarshipController@index'); // New route

// Lecturer routes
Router::get('/lecturer/dashboard', 'ClassController@dashboard');
Router::get('/lecturer/classes', 'ClassController@index');
Router::get('/lecturer/class', 'ClassController@show');
Router::get('/lecturer/scores', 'ScoreInputController@show');
Router::post('/lecturer/scores/save', 'ScoreInputController@save');
Router::get('/lecturer/attendance', 'AttendanceController@show');
Router::post('/lecturer/attendance/save', 'AttendanceController@save');
Router::get('/lecturer/import-score', 'ImportScoreController@show');
Router::get('/lecturer/import-score/template', 'ImportScoreController@downloadTemplate');
Router::post('/lecturer/import-score', 'ImportScoreController@import');

// Student routes
Router::get('/student/dashboard', 'StudentDashboardController@dashboard');
Router::get('/student/scores', 'StudentScoreController@index');


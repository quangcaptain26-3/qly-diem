<?php
/**
 * URL Helper Functions
 */

if (!function_exists('url')) {
    function url($path = '') {
        require_once __DIR__ . '/../core/Router.php';
        return Router::url($path);
    }
}

if (!function_exists('route')) {
    function route($path) {
        return url($path);
    }
}


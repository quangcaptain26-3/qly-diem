<?php
/**
 * Auth Helper Functions
 */

if (!function_exists('auth_check')) {
    function auth_check() {
        return Auth::check();
    }
}

if (!function_exists('auth_user')) {
    function auth_user() {
        return Auth::user();
    }
}

if (!function_exists('auth_role')) {
    function auth_role() {
        return Auth::role();
    }
}

if (!function_exists('auth_name')) {
    function auth_name() {
        return Auth::name();
    }
}


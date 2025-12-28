<?php
/**
 * Entry Point
 */

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/config/app.php';

// Load core classes
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/Router.php';

// Initialize Auth
Auth::start();

// Load helpers
require_once __DIR__ . '/helpers/url_helper.php';

// Load routes
require_once __DIR__ . '/routes/web.php';

// Dispatch
Router::dispatch();


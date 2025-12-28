<?php
/**
 * Simple Router - Không cần .htaccess
 */

class Router {
    private static $routes = [];

    public static function get($path, $handler) {
        self::$routes['GET'][$path] = $handler;
    }

    public static function post($path, $handler) {
        self::$routes['POST'][$path] = $handler;
    }

    public static function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Lấy route từ query string hoặc PATH_INFO
        $uri = self::getUri();
        $uri = rtrim($uri, '/') ?: '/';

        if (isset(self::$routes[$method][$uri])) {
            $handler = self::$routes[$method][$uri];
            
            if (is_string($handler)) {
                list($controllerName, $methodName) = explode('@', $handler);
                
                // Auto-load controller
                $controllerFile = self::findControllerFile($controllerName, $uri);
                if ($controllerFile && file_exists($controllerFile)) {
                    require_once $controllerFile;
                }
                
                if (class_exists($controllerName)) {
                    $controller = new $controllerName();
                    if (method_exists($controller, $methodName)) {
                        return $controller->$methodName();
                    }
                }
            } elseif (is_callable($handler)) {
                return call_user_func($handler);
            }
        }

        http_response_code(404);
        echo "<h1>404 - Page not found</h1>";
        echo "<p>URI: " . htmlspecialchars($uri) . "</p>";
        echo "<p>Method: " . htmlspecialchars($method) . "</p>";
        echo "<p><a href='?route=/'>← Về trang chủ</a></p>";
        die();
    }
    
    private static function getUri() {
        // Ưu tiên query string ?route=...
        if (isset($_GET['route'])) {
            return '/' . ltrim($_GET['route'], '/');
        }
        
        // Nếu có PATH_INFO (khi chạy với .htaccess hoặc server config)
        if (isset($_SERVER['PATH_INFO'])) {
            return $_SERVER['PATH_INFO'];
        }
        
        // Fallback: parse từ REQUEST_URI
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove script name nếu chạy trong subdirectory
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && strpos($uri, $scriptName) === 0) {
            $uri = substr($uri, strlen($scriptName));
        }
        
        return $uri;
    }
    
    private static function findControllerFile($controllerName, $uri) {
        $basePath = __DIR__ . '/../controllers/';
        
        // Try direct path first
        $directPath = $basePath . $controllerName . '.php';
        if (file_exists($directPath)) {
            return $directPath;
        }
        
        // Try based on URI path
        $uriParts = explode('/', trim($uri, '/'));
        if (count($uriParts) > 0 && $uriParts[0] !== '') {
            $subDir = $uriParts[0];
            $subPath = $basePath . $subDir . '/' . $controllerName . '.php';
            if (file_exists($subPath)) {
                return $subPath;
            }
        }
        
        // Try common subdirectories
        $subDirs = ['auth', 'root', 'dean', 'academicAffairs', 'lecturer', 'student'];
        foreach ($subDirs as $subDir) {
            $subPath = $basePath . $subDir . '/' . $controllerName . '.php';
            if (file_exists($subPath)) {
                return $subPath;
            }
        }
        
        return null;
    }
    
    public static function url($path, $fullUrl = false) {
        // Generate URL với query string
        $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
        if ($baseUrl === '/' || $baseUrl === '\\' || $baseUrl === '.') {
            $baseUrl = '';
        } else {
            $baseUrl = rtrim(str_replace('\\', '/', $baseUrl), '/');
        }
        
        // Xử lý query string nếu có trong path
        $queryString = '';
        if (strpos($path, '?') !== false) {
            list($path, $query) = explode('?', $path, 2);
            $queryString = '&' . $query;
        }
        
        $route = ltrim($path, '/');
        $relativeUrl = $baseUrl . '/?route=' . $route . $queryString;
        
        // Nếu cần full URL (với domain)
        if ($fullUrl) {
            $config = require __DIR__ . '/../config/app.php';
            $siteUrl = $config['site_url'] ?? '';
            if ($siteUrl) {
                return rtrim($siteUrl, '/') . $relativeUrl;
            }
            // Fallback: tự động detect
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            return $protocol . $host . $relativeUrl;
        }
        
        return $relativeUrl;
    }
}

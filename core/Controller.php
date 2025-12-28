<?php
/**
 * Base Controller Class
 */

class Controller {
    protected function view($view, $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            die("View not found: {$view}");
        }
        
        require $viewFile;
    }

    protected function json($data, $statusCode = 200) {
        // Clear any previous output (e.g. notices, warnings)
        if (ob_get_length()) ob_clean();
        
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url) {
        // Nếu URL không có ?route=, thêm vào
        if (strpos($url, '?route=') === false && strpos($url, 'http') !== 0) {
            $url = Router::url($url);
        }
        header("Location: {$url}");
        exit;
    }

    protected function back() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $ruleArray = explode('|', $rule);
            
            foreach ($ruleArray as $r) {
                if ($r === 'required' && empty($data[$field])) {
                    $errors[$field] = "Trường {$field} là bắt buộc";
                } elseif ($r === 'email' && !filter_var($data[$field] ?? '', FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Email không hợp lệ";
                } elseif (strpos($r, 'min:') === 0) {
                    $min = (int)substr($r, 4);
                    if (strlen($data[$field] ?? '') < $min) {
                        $errors[$field] = "{$field} phải có ít nhất {$min} ký tự";
                    }
                }
            }
        }
        
        return $errors;
    }
}


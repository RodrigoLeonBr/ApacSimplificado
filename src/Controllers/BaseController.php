<?php

namespace App\Controllers;

use App\Database\Database;

abstract class BaseController
{
    protected $db;
    
    public function __construct($db = null)
    {
        $this->db = $db ?? Database::getInstance();
    }
    
    protected function render($view, array $data = [])
    {
        extract($data);
        
        $viewPath = __DIR__ . '/../../views/' . str_replace('.', '/', $view) . '.php';
        
        if (!file_exists($viewPath)) {
            http_response_code(404);
            die("View not found: {$view}");
        }
        
        ob_start();
        require $viewPath;
        $content = ob_get_clean();
        
        echo $content;
    }
    
    protected function redirect($url)
    {
        if (!headers_sent()) {
            header("Location: {$url}");
            exit;
        }
        
        echo "<script>window.location.href='{$url}';</script>";
        exit;
    }
    
    protected function jsonResponse(array $data, $status = 200)
    {
        if (!headers_sent()) {
            http_response_code($status);
            header('Content-Type: application/json');
        }
        
        echo json_encode($data);
        exit;
    }
    
    protected function flash($message, $type = 'success')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['flash'] = [
            'message' => $message,
            'type' => $type
        ];
    }
    
    protected function getFlash()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        
        return null;
    }
    
    protected function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    protected function getMethod()
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }
    
    protected function getInput($key = null, $default = null)
    {
        if ($this->getMethod() === 'POST') {
            $data = $_POST;
        } else {
            $data = $_GET;
        }
        
        if ($key === null) {
            return $data;
        }
        
        return $data[$key] ?? $default;
    }
}

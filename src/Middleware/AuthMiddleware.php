<?php

namespace App\Middleware;

use App\Models\Usuario;
use App\Utils\Router;

class AuthMiddleware
{
    private static $user = null;
    
    public static function handle()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            if (self::isAjaxRequest()) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode([
                    'error' => 'Não autenticado',
                    'message' => 'Você precisa estar logado para acessar este recurso'
                ]);
                exit;
            }
            
            Router::redirect('/login');
        }
        
        return true;
    }
    
    public static function getLoggedInUser()
    {
        if (self::$user !== null) {
            return self::$user;
        }
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        
        $usuarioModel = new Usuario();
        self::$user = $usuarioModel->findById($_SESSION['user_id']);
        
        return self::$user;
    }
    
    public static function getUserId()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function isAuthenticated()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']);
    }
    
    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_unset();
        session_destroy();
        self::$user = null;
    }
    
    private static function isAjaxRequest()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    public static function checkPermission($role)
    {
        $user = self::getLoggedInUser();
        
        if (!$user) {
            return false;
        }
        
        if (is_array($role)) {
            return in_array($user['role'], $role);
        }
        
        return $user['role'] === $role;
    }
}

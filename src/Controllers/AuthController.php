<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Utils\Router;
use App\Utils\Session;
use App\Utils\Validation;

class AuthController
{
    private $authService;
    
    public function __construct()
    {
        $this->authService = new AuthService();
    }
    
    public function showLogin()
    {
        if ($this->authService->check()) {
            Router::redirect('/dashboard');
        }
        
        require VIEWS_PATH . '/auth/login.php';
    }
    
    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $errors = Validation::errors($_POST, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $_POST);
            Router::redirect('/login');
        }
        
        $result = $this->authService->login($email, $password);
        
        if ($result['success']) {
            Session::flash('success', $result['message']);
            Router::redirect('/dashboard');
        } else {
            Session::flash('error', $result['message']);
            Session::flash('old', $_POST);
            Router::redirect('/login');
        }
    }
    
    public function logout()
    {
        $this->authService->logout();
        Session::flash('success', 'Logout realizado com sucesso.');
        Router::redirect('/login');
    }
}

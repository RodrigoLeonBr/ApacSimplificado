<?php

namespace App\Services;

use App\Database\Database;
use App\Utils\Session;
use App\Models\Usuario;

class AuthService
{
    private $usuarioModel;
    
    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }
    
    public function login($email, $password)
    {
        $usuario = $this->usuarioModel->findByEmail($email);
        
        if (!$usuario) {
            return ['success' => false, 'message' => 'Credenciais inválidas.'];
        }
        
        if (!$usuario['ativo']) {
            return ['success' => false, 'message' => 'Usuário inativo.'];
        }
        
        if (!password_verify($password, $usuario['senha_hash'])) {
            return ['success' => false, 'message' => 'Credenciais inválidas.'];
        }
        
        Session::set('user_id', $usuario['id']);
        Session::set('user_email', $usuario['email']);
        Session::set('user_nome', $usuario['nome']);
        Session::set('user_role', $usuario['role']);
        Session::set('authenticated', true);
        Session::regenerate();
        
        return ['success' => true, 'message' => 'Login realizado com sucesso.'];
    }
    
    public function logout()
    {
        Session::destroy();
    }
    
    public function check()
    {
        return Session::get('authenticated', false) === true;
    }
    
    public function user()
    {
        if (!$this->check()) {
            return null;
        }
        
        return [
            'id' => Session::get('user_id'),
            'email' => Session::get('user_email'),
            'nome' => Session::get('user_nome'),
            'role' => Session::get('user_role'),
        ];
    }
    
    public function guard()
    {
        if (!$this->check()) {
            Session::flash('error', 'Você precisa estar autenticado para acessar esta página.');
            header('Location: /login');
            exit;
        }
    }
    
    public function isAdmin()
    {
        $user = $this->user();
        return $user && $user['role'] === 'admin';
    }
}

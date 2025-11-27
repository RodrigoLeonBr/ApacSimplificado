<?php

namespace App\Utils;

class Router
{
    private $routes = [];
    private $currentRoute = null;
    private $basePath = '';
    
    public function __construct()
    {
        $this->basePath = $this->detectBasePath();
    }
    
    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
        return $this;
    }
    
    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
        return $this;
    }
    
    private function addRoute($method, $path, $callback)
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'callback' => $callback
        ];
    }
    
    /**
     * Detecta automaticamente o base path do projeto
     * Considera se está em subdiretório (ex: /ApacSimplificado/)
     */
    private function detectBasePath()
    {
        if (defined('BASE_URL')) {
            return BASE_URL;
        }
        
        // Fallback: detectar base path automaticamente
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $scriptDir = dirname($scriptName);
        
        // Se estiver em subdiretório (ex: /ApacSimplificado/public/index.php)
        if (strpos($scriptDir, '/public') !== false) {
            $scriptDir = str_replace('/public', '', $scriptDir);
        }
        
        // Normalizar
        $basePath = rtrim(str_replace('\\', '/', $scriptDir), '/');
        
        if ($basePath === '' || $basePath === '/') {
            return '';
        }
        
        return '/' . ltrim($basePath, '/');
    }
    
    public function dispatch()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove o base path do REQUEST_URI antes de fazer match
        if ($this->basePath && strpos($requestUri, $this->basePath) === 0) {
            $requestUri = substr($requestUri, strlen($this->basePath));
        }
        
        // Remove /public/ do início do REQUEST_URI se presente
        // Isso é necessário porque quando acessamos /ApacSimplificado/public/, 
        // após remover o base path, fica /public/
        if (strpos($requestUri, '/public/') === 0) {
            $requestUri = substr($requestUri, 7); // Remove '/public'
        } elseif ($requestUri === '/public') {
            $requestUri = '/';
        }
        
        // Garante que começa com /
        if ($requestUri === '') {
            $requestUri = '/';
        } elseif ($requestUri[0] !== '/') {
            $requestUri = '/' . $requestUri;
        }
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = $this->convertToRegex($route['path']);
                if (preg_match($pattern, $requestUri, $matches)) {
                    array_shift($matches);
                    $this->currentRoute = $route;
                    return call_user_func_array($route['callback'], $matches);
                }
            }
        }
        
        http_response_code(404);
        echo "404 - Página não encontrada";
    }
    
    private function convertToRegex($path)
    {
        // Aceita caracteres alfanuméricos, underscore, hífen e caracteres URL-encoded (para chaves base64)
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_%+-]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    public static function redirect($path, $statusCode = 302)
    {
        $basePath = defined('BASE_URL') ? BASE_URL : '';
        
        // Se o path já começa com http:// ou https://, usa diretamente
        if (preg_match('/^https?:\/\//', $path)) {
            header("Location: $path", true, $statusCode);
            exit;
        }
        
        // Se o path já contém /public/, não adiciona novamente
        if (strpos($path, '/public/') !== false) {
            // Já tem /public/, apenas adiciona base path se necessário
            if ($basePath && strpos($path, $basePath) !== 0) {
                $path = rtrim($basePath, '/') . '/' . ltrim($path, '/');
            }
            header("Location: $path", true, $statusCode);
            exit;
        }
        
        // Adiciona /public/ ao path se não estiver presente
        // Remove barra inicial do path para normalizar
        $path = ltrim($path, '/');
        
        // Constrói a URL completa: basePath + /public/ + path
        if ($basePath) {
            $path = rtrim($basePath, '/') . '/public/' . $path;
        } else {
            $path = '/public/' . $path;
        }
        
        header("Location: $path", true, $statusCode);
        exit;
    }
    
    public static function url($path)
    {
        $basePath = defined('BASE_URL') ? BASE_URL : '';
        
        // Se o path já começa com http:// ou https://, retorna diretamente
        if (preg_match('/^https?:\/\//', $path)) {
            return $path;
        }
        
        // Se o path já contém /public/, não adiciona novamente
        if (strpos($path, '/public/') !== false) {
            // Já tem /public/, apenas adiciona base path se necessário
            if ($basePath && strpos($path, $basePath) !== 0) {
                return rtrim($basePath, '/') . '/' . ltrim($path, '/');
            }
            return $path;
        }
        
        // Adiciona /public/ ao path se não estiver presente
        // Remove barra inicial do path para normalizar
        $path = ltrim($path, '/');
        
        // Constrói a URL completa: basePath + /public/ + path
        if ($basePath) {
            return rtrim($basePath, '/') . '/public/' . $path;
        } else {
            return '/public/' . $path;
        }
    }
    
    public function getBasePath()
    {
        return $this->basePath;
    }
}

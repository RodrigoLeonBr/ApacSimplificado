<?php

namespace App\Utils;

class Router
{
    private $routes = [];
    private $currentRoute = null;
    
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
    
    public function dispatch()
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
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
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    public static function redirect($path, $statusCode = 302)
    {
        header("Location: $path", true, $statusCode);
        exit;
    }
    
    public static function url($path)
    {
        return $path;
    }
}

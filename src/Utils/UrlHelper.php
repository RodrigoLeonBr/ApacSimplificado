<?php

namespace App\Utils;

class UrlHelper
{
    /**
     * Gera uma URL completa considerando o base path
     * 
     * @param string $path Caminho relativo (ex: '/pacientes', 'pacientes/create')
     * @return string URL completa com base path
     */
    public static function url($path = '')
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
        
        // Remove barra inicial se existir para normalizar
        $path = ltrim($path, '/');
        
        // Adiciona /public/ ao path se não estiver presente
        // Constrói a URL completa: basePath + /public/ + path
        if ($basePath) {
            return rtrim($basePath, '/') . '/public/' . $path;
        } else {
            return '/public/' . $path;
        }
    }
    
    /**
     * Gera URL para assets estáticos (CSS, JS, imagens)
     * 
     * @param string $path Caminho do asset relativo a public/
     * @return string URL completa do asset
     */
    public static function asset($path)
    {
        $basePath = defined('BASE_URL') ? BASE_URL : '';
        $path = ltrim($path, '/');
        
        // Assets sempre começam com /public/ ou apenas o nome do arquivo
        if (strpos($path, 'public/') === 0) {
            $path = substr($path, 7); // Remove 'public/'
        }
        
        if (empty($basePath)) {
            return '/public/' . $path;
        }
        
        return rtrim($basePath, '/') . '/public/' . $path;
    }
    
    /**
     * Gera URL para uma rota específica
     * 
     * @param string $route Nome da rota ou path
     * @param array $params Parâmetros para substituir na rota
     * @return string URL completa
     */
    public static function route($route, $params = [])
    {
        // Por enquanto, apenas chama url() já que não temos sistema de nomes de rotas
        // Pode ser expandido no futuro para usar um sistema de rotas nomeadas
        return self::url($route);
    }
    
    /**
     * Retorna o base path atual
     * 
     * @return string Base path (ex: '/ApacSimplificado' ou '')
     */
    public static function basePath()
    {
        return defined('BASE_URL') ? BASE_URL : '';
    }
    
    /**
     * Retorna a URL base completa (protocolo + host + base path)
     * 
     * @return string URL base completa
     */
    public static function baseUrl()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = self::basePath();
        
        return $protocol . '://' . $host . $basePath;
    }
}


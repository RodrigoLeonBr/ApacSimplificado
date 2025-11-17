<?php

namespace App\Utils;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            $config = require __DIR__ . '/../../config/app.php';
            session_name($config['session_name']);
            session_start();
        }
    }
    
    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key)
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    public static function flash($key, $message)
    {
        self::start();
        $_SESSION['_flash'][$key] = $message;
    }
    
    public static function getFlash($key, $default = null)
    {
        self::start();
        $message = $_SESSION['_flash'][$key] ?? $default;
        if (isset($_SESSION['_flash'][$key])) {
            unset($_SESSION['_flash'][$key]);
        }
        return $message;
    }
    
    public static function hasFlash($key)
    {
        self::start();
        return isset($_SESSION['_flash'][$key]);
    }
    
    public static function destroy()
    {
        self::start();
        session_unset();
        session_destroy();
    }
    
    public static function regenerate()
    {
        self::start();
        session_regenerate_id(true);
    }
}

<?php
namespace app\lib;

class Session
{
    public static function setSession($sessionName, $sessionValue)
    {
        if(isset($_SESSION[$sessionName])) {
            self::removeSession($sessionName);
        }
        $_SESSION[$sessionName] = $sessionValue;
    }


    public static function removeSession($sessionName)
    {
        if(isset($_SESSION[$sessionName])) {
            unset($_SESSION[$sessionName]);
        }
    }


    public static function isSet(string $key)
    {
        return $_SESSION[$key] ?? false;
    }


    public static function getFlash(string $key)
    {
        $message = $_SESSION[$key] ?? false;
        if($message) {
            unset($_SESSION[$key]);
        }
        return $message;
    }
}
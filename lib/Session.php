<?php
namespace app\lib;

class Session
{
    public function __construct()
    {
        // 세션 아이디가 있다면 아이디 반환 없으면 false 반환
        if(!session_id()) {
            session_start();
        }
    }


    public function setSession($sessionName, $sessionValue)
    {
        if(!session_id()) {
            session_start();
        }
        if(isset($_SESSION["$sessionName"])) {
            $this->removeSession($sessionName);
        }
        $_SESSION["$sessionName"] = $sessionValue;
    }


    public function removeSession($sessionName)
    {
        if(!session_id()) {
            session_start();
        }
        if(isset($_SESSION["$sessionName"])) {
            unset($_SESSION["$sessionName"]);
        }
    }


    public static function isSet(string $key)
    {
        if(!session_id()) {
            session_start();
        }
        return $_SESSION[$key] ?? false;
    }


    public static function getFlash(string $key)
    {
        if(!session_id()) {
            session_start();
        }
        $message = $_SESSION[$key] ?? false;
        if($message) {
            unset($_SESSION[$key]);
        }
        return $message;
    }


}
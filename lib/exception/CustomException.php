<?php
namespace app\lib\exception;

use app\lib\Session;

class CustomException extends \Exception
{
    // 예외처리 전용 클래스
    public function setErrorMessages($e) {
        Session::setSession('error', $e->getMessage().$e->getLine());
    }
}
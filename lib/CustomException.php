<?php
namespace app\lib;

class CustomException extends \Exception
{
    // 예외처리 전용 클래스
    public function setErrorMessage($msg) {
        (new Session)->setSession('error', $msg);
    }


}
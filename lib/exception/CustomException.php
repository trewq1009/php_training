<?php
namespace app\lib\exception;

class CustomException extends \Exception
{
    // 예외처리 전용 클래스
    public function setErrorMessages($e) {
        $message = $e->getMessage();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/view/error/error.php';
        die();
    }
}
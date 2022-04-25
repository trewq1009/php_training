<?php
namespace app\lib\exception;

use app\lib\Session;

class DatabaseException extends \Exception
{
    public function setErrorMessages($e) {
        $message = $e->getMessage();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/view/error/error.php';
        die();
    }
}
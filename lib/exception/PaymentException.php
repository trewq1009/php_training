<?php
namespace app\lib\exception;

use app\lib\Session;

class PaymentException extends \Exception
{
    public function setErrorMessages($e)
    {
        $message = $e->getMessage();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/view/error/error_prv.php';
        die();
    }
}
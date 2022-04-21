<?php
namespace app\lib\exception;

use app\lib\Session;

class PaymentException extends \Exception
{
    public function setErrorMessages($e)
    {
        Session::setSession('error', $e->getMessage().$e->getLine());
    }
}
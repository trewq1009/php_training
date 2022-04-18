<?php
namespace app\lib\exception;

use app\lib\Session;

class DatabaseException extends \Exception
{
    public function setErrorMessages($e) {
        Session::setSession('error', $e->getMessage().$e->getLine());
    }
}
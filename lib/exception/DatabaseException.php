<?php
namespace app\lib\exception;

use app\lib\Session;

class DatabaseException extends \Exception
{
    public function setErrorMessages($e) {
        (new Session)->setSession('error', $e->getMessage().$e->getLine());
    }
}
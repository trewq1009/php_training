<?php

namespace app\lib\exception;

use app\lib\Session;

class InputDataNullException extends \Exception
{
    public function setErrorMessages($msg) {
        (new Session)->setSession('error', $msg);
    }
}
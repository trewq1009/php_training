<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use app\lib\Session;
use app\lib\exception\CustomException;

try {
    if(!$auth) {
        throw new CustomException('잘못된 경로 입니다.');
    }
    Session::removeSession('auth');
    header('Location: /');

} catch(CustomException $e) {
    $e->setErrorMessages($e);
}

?>
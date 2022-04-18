<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\User;

if(strtolower($_SERVER['REQUEST_METHOD']) == 'get')  {
    (new User)->emailAuthentication($_GET);
}

?>



<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/header.php';

use app\lib\User;

$method = (new app\lib\Utils)->getMethod($_SERVER);

if($method == 'get') {
    (new User)->emailAuthentication($_GET);
}

?>



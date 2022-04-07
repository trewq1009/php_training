<?php
require_once __DIR__ . '/../vendor/autoload.php';

use app\lib\User;


$method = (new app\lib\Utils)->getMethod($_SERVER);

    if($method == 'get') {
        (new User)->emailAuthentication($_GET);
    }

?>



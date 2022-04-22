<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Session;

?>

<section>
    <?php if(Session::isSet('success')): ?>
        <div class="alert alert-success">
            <?php echo (Session::getFlash('success') ?>
        </div>
    <?php elseif(Session::isSet('error')): ?>
        <div class="alert alert-danger">
            <?php echo Session::getFlash('error') ?>
        </div>
    <?php endif; ?>


</section>
</body>
</html>

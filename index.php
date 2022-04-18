<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Session;
use app\lib\Database;

if($auth) {
    $mileage = (new Database)->findOne('tr_mileage', ['user_no'=>$auth['no']]);
}

?>

    <section class="container">
        <?php if((new Session)->isSet('success')): ?>
            <div class="alert alert-success">
                <?php echo (new Session)->getFlash('success') ?>
            </div>
        <?php elseif((new Session)->isSet('error')): ?>
            <div class="alert alert-danger">
                <?php echo (new Session)->getFlash('error') ?>
            </div>
        <?php endif; ?>

        <div>
            <h3>Hello PHP</h3>
        </div>
        <?php if($auth): ?>
        <div>
            <h5>유저 : <?php echo $auth['name'] ?></h5>
            <h5>사용 중인 마일리지 : <?php echo $mileage['using_mileage'] ?></h5>
            <h5>사용 가능 마일리지 : <?php echo $mileage['use_mileage'] ?></h5>
            <h5>출금 가능 마일리지 : <?php echo $mileage['real_mileage'] ?></h5>
        </div>
        <?php endif ?>
    </section>


    <!-- 추후 파일화 해서 footer 관리 -->
    <footer>

    </footer>
</body>
</html>

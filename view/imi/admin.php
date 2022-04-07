<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/imi/head.php';

use app\lib\Session;


/* 헤더 레이아웃 */
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/imi/header.php'
?>


    <section class="container">
        <?php if(Session::isSet('success')): ?>
            <div class="alert alert-success">
                <?php echo Session::getFlash('success') ?>
            </div>
        <?php elseif(Session::isSet('error')): ?>
            <div class="alert alert-danger">
                <?php echo Session::getFlash('error') ?>
            </div>
        <?php endif; ?>
        <div>
            <h3>Hello ADMIN</h3>
        </div>
    </section>


    <!-- 추후 파일화 해서 footer 관리 -->
    <footer>

    </footer>
    </body>
</html>


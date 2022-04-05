<?php
require_once __DIR__.'/layout/head.php';
use app\lib\Session;

?>
<body>
    <!-- 추후 파일화 해서 header 관리 -->
    <?php require_once __DIR__.'/layout/header.php'?>

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
            <h3>Hello PHP</h3>
        </div>
    </section>


    <!-- 추후 파일화 해서 footer 관리 -->
    <footer>

    </footer>
</body>
</html>

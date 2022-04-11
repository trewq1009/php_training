<?php
require_once __DIR__.'/layout/head.php';
require_once __DIR__.'/layout/header.php';

use app\lib\Session;

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
    </section>


    <!-- 추후 파일화 해서 footer 관리 -->
    <footer>

    </footer>
</body>
</html>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

?>

<section class="container">

    <div class="alert alert-danger">
        <?php echo $message ?>
    </div>
    <a href="<?php echo $preUrl ?>" class="btn btn-secondary">이전</a>
    <a href="/" class="btn btn-secondary">홈</a>

</section>
</body>
</html>

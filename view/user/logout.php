<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Session;

try {
    if(!$auth) {
        throw new Exception('잘못된 경로 입니다.');
    }
    Session::removeSession('auth');
    header('Location: /');

} catch(Exception $e) {
    $message = $e->getMessage();
}

?>
<section class="container">

<div class="alert alert-danger">
    <?php echo $message ?>
</div>
<a href="/" class="btn btn-secondary">홈</a>

</section>
</body>
</html>
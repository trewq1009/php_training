<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/imi/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/imi/header.php';

use app\lib\Board;
use app\lib\Session;

if(strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
    $board = new Board;
    if(isset($_GET['viewUser'])) {
        $url = sprintf('/view/admin/user_detail.php?user=%s', $_GET['viewUser']);
        header("Location: $url");
    } else {
        $board->listUp($_SERVER['REQUEST_URI'], $_GET);
    }
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

        <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method="get">
            <?php echo $board->listHtml ?>
        </form>
        <?php echo $board->listBtn ?>
    </section>
</body>
</html>

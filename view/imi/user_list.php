<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use app\lib\Utils;
use app\lib\Board;
use app\lib\Session;

$method = (new Utils)->getMethod($_SERVER);
if($method == 'get') {
    $board = new Board;

    if(isset($_GET['viewUser'])) {
        $url = sprintf('/view/imi/user_detail.php?user=%s', $_GET['viewUser']);
        header("Location: $url");
    } else {
        $board->listUp($_SERVER['REQUEST_URI'], $_GET);
    }
}

require_once $_SERVER['DOCUMENT_ROOT'].'/layout/imi/head.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/imi/header.php';
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

        <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method="get">
            <?php echo $board->listHtml ?>
        </form>
        <?php echo $board->listBtn ?>
    </section>
</body>
</html>

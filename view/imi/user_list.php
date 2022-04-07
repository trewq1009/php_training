<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use app\lib\Utils;
use app\lib\Board;

$method = (new Utils)->getMethod($_SERVER);
if($method == 'get') {
    $board = new Board;
    if($_GET['viewUser']) {
        $board->search($_GET);
    } else {
        $bool = $board->listUp($_SERVER['REQUEST_URI'], $_GET);
    }
}





require_once $_SERVER['DOCUMENT_ROOT'].'/layout/imi/head.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/imi/header.php';
?>
    <section class="container">
        <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method="get">
            <?php echo $board->listHtml ?>
        </form>
        <?php echo $board->listBtn ?>
    </section>
</body>
</html>

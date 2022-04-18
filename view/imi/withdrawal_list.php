<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/imi/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/imi/header.php';

use app\lib\Session;
use app\lib\Board;

if(strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
    if(isset($_GET['viewDetail'])) {
        $url = sprintf('/view/imi/withdrawal_detail.php?info=%s', $_GET['viewDetail']);
        header("Location: $url");
        exit();
    }

    $list = (new Board)->withdrawalList();
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
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">NAME</th>
                <th scope="col">ID</th>
                <th scope="col">TOTAL MOUNT</th>
                <th scope="col">STATUS</th>
                <th scope="col">REQ_AT</th>
                <th scope="col">VIEW</th>
            </tr>
            </thead>
            <tbody>
            <?php if($list): ?>
                <?php foreach ($list as $item): ?>
                <tr>
                    <th scope="row"><?php echo $item['user_no'] ?></th>
                    <td><?php echo $item['name'] ?></td>
                    <td><?php echo $item['id'] ?></td>
                    <td><?php echo $item['use_mileage'] ?></td>
                    <td><?php echo $item['status'] ?></td>
                    <td><?php echo $item['requested_at'] ?></td>
                    <td>
                        <button type="submit" name="viewDetail" value="<?php echo $item['no'] ?>" class="btn btn-outline-info">Info</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">데이터가 존재하지 않습니다.</td>
                </tr>
            <?php endif ?>
            </tbody>
        </table>
    </form>
</section>
</body>
</html>


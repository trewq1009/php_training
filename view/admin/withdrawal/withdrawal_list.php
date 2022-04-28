<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/header.php';

use app\lib\Session;
use app\lib\Database;

try {
    $withdrawalList = (new Database)->findAll('tr_withdrawal_log', ['status'=>'await']);
    foreach ($withdrawalList as $key => $value) {
        $userModel = (new Database)->findOne('tr_account', ['no'=>$value['user_no']], 'i');
        $withdrawalList[$key]['name'] = $userModel['name'];
        $withdrawalList[$key]['id'] = $userModel['id'];
        $withdrawalList[$key]['status'] = '출금신청';
    }

} catch (Exception $e) {
    echo $e->getMessage();
}
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

    <form action='<?php echo htmlspecialchars('./withdrawal_detail.php');?>' method="get">
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
            <?php if($withdrawalList): ?>
                <?php foreach ($withdrawalList as $item): ?>
                <tr>
                    <th scope="row"><?php echo $item['user_no'] ?></th>
                    <td><?php echo $item['name'] ?></td>
                    <td><?php echo $item['id'] ?></td>
                    <td><?php echo $item['withdrawal_mileage'] ?></td>
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


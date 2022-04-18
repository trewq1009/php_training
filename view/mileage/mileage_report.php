<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Session;
use app\lib\Database;

try {
    if(empty($_GET['no'])) {
        throw new Exception('잘못된 경로 입니다.');
    }
    $list = (new Database)->findAll('tr_mileage_log', ['user_no'], ['user_no' => $_GET['no']]);

} catch (Exception $e) {
    Session::setSession('error', $e->getMessage());
    header('Location: /');
}

?>
<section class="container">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Method</th>
                <th scope="col">Before Mileage</th>
                <th scope="col">Use Mileage</th>
                <th scope="col">After Mileage</th>
                <th scope="col">Registration Date</th>
                <th scope="col">#</th>
            </tr>
            </thead>
            <tbody>
            <?php if($list): ?>
                <?php foreach ($list as $key => $item): ?>
                    <tr>
                        <th scope="row"><?php echo $key + 1 ?></th>
                        <td><?php echo $item['method'] ?></td>
                        <td><?php echo $item['before_mileage'] ?></td>
                        <td><?php echo $item['use_mileage'] ?></td>
                        <td><?php echo $item['after_mileage'] ?></td>
                        <td><?php echo $item['registration_date'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">데이터가 존재하지 않습니다.</td>
                </tr>
            <?php endif ?>
            </tbody>
        </table>
</section>
</body>
</html>
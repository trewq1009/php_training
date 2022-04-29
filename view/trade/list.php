<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\Field;

try {
    $page = $_GET['page'] ?? 1;

    $db = new Database;
    $listData = $db->list('tr_trade_board', $page, []);

    if($listData) {
        $boardList = $listData['listData'];
        unset($listData['listData']);
        $listData['page'] = $page;
        $listBtn = Field::listBtn($listData);

        foreach ($boardList as $key => $value) {
            $productData = $db->findOne('tr_product', ['no'=>$value['reference_no']], 'i');
            $boardList[$key]['productName'] = $productData['name'];
            $boardList[$key]['price'] = $productData['before_price'];
            $tradeLogData = $db->findOne('tr_trade_log', ['trade_board_no'=>$value['no']], 'i');
            $boardList[$key]['tradeStatus'] = $tradeLogData['status'] ?? false;
        }
    }

} catch (Exception $e) {
    $message = $e->getMessage();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/view/error/error.php';
    die();
}

?>
<section class="container">

    <div style="margin: 1rem 0">
        <a class="btn btn-outline-primary" href="registration.php">거래등록</a>
        <a class="btn btn-outline-info" href="trade_list.php">거래내역</a>
    </div>

    <div class="list-group">
        <?php if($boardList): ?>
            <?php foreach ($boardList as $item) { ?>
                <a href="<?php echo htmlspecialchars("./detail.php?boardNo={$item['no']}");?>" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?php echo $item['title'] ?></h5>
                        <span><?php echo $item['price'] ?> 원</span>
                    </div>
                    <div class="d-flex w-100 justify-content-between">
                        <p class="mb-1"><?php echo $item['productName'] ?></p>
                        <?php if($item['tradeStatus']): ?>
                            <?php if($item['tradeStatus'] == 'ongoing'): ?>
                                <span>거래중</span>
                            <?php elseif($item['tradeStatus'] == 'success') : ?>
                                <span>거래 완료</span>
                            <?php endif ?>
                        <?php endif ?>
                    </div>
                    <div class="d-flex w-100 justify-content-between">
                        <small><?php echo $item['content'] ?></small>
                        <small><?php echo $item['registration_date'] ?></small>
                    </div>
                </a>
            <?php } ?>
        <?php else : ?>
            <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">거래중인 유저가 없습니다.</h5>
                </div>
            </a>
        <?php endif ?>
    </div>
    <div>
        <?php echo $listBtn ?>
    </div>

</section>
</body>
</html>
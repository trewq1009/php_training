<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\Session;

try {
    if(!$auth) {
        throw new Exception('로그인 후 이용해 주세요.');
    }

    $db = new Database;
    $listData = $db->findOr('tr_trade_log', ['seller_no'=>$auth['no'], 'buyer_no'=>$auth['no']]);
    foreach ($listData as $key => $value) {
        $productData = $db->findOne('tr_product', ['no'=>$value['trade_product_no']]);
        $listData[$key]['productName'] = $productData['name'];
        if($value['seller_no'] == $auth['no']) {
            $listData[$key]['tradeName'] = '판매';
            $listData[$key]['traderStatus'] = $value['buyer_trade_status'];
            $listData[$key]['userStatus'] = $value['seller_trade_status'];
        } else {
            $listData[$key]['tradeName'] = '구매';
            $listData[$key]['traderStatus'] = $value['seller_trade_status'];
            $listData[$key]['userStatus'] = $value['buyer_trade_status'];
        }
    }

} catch (Exception $e) {
    Session::setSession('error', $e->getMessage());
    header('Location: /');
}

?>

<section class="container">
    <?php if(Session::isSet('success')): ?>
        <div class="alert alert-success">
            <?php echo Session::getFlash('success') ?>
        </div>
    <?php elseif((new Session)->isSet('error')): ?>
        <div class="alert alert-danger">
            <?php echo Session::getFlash('error') ?>
        </div>
    <?php endif; ?>

    <form action='<?php echo htmlspecialchars('./trade_action.php');?>' method="post" id="formAction">
        <input type="hidden" id="tradeNo" name="tradeNo" value="">
        <input type="hidden" id="tradeType" name="tradeType" value="">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">TRADE 종류</th>
                <th scope="col">상품명</th>
                <th scope="col">가격</th>
                <th scope="col">상대상태</th>
                <th scope="col">나의거래상태</th>
                <th scope="col">최종거래상태</th>
                <th scope="col">#</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($listData as $item): ?>
            <tr>
                <td><?php echo $item['tradeName'] ?></td>
                <td><?php echo $item['productName'] ?></td>
                <td><?php echo $item['trade_price'] ?></td>
                <td><?php echo $item['traderStatus'] ?></td>
                <td><?php echo $item['userStatus'] ?></td>
                <td><?php echo $item['status'] ?></td>
                <td>
                    <button type="button" onclick="tradeSuccess(this)" value="<?php echo $item['no'] ?>" data-trade="<?php echo $item['tradeName'] ?>" name="tradeNo" class="btn btn-outline-info">거래 완료</button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</section>
</body>
<script>
    function tradeSuccess(event) {
        const tradeName = event.dataset.trade;
        const resultConfirm = window.confirm(tradeName + '를 완료 하시겠습니까?');
        if(!resultConfirm) return;

        event.dataset.trade === '판매' ? document.querySelector('#tradeType').value = 'seller' : document.querySelector('#tradeType').value = 'buyer';
        document.querySelector('#tradeNo').value = event.value;
        document.querySelector('#formAction').submit();
    }
</script>
</html>
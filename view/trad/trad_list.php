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
    $listData = $db->findOr('tr_trad_log', ['seller_no'=>$auth['no'], 'buyer_no'=>$auth['no']]);
    foreach ($listData as $key => $value) {
        $productData = $db->findOne('tr_product', ['no'=>$value['trad_product_no']]);
        $listData[$key]['productName'] = $productData['name'];
        if($value['seller_no'] == $auth['no']) {
            $listData[$key]['tradName'] = '판매';
            $listData[$key]['traderStatus'] = $value['buyer_trad_status'];
        } else {
            $listData[$key]['tradName'] = '구매';
            $listData[$key]['traderStatus'] = $value['seller_trad_status'];
        }
    }

} catch (Exception $e) {
    Session::setSession('error', $e->getMessage());
    header('Location: /');
}

?>

<section class="container">
    <form action='<?php echo htmlspecialchars('./trad_action.php');?>' method="post" id="formAction">
        <input type="hidden" id="tradNo" name="tradNo" value="">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">TRAD 종류</th>
                <th scope="col">상품명</th>
                <th scope="col">가격</th>
                <th scope="col">상대상태</th>
                <th scope="col">최종거래상태</th>
                <th scope="col">#</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($listData as $item): ?>
            <tr>
                <td><?php echo $item['tradName'] ?></td>
                <td><?php echo $item['productName'] ?></td>
                <td><?php echo $item['trad_price'] ?></td>
                <td><?php echo $item['traderStatus'] ?></td>
                <td><?php echo $item['status'] ?></td>
                <td>
                    <button type="button" onclick="tradSuccess(this)" value="<?php echo $item['no'] ?>" data-trad="<?php echo $item['tradName'] ?>" name="tradNo" class="btn btn-outline-info">거래 완료</button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </form>
</section>
</body>
<script>
    function tradSuccess(event) {
        const tradName = event.dataset.trad;
        const resultConfirm = window.confirm(tradName + '를 완료 하시겠습니까?');
        if(!resultConfirm) return;

        document.querySelector('#tradNo').value = event.value;
        document.querySelector('#formAction').submit();
    }
</script>
</html>
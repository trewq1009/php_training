<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;

try {
    if(empty($_GET['boardNo'])) {
        throw new Exception('잘못된 경로 입니다.');
    }
    $db = new Database;
    $boardData = $db->findOne('tr_board', ['no'=>$_GET['boardNo']], 'i');
    $productData = $db->findOne('tr_product', ['no'=>$boardData['reference_no']], 'i');
    $imageData = $db->findOne('tr_image', ['no'=>$productData['image_no']], 'i');
    $tradeLogData = $db->findOne('tr_trade_log', ['trade_board_no'=>$boardData['no']], 'i');

} catch (Exception $e) {
    $message = $e->getMessage();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/view/error/error.php';
    die();
}

?>
<section class="container">

    <form style="margin: 3rem 0 0 0" action="<?php echo htmlspecialchars('./detail_action.php');?>" method="post">
        <input type="hidden" name="boardNo" value="<?php echo $boardData['no'] ?>">
        <input type="hidden" name="seller" value="<?php echo $boardData['user_no'] ?>">
        <input type="hidden" name="productNo" value="<?php echo $productData['no'] ?>">
        <input type="hidden" name="price" value="<?php echo $productData['before_price'] ?>">
        <?php if($tradeLogData): ?>
            <?php if($tradeLogData['status'] == 'ongoing') : ?>
                <div style="margin: 0 0 1rem 0">
                    <button type="button" class="btn btn-primary">거래중</button>
                </div>
            <?php elseif($tradeLogData['status'] == 'success') : ?>
                <div style="margin: 0 0 1rem 0">
                    <button type="button" class="btn btn-primary">거래 완료</button>
                </div>
            <?php endif ?>
        <?php else : ?>
            <div style="margin: 0 0 1rem 0">
                <button type="submit" class="btn btn-primary">거래 신청</button>
            </div>
        <?php endif ?>
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">제목</span>
            <p class="form-control" style="margin: 0"><?php echo $boardData['title'] ?></p>
            <span class="input-group-text" id="basic-addon1">등록일</span>
            <p class="form-control" style="margin: 0"><?php echo $boardData['registration_date'] ?></p>
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">판매 상품</span>
            <p class="form-control" style="margin: 0"><?php echo $productData['name'] ?></p>
        </div>

        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">가격</span>
            <p class="form-control" style="margin: 0"><?php echo $productData['before_price'] ?></p>
        </div>

        <div class="input-group">
            <span class="input-group-text">상품 이미지</span>
            <div class="text-center">
                <img src="../..<?php echo $imageData['image_path'].$imageData['image_name'] ?>" class="img-thumbnail" >
            </div>
        </div>
        
        <div class="input-group">
            <span class="input-group-text">상품 설명</span>
            <p class="form-control" style="margin: 0"><?php echo nl2br($boardData['content']) ?></p>
        </div>
    </form>

</section>
</body>
</html>

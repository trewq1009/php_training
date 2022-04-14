<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/header.php';

use app\lib\Session;
use app\lib\Payment;

if(!$auth) {
    (new Session)->setSession('error', '잘못된 경로 입니다.');
    header('Location: /');
}
if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    (new Payment)->cashWithdrawal($_POST);
} else {
    $use_mileage = (new Payment)->getMileageInfo($auth['no']);
}

?>
<section class="container">

    <?php if((new Session)->isSet('error')): ?>
        <div class="alert alert-danger">
            <?php echo (new Session)->getFlash('error') ?>
        </div>
    <?php endif; ?>


    <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method="post" id="methodForm">
        <input type="hidden" name="userNo" value="<?php echo $auth['no'] ?>">
        <div class="mb-3">
            <label for="userMileage" class="form-label">보유 마일리지</label>
            <input type="text" class="form-control" value="<?php echo $auth['mileage'] ?>" name="userMileage" id="userMileage" readonly required>
        </div>
        <div class="mb-3">
            <label for="trueMileage" class="form-label">출금 가능 마일리지</label>
            <input type="text" class="form-control" value="<?php echo $auth['mileage'] - $use_mileage ?>" name="trueMileage" id="trueMileage" readonly required>
        </div>
        <div class="mb-3">
            <label for="drawalMileage" class="form-label">출금할 마일리지</label>
            <input type="text" class="form-control" name="drawalMileage" id="drawalMileage" required>
        </div>
        <div class="mb-3">
            <div class="form-check">
                <label for="flexRadioDefault1" class="form-label">신한</label>
                <input class="form-check-input" type="radio" value="신한" name="bankValue" id="flexRadioDefault1" required>
            </div>
            <div class="form-check">
                <label for="flexRadioDefault2" class="form-label">기업</label>
                <input class="form-check-input" type="radio" value="기업" name="bankValue" id="flexRadioDefault2" required>
            </div>
            <div class="form-check">
                <label for="flexRadioDefault2" class="form-label">농협</label>
                <input class="form-check-input" type="radio" value="농협" name="bankValue" id="flexRadioDefault2" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="bankNumber" class="form-label">계좌번호</label>
            <input type="text" class="form-control" name="bankNumber" id="bankNumber" required>
        </div>

        <button type="submit" class="btn btn-primary">출금신청</button>
    </form>


</section>
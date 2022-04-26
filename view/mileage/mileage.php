<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Session;
use app\lib\Utils;
try {
    if(!$auth) {
        throw new Exception('로그인 후 이용해 주세요.');
    }

} catch(Exceptioin $e) {
    $message = $e->getMessage();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/view/error/error.php';
    die();
}


?>
<section class="container">

    <form action='<?php echo htmlspecialchars('./mileage_payment.php');?>' method="get" id="methodForm">
    <div style="display: flex; align-items: center; justify-content: center;">
        <span style="font-size: 3rem; color: rgba(13,110,153,1);">충전</span>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">금액</label>
        <input type="number" class="form-control" name="price" id="price" required>
    </div>

    <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div class="form-check">
                <label class="form-check-label" for="flexRadioDefault1">
                    신용카드
                </label>
                <input class="form-check-input" type="radio" value="credit" name="radioValue" id="flexRadioDefault1" required>
            </div>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div class="form-check">
                <label class="form-check-label" for="flexRadioDefault2">
                    휴대전화
                </label>
                <input class="form-check-input" type="radio" value="phone" name="radioValue" id="flexRadioDefault2" required>
            </div>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center">
            <div class="form-check">
                <label class="form-check-label" for="flexRadioDefault3">
                    상품권
                </label>
                <input class="form-check-input" type="radio" value="voucher" name="radioValue" id="flexRadioDefault3" required>
            </div>
        </li>
    </ul>
    <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</section>

</body>
</html>

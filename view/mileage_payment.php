<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/header.php';

use app\lib\Session;
use app\lib\Payment;

$paymentModel = '';
$preUrl = $_SERVER['HTTP_REFERER'];
if(strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
    $paymentModel = $_GET;
    if($paymentModel['price'] < 1000) {
        (new Session)->setSession('error', '최소 금액은 1000원 입니다.');
        header("Location: $preUrl");
        exit();
    }
} else {
    $paymentModel = $_POST;
    (new Payment)->cardPayment($_POST);
}

?>

<?php if($paymentModel['radioValue'] == 'credit'): ?>
<section class="container">
    <h1>카드 결재</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="methodForm">
        <input type="hidden" value="<?php echo $paymentModel['radioValue'] ?>" name="radioValue">
        <input type="hidden" value="<?php echo $paymentModel['price'] ?>" name="price">
        <input type="hidden" value="<?php echo $preUrl ?>" name="preUrl">

        <div class="mb-3">
            <label for="cardNumber1" class="form-label">Card Number</label>
            <div style="display: flex;">
                <input type="text" class="form-control" value="" name="cardNumber[]" id="creditNumber1" maxlength="4" required>
                <input type="password" class="form-control" value="" name="cardNumber[]" id="creditNumber2" maxlength="4" required>
                <input type="password" class="form-control" value="" name="cardNumber[]" id="creditNumber3" maxlength="4" required>
                <input type="text" class="form-control" value="" name="cardNumber[]" id="creditNumber4" maxlength="4" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="cardYear" class="form-label">Valid Month/Year</label>
            <div style="display: flex;">
                <input type="text" class="form-control" name="cardYear" id="cardYear" maxlength="2">
                <input type="text" class="form-control" name="cardMonth" id="cardMonth" maxlength="2">
            </div>
        </div>
        <div class="mb-3">
            <label for="cardCVC" class="form-label">CVC</label>
            <input type="password" class="form-control" name="cardCVC" id="cardCVC" maxlength="3" >
        </div>
        <div class="mb-3">
            <label for="cardPassword" class="form-label">Password</label>
            <input type="password" class="form-control" name="cardPassword" id="cardPassword" maxlength="4" >
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

<?php elseif($paymentModel['radioValue'] == 'phone'): ?>
    <h1>휴대폰</h1>
<?php elseif($paymentModel['radioValue'] == 'voucher'): ?>
    <h1>상품권</h1>
<?php endif; ?>

</section>
</body>
</html>

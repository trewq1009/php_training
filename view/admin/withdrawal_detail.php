<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/imi/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/imi/header.php';

use app\lib\Database;
use app\lib\Session;
use app\lib\Utils;
use app\lib\Payment;

if(strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
    if(empty($_GET['info'])) {
        (new Session)->setSession('error', '잘못된 경로 입니다.');
        $prvUrl = $_SERVER['HTTP_REFERER'];
        header("Location: $prvUrl");
        exit();
    }
    $withdrawalData = (new Database)->findOne('tr_mileage_use_log', ['no'], ['no' => $_GET['info']]);
    $userData = (new Database)->findOne('tr_account', ['no'], ['no' => $withdrawalData['user_no']]);
    $bankInfo = json_decode($withdrawalData['log_information']);
    $bankInfo->account_decrypt = (new Utils)->decrypt($bankInfo->bank_account_number);
} else {
    (new Payment)->withdrawalSuccess($_POST);
}

?>
<section class="container">
    <h1>출금신청 상세 내역</h1>
    <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method="post" id="methodForm">
        <input type="hidden" name="userNo" value="<?php echo $userData['no'] ?>" readonly>
        <input type="hidden" name="logNo" value="<?php echo $withdrawalData['no'] ?>" readonly>
        <div class="mb-3">
            <label for="userId" class="form-label">ID</label>
            <input type="text" class="form-control" value="<?php echo $userData['id'] ?>" name="userId" id="userId" readonly required>
        </div>
        <div class="mb-3">
            <label for="userName" class="form-label">Name</label>
            <input type="text" class="form-control" value="<?php echo $userData['name'] ?>" name="userName" id="userName" readonly required>
        </div>
        <div class="mb-3">
            <label for="userMileage" class="form-label">Mileage</label>
            <input type="text" class="form-control" value="<?php echo $userData['mileage'] ?>" name="userMileage" id="userMileage" readonly required>
        </div>
        <div class="mb-3">
            <label for="userWithdrawalAmount" class="form-label">Total Amount</label>
            <input type="text" class="form-control" value="<?php echo $withdrawalData['use_mileage'] ?>" name="userWithdrawalAmount" id="userWithdrawalAmount" readonly required>
        </div>
        <div class="mb-3">
            <label for="userRequested" class="form-label">Requested AT</label>
            <input type="text" class="form-control" value="<?php echo $withdrawalData['requested_at'] ?>" name="userRequested" id="userRequested" readonly required>
        </div>
        <div class="mb-3">
            <label for="userBankName" class="form-label">Bank</label>
            <input type="text" class="form-control" value="<?php echo $bankInfo->bank ?>" name="userBankName" id="userBankName" readonly required>
        </div>
        <div class="mb-3">
            <label for="userBankAccountNumber" class="form-label">Bank Account Number</label>
            <input type="text" class="form-control" value="<?php echo $bankInfo->account_decrypt ?>" name="userBankAccountNumber" id="userBankAccountNumber" readonly required>
        </div>
        <a class="btn btn-info" href="<?php echo $_SERVER['HTTP_REFERER'] ?>">이전페이지</a>
        <button type="submit" name="action" value="update" class="btn btn-primary">출금완료</button>
    </form>



</section>
</body>
</html>

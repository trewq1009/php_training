<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/header.php';

use app\lib\Database;
use app\lib\Session;
use app\lib\Utils;

try {
    $prvUrl = $_SERVER['HTTP_REFERER'];
    if(empty($_GET['viewDetail'])) {
        throw new Exception('잘못된 경로입니다.');
    }

    $db = new Database;
    $withdrawalData = $db->findOne('tr_withdrawal_log', ['no'=>$_GET['viewDetail']]);
    $userModel = $db->findOne('tr_account', ['no'=>$withdrawalData['user_no']]);
    $userMileageData = $db->findOne('tr_mileage', ['user_no'=>$userModel['no']]);

    $bankInfo = Utils::decrypt($withdrawalData['bank_account_number']);

} catch (Exception $e) {
    Session::setSession('error', $e->getMessage());
    header("Location: $prvUrl");
}

?>
<section class="container">
    <h1>출금신청 상세 내역</h1>
    <form action='<?php echo htmlspecialchars('./withdrawal_action.php');?>' method="post" id="methodForm">
        <input type="hidden" name="userNo" value="<?php echo $userModel['no'] ?>" readonly>
        <input type="hidden" name="logNo" value="<?php echo $withdrawalData['no'] ?>" readonly>
        <div class="mb-3">
            <label for="userId" class="form-label">ID</label>
            <input type="text" class="form-control" value="<?php echo $userModel['id'] ?>" name="userId" id="userId" readonly required>
        </div>
        <div class="mb-3">
            <label for="userName" class="form-label">Name</label>
            <input type="text" class="form-control" value="<?php echo $userModel['name'] ?>" name="userName" id="userName" readonly required>
        </div>
        <div class="mb-3">
            <label for="userMileage" class="form-label">Use Mileage</label>
            <input type="text" class="form-control" value="<?php echo $userMileageData['use_mileage'] ?>" name="userMileage" id="userMileage" readonly required>
        </div>
        <div class="mb-3">
            <label for="userWithdrawalAmount" class="form-label">Total Amount</label>
            <input type="text" class="form-control" value="<?php echo $withdrawalData['withdrawal_mileage'] ?>" name="userWithdrawalAmount" id="userWithdrawalAmount" readonly required>
        </div>
        <div class="mb-3">
            <label for="userRequested" class="form-label">Requested AT</label>
            <input type="text" class="form-control" value="<?php echo $withdrawalData['requested_at'] ?>" name="userRequested" id="userRequested" readonly required>
        </div>
        <div class="mb-3">
            <label for="userBankName" class="form-label">Bank</label>
            <input type="text" class="form-control" value="<?php echo $withdrawalData['bank_name'] ?>" name="userBankName" id="userBankName" readonly required>
        </div>
        <div class="mb-3">
            <label for="userBankAccountNumber" class="form-label">Bank Account Number</label>
            <input type="text" class="form-control" value="<?php echo $bankInfo ?>" name="userBankAccountNumber" id="userBankAccountNumber" readonly required>
        </div>
        <a class="btn btn-info" href="<?php echo $_SERVER['HTTP_REFERER'] ?>">이전페이지</a>
        <button type="submit" class="btn btn-primary">출금완료</button>
    </form>

</section>
</body>
</html>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\Utils;
use app\lib\exception\CustomException;
use app\lib\exception\DatabaseException;

try {
    $preUrl = $_SERVER['HTTP_REFERER'];

    if(!$auth) {
        throw new CustomException('로그인 정보가 없습니다.');
    }

    if(!isset($_POST['usingMileage']) || empty($_POST['useMileage']) || empty($_POST['realMileage']) || empty($_POST['drawalMileage']) || empty($_POST['bankValue']) || empty($_POST['bankNumber'])) {
        throw new Exception('입력 데이터를 다시 확인해 주세요.');
    }
    if($_POST['realMileage'] < 1000 || $_POST['drawalMileage'] < 1000) {
        throw new Exception('출금 가능한 최소 금액이 안됩니다.');
    }
    if($_POST['realMileage'] < $_POST['drawalMileage']) {
        throw new Exception('출금 가능 마일리지를 넘었습니다.');
    }
    if (!preg_match("/^[0-9]/i", $_POST['bankNumber'])) {
        throw new Exception('올바른 계좌 번호가 아닙니다. 숫자만 입력해 주세요.');
    }
    if (!preg_match("/^[0-9]/i", $_POST['drawalMileage'])) {
        throw new Exception('올바른 금앱을 입력해 주세요.');
    }

    // DB on
    $db = new Database;

    mysqli_autocommit($db->conn, FALSE);

    $userMileageModel = $db->findOne('tr_mileage', ['user_no'=>$auth['no']], 'i', 'FOR UPDATE');

    $withdrawalLogNo = $db->save('tr_withdrawal_log', ['user_no'=>$auth['no'], 'withdrawal_mileage'=>$_POST['drawalMileage'], 'bank_name'=>$_POST['bankValue'],
                        'bank_account_number'=>Utils::encrypt($_POST['bankNumber']), 'status'=>'await'], 'iiss');

    if(!$withdrawalLogNo) {
        throw new DatabaseException('출금 신청에 실패했습니다.');
    }

    // 마일리지 변동 DB
    $mileageLogNo = $db->save('tr_mileage_log', ['user_no'=>$auth['no'], 'method'=>'withdrawal', 'method_no'=>$withdrawalLogNo, 'before_mileage'=>$userMileageModel['use_mileage'],
                                            'use_mileage'=>$_POST['drawalMileage'], 'after_mileage'=>$userMileageModel['use_mileage'] - $_POST['drawalMileage']], 'isiiii');

    if(!$mileageLogNo) {
        throw new DatabaseException('마일리지 변동에 실패했습니다.');
    }


    $userMileageBoolean = $db->update('tr_mileage', ['user_no'=>$auth['no']], ['using_mileage'=>$userMileageModel['using_mileage'] + $_POST['drawalMileage'], 'use_mileage'=>$userMileageModel['use_mileage'] - $_POST['drawalMileage'],
                                            'real_mileage'=>$userMileageModel['real_mileage'] - $_POST['drawalMileage']]);

    if(!$userMileageBoolean) {
        throw new DatabaseException('마일리지 변경에 실패했습니다.');
    }


    mysqli_commit($db->conn);
    $message = '출금 신청이 완료되었습니다.';

} catch (DatabaseException $e) {
    mysqli_rollback($db->conn);
    $e->setErrorMessages($e);
} catch (CustomException $e) {
    $e->setErrorMessages($e);
} catch (Exception $e) {
    $message = $e->getMessage();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/view/error/error_prv.php';
    die();
}
?>
<section class="container">

<div class="alert alert-success">
    <?php echo $message ?>
</div>
<a href="/" class="btn btn-secondary">홈</a>

</section>
</body>
</html>


<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\Utils;
use app\lib\exception\PaymentException;
use app\lib\exception\DatabaseException;
use app\lib\exception\CustomException;

try {
    $postData = $_POST;
    $preUrl = $_SERVER['HTTP_REFERER'];

    // validation section
    if(empty($postData['radioValue'])) {
        throw new CustomException('올바른 경로가 아닙니다.');
    }

    // 임시 다른 결제 막아두기
    if($postData['radioValue'] !== 'credit') {
        throw new CustomException('현재 카드 결제만 열어 두었습니다.');
    }

    // price
    if (empty($postData['price'])) {
        throw new Exception('금액이 존재하지 않습니다.');
    }
    if ((integer)$postData['price'] < 1000) {
        throw new Exception('금액이 올바르지 않습니다.');
    }
    // card
    if (empty($postData['cardNumber']) || empty($postData['cardYear']) || empty($postData['cardMonth']) || empty($postData['cardCVC']) || empty($postData['cardPassword'])) {
        throw new Exception('카드 정보를 입력해 주세요.');
    }
    // integer validation
    foreach ($postData as $key => $value) {
        if ($key !== 'cardNumber' && $key !== 'radioValue') {
            if (!preg_match("/^[0-9]/i", $value)) {
                throw new Exception('숫자만 입력해 주세요');
            }
        }
    }
    foreach ($postData['cardNumber'] as $item) {
        if (strlen($item) !== 4) {
            throw new Exception('카드 번호길이가 알맞지 않습니다.');
        }
        if (!preg_match("/^[0-9]/i", $item)) {
            throw new Exception('숫자만 입력해 주세요');
        }
    }
    if ((integer)$postData['cardMonth'] < 1 || (integer)$postData['cardMonth'] > 12) {
        throw new Exception('카드 유효기간이 올바르지 않습니다.');
    }

    $cardDate = date("Y-m-d H:i:s", mktime(0, 0, 0, $postData['cardMonth'] + 1, 0, $postData['cardYear']));
    $toDate = date("Y-m-d H:i:s");
    if ($toDate > $cardDate) {
        throw new PaymentException('카드 유효기간이 지났습니다.');
    }
    if (strlen($postData['cardCVC']) !== 3) {
        throw new PaymentException('카드의 보안코드가 올바르지 않습니다.');
    }
    if (strlen($postData['cardPassword']) !== 4) {
        throw new PaymentException('카드 패스워드가 알맞지 않습니다.');
    }

    $db = new Database;
    $db->pdo->beginTransaction();

    $userMileageModel = $db->findOne('tr_mileage', ['user_no'=>$auth['no']], 'FOR UPDATE');

    $information = [
        'card_validity' => Utils::encrypt(date("Y-m", strtotime($cardDate))),
        'card_number' => Utils::encrypt(implode('-', $postData['cardNumber']))
    ] ;

    // 결제 로그 저장
    $paymentLogNo = $db->save('tr_payment_log', ['user_no'=>$auth['no'], 'method'=>'credit', 'payment_mileage'=>$postData['price'],
                            'payment_information'=>json_encode($information), 'status'=>'success', 'cancels'=>json_encode(['cancel'=>0])]);

    if(!$paymentLogNo) {
        throw new DatabaseException();
    }

    // 마일리지 변동 로그 저장
    $mileageLogNo = $db->save('tr_mileage_log', ['user_no'=>$auth['no'], 'method'=>'payment', 'method_no'=>$paymentLogNo, 'before_mileage'=>$userMileageModel['use_mileage'],
                            'use_mileage'=>$postData['price'], 'after_mileage'=>$userMileageModel['use_mileage'] + $postData['price']]);

    if(!$mileageLogNo) {
        throw new DatabaseException('마일리지 변동 로그 저장 실패했습니다.');
    }

    // 유저 마일리지 테이블 적용
    $mileageUpdateResult = $db->update('tr_mileage', ['user_no'=>$auth['no']], ['use_mileage'=>$userMileageModel['use_mileage'] + $postData['price'],
                                        'real_mileage'=>$userMileageModel['real_mileage'] + $postData['price']]);

    if(!$mileageUpdateResult) {
        throw new DatabaseException('마일리지 적용에 실패했습니다.');
    }


    $db->pdo->commit();
    $message = '결재가 완료 되었습니다.';


} catch (CustomException $e) {
    $e->setErrorMessages($e);
} catch (DatabaseException $e) {
    $db->pdo->rollBack();
    $e->setErrorMessages($e);
} catch (PaymentException $e) {
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


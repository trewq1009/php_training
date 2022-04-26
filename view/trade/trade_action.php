<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\Session;
use app\lib\exception\DatabaseException;

try {
    if(!$auth) {
        throw new Exception('로그인 후 이용해 주세요.');
    }
    if(empty($_POST['tradeNo']) || empty($_POST['tradeType'])) {
        throw new Exception('잘못된 경로 입니다.');
    }
    if(!($_POST['tradeType'] == 'seller' || $_POST['tradeType'] == 'buyer')) {
        throw new Exception('잘못된 경로 입니다.');
    }

    $db = new Database;
    $db->pdo->beginTransaction();
    $preUrl = $_SERVER['HTTP_REFERER'];

    // 1. 거래 로그 찾아서 스테이터스 값 변경
    // 거래 타입에 따라 상대 거래 상태값도 확인
    // 내가 이미 거래 완료 신청을 했나도 봐야함
    $tradeLogData = $db->findOne('tr_trade_log', ['no'=>$_POST['tradeNo']], 'FOR UPDATE');

    if($tradeLogData[$_POST['tradeType'].'_trade_status'] == 'success') {
        throw new DatabaseException('이미 거래 완료 신청을 했습니다.');
    }

    // 현재 날짜
    $date = new DateTime("NOW");
    $timeStamp = $date->format('Y-m-d H:i:s');

    if($_POST['tradeType'] == 'seller') {
        if($tradeLogData['seller_no'] != $auth['no']) {
            throw new DatabaseException('올바른 데이터가 아닙니다.');
        }

        // 상대 거래 상태 값에따라 업데이트 데이터 변화
        if($tradeLogData['buyer_trade_status'] == 'ongoing') {
            $params = ['seller_trade_status' => 'success', 'seller_status_date'=>$timeStamp];
            $mileageFlag = false;
        } else {
            $params = ['seller_trade_status'=>'success', 'seller_status_date'=>$timeStamp, 'trade_success_date'=>$timeStamp,'status'=>'success'];
            $mileageFlag = true;
        }
    } else {
        if($tradeLogData['buyer_no'] != $auth['no']) {
            throw new DatabaseException('올바른 데이터가 아닙니다.');
        }

        if($tradeLogData['seller_trade_status'] == 'ongoing') {
            $params = ['buyer_trade_status'=>'success', 'buyer_status_date'=>$timeStamp];
            $mileageFlag = false;
        } else {
            $params = ['buyer_trade_status'=>'success', 'buyer_status_date'=>$timeStamp, 'trade_success_date'=>$timeStamp, 'status'=>'success'];
            $mileageFlag = true;
        }
    }

    if(!$db->update('tr_trade_log', ['no'=>$_POST['tradeNo']], $params)) {
        throw new DatabaseException('작업에 실패하였습니다.');
    }

    // 상대방이 아직 거래중 상태일때 로직 종료
    if(!$mileageFlag) {
        $db->pdo->commit();
        $message = '거래 완료 되었습니다.';
    }

    // 3. 해당 거래 마일리지 최종 success 면 판매자에게 update
    // 3-1. 거래 성공일시 마일리지 로그 먼저 insert
    // 3-2. 그 다음 해당 유저 마일리지 update
    $sellerData = $db->findOne('tr_mileage', ['user_no'=>$tradeLogData['seller_no']], 'FOR UPDATE');

    // 수수료 작업
    $commissionPrice = $tradeLogData['trade_price'] * 0.05;
    $realPrice = ceil($tradeLogData['trade_price'] - $commissionPrice);

    $sellerMileageLogNo = $db->save('tr_mileage_log', ['user_no'=>$sellerData['user_no'], 'method'=>'trade', 'method_no'=>$_POST['tradeNo'], 'before_mileage'=>$sellerData['use_mileage'],
                                            'use_mileage'=>$realPrice, 'after_mileage'=>$sellerData['use_mileage'] + $realPrice]);

    if(!$sellerMileageLogNo) {
        throw new DatabaseException('작업에 실패하였습니다.');
    }

    $sellerMileageBoolean = $db->update('tr_mileage', ['user_no'=>$sellerData['user_no']], ['use_mileage'=>$sellerData['use_mileage'] + $realPrice,
                                            'real_mileage'=>$sellerData['use_mileage'] + $realPrice]);

    if(!$sellerMileageBoolean) {
        throw new DatabaseException('작업에 실패하였습니다.');
    }


    // 4. 거래 완전 확정일때 구매자의 using_mileage 도 사용 처리
    $buyerData = $db->findOne('tr_mileage', ['user_no'=>$tradeLogData['buyer_no']], 'FOR UPDATE');

    if(!$db->update('tr_mileage', ['user_no'=>$buyerData['user_no']], ['using_mileage'=>$buyerData['using_mileage'] - $tradeLogData['trade_price']])) {
        throw new DatabaseException('작업에 실패하였습니다.');
    }

    $db->pdo->commit();
    $message = '거래 완료 되었습니다.';

} catch (DatabaseException $e) {
    $db->pdo->rollBack();
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


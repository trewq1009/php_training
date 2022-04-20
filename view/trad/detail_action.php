<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\Session;
use app\lib\exception\CustomException;
use app\lib\exception\DatabaseException;

try {
    if(empty($_POST['boardNo']) || empty($_POST['seller']) || empty($_POST['price']) || empty($_POST['productNo'])) {
        throw new Exception('잘못된 경로 입니다.');
    }
    if($auth['no'] == $_POST['seller']) {
        throw new Exception('본인의 판매글에 거래 신청을 할 수 없습니다.');
    }

    // DB connect
    $db = new Database;

    // user mileage
    $userMileageData = $db->findOne('tr_mileage', ['user_no'=>$auth['no']]);

    // mileage validation
    if($userMileageData['use_mileage'] < $_POST['price']) {
        throw new CustomException('사용할 수 있는 마일리지가 부족합니다. 충전해 주세요');
    }


    // board validation
    $boardData = $db->findOne('tr_board', ['no'=>$_POST['boardNo']]);
    if($boardData['reference_no'] != $_POST['productNo']) {
        throw new Exception('데이터가 변경 되었습니다.');
    }

    // product validation
    $productData = $db->findOne('tr_product', ['no'=>$_POST['productNo']]);
    if($productData['before_price'] != $_POST['price']) {
        throw new Exception('가격이 변경 되었습니다.');
    }

    // transaction start
    $db->pdo->beginTransaction();

    // trad log insert
    $tradLogNo = $db->save('tr_trad_log', ['trad_board_no'=>$_POST['boardNo'], 'trad_product_no'=>$_POST['productNo'], 'seller_no'=>$_POST['seller'],
                                        'buyer_no'=>$auth['no'], 'trad_price'=>$_POST['price'], 'status'=>'ongoing']);

    if(!$tradLogNo) {
        throw new DatabaseException('로그 저장에 실패했습니다.');
    }

    // 차감 금액
    $differenceMileage = $userMileageData['use_mileage'] - $_POST['price'];
    
    $mileageLogNo = $db->save('tr_mileage_log', ['user_no'=>$auth['no'], 'method'=>'trad', 'method_no'=>$tradLogNo, 'before_mileage'=>$userMileageData['use_mileage'],
                                            'use_mileage'=>$_POST['price'], 'after_mileage'=>$differenceMileage]);

    if(!$mileageLogNo) {
        throw new DatabaseException('마일리지 로그 저장에 실패했습니다.');
    }

    // 사용할 수 있는 마일리지가 현금으로 충전된 마일리지 보다 작은경우
    // 이벤트 혹은 사용 전용 마일리지를 먼저 다 소모했다 생각하고 real_mileage 를 차감된 마일리지로 변경
    $usingMileage = $userMileageData['using_mileage'] + $_POST['price'];    // 사용중 마일리지 증가 값
    if($differenceMileage < $userMileageData['real_mileage']) {
        $mileageUpdateBool = $db->update('tr_mileage', ['user_no'=>$auth['no']], ['use_mileage'=>$differenceMileage, 'real_mileage'=>$differenceMileage, 'using_mileage'=>$usingMileage]);
    } else {
        $mileageUpdateBool = $db->update('tr_mileage', ['user_no'=>$auth['no']], ['use_mileage'=>$differenceMileage, 'using_mileage'=>$userMileageData]);
    }

    // mileage update boolean
    if(!$mileageUpdateBool) {
        throw new DatabaseException('마일리지 변경에 실패했습니다.');
    }

    $db->pdo->commit();
    header('Location: /view/trad/trad_list.php');

} catch (DatabaseException $e) {
    $db->pdo->rollBack();
    $e->setErrorMessages($e);
    header('Location: /view/trad/list.php');
} catch (CustomException $e) {
    $e->setErrorMessages($e);
    header('Location: /view/trad/list.php');
} catch (Exception $e) {
    Session::setSession('error', $e->getMessage());
    header('Location: /');
}
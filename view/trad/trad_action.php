<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\Session;
use app\lib\exception\DatabaseException;
use app\lib\exception\CustomException;

try {
    if(!$auth) {
        throw new Exception('로그인 후 이용해 주세요.');
    }
    if(empty($_POST['tradNo']) || empty($_POST['tradType'])) {
        throw new Exception('잘못된 경로 입니다.');
    }
    if(!($_POST['tradType'] == 'seller' || $_POST['tradType'] == 'buyer')) {
        throw new Exception('잘못된 경로 입니다.');
    }

    $db = new Database;
    $db->pdo->beginTransaction();
    $preUrl = $_SERVER['HTTP_REFERER'];

    // 1. 거래 로그 찾아서 스테이터스 값 변경
    // 거래 타입에 따라 상대 거래 상태값도 확인
    // 내가 이미 거래 완료 신청을 했나도 봐야함
    $tradLogData = $db->findOne('tr_trad_log', ['no'=>$_POST['tradNo']]);

    if($tradLogData[$_POST['tradType'].'_trad_status'] == 'success') {
        throw new DatabaseException('이미 거래 완료 신청을 했습니다.');
    }

    if($_POST['tradType'] == 'seller') {
        if($tradLogData['seller_no'] != $auth['no']) {
            throw new DatabaseException('올바른 데이터가 아닙니다.');
        }

        // 상대 거래 상태 값에따라 업데이트 데이터 변화
        if($tradLogData['buyer_trad_status'] == 'ongoing') {
            $params = ['seller_trad_status' => 'success'];
            $mileageFlag = false;
        } else {
            $params = ['seller_trad_status'=>'success', 'status'=>'success'];
            $mileageFlag = true;
        }
    } else {
        if($tradLogData['buyer_no'] != $auth['no']) {
            throw new DatabaseException('올바른 데이터가 아닙니다.');
        }

        if($tradLogData['seller_trad_status'] == 'ongoing') {
            $params = ['buyer_trad_status'=>'success'];
            $mileageFlag = false;
        } else {
            $params = ['buyer_trad_status'=>'success', 'status'=>'success'];
            $mileageFlag = true;
        }
    }

    if(!$db->update('tr_trad_log', ['no'=>$_POST['tradNo']], $params)) {
        throw new DatabaseException('작업에 실패하였습니다.');
    }

    // 상대방이 아직 거래중 상태일때 로직 종료
    if(!$mileageFlag) {
        $db->pdo->commit();
//        header('Location: /');
        throw new CustomException('작업에 성공하였습니다.');
    }

    // 3. 해당 거래 마일리지 최종 success 면 판매자에게 update
    // 3-1. 거래 성공일시 마일리지 로그 먼저 insert
    // 3-2. 그 다음 해당 유저 마일리지 update
    $sellerData = $db->findOne('tr_mileage', ['user_no'=>$tradLogData['seller_no']]);

    $sellerMileageLogNo = $db->save('tr_mileage_log', ['user_no'=>$sellerData['user_no'], 'method'=>'trad', 'method_no'=>$_POST['tradNo'], 'before_mileage'=>$sellerData['use_mileage'],
                                            'use_mileage'=>$tradLogData['trad_price'], 'after_mileage'=>$sellerData['use_mileage'] + $tradLogData['trad_price']]);

    if(!$sellerMileageLogNo) {
        throw new DatabaseException('작업에 실패하였습니다.');
    }

    $sellerMileageBoolean = $db->update('tr_mileage', ['user_no'=>$sellerData['user_no']], ['use_mileage'=>$sellerData['use_mileage'] + $tradLogData['trad_price'],
                                            'real_mileage'=>$sellerData['use_mileage'] + $tradLogData['trad_price']]);

    if(!$sellerMileageBoolean) {
        throw new DatabaseException('작업에 실패하였습니다.');
    }


    // 4. 거래 완전 확정일때 구매자의 using_mileage 도 사용 처리
    $buyerData = $db->findOne('tr_mileage', ['user_no'=>$tradLogData['buyer_no']]);

    if(!$db->update('tr_mileage', ['user_no'=>$buyerData['user_no']], ['using_mileage'=>$buyerData['using_mileage'] - $tradLogData['trad_price']])) {
        throw new DatabaseException('작업에 실패하였습니다.');
    }

    $db->pdo->commit();
    Session::setSession('success', '거래 완료 되었습니다.');
    header("Location: $preUrl");


} catch (CustomException $e) {
    Session::setSession('success', $e->getMessage());
    header("Location: $preUrl");
} catch (DatabaseException $e) {
    $db->pdo->rollBack();
    $e->setErrorMessages($e);
    $preUrl = $_SERVER['HTTP_REFERER'];
    header("Location: $preUrl");
} catch (Exception $e) {
    Session::setSession('error', $e->getMessage());
    header('Location: /');
}


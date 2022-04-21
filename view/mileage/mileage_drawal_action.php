<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\Session;
use app\lib\Utils;
use app\lib\exception\CustomException;
use app\lib\exception\DatabaseException;

try {
    $preUrl = $_SERVER['HTTP_REFERER'];

    if(!$auth) {
        throw new Exception('로그인 정보가 없습니다.');
    }

    if(!isset($_POST['usingMileage']) || empty($_POST['useMileage']) || empty($_POST['realMileage']) || empty($_POST['drawalMileage']) || empty($_POST['bankValue']) || empty($_POST['bankNumber'])) {
        throw new CustomException('입력 데이터를 다시 확인해 주세요.');
    }
    if($_POST['realMileage'] < 1000 || $_POST['drawalMileage'] < 1000) {
        throw new CustomException('출금 가능한 최소 금액이 안됩니다.');
    }
    if($_POST['realMileage'] < $_POST['drawalMileage']) {
        throw new CustomException('출금 가능 마일리지를 넘었습니다.');
    }
    if (!preg_match("/^[0-9]/i", $_POST['bankNumber'])) {
        throw new CustomException('올바른 계좌 번호가 아닙니다. 숫자만 입력해 주세요.');
    }
    if (!preg_match("/^[0-9]/i", $_POST['drawalMileage'])) {
        throw new CustomException('올바른 금앱을 입력해 주세요.');
    }

    // DB on
    $db = new Database;

    $db->pdo->beginTransaction();

    $userMileageModel = $db->findOne('tr_mileage', ['user_no'=>$auth['no']]);

    $withdrawalLogNo = $db->save('tr_withdrawal_log', ['user_no'=>$auth['no'], 'withdrawal_mileage'=>$_POST['drawalMileage'], 'bank_name'=>$_POST['bankValue'],
                        'bank_account_number'=>Utils::encrypt($_POST['bankNumber']), 'status'=>'await']);

    if(!$withdrawalLogNo) {
        throw new DatabaseException('출금 신청에 실패했습니다.');
    }

    // 마일리지 변동 DB
    $mileageLogNo = $db->save('tr_mileage_log', ['user_no'=>$auth['no'], 'method'=>'withdrawal', 'method_no'=>$withdrawalLogNo, 'before_mileage'=>$userMileageModel['use_mileage'],
                                            'use_mileage'=>$_POST['drawalMileage'], 'after_mileage'=>$userMileageModel['use_mileage'] - $_POST['drawalMileage']]);

    if(!$mileageLogNo) {
        throw new DatabaseException('마일리지 변동에 실패했습니다.');
    }


    $userMileageBoolean = $db->update('tr_mileage', ['user_no'=>$auth['no']], ['using_mileage'=>$userMileageModel['using_mileage'] + $_POST['drawalMileage'], 'use_mileage'=>$userMileageModel['use_mileage'] - $_POST['drawalMileage'],
                                            'real_mileage'=>$userMileageModel['real_mileage'] - $_POST['drawalMileage']]);

    if(!$userMileageBoolean) {
        throw new DatabaseException('마일리지 변경에 실패했습니다.');
    }


    $db->pdo->commit();
    Session::setSession('success', '출금 신청이 완료되었습니다.');
    header('Location: /');


} catch (DatabaseException $e) {
    $db->pdo->rollBack();
    $e->setErrorMessages($e);
    header("Location: $preUrl");
} catch (CustomException $e) {
    $e->setErrorMessages($e);
    header("Location: $preUrl");
} catch (Exception $e) {
    Session::setSession('error', $e->getMessage());
    header('Location: /');
}
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/header.php';

use app\lib\Database;
use app\lib\Session;
use app\lib\exception\DatabaseException;

try {
    if (empty($_POST['logNo']) || empty($_POST['userNo'])) {
        throw new Exception('필수 데이터가 없습니다.');
    }
    if($_POST['userMileage'] < $_POST['userWithdrawalAmount']) {
        throw new Exception('출금 하려는 금액이 잔액보다 큼니다.');
    }

    $db = new Database;
    mysqli_autocommit($db->conn, FALSE);

    $withdrawalLogData = $db->findOne('tr_withdrawal_log', ['no'=>$_POST['logNo']], 'i', 'FOR UPDATE');

    if($withdrawalLogData['user_no'] != $_POST['userNo']) {
        throw new DatabaseException('회원정보가 다릅니다.');
    }

    if(!$db->update('tr_withdrawal_log', ['status'=>'success'], ['no'=>$_POST['logNo']], 'si')) {
        throw new DatabaseException('로그 변경에 실패하였습니다.');
    }

    $userMileageData = $db->findOne('tr_mileage', ['user_no'=>$_POST['userNo']], 'i', 'FOR UPDATE');

    if($userMileageData['using_mileage'] < $withdrawalLogData['withdrawal_mileage']) {
        throw new DatabaseException('금액이 맞지 않습니다.');
    }

    $confrimMiileage = $userMileageData['using_mileage'] - $withdrawalLogData['withdrawal_mileage'];

    if(!$db->update('tr_mileage', ['using_mileage'=>$confrimMiileage], ['user_no'=>$_POST['userNo']], 'ii')) {
        throw new DatabaseException('출금 완료에 실패하였습니다.');
    }

    Session::setSession('success', '회원 출금 신청이 완료되었습니다.');
    header('Location: ./withdrawal_list.php');

} catch (DatabaseException $e) {
    mysqli_rollback($db->conn);
    $e->setErrorMessages($e);
    header('Location: ./withdrawal_list.php');
} catch (Exception $e) {
    Session::setSession('error', $e->getMessage());
    header('Location: ./withdrawal_list.php');
}

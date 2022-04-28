<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/header.php';

use app\lib\Database;
use app\lib\Session;
use app\lib\exception\DatabaseException;

try {
    if(empty($_POST['action'])) {
        throw new Exception('잘못된 경로 입니다.');
    }
    if(empty($_POST['userNo'])) {
        throw new Exception('수정하려는 회원의 정보가 없습니다.');
    }


    $db = new Database;
    mysqli_autocommit($db->conn, FALSE);
    if($_POST['action'] == 'update') {
        if(!$db->update('tr_account', ['name'=>$_POST['userName']], ['no'=>$_POST['userNo']], 'si')) {
            throw new DatabaseException('회원정보 변경에 실패했습니다.');
        }
    } else {
        if(!$db->update('tr_account', ['status'=>'DEAD'], ['no'=>$_POST['userNo']], 'si')) {
            throw new DatabaseException('회원 탈퇴에 실패하였습니다.');
        }
    }

    mysqli_commit($db->conn);
    Session::setSession('success', '회원 정보 수정에 성공하였습니다.');
    header('Location: ./user_list.php');
    exit();

} catch (DatabaseException $e) {
    mysqli_rollback($db->conn);
    Session::setSession('error', $e->getMessage());
    header('Location: ./user_list.php');
} catch (Exception $e) {
    Session::setSession('error', $e->getMessage());
    header('Location: ./user_list.php');
}

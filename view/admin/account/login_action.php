<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/header.php';

use app\lib\Database;
use app\lib\Session;

try {

    if(empty($_POST['imiId']) || empty($_POST['imiPw'])) {
        throw new Exception('정보를 확인해 주세요.');
    }
    // pattern 체크
    if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $_POST['imiId'])) {
        throw new Exception('아이디 형태가 올바르지 않습니다.');
    }

    $adminModel = (new Database)->findOne('tr_account_admin', ['id'=>$_POST['imiId'], 'status'=>'ALIVE']);
    if(!$adminModel) {
        throw new Exception('존재하지 않는 계정입니다.');
    }

    if(!password_verify($_POST['imiPw'], $adminModel['password'])) {
        throw new Exception('정보가 일치하지 않습니다.');
    }

    Session::setSession('admin', ['no'=>$adminModel['no'], 'name'=>$adminModel['name'], 'id'=>$adminModel['id']]);
    header('Location: /view/admin/admin.php');
    exit();

} catch (Exception $e) {
    Session::setSession('error', $e->getMessage());
    $prvUrl = $_SERVER['HTTP_REFERER'];
    header("location: $prvUrl");
}
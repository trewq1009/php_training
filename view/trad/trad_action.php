<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\Session;

try {
    if(!$auth) {
        throw new Exception('로그인 후 이용해 주세요.');
    }
    if(empty($_POST['tradNo'])) {
        throw new Exception('잘못된 경로 입니다.');
    }

    // 1. 거래 로그 찾아서 스테이터스 값 변경
    // 2. 혹시 유저 전부 success 면 최종 success 로 변경
    // 3. 해당 거래 마일리지 최종 success 면 판매자에게 update
    // 3-1. 거래 성공일시 마일리지 로그 먼저 insert
    // 3-2. 그 다음 해당 유저 마일리지 update
    // 4. 거래 완전 확정일때 구매자의 using_mileage 도 사용 처리







} catch (Exception $e) {
    Session::setSession('error', $e->getMessage());
    header('Location: /');
}


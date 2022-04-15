<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/header.php';


?>

<span>사용자간 거래 페이지</span>

<!--
    # 먼저 내가 아닌 3자의 거래 등록 리스트? 가 존재 해야함
    # 누르면 상세
    # 거래 진입
    ## 거래 진입시 보유 마일리지 확인 후 마일리지 안되면 거래 불가 & 출금 예정 마일리지 까지 확인 한다
    # 거래 중
    ## 거래중일때 출금 신청한 마일리지와 동일하게 status 값을 await 으로 두고 method 는 trad 상태가 된다
    # 거래 완료
    ## 거래 리스트에 등록한 유저가 거래 완료를 누르게 되면 거래 종료
    ## 이후 거래에 사용된 마일리지는 한쪽은 차감 한쪽은 증가? 되는 형태로 진행
 -->
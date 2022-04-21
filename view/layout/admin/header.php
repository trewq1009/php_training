<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use app\lib\Session;
if(!session_id()) {
    session_start();
}

$admin = Session::isSet('admin');

?>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/view/admin/admin.php">Home</a>
                    </li>
                    <?php if($admin): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/view/admin/member/user_list.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/view/admin/withdrawal/withdrawal_list.php">Withdrawal List</a>
                    </li>
                    <?php endif; ?>
                </ul>

                <?php if($admin): ?>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="/view/user/logout.php">로그아웃</a>
                        </li>
                    </ul>
                <?php else: ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/view/admin/account/login.php">Login</a>
                    </li>
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link" href="/">관리자 등록</a>-->
<!--                    </li>-->
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>
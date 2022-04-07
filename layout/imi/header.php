<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use app\lib\Session;


?>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/view/imi/admin.php">Home</a>
                    </li>
                    <?php if(Session::isSet('auth')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/">Users</a>
                    </li>
                    <?php endif; ?>
                </ul>

                <?php if(Session::isSet('auth')): ?>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="/view/logout.php">로그아웃</a>
                        </li>
                    </ul>
                <?php else: ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/view/imi/login.php">Login</a>
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
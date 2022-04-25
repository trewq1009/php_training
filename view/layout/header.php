<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

if(!session_id()) {
    session_start();
}

$auth = $_SESSION['auth'] ?? false;


?>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/view/trade/list.php">Trade</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/view/visitors/list.php">Visitors</a>
                    </li>
                </ul>

                <?php if($auth): ?>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="/view/user/profile.php">프로필</a>
                        </li>
                        <li class="nav-item" style="display: flex">
                            <a class="nav-link" href="/view/mileage/mileage.php">마일리지 충전</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/view/user/logout.php">로그아웃</a>
                        </li>
                    </ul>
                <?php else: ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/view/user/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/view/registered/register.php">회원가입</a>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

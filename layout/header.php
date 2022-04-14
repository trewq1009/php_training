<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

use app\lib\Session;

$auth = (new Session)->isSet('auth');


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
                        <a class="nav-link" href="#">Link</a>
                    </li>
                </ul>

                <?php if($auth): ?>
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="/view/profile.php">프로필</a>
                        </li>
                        <li class="nav-item" style="display: flex">
                            <!--
                            <p class="nav-link"><?php echo $auth['mileage'] ?></p>
                            -->
                            <a class="nav-link" href="/view/mileage.php">마일리지 충전</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/view/logout.php">로그아웃</a>
                        </li>
                    </ul>
                <?php else: ?>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/view/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/view/register.php">회원가입</a>
                    </li>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </nav>
</header>

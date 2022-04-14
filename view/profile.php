<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/header.php';

use app\lib\Session;
use app\lib\User;

if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    if($_POST['action'] === 'update') {
        (new User)->update($_POST);
    } else if($_POST['action'] === 'delete') {
        (new User)->delete($auth);
    } else {
        header('Location: /view/mileage_drawal.php');
    }
} else {
    if(!$auth) {
        (new Session)->setSession('error', '잘못된 경로 입니다.');
        header('Location: /');
        exit();
    }
}

?>
<section class="container">

    <?php if((new Session)->isSet('error')): ?>
        <div class="alert alert-danger">
            <?php echo (new Session)->getFlash('error') ?>
        </div>
    <?php endif; ?>

    <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method="post" id="methodForm">
        <div class="mb-3">
            <label for="userId" class="form-label">ID</label>
            <input type="text" class="form-control" value="<?php echo $auth['id'] ?>" name="userId" id="userId" readonly required>
        </div>
        <div class="mb-3">
            <label for="userName" class="form-label">Name</label>
            <input type="text" class="form-control" value="<?php echo $auth['name'] ?>" name="userName" id="userName" required>
        </div>
        <div class="mb-3">
            <label for="userPassword" class="form-label">Password</label>
            <input type="password" class="form-control" name="userPw" id="userPassword">
        </div>
        <div class="mb-3">
            <label for="userEmail" class="form-label">Email</label>
            <input type="email" class="form-control" value="<?php echo $auth['email'] ?>" name="userEmail" id="userEmail" readonly required>
        </div>
        <div class="mb-3">
            <label for="userMileage" class="form-label">Mileage</label>
            <input type="text" class="form-control" value="<?php echo $auth['mileage'] ?>" name="userMileage" id="userMileage" readonly required>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between">
            <div>
                <button type="submit" name="action" value="update" class="btn btn-primary">정보수정</button>
                <button type="submit" name="action" value="withdrawal" class="btn btn-primary">마일리지 출금</button>
            </div>
            <button type="submit" name="action" value="delete" class="btn btn-danger">탈퇴신청</button>
        </div>
    </form>
</section>

</body>
</html>


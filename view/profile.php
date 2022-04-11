<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/header.php';

use app\lib\Session;
use app\lib\User;

if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    if($_POST['action'] === 'update') {
        (new User)->update($_POST);
    } else {
        (new User)->delete(Session::isSet('auth'));
    }
} else {
    $userModel = (new Session)->isSet('auth');
    if(!$userModel) {
        (new Session)->setSession('error', '잘못된 경로 입니다.');
        header('Location: /');
        exit();
    }
}

?>
<section class="container">
    <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method="post" id="methodForm">
        <div class="mb-3">
            <label for="userId" class="form-label">ID</label>
            <input type="text" class="form-control" value="<?php echo $userModel['id'] ?>" name="userId" id="userId" readonly required>
        </div>
        <div class="mb-3">
            <label for="userName" class="form-label">Name</label>
            <input type="text" class="form-control" value="<?php echo $userModel['name'] ?>" name="userName" id="userName" required>
        </div>
        <div class="mb-3">
            <label for="userPassword" class="form-label">Password</label>
            <input type="password" class="form-control" name="userPw" id="userPassword">
        </div>
        <div class="mb-3">
            <label for="userEmail" class="form-label">Email</label>
            <input type="email" class="form-control" value="<?php echo $userModel['email'] ?>" name="userEmail" id="userEmail" readonly required>
        </div>
        <button type="submit" name="action" value="update" class="btn btn-primary">정보수정</button>
        <button type="submit" name="action" value="delete" class="btn btn-danger">탈퇴신청</button>
    </form>
</section>

</body>
</html>


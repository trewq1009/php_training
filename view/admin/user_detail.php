<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/imi/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/imi/header.php';

use app\lib\Session;
use app\lib\Imi;

$model = new Imi;
if(strtolower($_SERVER['REQUEST_METHOD']) == 'get') {
    $model->getUserInfo($_GET);
} else {
    if($_POST['action'] == 'update') {
        $model->userUpdate($_POST);
    } else {
        $model->userDelete($_POST);
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
        <input type="hidden" name="userNo" value="<?php echo $model->model['no'] ?>" readonly>
        <div class="mb-3">
            <label for="userId" class="form-label">ID</label>
            <input type="text" class="form-control" value="<?php echo $model->model['id'] ?>" name="userId" id="userId" readonly required>
        </div>
        <div class="mb-3">
            <label for="userName" class="form-label">Name</label>
            <input type="text" class="form-control" value="<?php echo $model->model['name'] ?>" name="userName" id="userName" required>
        </div>
        <div class="mb-3">
            <label for="userEmail" class="form-label">Email</label>
            <input type="email" class="form-control" value="<?php echo $model->model['email'] ?>" name="userEmail" id="userEmail" readonly required>
        </div>
        <div class="mb-3">
            <label for="userEmailStatus" class="form-label">Email Authentication</label>
            <input type="text" class="form-control" value="<?php echo $model->model['email_status'] ?>" name="userEmailStatus" id="userEmailStatus" readonly required>
        </div>
        <div class="mb-3">
            <label for="userStatus" class="form-label">Status</label>
            <input type="text" class="form-control" value="<?php echo $model->model['status'] ?>" name="userStatus" id="userStatus" readonly required>
        </div>
        <div class="mb-3">
            <label for="userRegistered" class="form-label">Registered</label>
            <input type="text" class="form-control" value="<?php echo $model->model['registered'] ?>" name="userRegistered" id="userRegistered" readonly required>
        </div>
        <button type="submit" name="action" value="update" class="btn btn-primary">회원수정</button>

        <?php if($model->model['status'] == 'AWAIT'): ?>
        <button type="submit" name="action" value="delete" class="btn btn-danger">회원탈퇴</button>
        <?php endif ?>
    </form>
</section>

</body>
</html>

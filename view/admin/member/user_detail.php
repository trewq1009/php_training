<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/header.php';

use app\lib\Session;
use app\lib\Database;

try {
    $userModel = (new Database)->findOne('tr_account', ['no'=>$_GET['userNo']], 'i');
    $userMileageData = (new Database)->findOne('tr_mileage', ['user_no'=>$_GET['userNo']], 'i');

} catch (Exception $e) {
    echo $e->getMessage();
}

?>

<section class="container">

    <?php if(Session::isSet('error')): ?>
        <div class="alert alert-danger">
            <?php echo Session::getFlash('error') ?>
        </div>
    <?php endif; ?>

    <form action='<?php echo htmlspecialchars('./user_action.php');?>' method="post" id="methodForm">
        <input type="hidden" name="userNo" value="<?php echo $userModel['no'] ?>" readonly>
        <div class="mb-3">
            <label for="userId" class="form-label">ID</label>
            <input type="text" class="form-control" value="<?php echo $userModel['id'] ?>" name="userId" id="userId" readonly required>
        </div>
        <div class="mb-3">
            <label for="userName" class="form-label">Name</label>
            <input type="text" class="form-control" value="<?php echo $userModel['name'] ?>" name="userName" id="userName" required>
        </div>
        <div class="mb-3">
            <label for="userEmail" class="form-label">Email</label>
            <input type="email" class="form-control" value="<?php echo $userModel['email'] ?>" name="userEmail" id="userEmail" readonly required>
        </div>
        <div class="mb-3">
            <label for="userEmailStatus" class="form-label">Email Authentication</label>
            <input type="text" class="form-control" value="<?php echo $userModel['email_status'] ?>" name="userEmailStatus" id="userEmailStatus" readonly required>
        </div>
        <div class="mb-3">
            <label for="usingMileage" class="form-label">Using Mileage</label>
            <input type="text" class="form-control" value="<?php echo $userMileageData['using_mileage'] ?>"id="usingMileage" readonly required>
        </div>
        <div class="mb-3">
            <label for="useMileage" class="form-label">Use Mileage</label>
            <input type="text" class="form-control" value="<?php echo $userMileageData['use_mileage'] ?>" id="useMileage" readonly required>
        </div>
        <div class="mb-3">
            <label for="realMileage" class="form-label">Real Mileage</label>
            <input type="text" class="form-control" value="<?php echo $userMileageData['real_mileage'] ?>" id="realMileage" readonly required>
        </div>
        <div class="mb-3">
            <label for="userStatus" class="form-label">Status</label>
            <input type="text" class="form-control" value="<?php echo $userModel['status'] ?>" name="userStatus" id="userStatus" readonly required>
        </div>
        <div class="mb-3">
            <label for="userRegistered" class="form-label">Registered</label>
            <input type="text" class="form-control" value="<?php echo $userModel['registered'] ?>" name="userRegistered" id="userRegistered" readonly required>
        </div>
        <button type="submit" name="action" value="update" class="btn btn-primary">회원수정</button>

        <?php if($userModel['status'] == 'AWAIT'): ?>
        <button type="submit" name="action" value="delete" class="btn btn-danger">회원탈퇴</button>
        <?php endif ?>
    </form>
</section>

</body>
</html>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\exception\CustomException;

try {
    if(!$auth) {
        throw new CustomException('잘못된 경로 입니다.');
    }
    $userModel = (new Database)->findOne('tr_account', ['no'=>$auth['no']]);
    if(!$userModel) {
        throw new CustomException('회원 정보가 없습니다.');
    }
    $userMileage = (new Database)->findOne('tr_mileage', ['user_no'=>$userModel['no']]);
    if(!$userMileage) {
        throw new CustomException('마일리지를 정보를 가져올 수 없습니다.');
    }

} catch (CustomException $e) {
    $e->setErrorMessages($e);
}


?>
<section class="container">

    <form action='<?php echo htmlspecialchars('./profile_action.php');?>' method="post" id="methodForm">
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
        <div class="mb-3">
            <label for="userMileage" class="form-label">Using Mileage</label>
            <input type="text" class="form-control" value="<?php echo $userMileage['using_mileage'] ?>" name="userUsingMileage" id="userMileage" readonly required>
        </div>
        <div class="mb-3">
            <label for="userUseMileage" class="form-label">Use Mileage</label>
            <input type="text" class="form-control" value="<?php echo $userMileage['use_mileage'] ?>" name="userUseMileage" id="userUseMileage" readonly required>
        </div>
        <div class="mb-3">
            <label for="userRealMileage" class="form-label">Withdrawal Mileage</label>
            <input type="text" class="form-control" value="<?php echo $userMileage['real_mileage'] ?>" name="userRealMileage" id="userRealMileage" readonly required>
        </div>
        <div style="display: flex; align-items: center; justify-content: space-between">
            <div>
                <button type="submit" name="action" value="update" class="btn btn-primary">정보수정</button>
                <button type="submit" name="action" value="withdrawal" class="btn btn-primary">마일리지 출금</button>
                <button type="submit" name="action" value="mileageReport" class="btn btn-info">마일리지 내역확인</button>
            </div>
            <button type="submit" name="action" value="delete" class="btn btn-danger">탈퇴신청</button>
        </div>
    </form>
</section>

</body>
</html>


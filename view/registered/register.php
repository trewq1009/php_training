<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Session;

try {
    if($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('올바른 경로가 아닙니다.');
    }
    $userId = $_GET['userId'] ?? '';
    $userName = $_GET['userName'] ?? '';
    $userEmail = $_GET['userEmail'] ?? '';
    
} catch(Exception $e) {
    $e->setErrorMessage($e);
}


?>
    <section class="container">
        <form action='<?php echo htmlspecialchars('./register_action.php');?>' method="post" id="methodForm">
            <div class="mb-3">
                <label for="userId" class="form-label">ID</label>
                <input type="text" class="form-control" value="<?php echo $userId ?>" name="userId" id="userId" required>
            </div>
            <div class="mb-3">
                <label for="userName" class="form-label">Name</label>
                <input type="text" class="form-control" value="<?php echo $userName ?>" name="userName" id="userName" required>
            </div>
            <div class="mb-3">
                <label for="userPassword" class="form-label">Password</label>
                <input type="password" class="form-control" name="userPw" id="userPassword" required>
            </div>
            <div class="mb-3">
                <label for="passwordConfirm" class="form-label">Password Confirm</label>
                <input type="password" class="form-control" name="userPwC" id="passwordConfirm" required>
            </div>
            <div class="mb-3">
                <label for="userEmail" class="form-label">Email</label>
                <input type="email" class="form-control" value="<?php echo $userEmail ?>" name="userEmail" id="userEmail" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

    </section>

</body>
</html>

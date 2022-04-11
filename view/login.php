<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/header.php';

use app\lib\User;
use app\lib\Session;

if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $userModel = new User();
    $userModel->logIn($_POST);
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
            <input type="text" class="form-control" value="<?php echo $userModel->userId ?>" name="userId" id="userId" required>
        </div>
        <div class="mb-3">
            <label for="userPassword" class="form-label">Password</label>
            <input type="password" class="form-control" name="userPw" id="userPassword" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

</section>

</body>
</html>



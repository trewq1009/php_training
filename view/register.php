<?php
require_once __DIR__.'/../lib/Utils.php';
require_once __DIR__.'/../lib/User.php';
require_once __DIR__.'/../lib/Database.php';
require_once __DIR__.'/../lib/Session.php';


    $userModel = new \app\lib\User();


    $method = (new app\lib\Utils)->getMethod($_SERVER);
    if($method === 'post') {
        $userModel->register($_POST);
    }



    require_once __DIR__.'/../layout/head.php';
    require_once __DIR__.'/../layout/header.php';
?>
    <section class="container">
        <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method="post" id="methodForm">
            <div class="mb-3">
                <label for="userId" class="form-label">ID</label>
                <input type="text" class="form-control" value="<?php echo $userModel->userId ?>" name="userId" id="userId" required>
            </div>
            <div class="mb-3">
                <label for="userName" class="form-label">Name</label>
                <input type="text" class="form-control" value="<?php echo $userModel->userName ?>" name="userName" id="userName" required>
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
                <input type="email" class="form-control" value="<?php echo $userModel->userEmail ?>" name="userEmail" id="userEmail" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

    </section>

</body>
</html>

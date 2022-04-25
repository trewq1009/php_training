<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\exception\CustomException;

try {
    if($_SERVER['REQUEST_METHOD'] != 'GET') {
        throw new CustomException('잘못된 경로 입니다.');
    }
} catch(CustomException $e) {
    $e->setErrorMessages($e);
}
?>

<section class="container">

    <form action='<?php echo htmlspecialchars('./login_action.php');?>' method="post" id="methodForm">
        <div class="mb-3">
            <label for="userId" class="form-label">ID</label>
            <input type="text" class="form-control" name="userId" id="userId" required>
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



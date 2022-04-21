<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/header.php';

use app\lib\Session;

?>
    <section class="container">
        <?php if(Session::isSet('error')): ?>
            <div class="alert alert-danger">
                <?php echo Session::getFlash('error') ?>
            </div>
        <?php endif; ?>

        <form action='<?php echo htmlspecialchars('./login_action.php');?>' method="post" id="methodForm">
            <div class="mb-3">
                <label for="imiId" class="form-label">ID</label>
                <input type="text" class="form-control" name="imiId" id="imiId" required>
            </div>
            <div class="mb-3">
                <label for="imiPassword" class="form-label">Password</label>
                <input type="password" class="form-control" name="imiPw" id="imiPassword" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

    </section>

</body>
</html>

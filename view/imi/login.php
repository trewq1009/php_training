<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use app\lib\Imi;
use app\lib\Session;
use app\lib\Utils;


    $method = (new Utils)->getMethod($_SERVER);
    if($method == 'post') {
        $imiModel = new imi();
        $imiModel->login($_POST);
    }




require_once $_SERVER['DOCUMENT_ROOT'].'/layout/imi/head.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/layout/imi/header.php';
?>



    <section class="container">
        <?php if(Session::isSet('error')): ?>
            <div class="alert alert-danger">
                <?php echo Session::getFlash('error') ?>
            </div>
        <?php endif; ?>

        <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method="post" id="methodForm">
            <div class="mb-3">
                <label for="imiId" class="form-label">ID</label>
                <input type="text" class="form-control" value="<?php echo $imiModel->id; ?>" name="imiId" id="imiId" required>
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

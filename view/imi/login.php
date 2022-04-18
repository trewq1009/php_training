<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/imi/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/imi/header.php';

use app\lib\Imi;
use app\lib\Session;

$imiId = '';
if(strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $imiId = $_POST['imiId'];
    (new Imi)->login($_POST);
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
                <label for="imiId" class="form-label">ID</label>
                <input type="text" class="form-control" value="<?php echo $imiId; ?>" name="imiId" id="imiId" required>
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

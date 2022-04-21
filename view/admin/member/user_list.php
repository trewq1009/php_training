<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/admin/header.php';

use app\lib\Session;
use app\lib\Database;
use app\lib\Field;
use app\lib\exception\CustomException;

try {
    if(isset($_GET['viewUser'])) {
        throw new CustomException();
    }
    $page = $_GET['page'] ?? 1;

    $url = explode('?', $_SERVER['REQUEST_URI'])[0];
    $url = explode('/', $url);
    $url = end($url);

    $listModel = (new Database)->list('tr_account', $page, []);
    $userList = $listModel['listData'];
    unset($listModel['listData']);

    $listModel['page'] = $page;
    $listBtn = Field::listBtn($listModel);

} catch (CustomException $e) {
    $url = sprintf('/view/admin/member/user_detail.php?userNo=%s', $_GET['viewUser']);
    header("Location: $url");
} catch (Exception $e) {
    echo $e->getMessage();
}

?>
    <section class="container">
        <?php if(Session::isSet('success')): ?>
            <div class="alert alert-success">
                <?php echo Session::getFlash('success') ?>
            </div>
        <?php elseif(Session::isSet('error')): ?>
            <div class="alert alert-danger">
                <?php echo Session::getFlash('error') ?>
            </div>
        <?php endif; ?>

        <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>' method="get">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">NAME</th>
                    <th scope="col">ID</th>
                    <th scope="col">EMAIL</th>
                    <th scope="col">STATUS</th>
                    <th scope="col">REG_DT</th>
                    <th scope="col">VIEW</th>
                </tr>
                </thead>
                <tbody>
                <?php if($userList): ?>
                    <?php foreach ($userList as $item): ?>
                        <tr>
                            <th scope="row"><?php echo $item['no'] ?></th>
                            <td><?php echo $item['name'] ?></td>
                            <td><?php echo $item['id'] ?></td>
                            <td><?php echo $item['email'] ?></td>
                            <td><?php echo $item['status'] ?></td>
                            <td><?php echo $item['registered'] ?></td>
                            <td>
                                <button type="submit" name="viewUser" value="<?php echo $item['no'] ?>" class="btn btn-outline-info">Info</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7">데이터가 없습니다.</td>
                    </tr>
                <?php endif ?>
                </tbody>
            </table>
        </form>
        <?php echo $listBtn ?>
    </section>
</body>
</html>

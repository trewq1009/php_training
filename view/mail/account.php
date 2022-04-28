<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Session;
use app\lib\Database;
use app\lib\exception\DatabaseException;

try {
    if(!isset($_GET['training'])) {
        throw new Exception('올바른 주소가 아닙니다.');
    }

    $db = new Database;
    mysqli_autocommit($db->conn, FALSE);

    $userData = $db->findOne('tr_account', ['no'=>$_GET['training'], 'status'=>'t'], 'is');
    if(!$userData) {
        throw new Exception('올바른 회원이 아닙니다.');
    }
    if($userData['email_status'] == 't') {
        throw new Exception('이미 인증된 회원 입니다.');
    }

    if(!$db->update('tr_account', ['email_status'=>'t'], ['no'=>$userData['no']], 'si')) {
        throw new DatabaseException('회원 활성화에 실패하였습니다.');
    }

    mysqli_commit($db->conn);
    $message = '이메일 인증에 성공하였습니다. 로그인 해주세요.';

} catch (DatabaseException $e) {
    mysqli_rollback($db->conn);
    $e->setErrorMessages($e);
} catch (Exception $e) {
    $message = $e->getMessage();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/view/error/error.php';
    die();
}

?>
<section class="container">

<div class="alert alert-success">
    <?php echo $message ?>
</div>
<a href="/" class="btn btn-secondary">홈</a>

</section>
</body>
</html>



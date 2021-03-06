<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Session;
use app\lib\Database;
use app\lib\exception\DatabaseException;
use app\lib\exception\CustomException;

try {
    // DB connect
    $db = new Database;

    $preUrl = $_SERVER['HTTP_REFERER'];
    $postData = $_POST;

    if($_POST['action'] === 'update') {
        if (!empty($postData['userPw'])) {
            if (strlen($postData['userPw']) < 8 || strlen($postData['userPw']) > 20) {
                throw new CustomException('비밀번호 형식에 맞지 않습니다.');
            }
            $postData['userPw'] = password_hash($postData['userPw'], PASSWORD_BCRYPT);
        } else {
            $userModel = $db->findOne('tr_account', ['no'=>$auth['no']], 'i');
            $postData['userPw'] = $userModel['password'];
        }
        if (empty($postData['userName'])) {
            throw new CustomException('유저 이름이 공백 입니다.');
        }
        if ($postData['userName'] == 'userName') {
            if (!preg_match("/^[가-힣]/", $postData['userName'])) {
                throw new CustomException('올바른 이름의 형태가 아닙니다.');
            }
        }


        mysqli_autocommit($db->conn, FALSE);

        if (!$db->update('tr_account', ['name' => $postData['userName'], 'password' => $postData['userPw']], ['no' => $auth['no']], 'ssi')) {
            throw new DatabaseException('정보 수정에 실패했습니다.');
        }

        mysqli_commit($db->conn);

        $afterUserData = $db->findOne('tr_account', ['no' => $auth['no']], 'i');
        Session::setSession('auth', ['no' => $afterUserData['no'], 'name' => $afterUserData['name']]);
        $message = '정보 수정이 완료 되었습니다.';

    } else if($_POST['action'] === 'delete') {

        mysqli_autocommit($db->conn, FALSE);
        if(!$db->update('tr_account',['status' => 'a'], ['no' => $auth['no']], 'si')) {
            throw new DatabaseException('회원 탈퇴 신청을 실패하였습니다.');
        }

        mysqli_commit($db->conn);

        Session::removeSession('auth');
        $message = '회원 탈퇴 신청이 완료 되었습니다.';

    } else if($_POST['action'] === 'mileageReport') {
        $userNo = $auth['no'];
        header("Location: /view/mileage/mileage_report.php?no=$userNo");
        exit();
    } else if($_POST['action'] === 'withdrawal') {
        header('Location: /view/mileage/mileage_drawal.php');
        exit();
    }

} catch (DatabaseException $e) {
    mysqli_rollback($db->conn);
    $e->setErrorMessages($e);
} catch (CustomException $e) {
    $e->setErrorMessages($e);
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
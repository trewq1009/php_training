<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\Session;

try {
    $preUrl = $_SERVER['HTTP_REFERER'];

    if(strtolower($_SERVER['REQUEST_METHOD']) === 'get') {
        throw new Exception('올바른 경로가 아닙니다.');
    }
    if(empty($_POST['userId']) || empty($_POST['userPw'])) {
        throw new Exception('정보를 입력해 주세요');
    }
    // pattern 체크
    if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $_POST['userId'])) {
        throw new Exception('아이디 형태가 올바르지 않습니다.');
    }

    $userData = (new Database)->findOne('tr_account', ['id'=>$_POST['userId'], 'status'=>'t']);
    if(!$userData) {
        throw new Exception('계정을 다시 확인해 주세요');
    }
    // Password 확인
    if(!password_verify($_POST['userPw'], $userData['password'])) {
        throw new Exception('패스워드가 일치하지 않습니다.');
    }
    // Email 미 인증 유저
    if($userData['email_status'] == 'INACTIVE') {
        throw new Exception('이메일 인증을 완료하지 않았습니다.');
    }
    // 로그인
    Session::setSession('auth', ['no'=>$userData['no'], 'name'=>$userData['name']]);
    header('Location: /');
    exit();


} catch (Exception $e) {
    $message = $e->getMessage();
}
?>

<section class="container">

    <div class="alert alert-danger">
        <?php echo $message ?>
    </div>
    <button type="button" onclick="btnEvent()" class="btn btn-secondary">이전</button>

</section>
</body>
<script>
    function btnEvent() {
        history.back();
    }
</script>
</html>

<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\MailSend;
use app\lib\Session;
use app\lib\exception\DatabaseException;
use app\lib\exception\CustomException;

try {
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new CustomException('잘못된 경로 입니다.');
    }

    // 가입을 위한 Validation 작업
    $postData = $_POST;
    $inputTagValue = ['userId'=>$postData['userId'], 'userEmail'=>$postData['userEmail'], 'userName'=>$postData['userName']];
    if (empty($postData['userId']) || empty($postData['userName']) || empty($postData['userPw']) || empty($postData['userPwC']) || empty($postData['userEmail'])) {
        throw new CustomException('필수 정보를 기입해 주세요');
    }

    // DB 연결 시작
    $db = new Database;

    $rebuildData = [];
    foreach ($postData as $key => $value) {
        $inputData = trim($value);
        $inputData = stripslashes($inputData);
        $inputData = htmlspecialchars($inputData);
        if($key == 'userId') {
            // pattern 체크
            if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $inputData)) {
                throw new CustomException('아이디 형태가 올바르지 않습니다.');
            }
            // DB 중복 확인
            if ($db->findOne('tr_account', ['id' => $inputData])) {
                throw new CustomException('중복된 아이디 입니다.');
            }
        }
        if($key == 'userPw') {
            if(strlen($inputData) < 8 || strlen($inputData) > 20) {
                throw new CustomException('비밀번호 형식에 맞지 않습니다.');
            }
            $inputData = password_hash($inputData, PASSWORD_BCRYPT);
        }
        if($key == 'userPwC') {
            if($postData['userPw'] !== $postData['userPwC']) {
                throw new CustomException('패스워드가 일치 하지 않습니다.');
            }
        }
        if($key == 'userName') {
            if (!preg_match("/^[가-힣]{9,}$/", $inputData)) {
                throw new CustomException('올바른 이름의 형태가 아닙니다.');
            }
        }
        if($key == 'userEmail') {
            if(!filter_var($inputData, FILTER_VALIDATE_EMAIL)) {
                throw new CustomException('이메일 형식에 맞지 않습니다.');
            }
            if ($db->findOne('tr_account', ['email' => $inputData])) {
                throw new CustomException('중복된 이메일 입니다.');
            }
        }
        $rebuildData[$key] = $inputData;
    }

    // 트렌 시작
    $db->pdo->beginTransaction();

    // 회원정보 저장
    $userNo = $db->save('tr_account', ['id'=>$rebuildData['userId'], 'password'=>$rebuildData['userPw'], 'name'=>$rebuildData['userName'], 'email'=>$rebuildData['userEmail']]);
    if(!$userNo) {
        throw new DatabaseException('회원가입 실패했습니다. 다시 시도해 주세요');
    }

    // 회원 마일리지 로그 테이블
    if(!$db->save('tr_mileage_log', ['user_no'=>$userNo, 'method'=>'join'])) {
        throw new DatabaseException('마일리지 로그 테이블 등록에 실패했습니다. 다시 시도해 주세요.');
    }

    // 회원 마일리지 테이블
    if(!$db->save('tr_mileage', ['user_no'=>$userNo])) {
        throw new DatabaseException('마일리지 로그 테이블 등록에 실패했습니다. 다시 시도해 주세요.');
    }

    // 인증 이메일 발송
    if((new MailSend)->sendRegisterEmail($rebuildData, $userNo) !== true) {
        throw new DatabaseException('이메일 발송에 오류가 있습니다. 관리자에게 문의 주세요.');
    }


    $db->pdo->commit();
    $message = '회원가입 신청 되었습니다. 이메일 인증을 통해 완료 해주세요.';

} catch (CustomException $e) {
    $e->setErrorMessages($e);
} catch (DatabaseException $e) {
    $db->pdo->rollBack();
    $e->setErrorMessages($e);
} catch (\Exception $e) {
    $message = $e->getMessage();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/view/error/error.php';
    die();
}

?>

<section class="container">

    <div class="alert alert-success">
        <?php echo $message ?>
    </div>
    <a herf="/" class="btn btn-secondary">홈</a>

</section>
</body>
</html>
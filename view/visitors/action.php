<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\exception\DatabaseException;

try {
    $preUrl = $_SERVER['HTTP_REFERER'];
    if(empty($_POST['content'])) {
        throw new Exception('글 내용을 입력해 주세요.');
    }

    $inputData = trim($_POST['content']);
    $inputData = stripslashes($inputData);
    $content = htmlspecialchars($inputData);

    if(!$auth) {
        if(empty($_POST['visitorsPassword'])) {
            throw new Exception('게스트는 글 등록시 패스워드 필수 입니다.');
        }
        $params = ['user_type'=>'g', 'visitors_password'=>password_hash($_POST['visitorsPassword'], PASSWORD_BCRYPT), 'content'=>$content];
    } else {
        $params = ['user_type'=>'m', 'user_no'=>$auth['no'], 'user_name'=>$auth['name'], 'content'=>$content];
    }

    $db = new Database;
    $db->pdo->beginTransaction();

    $boardNo = $db->save('tr_visitors_board', $params);
    if(!$boardNo) {
        throw new DatabaseException('방명록 등록에 실패했습니다.');
    }

    $db->pdo->commit();
    header("Location: $preUrl");
    exit();
    
} catch (DatabaseException $e) {
    $db->pdo->rollBack();
    $e->setErrorMessages($e);
} catch (Exception $e) {
    $message = $e->getMessage();
}
?>

<section class="container">

    <div class="alert alert-danger">
        <?php echo $message ?>
    </div>
    <a href="<?php echo $preUrl ?>" class="btn btn-secondary">이전</a>
    <a href="/" class="btn btn-secondary">홈</a>

</section>
</body>
</html>

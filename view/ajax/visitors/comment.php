<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use app\lib\Database;
use app\lib\MailSend;
use app\lib\exception\DatabaseException;

try {
    if(!session_id()) {
        session_start();
    }
    $auth = $_SESSION['auth'] ?? false;

    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        throw new Exception('잘못된 경로 입니다.');
    }
    if(empty($_POST['parent_no']) || empty($_POST['comment'])) {
        throw new Exception('필수 데이터가 없습니다.');
    }

    if(!$auth) {
        if(empty($_POST['comment_password'])) {
            throw new Exception('게스트 댓글은 패스워드가 필수 입니다.');
        }
        $params = ['user_type'=>'g', 'user_name'=>'게스트', 'visitors_password'=>password_hash($_POST['comment_password'], PASSWORD_BCRYPT), 'parents_no'=>$_POST['parent_no'], 'content'=>$_POST['comment']];
        $dbType = 'sssis';
    } else {
        $params = ['user_type'=>'m', 'user_no'=>$auth['no'], 'user_name'=>$auth['name'], 'parents_no'=>$_POST['parent_no'], 'content'=>$_POST['comment']];
        $dbType = 'sisis';
    }

    $db = new Database;
    mysqli_autocommit($db->conn, FALSE);

    $saveResult = $db->save('tr_visitors_board', $params, $dbType);
    if(!$saveResult) {
        throw new DatabaseException('댓글 등록에 실패했습니다.');
    }

    $parentsData = $db->findOne('tr_visitors_board', ['no'=>$_POST['parent_no'], 'status'=>'t'], 'is', 'FOR UPDATE');
    if(empty($parentsData)) {
        throw new DatabaseException('댓글 등록에 실패했습니다.');
    }

    $updateResult = $db->update('tr_visitors_board', ['comment_count'=>$parentsData['comment_count'] + 1], ['no'=>$_POST['parent_no']], 'ii');
    if(!$updateResult) {
        throw new DatabaseException('댓글 등록에 실패했습니다.');
    }

    // mail 발송
    if(!empty($parentsData['user_no'])) {
        $parentsUser = $db->findOne('tr_account', ['no'=>$parentsData['user_no']], 'i');
        $mailBool = (new MailSend)->sendCommentEmail($parentsUser);
    }

    mysqli_commit($db->conn);
    echo json_encode(['status'=>'success', 'message'=>'댓글 등록이 완료 되었습니다.']);

} catch (DatabaseException $e) {
    mysqli_rollback($db->conn);
    echo json_encode(['status'=>'fail', 'message'=>$e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status'=>'fail', 'message'=>$e->getMessage()]);
}
<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use app\lib\Database;
use app\lib\exception\DatabaseException;

try {

    if($_SERVER['REQUEST_METHOD'] === 'GET') {
        throw new Exception('잘못된 경로 입니다.');
    }
    if(empty($_POST['board_no']) || empty($_POST['text_data']) || empty($_POST['board_type'])) {
        throw new Exception('필수 정보가 없습니다.');
    }

    $db = new Database;
    $boardData = $db->findOne('tr_visitors_board', ['no'=>$_POST['board_no'], 'status'=>'t'], 'is');
    if(!$boardData) {
        throw new Exception('게시글이 존재하지 않습니다.');
    }

    if($_POST['board_type'] == 'g') {
        if(empty($_POST['password'])) {
            throw new Exception('비회원 게시글 수정에는 패스워드는 필수 입니다.');
        }
        if(!password_verify($_POST['password'], $boardData['visitors_password'])) {
            throw new Exception('패스워드를 다시 확인 해주세요');
        }
    } else {
        if (!session_id()) {
            session_start();
        }
        $auth = $_SESSION['auth'] ?? false;
        if ($boardData['user_no'] != $auth['no']) {
            throw new Exception('등록한 회원이 아닙니다.');
        }
    }
    mysqli_autocommit($db->conn, FALSE);

    $updateBool = $db->update('tr_visitors_board', ['no'=>$_POST['board_no']], ['content'=>$_POST['text_data']]);
    if(!$updateBool) {
        throw new DatabaseException('게시글 수정에 실패했습니다.');
    }

    mysqli_commit($db->conn);
    echo json_encode(['status'=>'success', 'message'=>'수정이 완료 되었습니다.']);
    die();

} catch (DatabaseException $e) {
    mysqli_rollback($db->conn);
    echo json_encode(['status'=>'fail', 'message'=>$e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status'=>'fail', 'message'=>$e->getMessage()]);
}
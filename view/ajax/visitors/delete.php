<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use app\lib\Database;
use app\lib\exception\DatabaseException;

try {
    if(empty($_POST['board_no']) || empty($_POST['board_type'])) {
        throw new Exception('글에 대한 정보가 없습니다.');
    }

    $db = new Database;

    if($_POST['board_type'] === 'g') {
        if(empty($_POST['password'])) {
            throw new Exception('패스워드는 필수 입니다.');
        }
        $boardData = $db->findOne('tr_visitors_board', ['no'=>$_POST['board_no']], 'FOR UPDATE');
        if(!$boardData) {
            throw new Exception('게시글에 대한 정보가 없습니다.');
        }
        if(!password_verify($_POST['password'], $boardData['visitors_password'])) {
            throw new Exception('패스워드가 틀렸습니다.');
        }

    } else {
        if(!session_id()) {
            session_start();
        }
        $auth = $_SESSION['auth'] ?? false;

        $boardData = $db->findOne('tr_visitors_board', ['no' => $_POST['board_no']], 'FOR UPDATE');
        if (!$boardData) {
            throw new Exception('게시글에 대한 정보가 없습니다.');
        }
        if ($boardData['user_no'] !== $auth['no']) {
            throw new Exception('등록한 회원이 아닙니다.');
        }

    }

    $db->pdo->beginTransaction();
    $result = $db->update('tr_visitors_board', ['no' => $_POST['board_no']], ['status' => 'f']);

    if (!$result) {
        throw new DatabaseException('게시글 삭제에 실패했습니다.');
    }

    if($boardData['parents_no'] != 0) {
        $parentsData = $db->findOne('tr_visitors_board', ['no'=>$boardData['parents_no']], 'FOR UPDATE');
        $parentsBool = $db->update('tr_visitors_board', ['no'=>$boardData['parents_no']], ['comment_count'=>$parentsData['comment_count'] - 1]);
        if(!$parentsBool) {
            throw new DatabaseException('작업에 실패하였습니다.');
        }
    }

    $db->pdo->commit();
    echo json_encode(['status' => 'success', 'message' => '성공했습니다.']);
    exit();

} catch (DatabaseException $e) {
    $db->pdo->rollBack();
    echo json_encode(['status'=>'fail', 'message'=>$e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['status'=>'fail', 'message'=>$e->getMessage()]);
}
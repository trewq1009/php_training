<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use app\lib\Database;

try {
    if(!session_id()) {
        session_start();
    }
    $auth = $_SESSION['auth'] ?? false;
    if(empty($_POST['board_num'])) {
        throw new Exception('댓글 불러오기에 실패했습니다.');
    }

    $db = new Database;
    $boardData = $db->findAll('tr_visitors_board', ['parents_no'=>$_POST['board_num'], 'status'=>'t']);

    $html = '';
    if(count($boardData) == 0) {
        echo json_encode(['status'=>'success', 'message'=>$html]);
        die();
    }

    foreach ($boardData as $key => $value) {
        $html .= "<li class='list-group-item'>
                    <div class='firstBox'>
                        <div data-board='{$value['no']}' data-user='{$value['user_no']}' data-type='{$value['user_type']}'>
                            <div>
                                <span>{$value['user_name']}</span>
                                <small>{$value['registration_date']}</small>
                                <small onclick='commentList(this)'>답글</small>
                            </div>
                        <div>";

        if($value['user_type'] == 'g') {
            $html .= '<input type="password" name="boardPassword" style="height: 1.25rem; width: 10rem;" placeholder="password">';
        }
        $html .=  "<small>수정</small>
                  <small onclick='deleteAction(this)'>삭제</small>
                  </div></div></div><div>
                  <p>{$value['content']}</p>
                  </div><div id='commentBox{$value['no']}' class='commentBox'><div id='commentBlock'><ul class='list-group'></ul></div>
                  <div class='input-group'><textarea class='form-control' aria-label='With textarea' id='comment{$value['no']}' placeholder='글을 입력해 주세요.'></textarea>
                  <span class='input-group-text'>";

        if(!$auth) {
            $html .= '<input type="password" name="boardPassword" style="height: 1.25rem; width: 10rem;" placeholder="password">';
        }

        $html .= "<button type='button' class='btn btn-secondary' onclick='commentEvent(this)' data-board='{$value['no']}'>등록</button></span></div></div></li>";
    }



    echo json_encode(['status'=>'success', 'message'=>$html]);

} catch (Exception $e) {
    echo json_encode(['status'=>'fail', 'message'=>$e->getMessage()]);
}
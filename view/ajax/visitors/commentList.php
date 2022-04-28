<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use app\lib\Database;

try {
    if(!session_id()) {
        session_start();
    }
    $auth = $_SESSION['auth'] ?? false;
    if(empty($_POST['board_num']) || empty($_POST['page'])) {
        throw new Exception('댓글 불러오기에 실패했습니다.');
    }

    $db = new Database;
    $totalData = $db->list('tr_visitors_board', $_POST['page'], ['parents_no'=>$_POST['board_num'], 'status'=>'t']);

    $boardData = $totalData['listData'];

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
            $html .= "<input type='password' id='boardPassword{$value["no"]}' name='boardPassword' style='height: 1.25rem; width: 10rem;' placeholder='password'>";
        }
        $html .=  "<small onclick='updateHtml(this)'>수정</small>
                  <small onclick='deleteAction(this)'>삭제</small>
                  </div></div></div><div id='contentBox{$value["no"]}'>
                  <p>{$value['content']}</p>
                  </div><div id='commentBox{$value['no']}' class='commentBox'><div id='commentBlock'><ul class='list-group' data-board='{$value['no']}'></ul></div>
                  <div class='input-group'><textarea class='form-control' aria-label='With textarea' id='comment{$value['no']}' placeholder='글을 입력해 주세요.'></textarea>
                  <span class='input-group-text'>";

        if(!$auth) {
            $html .= '<input type="password" name="boardPassword" style="height: 1.25rem; width: 10rem;" placeholder="password">';
        }

        $html .= "<button type='button' class='btn btn-secondary' onclick='commentEvent(this)' data-board='{$value['no']}'>등록</button></span></div></div></li>";
    }
    if(ceil($totalData['total'] / $totalData['resultOnPage']) > 0) {
        if($_POST['page'] > 1) {
            $pev = $_POST['page'] - 1;
            $html .= "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='commentList(this, $pev)'>Previous</a></li>";
        }
        if($_POST['page'] < ceil($totalData['total'] / $totalData['resultOnPage'])) {
            $next = $_POST['page'] + 1;
            $html .= "<li class='page-item'><a class='page-link' href='javascript:void(0)' onclick='commentList(this, $next)'>Next</a></li>";
        }
    }


    echo json_encode(['status'=>'success', 'message'=>$html]);

} catch (Exception $e) {
    echo json_encode(['status'=>'fail', 'message'=>$e->getMessage()]);
}
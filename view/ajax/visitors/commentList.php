<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use app\lib\Database;

try {
    if(empty($_POST['board_num'])) {
        throw new Exception('댓글 불러오기에 실패했습니다.');
    }

    $db = new Database;
    $boardData = $db->findAll('tr_visitors_board', ['parents_no'=>$_POST['board_num'], 'status'=>'t']);

    if(count($boardData) == 0) {
        throw new Exception('불러올 데이터가 없습니다.');
    }

    $html = '';
    foreach ($boardData as $key => $value) {
        $html .= "<li class='list-group-item'><div class='firstBox'><div><div><div>
                  <span>{$value['user_name']}</span>
                  <small>{$value['registration_date']}</small>
                  </div><div>
                  <small>수정</small>
                  <small>삭제</small>
                  </div></div></div></div><div>
                  <span>{$value['content']}</span>
                  </div></li>";
    }



    echo json_encode(['status'=>'success', 'message'=>$html]);

} catch (Exception $e) {
    echo json_encode(['status'=>'fail', 'message'=>$e->getMessage()]);
}
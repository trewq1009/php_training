<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\Field;

try {
    $page = $_GET['page'] ?? 1;
    $totalData = (new Database)->list('tr_visitors_board', $page, ['status'=>'t', 'parents_no'=>0]);
    $listData = $totalData['listData'];
    unset($totalData['listData']);
    $totalData['page'] = $page;
    $btnHtml = Field::listBtn($totalData);

} catch(Exception $e) {
    $message = $e->getMessage();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/view/error/error.php';
    die();
}

?>
<section class="container">
    <form method="POST" action='<?php echo htmlspecialchars('./action.php');?>' id="methodForm">
        <div>
            <label for="content" class="form-label">방명록</label>
            <div class="input-group">
                <textarea class="form-control mainArea" aria-label="With textarea" id='content' name="content"placeholder="글을 입력해 주세요."></textarea>
                <span class="input-group-text">
                    <button type="button" class="btn btn-secondary" onclick='btnEvent()' name="btn" value="insert">등록</button>
                </span>
            </div>

            <?php if(!$auth): ?>
            <div class="input-group mb-3">
                <span class="input-group-text" id="basic-addon1">게스트 패스워드</span>
                <input type="password" class="form-control" id="visitorsPassword" name="visitorsPassword" required>
            </div>
            <?php endif; ?>
            
        </div>
    </form>

    <div style="margin: 1rem 0 0 0;">
        <ul class="list-group">
            <?php foreach($listData as $key => $value): ?>
                <li class="list-group-item" data-board="<?php echo $value['no']; ?>">
                    <div class="firstBox">
                        <div data-board="<?php echo $value['no']; ?>" data-user="<?php echo $value['user_no']; ?>" data-type="<?php echo $value['user_type']; ?>">
                            <span><?php echo $value['user_name']; ?></span>
                            <div>
                                <?php if($value['user_type'] == 'g'): ?>
                                <input type="password" id="boardPassword<?php echo $value['no']; ?>" name="boardPassword" style="height: 1.25rem; width: 10rem;" placeholder="password">
                                <?php endif; ?>
                                <small onclick="updateHtml(this)">수정</small>
                                <small onclick="deleteAction(this)">삭제</small>
                            </div>
                        </div>
                        <small><?php echo $value['registration_date']; ?></small>
                    </div>

                    <div id="contentBox<?php echo $value['no']; ?>">
                        <p><?php echo $value['content']; ?></p>
                    </div>

                    <div>
                        <a href="javascript:void(0);" onclick="commentList(this)" style="color: red;">댓글 <?php echo $value['comment_count']; ?></a>
                    </div>

                    <div id="commentBox<?php echo $value['no']; ?>" class="commentBox">
                        <div id="commentBlock">
                            <ul class="list-group" data-board="<?php echo $value['no']; ?>"></ul>
                        </div>

                        <div class="input-group">
                            <textarea class="form-control" aria-label="With textarea" id="comment<?php echo $value['no']; ?>" placeholder="글을 입력해 주세요."></textarea>
                            <span class="input-group-text">
                                <?php if(!$auth): ?>
                                <input type="password" name="boardPassword" style="height: 1.25rem; width: 10rem;" placeholder="password">
                                <?php endif; ?>
                                <button type="button" class="btn btn-secondary" onclick='commentEvent(this)' data-board="<?php echo $value['no']; ?>">등록</button>
                            </span>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php echo $btnHtml; ?>

</section>
</body>
<script>
    // 방명록 등록
    function btnEvent() {
        const user_info = <?php echo json_encode($auth) ?>;
        if(!user_info) {
            const result = window.confirm('비회원으로 글을 등록 하시겠습니까?');
            if(!result) {
                return;
            }
            // 비회원 글 등록
            document.querySelector('#methodForm').submit();
            return;
        }
        // 회원 글 등록
        document.querySelector('#methodForm').submit();
    };

    // 댓글 불러오기
    function commentList(event, page = 1) {
        // 댓글 카운트 확인 후 없으면 댓글이 없습니다.
        // 있으면 댓글 보여주기
        const board_num = event.parentElement.parentElement.dataset.board;

        $.ajax({
            type : 'POST',
            url : '/view/ajax/visitors/commentList.php',
            data : {
                board_num : board_num,
                page : page
            },
            success : result => {
                const re_data = JSON.parse(result);
                if(re_data.status === 'success') {
                    const comment_box = document.querySelector('#commentBox'+board_num).firstElementChild.firstElementChild;
                    comment_box.innerHTML = re_data.message;

                    const comment_section = document.querySelector('#commentBox'+board_num);
                    comment_section.style.display = 'block';
                } else {
                    window.alert('댓글 불러오기 에러');
                    console.log(re_data.message);
                }
            },error : e => {
                console.log(e);
                window.alert('댓글을 불러오는중 에러가 발생했습니다.');
            }
        });
    }

    // 댓글 등록
    function commentEvent(event) {
        const user_info = <?php echo json_encode($auth) ?>;
        const parent_board_num = event.dataset.board;
        const comment = document.getElementById('comment'+parent_board_num).value;

        if(!user_info) {
            const result = window.confirm('비회원으로 댓글을 등록 하시겠습니까?');
            if(!result) {
                return;
            }
            // 비회원 댓글 등록
            const password = event.previousElementSibling.value;
            if(password === '') {
                window.alert('비회원은 패스워드가 필수 입니다.');
                console.log(password);
                return;
            }
            $.ajax({
                type: 'POST',
                url: '/view/ajax/visitors/comment.php',
                data: {
                    parent_no: parent_board_num,
                    comment: comment,
                    comment_password: password
                },
                success: result => {
                    const re_data = JSON.parse(result);
                    if (re_data.status === 'success') {
                        window.alert(re_data.message);
                        window.location.reload();
                    } else {
                        window.alert(re_data.message);
                    }
                }, error: e => {
                    console.log(e);
                    window.alert('에러가 발생했습니다.')
                }
            });

        } else {
            // 회원 댓글 등록
            $.ajax({
                type: 'POST',
                url: '/view/ajax/visitors/comment.php',
                data: {
                    parent_no: parent_board_num,
                    comment: comment
                },
                success: result => {
                    const re_data = JSON.parse(result);
                    if (re_data.status === 'success') {
                        window.alert(re_data.message);
                        window.location.reload();
                    } else {
                        window.alert(re_data.message);
                    }
                }, error: e => {
                    console.log(e);
                    window.alert('에러가 발생했습니다.')
                }
            });
        }
    }

    function deleteAction(event) {
        const user_info = <?php echo json_encode($auth) ?>;
        const board_no = event.parentElement.parentElement.dataset.board;
        const type = event.parentElement.parentElement.dataset.type;

        if(type === 'm') {
            if(user_info === false) {
                window.alert('로그인 후 이용해 주세요.');
                return;
            }

            const result = window.confirm('삭제 하시겠습니까?');
            if(!result) {
                return;
            }
            $.ajax({
                type : 'POST',
                url : '/view/ajax/visitors/delete.php',
                data : {
                    board_no: board_no,
                    board_type : type
                },
                success : result => {
                    const re_data = JSON.parse(result);
                    if(re_data.status === 'success') {
                        window.alert(re_data.message);
                        window.location.reload();
                    } else {
                        window.alert(re_data.message);
                    }
                },error : e => {
                    console.log(e);
                    window.alert('에러가 발생했습니다.')
                }
            });
        } else {
            const password = event.previousElementSibling.previousElementSibling.value;
            if(password === '') {
                window.alert('패스워드를 입력해 주세요');
                return;
            }
            $.ajax({
                type : 'POST',
                url : '/view/ajax/visitors/delete.php',
                data : {
                    board_no: board_no,
                    password: password,
                    board_type : type
                },
                success : result => {
                    const re_data = JSON.parse(result);
                    if(re_data.status === 'success') {
                        window.alert(re_data.message);
                        window.location.reload();
                    } else {
                        window.alert(re_data.message);
                    }
                },error : e => {
                    console.log(e);
                    window.alert('에러가 발생했습니다.')
                }
            });
        }
    };

    function updateHtml(event) {
        const board_no = event.parentElement.parentElement.dataset.board;
        const board_type = event.parentElement.parentElement.dataset.type;
        const user_info = <?php echo json_encode($auth) ?>;
        if(board_type === 'm' && user_info === false) {
            window.alert('로그인 후 이용해 주세요.');
            return;
        }

        const textareaBox = document.querySelector('#contentBox'+board_no);
        const inner_text = textareaBox.firstElementChild.innerHTML;
        textareaBox.innerHTML = '<div class="input-group"><textarea class="form-control" id="updateText'+board_no+'" aria-label="With textarea" placeholder="글을 입력해 주세요."></textarea>' +
            '<span class="input-group-text"><button type="button" onclick="updateAction(this)" data-type="'+board_type+'" data-board="'+board_no+'" class="btn btn-secondary">수정</button></span></div>';

        textareaBox.firstElementChild.firstElementChild.innerHTML = inner_text;
    }

    function updateAction(event) {
        const board_no = event.dataset.board;
        const board_type = event.dataset.type;
        const text_data = document.querySelector('#updateText'+board_no).value;

        if(board_type === 'g') {
            const password = document.querySelector('#boardPassword'+board_no).value;
            if(password === '') {
                window.alert('비회원 게시글은 패스워드가 필수 입니다.');
                return;
            }
            $.ajax({
                type: 'POST',
                url: '/view/ajax/visitors/update.php',
                data: {
                    board_no: board_no,
                    text_data: text_data,
                    board_type: board_type,
                    password: password
                },
                success: result => {
                    const re_data = JSON.parse(result);
                    if (re_data.status === 'success') {
                        window.alert(re_data.message);
                        window.location.reload();
                    } else {
                        window.alert(re_data.message);
                        window.location.reload();
                    }
                }, error: e => {
                    console.log(e);
                    window.alert('에러가 발생했습니다.')
                }
            });

        } else {
            $.ajax({
                type: 'POST',
                url: '/view/ajax/visitors/update.php',
                data: {
                    board_no: board_no,
                    text_data: text_data,
                    board_type: board_type
                },
                success: result => {
                    const re_data = JSON.parse(result);
                    if (re_data.status === 'success') {
                        window.alert(re_data.message);
                        window.location.reload();
                    } else {
                        window.alert(re_data.message);
                        window.location.reload();
                    }
                }, error: e => {
                    console.log(e);
                    window.alert('에러가 발생했습니다.')
                }
            });
        }
    }

</script>
</html>

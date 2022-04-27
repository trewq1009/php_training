<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;

try {
    $listData = (new Database)->findAll('tr_visitors_board', ['status'=>'t']);


} catch(Exception $e) {

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
                        <div>
                            <div>
                                <span><?php echo $value['user_name']; ?></span>
                                <?php if(!$auth): ?>
                                    <?php if($value['user_type'] == 'g'): ?>
                                    <div>
                                        <small>수정</small>
                                        <small>삭제</small>
                                    </div>
                                    <?php endif; ?>
                                <?php elseif($auth['no'] == $value['user_no']): ?>
                                    <div>
                                        <small>수정</small>
                                        <small>삭제</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <small><?php echo $value['registration_date']; ?></small>
                        </div>
                    </div>

                    <div>
                        <p><?php echo $value['content']; ?></p>
                    </div>

                    <div>
                        <a href="javascript:void(0);" onclick="commentList(this)" data-count="<?php echo $value['comment_count']; ?>" style="color: red;">댓글 <?php echo $value['comment_count']; ?></a>
                    </div>

                    <div id="commentBox<?php echo $value['no']; ?>" class="commentBox">
                        <div id="commentBlock">
                            <ul class="list-group"></ul>
                        </div>

                        <div class="input-group">
                            <textarea class="form-control" aria-label="With textarea" id="comment<?php echo $value['no']; ?>" placeholder="글을 입력해 주세요."></textarea>
                            <span class="input-group-text">
                                <button type="button" class="btn btn-secondary" onclick='commentEvent(this)' data-board="<?php echo $value['no']; ?>">등록</button>
                            </span>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

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
    function commentList(event) {
        // 댓글 카운트 확인 후 없으면 댓글이 없습니다.
        // 있으면 댓글 보여주기
        console.log('현재 댓글 갯수 : ' + event.dataset.count);
        const count = event.dataset.count;
        console.log('댓글 확인할 보드 NO : ' + event.parentElement.parentElement.dataset.board);
        const board_num = event.parentElement.parentElement.dataset.board;
        
        if(count > 0) {
            console.log('댓글 존재 ajax 작업');
            $.ajax({
                type : 'POST',
                url : '/view/ajax/visitors/commentList.php',
                data : {
                    board_num : board_num
                },
                success : function(result){
                    const re_data = JSON.parse(result);
                    if(re_data.status === 'success') {
                        const comment_box = document.querySelector('#commentBox'+board_num).firstElementChild.firstElementChild;
                        comment_box.innerHTML = re_data.message;
                    } else {
                        console.log(re_data);
                        window.alert('댓글 불러오기 에러');
                    }
                },error : function(e){
                    console.log(e);
                    window.alert('댓글을 불러오는중 에러가 발생했습니다.');
                }
            });
            const comment_section = document.querySelector('#commentBox'+board_num);
            comment_section.style.display = 'block';
        } else {
            console.log('댓글 없음 html 작업만');
            const comment_section = document.querySelector('#commentBox'+board_num);
            comment_section.style.display = 'block';
        }

    }

    // 댓글 등록
    function commentEvent(event) {
        const user_info = <?php echo json_encode($auth) ?>;
        if(!user_info) {
            const result = window.confirm('비회원으로 댓글을 등록 하시겠습니까?');
            if(!result) {
                return;
            }
            // 비회원 댓글 등록
            return;
        }
        // 회원 댓글 등록
        const parent_board_num = event.dataset.board;
        const comment = document.getElementById('comment'+parent_board_num).value;
        
        $.ajax({
            type : 'POST',
            url : '/view/ajax/visitors/comment.php',
            data : {
                parent_no: parent_board_num,
                comment : comment
            },
            success : function(result){
                console.log(result);
                const re_data = JSON.parse(result);
                console.log(re_data);
                if(re_data.status === 'success') {
                    window.alert(re_data.message);
                    window.location.reload();
                } else {
                    window.alert(re_data.message);
                }
            },error : function(e){
                console.log(e);
                window.alert('에러가 발생했습니다.')
            }
        });
    }
</script>
</html>

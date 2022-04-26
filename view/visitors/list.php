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
                <textarea class="form-control" aria-label="With textarea" id='content' name="content" style="height: 6.25em; resize: none;" placeholder="글을 입력해 주세요."></textarea>
                <span class="input-group-text">
                    <button type="button" class="btn btn-secondary" onclick='btnEvent(this)' name="btn" value="insert">등록</button>
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
                    <div style="margin: 0 0 1rem 0;">
                        <div style="display: flex; flex-direction: column;">
                            <div style="display: flex; align-items: center; justify-content: space-between;">
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

                    <div style="background-color: rgba(211,211,211); display: none; padding: 1rem 1rem;" id="comment<?php echo $value['no']; ?>">
                        <div id="commentBlock"></div>
                        <div class="input-group">
                            <textarea class="form-control" aria-label="With textarea" id='content' name="content" style="height: 3.25em; resize: none;" placeholder="글을 입력해 주세요."></textarea>
                            <span class="input-group-text">
                                <button type="button" class="btn btn-secondary" onclick='commentEvent(this)' name="btn" value="insert">등록</button>
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
    function btnEvent(event) {
        const comment = document.querySelector('#content').value;
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

    function commentList(event) {
        // 댓글 카운트 확인 후 없으면 댓글이 없습니다.
        // 있으면 댓글 보여주기
        console.log('현재 댓글 갯수 : ' + event.dataset.count);
        const count = event.dataset.count;
        console.log('댓글 확인할 보드 NO : ' + event.parentElement.parentElement.dataset.board);
        const board_num = event.parentElement.parentElement.dataset.board;
        
        if(count > 0) {
            console.log('댓글 존재 ajax 작업');
        } else {
            console.log('댓글 없음 html 작업만');
            const comment_section = document.querySelector('#comment'+board_num);
            comment_section.style.display = 'block';
        }

    }

    function commentEvent() {
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
    }
</script>
</html>

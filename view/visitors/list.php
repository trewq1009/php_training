<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

?>
<section class="container">

    <div>
        <label for="comment" class="form-label">방명록</label>
        <div class="input-group">
            <textarea class="form-control" aria-label="With textarea" id='comment' name="comment" style="height: 6.25em; resize: none;" placeholder="글을 입력해 주세요."></textarea>
            <span class="input-group-text">
                <button type="button" class="btn btn-secondary" onclick='btnEvent(this)' name="btn" value="insert">등록</button>
            </span>
        </div>
    </div>

    <div style="margin: 1rem 0 0 0;">
        <ul class="list-group">
            <li class="list-group-item">
                <div style="margin: 0 0 1rem 0;">
                    <div style="display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span>김유저</span>
                            <div>
                                <small>수정</small>
                                <small>삭제</small>
                            </div>
                        </div>
                        <small>2022-04-26 12:12:12</small>
                    </div>
                </div>

                <div>
                    <p>방명록 컨텐츠</p>
                </div>

                <div>
                    <a href="javascript:void(0);" onclick="commentList()" style="color: red;">댓글 1</a>
                </div>

                <div style="background-color: gray;">
                    댓글 리스트 영역
                </div>
            </li>
        </ul>
    </div>

</section>
</body>
<script>
    function btnEvent(event) {
        const comment = document.querySelector('#comment').value;
        let user_info = "<?php echo $_SESSION['auth'] ?? false; ?>";
        if(!user_info) {
            const result = window.confirm('비회원으로 글을 등록 하시겠습니까?');
            if(!result) {
                return;
            }
            // 비회원 글 등록

            return;
        }
        // 회원 글 등록



        // ajax 를 이용해 게시글 등록을 비동기로 이루어 진다.
        // 글 등록이 완료시에 성공 했으면 새로고침
        // 답글에 경우는 버튼 클릭시 ajax로 불러오기
        // 불러온 데이터 띄우고
        // 답글 등록시 위와 다르게 새로고침 없이 스크립트로 추가하기
    };

    function commentList() {
        // 댓글 카운트 확인 후 없으면 댓글이 없습니다.
        // 있으면 댓글 보여주기
        console.log(1);
    }
</script>
</html>

<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

?>
<section class="container">

    <div>
        <div class="input-group">
            <textarea class="form-control" aria-label="With textarea" id='comment' name="comment" style="height: 6.25em; resize: none;"></textarea>
            <span class="input-group-text">
                <button type="button" class="btn btn-secondary" onclick='btnEvent(this)' name="btn" value="insert">등록</button>
            </span>
        </div>
    </div>

</section>
</body>
<script>
    function btnEvent(event) {
        const comment = document.querySelector('#comment').value;
        // ajax 를 이용해 게시글 등록을 비동기로 이루어 진다.
        // 글 등록이 완료시 두가지 방법이 있다.
        // 1. 바로 새로 등록한 글이 최 상단에 보이게 작업
        // 2. 새로고침 하여 새로 로딩 하는 방법
        // 답글에 경우도 ajax 를 이용하여 등록을 한다.
        // 위와 마찬가지로 방식을 정해야함
        // 답글 보기에 경우도 생각을 하고 진행 해야한다.
    };
</script>
</html>

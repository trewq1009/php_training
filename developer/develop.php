개발 일지

수정 사항
1. 트랜잭션 위치 변경
현재 트랜잭션을 실행하는 위치는 모델이 이용하는 클래스 단 위치
만약 여러가지 로직 수행시 여러번 호출 함 과 동시에 선 로직 완료 후 이후 로직 실패시
선 테이블은 커밋 된 상황이고 후 테이블은 롤백 된 상황이다
이러한 이유로 위치를 변경해서 사용 해야한다.

2. try ~ catch 문 사용
try ~ catch 이하 트라이문 이라 명하며 이것을 사용 할 때
Exception 클래스를 만들어 예외 처리 로직을 만들어 간다

3. 세션 사용 할 때 (확인)
세션 함수 중에 isSet 혹은 다른 함수중 static 으로 설계가 되어서 매번 호출 방식이 다르다
함수에 static 을 제거한 후 construct 들어가 있는 함수 호출 구문을 정적 메소드로 활용 한다.

4. 유틸 클래스에 getMethod (확인)
단순 뷰 화면에서 처리 하도록 한다 데이터가 많으면 괜한 메모리 소모가 이루어 진다.

5. 3번 과 마찬가지로 생성자 만들때 사용할 클래스들을 생성 말고 정적으로 사용을 한다

6. 모든 데이터들에 관해 유효성 검사를 실시한다.
바로 DB 접근이 아니라 값이 존재 하는지 아이디는 숫자만 존재 하는지 이러한 규칙을 먼저 validation 을 진행 후
DB에 접근하여 또 다른 validation 을 진행 하도록 한다.

7. 모든 페이지에서 로직을 먼저 만든다
먼저 클래스화를 하지 말고 로직을 먼저 만들고 필요한 영역에 관해서만 클래스화를 시킨다.
안그러면 나중에 로직이 꼬인다

8. 리스트 페이지
리스트는 뷰에서 사용 해도 될 것 같다 다만 버튼 같은 경우는 호출을 하여 사용한다.

9. DB 컬럼명은 풀네임이 좋다 현재 컬럼명이 짧다
또한 각 데이터별 주어지는 타입과 크기가 낭비! 각각 컬럼에 알맞는 크기를 넣어 주도록 한다.
ex: status 가 전부 5글자다 하면 char(5)이런식으로, 패스워드 영역은 암호화를 진행하다 보면
얼만큼의 크기인지 모르니 최대한 크게 잡아준다?

10. 인젝션?

가장 중요!!
현재 try catch 영역에 Exception 을 클래스화 해서 커스텀을 진행 하도록 한다.


### 22.04.13 ###
1. 상단 마일리지는 포기한다
계속 DB에 접근하여 표기를 할 방법말고는 스케줄러를 사용 혹은 새로고침 영역을 만들어 사용할 수 있지만
이러한 개발 비용이 너무 높다

2. 마일리지 표기는 프로필과 만에하나 생겨날 거래 창에서만 띄운다
필요할 때에 DB 접속을 통하여 표기한다.




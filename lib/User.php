<?php
namespace app\lib;

class User
{
    public string $userId = '';
    public string $userName = '';
    public string $userEmail = '';
    protected $db;
    protected $util;
    protected $session;
    protected $mail;


    public function __construct()
    {
        $this->db = new Database;
        $this->util = new Utils;
        $this->session = new Session;
        $this->mail = new MailSend;
    }


    /**
     * @return string[]
     * [InputName => DB TableName]
     */
    private function rule()
    {
        return [
            'userId' => 'id',
            'userPw' => 'pw',
            'userEmail' => 'email',
            'userName' => 'name'
        ];
    }


    private function getTableName()
    {
        return 'tr_account';
    }


    public function register($postData)
    {
        $this->userId = $postData['userId'];
        $this->userName = $postData['userName'];
        $this->userEmail = $postData['userEmail'];

        // 가입을 위한 Validation 작업
        $userData = $this->util->registedValidation($this->getTableName(), $this->rule(), $postData);
        if(count($userData) <= 1) {
            $this->session->setSession('error', $userData['error']);
            return;
        }

        // DB 연결 & 저장
        if(!$this->db->save($this->getTableName(), $this->rule(), $userData)) {
            $this->session->setSession('error', '회원가입에 실패 하였습니다. 다시 시도해 주세요.');
            return;
        }

        // 성공 하면 이메일 인증 함수 실행
        // 그리고 메인 화면 리다이렉트 하며 메시지 전송?

        if(!$this->mail->sendRegisterEmail($userData)) {
            $this->session->setSession('error', '이메일 발송에 오류가 있습니다. 관리자에게 문의 주세요.');
            header('Location: /');
            exit();
        }

        $this->session->setSession('success', '회원가입 신청 되었습니다. 이메일 인증을 통해 완료 해주세요.');
        header('Location: /');
        exit();
    }


    public function logIn($postData)
    {
        $this->userId = $postData['userId'];

        $userData = $this->db->findOne($this->getTableName(), ['id', 'status'], ['id' => $this->userId, 'status' => 'ALIVE']);
        if(!$userData) {
            $this->session->setSession('error', '계정을 다시 확인 해주세요.');
            return;
        }
        // 비밀번호 인증
        if(!password_verify($postData['userPw'], $userData['pw'])) {
            $this->session->setSession('error', '비밀번호가 일치하지 않습니다.');
            return;
        }
        // Email 미 인증 유저 Validation
        if($userData['email_status'] == 'INACTIVE') {
            $this->session->setSession('error', '이메일 인증을 완료하지 않았습니다. 메일 인증을 해주세요.');
            return;
        }

        $this->session->setSession('auth', $userData);
        header('Location: /');
        exit();
    }


    public function logOut()
    {
        $this->session->removeSession('auth');
        header('Location: /');
        exit();
    }


    public function update($updateData)
    {
        // 로그인 중인가 다시 검증
        if(!$this->session->isSet('auth')) {
            $this->session->setSession('error', '로그인이 해제되었습니다.');
            header('Location: /');
            exit();
        }

        // 비밀번호 변경 안하면
        if(empty($updateData['userPw'])) {
            $updateData['userPw'] = $this->session->isSet('auth')['pw'];
        } else {
            $updateData['userPw'] = password_hash($updateData['userPw'], PASSWORD_BCRYPT);
        }
        
        // Validation 작업 추가 해야함
        // 재사용 할 수 있도록 로직 생각 해야함



        // update 성공하면 true 반환
        if(!$this->db->update($this->getTableName(), $this->rule(), ['no' => $_SESSION['auth']['no']],$updateData)) {
            $this->session->setSession('error', '정보 수정에 실패하였습니다.');
            header('Location: /');
            exit();
        }

        $updateUserData = $this->db->findOne($this->getTableName(), ['no' => 'no'], ['no' => $_SESSION['auth']['no']]);
        $this->session->setSession('auth', $updateUserData);
        $this->session->setSession('success', '정보 수정 완료 되었습니다.');
        header('Location: /');
        exit();
    }


    public function delete($deleteData)
    {
        // update 성공하면 true 반환
        if(!$this->db->update($this->getTableName(), ['status' => 'status'], ['no' => $deleteData['no']], ['status' => 'AWAIT'])) {
            $this->session->setSession('error', '회원 탈퇴 신청이 실패 했습니다.');
            header('Location: /');
            exit();
        }
        $this->session->setSession('success', '회원 탈퇴 신청이 완료 되었습니다.');
        $this->session->removeSession('auth');
        header('Location: /');
        exit();
    }


    // 이메일 인증
    public function emailAuthentication($getData)
    {
        if(!isset($getData['training'])) {
            $this->session->setSession('error', 'URL 이 올바르지 않습니다.');
            header('Location: /');
            exit();
        }

        $userData = $this->db->findOne($this->getTableName(), ['id', 'status'], ['id' => $getData['training'], 'status' => 'ALIVE']);
        if(!$userData) {
            $this->session->setSession('error', '올바른 회원이 아닙니다.');
            header('Location: /');
            exit();
        }

        if(!$this->db->update($this->getTableName(), ['email_status' => 'email_status'], ['id' => $userData['id']], ['email_status' => 'ACTIVE'])) {
            $this->session->setSession('error', '인증에 문제가 발생했습니다. 관리자에게 문의 주세요.');
            header('Location: /');
            exit();
        }
        $this->session->setSession('success', '이메일 인증이 완료 되었습니다. 로그인 해주세요.');
        header('Location: /');
        exit();
    }

}
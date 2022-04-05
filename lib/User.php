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
    private function rule() {
        return [
            'userId' => 'id',
            'userPw' => 'pw',
            'userEmail' => 'email',
            'userName' => 'name'
        ];
    }


    private function getTableName() {
        return 'tr_account';
    }


    public function register($postData) {
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


    public function logIn($postData) {
        $this->userId = $postData['userId'];

        $userData = $this->db->findOne($this->getTableName(), ['id', 'status'], ['id' => $this->userId, 'status' => 'ALIVE']);
        if(!$userData) {
            $this->session->setSession('error', '계정을 다시 확인 해주세요.');
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


    public function logOut() {
        $this->session->removeSession('auth');
        header('Location: /');
        exit();
    }


}
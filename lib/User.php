<?php

namespace app\lib;


class User
{
    public string $userId = '';
    public string $userName = '';
    public string $userEmail = '';
    public $db;
    public $util;
    public $session;


    public function __construct()
    {
        $this->db = new Database;
        $this->util = new Utils;
        $this->session = new Session;
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


    public function register($post) {
        $this->userId = $post['userId'];
        $this->userName = $post['userName'];
        $this->userEmail = $post['userEmail'];


        $userData = $this->util->registedValidation($this->getTableName(), $this->rule(), $post);
        if(count($userData) <= 1) {
            $this->session->setSession('error', $userData['error']);
            return;
        }


        // DB 연결 & 저장
        if($this->db->save($this->getTableName(), $this->rule(), $userData)) {
            $this->session->setSession('success', '회원가입 신청 되었습니다. 이메일 인증을 통해 완료 해주세요.');
            header('Location: /');
            exit();
        } else {
            throw new \Exception('Test중 다시 시도하세요');
        }

        // 성공 하면 이메일 인증 함수 실행

        // 그리고 메인 화면 리다이렉트 하며 메시지 전송?
    }






}
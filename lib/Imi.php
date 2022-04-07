<?php

namespace app\lib;



class Imi
{
    public string $id;
    protected $db;
    protected $util;
    protected $session;

    public function __construct() {
        $this->db = new Database;
        $this->util = new Utils;
        $this->session = new Session;
    }


    private function rule() {
        return [
            'imiId' => 'id',
            'imiPw' => 'pw',
        ];
    }


    private function getTableName() {
        return 'tr_account_admin';
    }


    public function login($postData) {
        $this->id = $postData['imiId'];

        foreach ($this->rule() as $key => $value) {
            if(empty($postData[$key])) {
                $this->session->setSession('error', "$value 를 입력해 주세요");
                return;
            }
        }

        $userData = $this->db->findOne($this->getTableName(), ['id', 'status'], ['id' => $this->id, 'status' => 'ALIVE']);
        if(!$userData) {
            $this->session->setSession('error', '계정을 다시 확인 해주세요.');
            return;
        }

        // 비밀번호 인증
        if(!password_verify($postData['imiPw'], $userData['pw'])) {
            $this->session->setSession('error', '비밀번호가 일치하지 않습니다.');
            return;
        }

        $this->session->setSession('auth', $userData);
        header('Location: /view/imi/admin.php');
        exit();
    }


}
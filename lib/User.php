<?php

namespace app\lib;


class User
{
    public string $userId = '';
    public string $userName = '';
    public string $userEmail = '';


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


        $userData = (new Utils)->validation($this->rule(), $post);
        if(gettype($userData) !== 'array') {
            var_dump($userData);
        }
        // DB 연결
        if((new Database)->save($this->getTableName(), $this->rule(), $userData)) {
            (new Session)->setSession('userData', ['test'=>'test', 't1'=> '우승']);
        } else {
            throw new \Exception('Test중 다시 시도하세요');
        }

        // 성공 하면 이메일 인증 함수 실행

        // 그리고 메인 화면 리다이렉트 하며 메시지 전송?
    }






}
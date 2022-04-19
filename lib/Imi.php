<?php
namespace app\lib;

use Exception;

class Imi
{
    private function userRule()
    {
        return [
          'userId' => 'id',
          'userName' => 'name',
          'userEmail' => 'email',
          'userEmailStatus' => 'email_status',
          'userStatus' => 'status'
        ];
    }

    public function login($postData)
    {
        try {
            if(empty($postData['imiId']) || empty($postData['imiPw'])) {
                throw new Exception('입력값을 확인 해주세요.');
            }

            $userData = (new Database)->findOne('tr_account_admin', ['id', 'status'], ['id' => $postData['imiId'], 'status' => 'ALIVE']);
            if(!$userData) {
                throw new Exception('계정을 다시 확인 해주세요.');
            }

            if(!password_verify($postData['imiPw'], $userData['password'])) {
                throw new Exception('패스워드를 다시 확인 해주세요.');
            }


            (new Session)->setSession('auth', $userData);
            header('Location: /view/admin/admin.php');
            exit();

        } catch (Exception $e) {
            (new Session)->setSession('error', $e->getMessage());
        }
    }


    public function getUserInfo($getData)
    {
        $this->model = (new Database)->findOne('tr_account', ['no'], ['no' => $getData['user']]);
    }


    public function userUpdate($postData)
    {
        // Validation 작업 요망
        
        
        if(!(new Database)->update('tr_account', $this->userRule(), ['no' => $postData['userNo']], $postData)) {
            $this->session->setSession('error', '정보 수정에 실패했습니다.');
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit();
        }
        $this->session->setSession('success', '정보 수정에 성공했습니다.');
        header('Location: /view/admin/user_list.php');
        exit();
    }


    public function userDelete($postData)
    {
        if(!(new Database)->update('tr_account', ['status' => 'status'], ['no' => $postData['userNo']], ['status' => 'DEAD'])) {
            $this->session->setSession('error', '회원 탈퇴에 실패하였습니다.');
            header('Location: '.$_SERVER['HTTP_REFERER']);
            exit();
        }
        $this->session->setSession('success', '회원 탈퇴에 성공하였습니다.');
        header('Location: /view/admin/user_list.php');
        exit();
    }

}
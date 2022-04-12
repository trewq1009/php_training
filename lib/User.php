<?php
namespace app\lib;

use app\lib\exception\CustomException;
use Exception;

class User
{
    /**
     * @return string[]
     * [InputName => DB TableName]
     */
    private function rule()
    {
        return [
            'userId' => 'id',
            'userPw' => 'password',
            'userEmail' => 'email',
            'userName' => 'name'
        ];
    }


    public function register($postData)
    {
        try {
            // 가입을 위한 Validation 작업
            if (empty($postData['userId']) || empty($postData['userName']) || empty($postData['userPw']) || empty($postData['userPwC']) || empty($postData['userEmail'])) {
                throw new Exception('필수 정보를 기입해 주세요');
            }

            // DB 연결 시작
            $db = new Database;

            $rebuildData = [];
            foreach ($postData as $key => $value) {
                $inputData = trim($value);
                $inputData = stripslashes($inputData);
                $inputData = htmlspecialchars($inputData);
                if($key == 'userId') {
                    // pattern 체크
                    if (!preg_match("/^[a-zA-Z0-9-' ]*$/", $inputData)) {
                        throw new Exception('아이디 형태가 올바르지 않습니다.');
                    }
                    // DB 중복 확인
                    if ($db->findOne('tr_account', ['id'], ['id' => $inputData])) {
                        throw new Exception('중복된 아이디 입니다.');
                    }
                }

                if($key == 'userPw') {
                    if(strlen($inputData) < 8 || strlen($inputData) > 20) {
                        throw new Exception('비밀번호 형식에 맞지 않습니다.');
                    }
                    $inputData = password_hash($inputData, PASSWORD_BCRYPT);
                }

                if($key == 'userPwC') {
                    if($postData['userPw'] !== $postData['userPwC']) {
                        throw new Exception('패스워드가 일치 하지 않습니다.');
                    }
                }

                if($key == 'userName') {
                    if (!preg_match("/^[가-힣]{9,}$/", $inputData)) {
                        throw new Exception('올바른 이름의 형태가 아닙니다.');
                    }
                }

                if($key == 'userEmail') {
                    if(!filter_var($inputData, FILTER_VALIDATE_EMAIL)) {
                        throw new Exception('이메일 형식에 맞지 않습니다.');
                    }
                    if ($db->findOne('tr_account', ['email'], ['email' => $inputData])) {
                        throw new Exception('중복된 이메일 입니다.');
                    }
                }

                $rebuildData[$key] = $inputData;
            }

            // 트렌 시작
            $db->pdo->beginTransaction();

            // 회원정보 저장
            $userNo = $db->save('tr_account', $this->rule(), $rebuildData);
            if(!$userNo) {
                throw new CustomException('회원가입 실패했습니다. 다시 시도해 주세요');
            }

            // 회원 마일리지 테이블 작성
            if(!$db->save('tr_mileage', ['user_no'=>'user_no', 'status'=>'status'], ['user_no' => $userNo, 'status'=>'회원가입'])) {
                throw new CustomException('회원 마일리지 테이블 등록에 실패했습니다. 다시 시도해 주세요.');
            }

            // 인증 이메일 발송
            if((new MailSend)->sendRegisterEmail($postData) !== true) {
                throw new CustomException('이메일 발송에 오류가 있습니다. 관리자에게 문의 주세요.');
            }


            $db->pdo->commit();
            (new Session)->setSession('success', '회원가입 신청 되었습니다. 이메일 인증을 통해 완료 해주세요.');
            header('Location: /');
            exit();

        } catch (CustomException $e) {
            $db->pdo->rollBack();
            $e->setErrorMessage($e->getMessage());
        } catch (Exception $e) {
            (new Session)->setSession('error', $e->getMessage());
        }
    }


    public function logIn($postData)
    {
        try {
            if(empty($postData['userId']) || empty($postData['userPw'])) {
                throw new CustomException('정보를 입력해 주세요.');
            }

            // DB 계정 확인
            $userData = (new Database)->findOne('tr_account', ['id', 'status'], ['id' => $postData['userId'], 'status' => 'ALIVE']);
            if(!$userData) {
                throw new CustomException('계정을 다시 확인 해주세요.');
            }

            // Password 확인
            if(!password_verify($postData['userPw'], $userData['password'])) {
                throw new CustomException('패스워드가 일치하지 않습니다.');
            }

            // Email 미 인증 유저
            if($userData['email_status'] == 'INACTIVE') {
                throw new CustomException('이메일 인증을 완료하지 않았습니다.');
            }

            // 로그인
            (new Session)->setSession('auth', $userData);
            header('Location: /');
            exit();

        } catch (CustomException $e) {
            $e->setErrorMessage($e->getMessage());
        }
    }


    public function logOut()
    {
        (new Session)->removeSession('auth');
        header('Location: /');
        exit();
    }


    public function update($updateData)
    {
        try {
            $session = new Session;
            if(empty($updateData['userPw'])) {
                $updateData['userPw'] = $session->isSet('auth')['password'];
            } else {
                if(strlen($updateData['userPw']) < 8 || strlen($updateData['userPw']) > 20) {
                    throw new Exception('비밀번호 형식에 맞지 않습니다.');
                }
                $updateData['userPw'] = password_hash($updateData['userPw'], PASSWORD_BCRYPT);
            }

            // DB connect
            $db = new Database;
            $db->pdo->beginTransaction();

            if(!$db->update('tr_account', $this->rule(), ['no' => $_SESSION['auth']['no']], $updateData)) {
                throw new CustomException('정보 수정에 실패했습니다.');
            }
            $db->pdo->commit();

            $afterUserData = $db->findOne('tr_account', ['no' => 'no'], ['no' => $_SESSION['auth']['no']]);
            $session->setSession('auth', $afterUserData);
            $session->setSession('success', '정보 수정이 완료 되었습니다.');
            header('Location: /');
            exit();

        } catch (CustomException $e) {
            $db->pdo->rollBack();
            $e->setErrorMessage($e->getMessage());

        } catch (Exception $e) {
            $session->setSession('error', $e->getMessage());
        }
    }


    public function delete($deleteData)
    {
        try {
            $db = new Database;
            $db->pdo->beginTransaction();

            if(!$db->update('tr_account', ['status' => 'status'], ['no' => $deleteData['no']], ['status' => 'AWAIT'])) {
                throw new CustomException('회원 탈퇴 신청을 실패하였습니다.');
            }

            $db->pdo->commit();
            $session = new Session;

            $session->setSession('success', '회원 탈퇴 신청이 완료 되었습니다.');
            $session->removeSession('auth');
            header('Location: /');
            exit();

        } catch (CustomException $e) {
            $db->pdo->rollBack();
            $e->setErrorMessage($e->getMessage());
        }
    }


    // 이메일 인증
    public function emailAuthentication($getData)
    {
        try {
            if(!isset($getData['training'])) {
                throw new Exception('올바른 주소가 아닙니다.');
            }

            $db = new Database;
            $db->pdo->beginTransaction();

            $userData = $db->findOne('tr_account', ['id', 'status'], ['id' => $getData['training'], 'status' => 'ALIVE']);
            if(!$userData) {
                throw new Exception('올바른 회원이 아닙니다.');
            }

            if(!$db->update('tr_account', ['email_status' => 'email_status'], ['id' => $userData['id']], ['email_status' => 'ACTIVE'])) {
                throw new CustomException('인증에 문제가 발생했습니다. 관리자에게 문의 주세요');
            }

            $db->pdo->commit();
            (new Session)->setSession('success', '이메일 인증이 완료 되었습니다. 로그인 해주세요.');
            header('Location: /');
            exit();

        } catch (CustomException $e) {
            $db->pdo->rollBack();
            $e->setErrorMessage($e->getMessage());
            header('Location: /');
            exit();
        } catch (Exception $e) {
            (new Session)->setSession('error', $e->getMessage());
            header('Location: /');
            exit();
        }
    }

}
<?php
namespace app\lib;

use app\lib\exception\CustomException;
use app\lib\exception\DataValueChangedException;
use app\lib\exception\InputDataNullException;
use app\lib\exception\InvalidParamsException;
use app\lib\exception\PaymentException;
use app\lib\exception\DatabaseException;
use stdClass;

class Payment
{
    public function cardPayment($postData)
    {
        try {
            // validation section
            if(empty($postData['radioValue'])) {
                throw new InputDataNullException('올바른 경로가 아닙니다.');
            }

            // price
            if (empty($postData['price'])) {
                throw new InputDataNullException('금액이 존재하지 않습니다.');
            }

            if ((integer)$postData['price'] < 1000) {
                throw new InvalidParamsException('금액이 올바르지 않습니다.');
            }

            // card
            if (empty($postData['cardNumber']) || empty($postData['cardYear']) || empty($postData['cardMonth']) || empty($postData['cardCVC']) || empty($postData['cardPassword'])) {
                throw new InputDataNullException('카드 정보를 입력해 주세요.');
            }

            // integer validation
            foreach ($postData as $key => $value) {
                if ($key !== 'cardNumber' && $key !== 'radioValue') {
                    if (!preg_match("/^[0-9]/i", $value)) {
                        throw new InvalidParamsException('숫자만 입력해 주세요');
                    }
                }
            }

            foreach ($postData['cardNumber'] as $item) {
                if (strlen($item) !== 4) {
                    throw new InvalidParamsException('카드 번호길이가 알맞지 않습니다.');
                }
                if (!preg_match("/^[0-9]/i", $item)) {
                    throw new InvalidParamsException('숫자만 입력해 주세요');
                }
            }

            if ((integer)$postData['cardMonth'] < 1 || (integer)$postData['cardMonth'] > 12) {
                throw new InvalidParamsException('카드 유효기간이 올바르지 않습니다.');
            }
            $cardDate = date("Y-m-d H:i:s", mktime(0, 0, 0, $postData['cardMonth'] + 1, 0, $postData['cardYear']));
            $toDate = date("Y-m-d H:i:s");
            if ($toDate > $cardDate) {
                throw new PaymentException('카드 유효기간이 지났습니다.');
            }

            if (strlen($postData['cardCVC']) !== 3) {
                throw new PaymentException('카드의 보안코드가 올바르지 않습니다.');
            }

            if (strlen($postData['cardPassword']) !== 4) {
                throw new PaymentException('카드 패스워드가 알맞지 않습니다.');
            }

            // 기본 검증만 끝나면 마일리지 작업
            $db = new Database;
            $utils = new Utils;
            $db->pdo->beginTransaction();

            // 추후 양방향 암호화 해서 정보 보안 후 DB 저장
            $cardInformation = new stdClass;
            $cardInformation->card_number = $utils->encrypt(implode("-", $postData['cardNumber']));
            $cardInformation->card_validity = $utils->encrypt(date("Y-m", strtotime($cardDate)));

            $cancels = new stdClass;
            $cancels->cancels = 0;

            $paymentParams = ['user_no' => $_SESSION['auth']['no'],
                'total_amount' => $postData['price'],
                'method' => $postData['radioValue'],
                'payment_information' => json_encode($cardInformation),
                'status' => 'success',
                'cancels' => json_encode($cancels),
            ];
            $paymentRule = [
                'user_no' => 'user_no',
                'total_amount' => 'total_amount',
                'method' => 'method',
                'payment_information' => 'payment_information',
                'status' => 'status',
                'cancels' => 'cancels'
            ];

            // 결재 로그
            $paymentNo = $db->save('tr_payment_log', $paymentRule, $paymentParams);
            if (!$paymentNo) {
                throw new CustomException('로그 저장에 실패');
            }

            // 마일리지 로그
            // 추후 status 값을 활용해 결제가 이루어졌을 때만 마일리지 로그를 실행해 마일리지를 부여한다.
            $mileageRule = [
                'user_no' => 'user_no',
                'payment_no' => 'payment_no',
                'method' => 'method',
                'before_mileage' => 'before_mileage',
                'use_mileage' => 'use_mileage',
                'total_mileage' => 'total_mileage'
            ];
            $mileageParams = [
                'user_no' => $_SESSION['auth']['no'],
                'payment_no' => $paymentNo,
                'method' => $postData['radioValue'],
                'before_mileage' => $_SESSION['auth']['mileage'],
                'use_mileage' => $postData['price'],
                'total_mileage' => $_SESSION['auth']['mileage'] + $postData['price']
            ];

            // 마일리지 로그 저장
            if(!$db->save('tr_mileage', $mileageRule, $mileageParams)) {
                throw new CustomException('마일리지 로그 저장 오류');
            }

            // 유저 정보 업데이트
            if(!$db->update('tr_account', ['mileage' => 'mileage'], ['no' => $_SESSION['auth']['no']], ['mileage' => $mileageParams['total_mileage']])) {
                throw new CustomException('유저 업데이트 오류');
            }

            $db->pdo->commit();
            $afterUserData = $db->findOne('tr_account', ['no' => 'no'], ['no' => $_SESSION['auth']['no']]);
            $session = new Session;
            $session->setSession('auth', $afterUserData);
            $session->setSession('success', '결재가 완료 되었습니다.');
            header('Location: /');
            exit();


        } catch (PaymentException $e) {
            $e->paymentFailLog($e, $postData);
            $e->setErrorMessages($e);
            header('location: /view/error/error.php');
        } catch (CustomException $e) {
            $db->pdo->rollBack();
            $e->setErrorMessages($e);
            header('location: /view/mileage.php');
        } catch (InvalidParamsException $e) {
            $e->setErrorMessages($e);
            header('location: /view/mileage.php');
        } catch (InputDataNullException $e) {
            $e->setErrorMessages($e);
            header('location: /view/mileage.php');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }


    public function phonePayment($postData)
    {
        try {
            if(empty($postData['radioValue'])) {
                throw new InputDataNullException('올바른 경로가 아닙니다.');
            }
            if(empty($postData['price'])) {
                throw new InputDataNullException('가격이 입력 되지 않았습니다.');
            }
            if ((integer)$postData['price'] < 1000) {
                throw new InvalidParamsException('금액이 올바르지 않습니다.');
            }
            if(empty($postData['phoneNumber'])) {
                throw new InputDataNullException('번호를 입력해 주세요.');
            }
            if (!preg_match("/^[0-9]/i", $postData['phoneNumber'])) {
                throw new InvalidParamsException('숫자만 입력해 주세요');
            }
//            if(!preg_match('/^(010|011|016|017|018|019)-[^0][0-9]{3,4}-[0-9]{4}/', $postData['phoneNumber'])) {
//                throw new InvalidParamsException('올바른 전화번호를 입력해 주세요.');
//            }
            if(empty($postData['agencyValue'])) {
                throw new InputDataNullException('통신사를 확인해 주세요.');
            }

            $db = new Database;
            $utils = new Utils;
            $db->pdo->beginTransaction();

            // 추후 양방향 암호화 해서 정보 보안 후 DB 저장
            $phoneInformation = new stdClass;
            $phoneInformation->phone_number = $utils->encrypt($postData['phoneNumber']);
            $phoneInformation->phone_agency = $postData['agencyValue'];

            $cancels = new stdClass;
            $cancels->cancels = 0;

            $paymentParams = ['user_no' => $_SESSION['auth']['no'],
                'total_amount' => $postData['price'],
                'method' => $postData['radioValue'],
                'payment_information' => json_encode($phoneInformation),
                'status' => 'success',
                'cancels' => json_encode($cancels),
            ];

            $paymentRule = [
                'user_no' => 'user_no',
                'total_amount' => 'total_amount',
                'method' => 'method',
                'payment_information' => 'payment_information',
                'status' => 'status',
                'cancels' => 'cancels'
            ];

            // 결재 로그
            $paymentNo = $db->save('tr_payment_log', $paymentRule, $paymentParams);
            if (!$paymentNo) {
                throw new CustomException('로그 저장에 실패');
            }

            // 마일리지 로그
            // 추후 status 값을 활용해 결제가 이루어졌을 때만 마일리지 로그를 실행해 마일리지를 부여한다.
            $mileageRule = [
                'user_no' => 'user_no',
                'payment_no' => 'payment_no',
                'method' => 'method',
                'before_mileage' => 'before_mileage',
                'use_mileage' => 'use_mileage',
                'total_mileage' => 'total_mileage'
            ];
            $mileageParams = [
                'user_no' => $_SESSION['auth']['no'],
                'payment_no' => $paymentNo,
                'method' => $postData['radioValue'],
                'before_mileage' => $_SESSION['auth']['mileage'],
                'use_mileage' => $postData['price'],
                'total_mileage' => $_SESSION['auth']['mileage'] + $postData['price']
            ];

            // 마일리지 로그 저장
            if(!$db->save('tr_mileage', $mileageRule, $mileageParams)) {
                throw new CustomException('마일리지 로그 저장 오류');
            }

            // 유저 정보 업데이트
            if(!$db->update('tr_account', ['mileage'=>'mileage'], ['no'=>$_SESSION['auth']['no']], ['mileage'=> $mileageParams['total_mileage']])) {
                throw new CustomException('유저 업데이트 오류');
            }

            $db->pdo->commit();
            $afterUserData = $db->findOne('tr_account', ['no' => 'no'], ['no' => $_SESSION['auth']['no']]);
            $session = new Session;
            $session->setSession('auth', $afterUserData);
            $session->setSession('success', '결재가 완료 되었습니다.');
            header('Location: /');
            exit();


        } catch (CustomException $e) {
            $db->pdo->rollBack();
            $e->setErrorMessages($e);
            header('location: /view/mileage.php');
        } catch (InvalidParamsException $e) {
            $e->setErrorMessages($e);
            header('location: /view/mileage.php');
        } catch (InputDataNullException $e) {
            $e->setErrorMessages($e);
            header('location: /view/mileage.php');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }


    public function voucherPayment($postData)
    {
        try {
            if(empty($postData['radioValue'])) {
                throw new InputDataNullException('올바른 경로가 아닙니다.');
            }
            if(empty($postData['price'])) {
                throw new InputDataNullException('가격이 입력 되지 않았습니다.');
            }
            if ((integer)$postData['price'] < 1000) {
                throw new InvalidParamsException('금액이 올바르지 않습니다.');
            }
            if (empty($postData['voucherNumber'])) {
                throw new InputDataNullException('상품권 번호가 없습니다.');
            }
            if (!preg_match('/^[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{6}/', $postData['voucherNumber'])) {
                throw new InvalidParamsException('올바른 상품권 번호가 아닙니다.');
            }

            $db = new Database;
            $utils = new Utils;
            $db->pdo->beginTransaction();

            // 추후 양방향 암호화 해서 정보 보안 후 DB 저장
            $voucherInformation = new stdClass;
            $voucherInformation->voucher_number = $utils->encrypt($postData['voucherNumber']);

            $cancels = new stdClass;
            $cancels->cancels = 0;

            $paymentParams = ['user_no' => $_SESSION['auth']['no'],
                'total_amount' => $postData['price'],
                'method' => $postData['radioValue'],
                'payment_information' => json_encode($voucherInformation),
                'status' => 'success',
                'cancels' => json_encode($cancels),
            ];

            $paymentRule = [
                'user_no' => 'user_no',
                'total_amount' => 'total_amount',
                'method' => 'method',
                'payment_information' => 'payment_information',
                'status' => 'status',
                'cancels' => 'cancels'
            ];

            // 결재 로그
            $paymentNo = $db->save('tr_payment_log', $paymentRule, $paymentParams);
            if (!$paymentNo) {
                throw new CustomException('로그 저장에 실패');
            }

            // 마일리지 로그
            // 추후 status 값을 활용해 결제가 이루어졌을 때만 마일리지 로그를 실행해 마일리지를 부여한다.
            $mileageRule = [
                'user_no' => 'user_no',
                'payment_no' => 'payment_no',
                'method' => 'method',
                'before_mileage' => 'before_mileage',
                'use_mileage' => 'use_mileage',
                'total_mileage' => 'total_mileage'
            ];
            $mileageParams = [
                'user_no' => $_SESSION['auth']['no'],
                'payment_no' => $paymentNo,
                'method' => $postData['radioValue'],
                'before_mileage' => $_SESSION['auth']['mileage'],
                'use_mileage' => $postData['price'],
                'total_mileage' => $_SESSION['auth']['mileage'] + $postData['price']
            ];

            // 마일리지 로그 저장
            if(!$db->save('tr_mileage', $mileageRule, $mileageParams)) {
                throw new CustomException('마일리지 로그 저장 오류');
            }

            // 유저 정보 업데이트
            if(!$db->update('tr_account', ['mileage'=>'mileage'], ['no'=>$_SESSION['auth']['no']], ['mileage'=> $mileageParams['total_mileage']])) {
                throw new CustomException('유저 업데이트 오류');
            }

            $db->pdo->commit();
            $afterUserData = $db->findOne('tr_account', ['no' => 'no'], ['no' => $_SESSION['auth']['no']]);
            $session = new Session;
            $session->setSession('auth', $afterUserData);
            $session->setSession('success', '결재가 완료 되었습니다.');
            header('Location: /');
            exit();


        } catch (InvalidParamsException $e) {
            $e->setErrorMessages($e);
            header('location: /view/mileage.php');
        } catch (InputDataNullException $e) {
            $e->setErrorMessages($e);
            header('location: /view/mileage.php');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }


    public function cashWithDrawal($postData)
    {
        try {
            $auth = (new Session)->isSet('auth');
            if(!$auth) {
                throw new CustomException('로그인 정보가 없습니다.');
            }
            if(empty($postData['userNo'])) {
                throw new CustomException('유저 정보가 없습니다.');
            }
            if(empty($postData['userMileage']) || empty($postData['trueMileage']) || empty($postData['drawalMileage']) || empty($postData['bankValue']) || empty($postData['bankNumber'])) {
                throw new InputDataNullException('입력 데이터를 다시 확인해 주세요.');
            }
            if($postData['userMileage'] < 1000 || $postData['trueMileage'] < 1000) {
                throw new InvalidParamsException('출금 가능한 최소 금액이 안됩니다.');
            }
            if($postData['trueMileage'] < $postData['drawalMileage']) {
                throw new InvalidParamsException('출금 가능 마일리지를 넘었습니다.');
            }
            if($postData['drawalMileage'] < 1000) {
                throw new InvalidParamsException('최소 출금 가능한 금액은 1000원 입니다.');
            }
            if (!preg_match("/^[0-9]/i", $postData['bankNumber'])) {
                throw new InvalidParamsException('올바른 계좌 번호가 아닙니다. 숫자만 입력해 주세요.');
            }
            if (!preg_match("/^[0-9]/i", $postData['drawalMileage'])) {
                throw new InvalidParamsException('올바른 금앱을 입력해 주세요.');
            }

            $db = new Database;
            $userModel = $db->findOne('tr_account', ['no'], ['no'=>$auth['no']]);
            if($userModel['mileage'] !== $auth['mileage']) {
                (new Session)->setSession('auth', $userModel);
                throw new DataValueChangedException('마일리지가 변동되었습니다. 다시 신청해 주세요.');
            }

            $logRule = ['user_no'=>'user_no', 'method'=>'method', 'use_mileage'=>'use_mileage', 'log_information'=>'log_information', 'status'=>'status'];
            $useLog = new stdClass;
            $useLog->bank = $postData['bankValue'];
            $useLog->bank_account_number = (new Utils)->encrypt($postData['bankNumber']);

            $logParams = [
                'user_no' => $userModel['no'],
                'method' => 'withdrawal',
                'use_mileage' => $postData['drawalMileage'],
                'log_information' => json_encode($useLog),
                'status' => 'AWAIT'
            ];

            // 로그 저장
            $db->pdo->beginTransaction();
            $logNum = $db->save('tr_mileage_use_log', $logRule, $logParams);
            if(!$logNum) {
                throw new DatabaseException('출금 신청에 실패하였습니다.');
            }

//            $mileageRule = ['user_no'=>'user_no', 'method'=>'method', 'mileage_use_log'=>'mileage_use_log',
//                            'before_mileage'=>'before_mileage', 'use_mileage'=>'use_mileage', 'total_mileage'=>'total_mileage'];
//
//            $mileageParams = [
//                'user_no' => $userModel['no'],
//                'method' => 'withdrawal',
//                'mileage_use_log' => $logNum,
//                'before_mileage' => $userModel['mileage'],
//                'use_mileage' => $postData['drawalMileage'],
//                'total_mileage' => $userModel['mileage'] - $postData['drawalMileage']
//            ];
//
//            // mileage table
//            if(!$db->save('tr_mileage', $mileageRule, $mileageParams)) {
//                throw new DatabaseException('출금 신청에 실패하였습니다.');
//            }
//
//            // user info update
//            if(!$db->update('tr_account', ['mileage'=>'mileage'], ['no'=>$userModel['no']], ['mileage'=>$userModel['mileage'] - $postData['drawalMileage']])) {
//                throw new DatabaseException('유저 정보 반영 실패');
//            }
//
//            $newUserModel = $db->findOne('tr_account', ['no'], ['no'=>$userModel['no']]);
            $db->pdo->commit();

            $session = new Session;
//            $session->setSession('auth', $newUserModel);
            $session->setSession('success', '출금 신청이 완료되었습니다.');
            header('Location: /');



        } catch (DatabaseException $e) {
            $db->pdo->rollBack();
            $e->setErrorMessages($e);
            header('Location: /');
        } catch (DataValueChangedException $e) {
            $e->setErrorMessages($e);
            header('Location: /view/profile.php');
        } catch (CustomException $e) {
            $e->setErrorMessages($e);
            header('Location: /');
        } catch (InvalidParamsException $e) {
            $e->setErrorMessages($e);
        } catch (InputDataNullException $e) {
            $e->setErrorMessages($e);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }


}
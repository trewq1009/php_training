<?php
namespace app\lib\exception;

use app\lib\Session;
use app\lib\Database;
use stdClass;


class PaymentException extends \Exception
{
    public function setErrorMessages($e)
    {
        (new Session)->setSession('error', $e->getMessage().$e->getLine());
    }

    public function paymentFailLog($e, $data)
    {
        $db = new Database;
        $db->pdo->beginTransaction();

        $cardInformation = new stdClass;
        $cardInformation->status = 'fail';

        $cancels = new stdClass;
        $cancels->cancel_info = $e->getMessage();
        $cancels->cancel_code = $e->getCode();

        $paymentParams = ['user_no' => $_SESSION['auth']['no'],
            'total_amount' => $data['price'],
            'method' => $data['radioValue'],
            'payment_information' => json_encode($cardInformation),
            'status' => 'FAIL',
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
            $db->pdo->rollBack();
            return;
        }
        $db->pdo->commit();
    }
}
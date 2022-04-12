<?php

namespace app\lib;

use app\lib\exception\CustomException;

class Payment
{
    public function cardPayment($postData) {
        try {
            // validation section
            if(empty($postData['price'])) {

            }




        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }



}
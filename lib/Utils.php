<?php
namespace app\lib;

require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

class Utils
{
    public function testValidation($table, $rule, $value)
    {
        /*
         * $table = DB 중복 같은 값을 확인 하기 위해 사용
         *
         * $rule = 검증 방법을 위해 사용
         * ex: $rule = ['input 태그 네임' => [RULE_REQUIRE, RULE_UNIQUE], 'input 태그 네임' => [[RULE_MIN => 8], RULE_REQUIRE]]
         *
         * $value = 검증 할 값
         * ex: $value = ['input 태그 네임' => 값, 'input 태그 네임' => 값2]
         *
         *
         * 먼저, $value foreach() 사용하여 length 만큼 작업을 돌린다
         * 다음으로 $value 에 키값을 이용해 해당되는 $rule[$key] 에 규칙을 적용한다
         */

        /*
        foreach ($value as $key => $item) {
            $ruleArr = $rule[$key];
            foreach ($ruleArr as $ruleName) {
                if($ruleName === RULE_REQUIRE) {
                    if(empty($item)) {
                        return '값 입력 해주세요';
                    }
                }

                if($ruleName === RULE_UNIQUE) {
                    // DB 검증
                }

            }
        }
         이런식??
        */
    }

    public function encrypt($msg) {
        $password = substr(hash('sha256', HASH, true), 0 ,32);
        $iv = '';
        for($i = 0; $i < 16; $i++) {
            $iv .= chr(0x0);
        }

        $encrypted = base64_encode(openssl_encrypt($msg, 'aes-256-cbc', $password, OPENSSL_RAW_DATA, $iv));

        return $encrypted;
    }

    public function decrypt($msg) {
        $password = substr(hash('sha256', HASH, true), 0 ,32);

        $iv = '';
        for($i = 0; $i < 16; $i++) {
            $iv .= chr(0x0);
        }

        $decrypted = openssl_decrypt(base64_decode($msg), 'aes-256-cbc', $password, OPENSSL_RAW_DATA, $iv);

        return $decrypted;
    }


}
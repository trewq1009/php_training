<?php
namespace app\lib;

class Utils
{
    public function registedValidation($tableName, $rule, $data)
    {
        $reBuildArray = [];
        foreach ($rule as $field => $value) {
            // validation section
            if( !isset($data[$field]) ) {
                return ['error' => "$field 를 입력해 주세요"];
            }
            
            // 값 정제
            $reBuildData = $this->inputDataReBuild($data[$field]);

            // id validation
            if($field == 'userId') {
                // pattern 체크
                if (!preg_match("/^[a-zA-Z0-9-' ]*$/",$reBuildData)) {
                    return ['error' => "아이디 값을 다시 확인해 주세요."];
                }
                // DB check(중복)
                if ((new Database)->findOne($tableName, [$field => $value], [$value => $reBuildData])) {
                    return ['error' => "중복된 아이디 입니다."];
                }
            }

            // pw validation
            if($field == 'userPw') {
                if($data[$field] !== $data['userPwC']) {
                    return ['error' => "비밀번호를 확인해 주세요."];
                }
                if(strlen($data[$field]) < 8 || strlen($data[$field]) > 20) {
                    return ['error' => "비밀번호 길이가 맞지 않습니다."];
                }

            }

            // Email
            if($field == 'userEmail') {
                if (!filter_var($reBuildData, FILTER_VALIDATE_EMAIL)) {
                    return ['error' => "이메일 형식에 맞춰 주세요."];
                }
                // DB check(중복)
                if ((new Database)->findOne($tableName, [$field => $value], [$value => $reBuildData])) {
                    return ['error' => "중복된 이메일 입니다."];
                }
            }

            $reBuildArray[$field] = $reBuildData;
        }

        $passwordHash = password_hash($reBuildArray['userPw'], PASSWORD_BCRYPT);
        unset($reBuildArray['userPw']);
        $reBuildArray['userPw'] = $passwordHash;
        return $reBuildArray;
    }


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


    private function inputDataReBuild($inputData)
    {
        $inputData = trim($inputData);
        $inputData = stripslashes($inputData);
        $inputData = htmlspecialchars($inputData);
        return $inputData;
    }

}
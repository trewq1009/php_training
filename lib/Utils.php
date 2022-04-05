<?php

namespace app\lib;


class Utils
{

    public function getMethod(array $server): string
    {
        return strtolower($server['REQUEST_METHOD']);
    }


    public function registedValidation($tableName, $rule, $data) {
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


    private function inputDataReBuild($inputData) {
        $inputData = trim($inputData);
        $inputData = stripslashes($inputData);
        $inputData = htmlspecialchars($inputData);
        return $inputData;
    }

}
<?php

namespace app\lib;

require_once '../config/config.php';

class Utils
{

    public function getMethod(array $server): string
    {
        return strtolower($server['REQUEST_METHOD']);
    }


    public function validation($rule, $data) {
        $reBuildArray = [];
        foreach ($rule as $field => $value) {
            // validation section
            if( !isset($data[$field]) ) {
                return $err = '입력값 비어있음';
            }

//            switch ($field) {
//                case 'userId' :
//                    if($data['userId']) {
//
//                    }
//                    break;
//
//                case 'userPw' :
//                    if($data['userPw'] !== $data['userPwC']) {
//
//                    }
//                    break;
//
//                default :
//                    break;
//            }


            // 값 정제
            $reBuildData = $this->inputDataReBuild($data[$field]);
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
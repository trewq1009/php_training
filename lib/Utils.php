<?php
namespace app\lib;

use Exception;

require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

class Utils
{
    public static function encrypt($msg) {
        $password = substr(hash('sha256', HASH, true), 0 ,32);
        $iv = '';
        for($i = 0; $i < 16; $i++) {
            $iv .= chr(0x0);
        }

        $encrypted = base64_encode(openssl_encrypt($msg, 'aes-256-cbc', $password, OPENSSL_RAW_DATA, $iv));

        return $encrypted;
    }

    public static function decrypt($msg) {
        $password = substr(hash('sha256', HASH, true), 0 ,32);

        $iv = '';
        for($i = 0; $i < 16; $i++) {
            $iv .= chr(0x0);
        }

        $decrypted = openssl_decrypt(base64_decode($msg), 'aes-256-cbc', $password, OPENSSL_RAW_DATA, $iv);

        return $decrypted;
    }


    public static function fileUpload($fileData)
    {
        try {
            // 수정 사항 22.04.29 미완
            // 이미지 이름을 DB 컬럼에서 제외했고
            // 이미지 저장할 때에는 DB 저장 후 Index를 추출해
            // 이미지 이름에 사용 예): 343(DBno).확장자

            // 임시 저장소
            $tempFile = $fileData['imageInfo']['tmp_name'];
            // 파일타입 및 확장자 가져오기
            $fileTypeExt = explode('/', $fileData['imageInfo']['type']);

            $fileType = $fileTypeExt[0];
            $fileExt = $fileTypeExt[1];

            if(!$fileType == 'image') {
                throw new Exception('이미지 파일이 아닙니다.');
            }
            if(!array_search($fileExt, ['jpeg','jpg','gif','bmp','png'])) {
                throw new Exception('알맞은 확장자가 아닙니다.');
            }

            // 시간을 월,일,시,분,초 로 표기
            $fileDate = date('mdhis', time());

            $newImageName = chr(rand(97,122)).chr(rand(97,122)).$fileDate.".$fileExt";

            // 임시 저장소에서 이동할 경로와 파일 명
            $fileRoot = $_SERVER['DOCUMENT_ROOT']."/upload/$newImageName";

            $imageUpload = move_uploaded_file($tempFile, $fileRoot);

            if(!$imageUpload) {
                throw new Exception('파일 저장에 실패했습니다.');
            }

            chmod($fileRoot,0777);

            return $newImageName;

        } catch (Exception $e) {
            return false;
        }
    }


}
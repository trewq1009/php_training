<?php
namespace app\lib;

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


}
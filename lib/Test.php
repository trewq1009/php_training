<?php
namespace app\lib;

use Exception;
use PDO;
use DateTime;

require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

class Test
{
    public PDO $pdo;

    public function __construct()
    {
        // 생성시 디비 연결 후 테스트 까지
        // 미 연결시 디비 오류
        try {
            $host = DB_HOST;
            $user = DB_USER;
            $password = DB_PASSWORD;

            $this->pdo = new PDO($host, $user, $password, [PDO::MYSQL_ATTR_FOUND_ROWS => true]);
        } catch (\PDOException | Exception $e) {
            die($e->getMessage());
        }
    }

    public function test($tableName, $where, $params)
    {

    }



}
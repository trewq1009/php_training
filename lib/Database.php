<?php
namespace app\lib;

use Exception;
use PDO;
use DateTime;

require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

class Database
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


    public function save($tableName, $params)
    {
        try {
            $columns = array_keys($params);
            $sqlValue = array_map(fn($attr) => ":$attr", $columns);

            $statement = $this->pdo->prepare("INSERT INTO $tableName (".implode(',', $columns).") VALUE (".implode(',', $sqlValue).")");

            foreach ($columns as $item) {
                $statement->bindValue(":$item", $params[$item]);
            }

            $statement->execute();

            // sql 문이 성공하면 1반환
            $result = $statement->rowCount();

            if($result == 0) {
                throw new Exception();
            }

            return $this->pdo->lastInsertId();

        } catch (\Exception $e) {
            return $e;
        }
    }


    public function findOne($tableName, $params)
    {
        try {
            $sqlWhere = array_keys($params);
            $sql = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $sqlWhere));

            $statement = $this->pdo->prepare("SELECT * FROM $tableName WHERE $sql");
            foreach ($sqlWhere as $item) {
                $statement->bindValue(":$item", $params[$item]);
            }

            $statement->execute();
            return $statement->fetch();
            
        } catch (Exception $e) {
            return false;
        }
    }


    public function findAll($tableName, $where, $params)
    {
        try {

            $sql = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $where));

            $statement = $this->pdo->prepare("SELECT * FROM $tableName WHERE $sql");
            foreach ($where as $item) {
                $statement->bindValue(":$item", $params[$item]);
            }

            $statement->execute();
            return $statement->fetchAll();

        } catch (Exception $e) {
            return false;
        }
    }


    public function update ($tableName, $where, $params)
    {
        try {
            $setValue = implode(' , ', array_map(fn($attr) => "$attr = :$attr", array_keys($params)));

            $setValue = $setValue.', updated = :updated';

            $setWhere = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", array_keys($where)));

            $statement = $this->pdo->prepare("UPDATE $tableName SET $setValue WHERE $setWhere");

            // value bind
            foreach ($params as $key => $value) {
                $statement->bindValue(":$key", $value);
            }

            // updated Date
            $date = new DateTime("NOW");
            $timeStamp = $date->format('Y-m-d H:i:s');
            $statement->bindValue(':updated', $timeStamp);

            // where bind
            foreach ($where as $key => $value) {
                $statement->bindValue(":$key", $value);
            }

            $statement->execute();

            // result 1 success
            $result = $statement->rowCount();

            if($result == 0) {
                throw new Exception();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function list($tableName, $page)
    {
        try {
            $resultOnPage = 5;                                    // 화면에 보여질 갯수
            $calcPage = $resultOnPage * (intval($page) - 1);     // 데이터 시작점

            $statement = $this->pdo->prepare("SELECT COUNT(*) AS count FROM $tableName");
            $statement->execute();
            $total = $statement->fetch();                           // 데이터 총 갯수

            $statement = $this->pdo->prepare("SELECT * FROM $tableName WHERE status = 'ALIVE' OR status = 'AWAIT' ORDER BY no DESC LIMIT $calcPage, $resultOnPage");
            $statement->execute();
            $listData = $statement->fetchAll();

            return [
              'total' => $total['count'],
              'resultOnPage' => $resultOnPage,
              'calcPage' =>  $calcPage,
              'listData' => $listData
            ];

        } catch (\Exception $e) {
            return false;
        }
    }
}
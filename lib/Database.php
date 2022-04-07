<?php

namespace app\lib;

use PDO;
use DateTime;

require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

class Database
{
    public \PDO $pdo;

    public function __construct() {
        // 생성시 디비 연결 후 테스트 까지
        // 미 연결시 디비 오류
        try {
            $host = DB_HOST;
            $user = DB_USER;
            $password = DB_PASSWORD;

            $this->pdo = new \PDO($host, $user, $password, [PDO::MYSQL_ATTR_FOUND_ROWS => true]);
        } catch (\PDOException $e) {
            die($e->getMessage());
        } catch (\Exception $e) {
            var_dump($e);
        }
    }


    public function save($tableName, $rule, $data) {
        try {
            // db 컬럼값 추출, 실제 들어온 데이터 이름
            $dbValueName = $realName = [];

            foreach ($rule as $key => $value) {
                $dbValueName[] = $value;
                $realName[] = $key;
            }

            // transaction 알아보고 적용할것
            // try catch 문도 적절히 사용할 것
            // query 각각 컬럼명 바인딩 준비
            $params = array_map(fn($attr) => ":$attr", $realName);

            // 트랜잭션 시작
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare("INSERT INTO $tableName (".implode(',', $dbValueName).")
                                    VALUE (".implode(',', $params).")");
            foreach ($realName as $item) {
                $statement->bindValue(":$item", $data[$item]);
            }
            $statement->execute();
            
            // sql 문이 성공 했으면 1 반환
            $result = $statement->rowCount();

            if($result > 0) {
                // 이 구간 에서 검증 한 후에 데이터가 올바르게 들어가면 커밋 명령어 실행
                $this->pdo->commit();

                return true;
            } else {
                $this->pdo->rollBack();
                $this->pdo->commit();
                return false;
            }
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }


    public function findOne($tableName, $where, $params) {
        try {

            $sql = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $where));

            $statement = $this->pdo->prepare("SELECT * FROM $tableName WHERE $sql");
            foreach ($where as $item) {
                $statement->bindValue(":$item", $params[$item]);
            }

            $statement->execute();
            return $statement->fetch();
            
        } catch (\Exception $e) {
            return false;
        }
    }


    public function update($tableName, $rule, $where, $params) {
        try {

            $sql = implode(" , ", array_map(fn($attr) => "$attr = :$attr", $rule));

            $sql = $sql.", updated = :updated";

            $whereKey = array_keys($where);
            $sqlWhere = implode(" AND ", array_map(fn($attr) => "$attr = :$attr", $whereKey));

            // 트랜잭션 시작
            $this->pdo->beginTransaction();

            $statement = $this->pdo->prepare("UPDATE $tableName SET $sql WHERE $sqlWhere");

            // value
            foreach ($rule as $key => $item) {
                $statement->bindValue(":$item", $params[$key]);
            }

            // update 날짜
            $date = new DateTime("NOW");
            $timeStamp = $date->format('Y-m-d H:i:s');
            $statement->bindValue(":updated", $timeStamp);

            // where
            foreach ($where as $whereKey => $whereItem) {
                $statement->bindValue(":$whereKey", $whereItem);
            }
            $statement->execute();

            // sql 문이 성공 했으면 1 반환
            $result = $statement->rowCount();

            // update 실패시 롤백
            if($result == 0) {
                $this->pdo->rollBack();
                $this->pdo->commit();
                return false;
            }

            $this->pdo->commit();
            return $sql;

        } catch (\Exception $e) {
            return false;
        }
    }


//    public function test($getValue, $tableName, $where) {
//
//    }

}
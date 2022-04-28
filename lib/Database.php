<?php
namespace app\lib;

use Exception;
use PDO;
use DateTime;

require_once $_SERVER['DOCUMENT_ROOT'].'/config/config.php';

class Database
{
    public $conn;
    public function __construct()
    {
        try {
            $db = @mysqli_connect(HOST_NAME, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
            if(!$db) {
                $error = mysqli_connect_errno();
                $errno = mysqli_connect_errno();
                throw new Exception("$errno : $error\n");
            }
            $this->conn = $db;

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function __destruct()
    {
        try {
            mysqli_close($this->conn);

        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function save($tableName, $params, $type)
    {
        try {
            $columns = implode(' , ', array_keys($params));
            $sqlValue = implode(',', array_map(fn($attr) => "?", $params));

            $query = "INSERT INTO $tableName ($columns) VALUE ($sqlValue)";

            $stmt = mysqli_prepare($this->conn, $query);
            if($stmt === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $param = array_values($params);
            $bind = mysqli_stmt_bind_param($stmt, $type, ...$param);
            if($bind === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $exec = mysqli_stmt_execute($stmt);
            if($exec === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $resultNo = mysqli_insert_id($this->conn);
            mysqli_stmt_close($stmt);

            return $resultNo;

        } catch (\Exception $e) {
            return false;
        }
    }

    public function findOne($tableName, $params, $type, $option = '') {
        try {

            $sql = implode(" AND ", array_map(fn($attr) => "$attr = ?", array_keys($params)));
            $sql .= " $option";

            $query = "SELECT * FROM $tableName WHERE $sql";

            $stmt = mysqli_prepare($this->conn, $query);
            if($stmt === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $param = array_values($params);

            $bind = mysqli_stmt_bind_param($stmt, $type, ...$param);
            if($bind === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $exec = mysqli_stmt_execute($stmt);
            if($exec === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $result = mysqli_stmt_get_result($stmt);
            if(!$result) {
                throw new Exception(mysqli_error($this->conn));
            }
            $data = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);

            return $data;

        } catch (Exception $e) {
            return false;
        }
    }


    public function findAll($tableName, $params, $type)
    {
        try {
            $sql = implode(" AND ", array_map(fn($attr) => "$attr = ?", array_keys($params)));

            $query = "SELECT * FROM $tableName WHERE $sql ORDER BY no DESC";

            $stmt = mysqli_prepare($this->conn, $query);
            if($stmt === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $param = array_values($params);

            $bind = mysqli_stmt_bind_param($stmt, $type, ...$param);
            if($bind === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $exec = mysqli_stmt_execute($stmt);
            if($exec === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $result = mysqli_stmt_get_result($stmt);
            if(!$result) {
                throw new Exception(mysqli_error($this->conn));
            }
            $data = mysqli_fetch_all($result);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);

            return $data;

        } catch (Exception $e) {
            return false;
        }
    }


    public function findOr($tableName, $params, $type = '')
    {
        try {
            $sql = implode(" OR ", array_map(fn($attr) => "$attr = ?", array_keys($params)));

            $query = "SELECT * FROM $tableName WHERE $sql";

            $stmt = mysqli_prepare($this->conn, $query);
            if($stmt === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $param = array_values($params);

            $bind = mysqli_stmt_bind_param($stmt, $type, ...$param);
            if($bind === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $exec = mysqli_stmt_execute($stmt);
            if($exec === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $result = mysqli_stmt_get_result($stmt);
            if(!$result) {
                throw new Exception(mysqli_error($this->conn));
            }
            $data = mysqli_fetch_all($result);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);

            return $data;

        } catch (Exception $e) {
            return false;
        }
    }


    public function update ($tableName, $params, $where, $type = '')
    {
        try {
            $setValue = implode(' , ', array_map(fn($attr) => "$attr = ?", array_keys($params)));

            // updated Date
            $setValue = $setValue.", update_date = default";

            $setWhere = implode(" AND ", array_map(fn($attr) => "$attr = ?", array_keys($where)));

            $query = "UPDATE $tableName SET $setValue WHERE $setWhere";

            $stmt = mysqli_prepare($this->conn, $query);
            if($stmt === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $param = array_merge(array_values($params), array_values($where));

            $bind = mysqli_stmt_bind_param($stmt, $type, ...$param);
            if($bind === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $exec = mysqli_stmt_execute($stmt);
            if($exec === false) {
                throw new Exception(mysqli_error($this->conn));
            }

            $resultRow = mysqli_stmt_affected_rows($stmt);
            if($resultRow == 0) {
                throw new Exception(mysqli_error($this->conn));
            }

            mysqli_stmt_close($stmt);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function list($tableName, $page, $where)
    {
        try {
            $resultOnPage = 5;                                    // 화면에 보여질 갯수
            $calcPage = $resultOnPage * (intval($page) - 1);     // 데이터 시작점

            $whereSql='';
            if(!empty($where)) {
                foreach ($where as $key => $value) {
                    $whereSql .= "$key = '$value' AND ";
                }
            }
            $whereSql .= "(status = 't' OR status = 'a')";

            $statement = $this->pdo->prepare("SELECT COUNT(*) AS count FROM $tableName WHERE $whereSql");
            $statement->execute();
            $total = $statement->fetch();                           // 데이터 총 갯수

            $statement = $this->pdo->prepare("SELECT * FROM $tableName WHERE $whereSql ORDER BY no DESC LIMIT $calcPage, $resultOnPage");
            $statement->execute();
            $listData = $statement->fetchAll();

            return [
              'total' => $total['count'],
              'resultOnPage' => $resultOnPage,
              'calcPage' =>  $calcPage,
              'listData' => $listData
            ];

        } catch (\Exception $e) {
            return $e;
        }
    }
}
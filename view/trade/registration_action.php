<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/head.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/view/layout/header.php';

use app\lib\Database;
use app\lib\Utils;
use app\lib\exception\DatabaseException;
use app\lib\exception\CustomException;

// db connect
$db = new Database;
try {
    $preUrl = $_SERVER['HTTP_REFERER'];
    $commission = 0.05;

    if (empty($_POST['boardName']) || empty($_POST['productName']) || empty($_POST['productPrice'])
        || empty($_POST['productRealPrice']) || empty($_POST['productCommission']) || empty($_POST['productInformation'])) {
        throw new Exception('필수 데이터를 입력해 주세요');
    }
    if ($_POST['productPrice'] < 1000) {
        throw new Exception('최소 금액은 1000원 입니다.');
    }
    if (!preg_match("/^[0-9]/i", $_POST['productPrice'])) {
        throw new Exception('숫자만 입력해 주세요');
    }

    // 금액 Validation
    $commissionPrice = $_POST['productPrice'] * $commission;
    if($commissionPrice != $_POST['productCommission']) {
        throw new CustomException('기입된 정보가 다릅니다.');
    }
    if($_POST['productRealPrice'] != $_POST['productPrice'] - $commissionPrice) {
        throw new CustomException('기입된 가격 정보가 다릅니다.');
    }



    if ($_FILES['imageInfo']['name']) {
        $fileName = Utils::fileUpload($_FILES);
        if (!$fileName) {
            throw new Exception('파일 저장에 실패하였습니다.');
        }
    } else {
        // 이미지 없으면 기본 이미지 적용
        $fileName = "default/basic.svg";
    }


    $db->pdo->beginTransaction();

    // image DB insert
    $imgNo = $db->save('tr_image', ['image_name' => $fileName, 'image_path' => '/upload/']);
    if (!$imgNo) {
        throw new DatabaseException('이미지 저장에 실패했습니다.');
    }

    // product DB insert
    $productNo = $db->save('tr_product', ['user_no'=>$_SESSION['auth']['no'], 'image_no'=>$imgNo, 'name'=>$_POST['productName'], 'information'=>$_POST['productInformation'],
                        'before_price'=>$_POST['productPrice'], 'commission'=>$_POST['productCommission'], 'after_price'=>$_POST['productRealPrice']]);
    if(!$productNo) {
        throw new DatabaseException('상품 저장에 실패 했습니다.');
    }

    // board DB insert
    $boardNo = $db->save('tr_board', ['user_no'=>$_SESSION['auth']['no'], 'image_no'=>$imgNo, 'title'=>$_POST['boardName'], 'content'=>$_POST['productInformation'],
                        'board_type'=>'trade', 'reference_no'=>$productNo, 'status'=>'ALIVE']);
    if(!$boardNo) {
        throw new DatabaseException('게시물 저장에 실패 했습니다.');
    }

    $db->pdo->commit();
    header('Location: /view/trade/list.php');
    exit();

} catch (CustomException $e) {
    $e->setErrorMessages($e);
} catch (DatabaseException $e) {
    $db->pdo->rollBack();
    $e->setErrorMessages($e);
} catch (Exception $e) {
    $message = $e->getMessage();
    require_once $_SERVER['DOCUMENT_ROOT'] . '/view/error/error_prv.php';
    die();
}
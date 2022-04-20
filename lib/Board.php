<?php
namespace app\lib;

class Board
{
    protected $field;
    protected $db;
    protected $session;
    public int $page = 1;
    public string $url;
    public $listHtml;
    public $listBtn;

    protected function rule()
    {
        return [
            'user_list.php' => 'tr_account'
        ];
    }


    public function listUp($url, $getData)
    {
        if(isset($getData['page'])) {
            $this->page = $getData['page'];
        }

        // url 별 테이블 분기
        $url = explode('?', $url)[0];
        $url = explode('/', $url);
        $this->url = end($url);

        $resultArr = (new Database)->list('tr_account', $this->page, []);

        // page list
        $this->listHtml = (new Field)->userList($resultArr['listData']);

        // page button
        unset($resultArr['listData']);
        $resultArr['page'] = $this->page;
        $this->listBtn = Field::listBtn($resultArr);
    }


    public function withdrawalList()
    {
        try {
            $db = new Database;
            $userList = $db->findAll('tr_mileage_use_log', ['status'=>'AWAIT', 'method'=>'withdrawal']);

            foreach ($userList as $key => $value) {
                $userInfo = $db->findOne('tr_account', ['no' => $value['user_no']]);
                $userList[$key]['name'] = $userInfo['name'];
                $userList[$key]['id'] = $userInfo['id'];
                $userList[$key]['status'] = '출금신청';
            }



            return $userList;

        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }


}
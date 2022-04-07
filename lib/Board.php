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

    public function __construct()
    {
        $this->field = new Field;
        $this->db = new Database;
        $this->session = new Session;
    }

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

        $resultArr = $this->db->findAll($this->rule()[$this->url], $this->page);

        // page list
        $this->listHtml = $this->field->userList($resultArr['listData']);

        // page button
        unset($resultArr['listData']);
        $resultArr['page'] = $this->page;
        $this->listBtn = $this->field->listBtn($resultArr);
    }



}
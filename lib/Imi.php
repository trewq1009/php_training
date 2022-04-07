<?php

namespace app\lib;



class Imi
{
    public string $id;
    public string $name;
    protected $db;
    protected $util;
    protected $session;

    public function __construct() {
        $this->db = new Database;
        $this->util = new Utils;
        $this->session = new Session;
    }


    private function rule() {
        return [
            'imiId' => 'id',
            'imiPw' => 'pw',
        ];
    }


    private function getTable() {
        return 'tr_account_admin';
    }


    public function login($postData) {
        var_dump($postData);
    }


}
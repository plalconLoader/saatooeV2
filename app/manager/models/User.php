<?php

namespace App\Manager\Models;
use Phalcon\Mvc\Model;
class User extends Base
{
    public $id;
    public $mobile;
    public $password;
    public $state;
    public $first;
    public $create_time;
    public $is_del;
    public $create_ip;
    public $last_time;
    //框架获取表名
    public function getSource()
    {
        return 'ste_user';
    }
}
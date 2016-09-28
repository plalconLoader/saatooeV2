<?php

namespace App\Manager\Models;


class UserWater extends Base
{

    public $id;
    public $user_id;
    public $money;
    public $classify; //0 => 余额流水  1 => 善贝流水
    public $create_time;
    public $water;

    public function getSource()
    {
        return 'ste_user_water';
    }
}
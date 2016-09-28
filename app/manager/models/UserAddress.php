<?php

namespace App\Manager\Models;


class UserAddress extends Base
{
    public $id;
    public $user_id;
    public $real_name;
    public $mobile;
    public $province;
    public $city;
    public $area;
    public $address;
    public $create_time;
    public $defaults;
    public $is_del;

    public function getSource()
    {
        return 'ste_user_address';
    }
}
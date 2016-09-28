<?php


namespace App\Manager\Models;


class UserCoupon extends Base
{

    public $id;
    public $user_id;
    public $ticket;
    public $is_use;
    public $value;
    public $condition;
    public $display;
    public $use_time;
    public $goods_limit;
    public $start_time;
    public $invalid_time;

    public function getSource()
    {
        return 'ste_user_coupon';
    }
}
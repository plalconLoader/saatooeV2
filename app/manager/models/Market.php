<?php

namespace App\Manager\Models;


class Market extends Base
{
    public $id;
    public $name;
    public $begin_time; //开始时间
    public $invalid_time; //结束时间
    public $condition; //条件
    public $value; //赠送的值 如果是商品促销则是商品的ID
    public $market_type; //0套餐促销 1 单品促销，只对商品促销起作用
    public $classify; //1满M元少N元  2满M元少N折 3满M元增送券 4充M元送N元 6 单品或套餐促销
    public $state; //状态
    public $is_del; //软删除

    public function getSource()
    {
        return 'ste_market';
    }
}
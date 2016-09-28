<?php

namespace App\Manager\Models;


class GoodsType extends Base
{


    public $id;
    public $pid;
    public $path;
    public $name;
    public $image;
    public $sort;
    public $create_time;
    public $state;

    public function getSource()
    {
        return 'ste_goods_type';
    }
}
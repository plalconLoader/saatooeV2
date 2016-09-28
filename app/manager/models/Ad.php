<?php

namespace App\Manager\Models;


class Ad extends Base
{
    public $id;
    public $position_name;
    public $image;
    public $link;
    public $sort;
    public $start_time;
    public $invalid_time; //生效时间
    public $state; //状态

    public function getSource()
    {
        return 'ste_ad';
    }
}
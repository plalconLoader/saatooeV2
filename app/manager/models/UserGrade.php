<?php

namespace App\Manager\Models;


class UserGrade extends Base
{
    public $id;
    public $grade; //等级名称
    public $start_exp;
    public $end_exp;
    public $sale;
    public $create_time;
    public function getSource()
    {
        return 'ste_user_grade';
    }
}
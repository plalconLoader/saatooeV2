<?php

namespace App\Manager\Models;


class AdPosition extends Base
{

    public $id;
    public $position_name;
    public $width;
    public $height;
    public $state;

    public function getSource()
    {
        return 'ste_ad_position';
    }
}
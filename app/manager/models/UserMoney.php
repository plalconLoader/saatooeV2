<?php
/**
 * Created by PhpStorm.
 * User: 王杰
 * Mail: laraveler@163.com
 * Date: 2016/9/23
 * Time: 17:11
 */

namespace App\Manager\Models;


class UserMoney extends Base
{

    public $id;
    public $user_id;
    public $balance;
    public $saapay;
    public $exp;

    public function getSource()
    {
        return 'ste_user_money';
    }
}
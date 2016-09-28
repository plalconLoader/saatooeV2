<?php

namespace App\Wechat\Controllers;


class AuthController extends ControllerBase
{

    public function loginAction()
    {
        var_dump('微信端用户登录!');
    }

    public function logoutAction()
    {
        var_dump('微信端!');
    }
}
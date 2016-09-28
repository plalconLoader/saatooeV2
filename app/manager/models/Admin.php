<?php


namespace App\Manager\Models;


class Admin extends Base
{
    public $id;
    public $role_id;
    public $username;
    public $password;
    public $create_time;
    public $state;
    public $last_time;
    public $last_ip;

    public function getSource()
    {
        return 'ste_admin';
    }
}
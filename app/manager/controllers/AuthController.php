<?php

namespace App\Manager\Controllers;


use App\Manager\Models\Admin;
use Phalcon\Mvc\Controller;
use Phalcon\Validation;

class AuthController extends Controller
{

    public function initialize()
    {

        if($this -> router -> getActionName() != 'logout'){
            if($this -> cookies -> has('admin_id') && $this -> cookies -> has('admin_login')
                && $this -> cookies -> has('admin_role_id')
            ){
                return $this -> response -> redirect('index/index');
            }
        }

    }

    public static function post($key = null)
    {
        return ($key) ? $_POST[$key] : $_POST;
    }

    public function loginAction()
    {
        if($this -> request -> isPost()){

            $valid = new Validation();

            $valid -> add(['username','password'],new Validation\Validator\PresenceOf());


            if(count($valid -> validate(self::post()))){
                return $this -> response -> redirect('auth/login');
            }

            $username = self::post('username');
            $orm = Admin::findFirst([
                "username = '{$username}'"
            ]);

            if(empty($orm)){
                return \helpers::redirect('auth/login','帐号不存在!');
            }

            if($orm -> state != '1'){
                return \helpers::redirect('auth/login','帐号已被冻结!');
            }

            if(password_verify(self::post('password'),$orm -> password)){

                //更新最后登录的时间
                $orm -> save([
                    'last_ip' => $this -> request -> getClientAddress(),
                    'last_time' => time(),
                ]);
                return $this -> response -> redirect('auth/set/'.$orm -> id.'/'.$orm -> role_id);
            }else{

                return \helpers::redirect('auth/login','密码错误!');
            }
        }

        return $this -> view -> pick('auth/login');
    }


    /**
     * @param $id
     * @param $rid
     * 设置cookie
     */
    public function setAction($id,$rid)
    {
        $expires = time() + 15 * 86400;
        $this -> cookies -> set('admin_login',true,$expires);
        $this -> cookies -> set('admin_id',$id,$expires);
        $this -> cookies -> set('admin_role_id',$rid,$expires);
        return $this -> response -> redirect('index/index'); //去到首页
    }

    //退出登录
    public function logoutAction()
    {

        $this -> cookies -> get('admin_id') -> delete();
        $this -> cookies -> get('admin_login') -> delete();
        $this -> cookies -> get('admin_role_id') -> delete();
        return $this -> response -> redirect('auth/login');
    }
}
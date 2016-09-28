<?php

namespace App\Manager\Controllers;
use App\Manager\Models\Admin;
use App\Manager\Models\User;
use Phalcon\Mvc\Controller;
use Phalcon\Validation;

class IndexController extends ControllerBase
{

    public static $models = [
        'u' => User::class
    ];

    /**
     * 后台首页
     */
    public function indexAction()
    {
        
    }


    public function modifyPasswordAction()
    {

        if(self::$isPost){
            $valid = new Validation();
            $valid -> add(['origin','newPwd','repeatPwd'],new Validation\Validator\PresenceOf());

            if(count($valid -> validate(self::post()))){
                return \helpers::redirect('index/index','信息错误!');
            }


            $response = json_decode($this -> verifyAction(self::post('origin')) -> getContent(),true);



            if(!(array_search('valid',$response))){
                return \helpers::redirect('index/index','原密码错误!');
            }

            $newPwd = self::post('newPwd');
            $repeat = self::post('repeatPwd');

            if(strcmp($newPwd,$repeat) !== 0){

                return \helpers::redirect('index/index','两次密码不一致!');
            }

            $orm = unserialize($response['orm']);

            $orm -> password = password_hash($repeat,PASSWORD_BCRYPT);
            $orm -> save()
                ? $msg = '修改成功,下次请使用新密码登录!'
                : $msg = '修改错误!';

            return \helpers::redirect('index/index',$msg);

        }
        return $this -> view -> pick('index/modify-password');
    }


    /**
     * @param $pwd
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     * 验证原密码
     */
    public function verifyAction($pwd)
    {
        $id = self::cookie('admin_id') -> getValue();
        $orm = Admin::findFirst($id);

        if(password_verify($pwd,$orm -> password)){
            $res = ['msg' => 'valid','orm' => serialize($orm)];
        }else{
            $res = ['msg' => 'invalid'];
        }

        return $this -> response -> setJsonContent($res);

    }


}
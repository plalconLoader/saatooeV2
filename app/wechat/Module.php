<?php

namespace App\Wechat;

use Phalcon\Crypt;
use Phalcon\Http\Response\Cookies;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\DiInterface;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Session\Adapter\Files as Session;

class Module implements ModuleDefinitionInterface
{
    /**
     * 注册自定义加载器
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new Loader();

        $loader->registerNamespaces(
            array(
                'App\Wechat\Controllers' => '../app/wechat/controllers/',
                'App\Wechat\Models'      => '../app/wechat/models/',
            )
        );

        $loader->register();
    }

    /**
     * 注册自定义服务
     */
    public function registerServices(DiInterface $di)
    {
        // Registering a dispatcher
        $di->set('dispatcher', function () {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace('App\Wechat\Controllers');
            return $dispatcher;
        });

        $di->set(
            'view',
            function () {

                $view = new View();

                $view->setViewsDir('../app/wechat/views/');

                $view->registerEngines(
                    array(
                        ".twig" => 'twigServer'
                    )
                );
                return $view;
            }
        );

        $di -> set('twigServer',function ($view,$di){

            $volt = new Volt($view,$di);

            $volt -> setOptions([
                'compiledPath' => function ($templatePath) {

                    return '../storage/views/wechat/'.md5($templatePath).'.php'; //解析的路径
                },
                'compileAlways' => true
            ]);
            return $volt;
        });


        //cookie设置
        $di->set('cookies', function () {
            $cookies = new Cookies();
            $cookies->useEncryption(true);
            return $cookies;
        });

        //设置加密信息
        $di -> set('crypt',function (){
            $crypt = new Crypt();
            $crypt -> setKey('.*//*^*f+_)6$%^&*(');
            return $crypt;
        });


        //设置session
        $di->setShared('session', function () {
            $session = new Session();
            $session->start(); //启动session
            return $session;
        });
    }
}
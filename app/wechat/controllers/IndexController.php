<?php

namespace App\Wechat\Controllers;

use Phalcon\Mvc\Controller;

class IndexController extends ControllerBase
{
    public function indexAction()
    {

        if($this -> request -> getHttpHost() != 'e.com'){
            return $this -> response -> redirect('http://e.com',true);
        }

        return $this -> response -> setJsonContent([
            'AngularJs to Saatooe V2.0'
        ]);
    }
}
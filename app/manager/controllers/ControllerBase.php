<?php


namespace App\Manager\Controllers;


use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public static $isPost;

    public static $checked = [
        1 => 'checked'
    ];

    //中间件
    public function initialize()
    {
        if($this -> request -> isPost()){
            self::$isPost = true;
        }
    }

    //认证用户是否登录了
    public function onConstruct()
    {
        if(!self::cookieHas('admin_id')|| !self::cookieHas('admin_login') || !self::cookieHas('admin_role_id')
        ){
            return $this -> response -> redirect('auth/login');
        }
    }


    /**
     * @param null $key
     * @param null $default
     * @param string $filter
     * @return mixed
     * todo 获取get参数
     */
    public function get($key = null, $default = null, $filter = 'trim')
    {
        return $this -> request -> get($key,$filter,$default);
    }

    /**
     * @param null $key
     * @param null $default
     * @param string $filter
     * @return mixed
     * todo 获取post数据
     */
    public function post($key = null, $default = null, $filter = 'trim')
    {
        return $this -> request -> getPost($key,$filter,$default);
    }


    /**
     * @return \Phalcon\Http\File[]|\Phalcon\Http\Request\FileInterface[]
     * todo 获取文件
     */
    public function file()
    {
        return $this -> request -> getUploadedFiles();
    }

    /**
     * @param $content
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     * todo 返回json数据格式
     */
    public function json(array $content)
    {
        return $this -> response -> setJsonContent($content);
    }


    /**
     * @param $name
     * @param null $value
     * @param int $expire
     * @param string $path
     * @param bool|false $secure
     * @param string $domain
     * @param bool|false $httpOnly
     * @return \Phalcon\Http\Cookie|\Phalcon\Http\CookieInterface|\Phalcon\Http\Response\Cookies|\Phalcon\Http\Response\CookiesInterface
     * todo 设置 | 获取 cookie
     */
    public function cookie($name,$value = null,$expire = 86400 * 100,$path = '/',$secure = false,$domain = null,$httpOnly = false)
    {
        if($value){

            return $this -> cookies -> set($name,$value,$expire,$path,$secure,$domain,$httpOnly);
        }else{
            return $this -> cookies -> get($name);
        }
    }

    /**
     * @param $name
     * @param null $value
     * todo 设置session
     */
    public function setSession($name, $value = null)
    {
        $this -> session -> set($name,$value);

    }


    /**
     * @param $name
     * @param null $default
     * @param bool|false $remove
     * @return mixed
     * todo 获取session
     */
    public function getSession($name,$default = null,$remove = false)
    {
        return $this -> session -> get($name,$default,$remove);
    }


    /**
     * @return bool|string
     * todo 获取IP地址
     */
    public function ip()
    {
        return $this -> request -> getClientAddress();
    }


    /**
     * @param array $data
     * todo 设置数据到视图中
     */
    public function view(array $data)
    {
        $this -> view -> setVars($data);
    }

    /**
     * todo 禁止视图展示
     */
    public function disable()
    {
        $this -> view -> disable();
    }

    /**
     * @param $key
     * @return bool
     * todo 获取cookie是否设置
     */
    public function cookieHas($key)
    {
        return $this -> cookies -> has($key);
    }

    /**
     * @param $key
     * @return bool
     * todo 验证key是否有
     */
    public function has($key)
    {
        return $this -> request -> has($key);
    }
}
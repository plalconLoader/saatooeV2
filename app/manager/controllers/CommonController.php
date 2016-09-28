<?php

namespace App\Manager\Controllers;


class CommonController extends ControllerBase
{
    //页面跳转信息提示
    public function showAction()
    {
        self::view([
            'uri' => self::get('uri'),
            'msg' => self::get('msg'),
            'time' => self::get('time',20000)
        ]);
    }

    //上传图片
    public function uploadAction()
    {
        $files = self::file(); //获取上传的图片信息

        $result = [];
        foreach ($files as $key => $val)
        {
            $origin = $val -> getName();
            $tmp = $val -> getTempName();
            if(!self::limitMime($val -> getType())){
                echo '<script>top.iframe_error_callback("'.$origin.'不合法!")</script>';
                continue;
            }
            $ossFilename = self::ossFileName($origin);
            $result[] = \upload::handle($tmp,$ossFilename);
        }
        if(count($result)){
            echo '<script>top.iframe_success_callback('.json_encode($result).')</script>';
        }else{
            echo '<script>top.iframe_error_callback("图片上传错误~")</script>';
        }
    }


    /**
     * @param $mime
     * @return bool
     * 限制类型是否合法
     */
    private static function limitMime($mime)
    {
        return in_array($mime,['image/png','image/jpeg','image/gif','image/jpeg']);
    }

    /**
     * @param $origin
     * 制作随机文件名称
     */
    private static function ossFileName($origin)
    {
        return md5(md5($origin).time().uniqid()) .'.'.pathinfo($origin,PATHINFO_EXTENSION);
    }
}
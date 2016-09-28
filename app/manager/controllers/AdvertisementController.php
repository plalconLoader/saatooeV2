<?php

namespace App\Manager\Controllers;


use App\Manager\Models\Ad;
use App\Manager\Models\AdPosition;
use OSS\OssClient;
use Phalcon\Config\Adapter\Ini;
use Phalcon\Validation;

class AdvertisementController extends ControllerBase
{
    public static $m = [
        'p' => AdPosition::class,
        'a' => Ad::class
    ];

    //广告位列表
    public function positionAction()
    {

        $builder = $this -> modelsManager ->createBuilder()
            -> addFrom(self::$m['p'],'p')
            -> orderBy('id desc');

        $data = \helpers::queryBuilder($builder,self::get('page',1),20);

        self::view([
            'data' => $data,
            'append' => ''
        ]);

        $this -> view -> pick('ad/position');
    }


    //添加广告位
    public function addPositionAction()
    {
        if(self::$isPost){
            $valid = new Validation();
            $valid -> add(['position_name'],new Validation\Validator\PresenceOf());

            if(count($valid -> validate(self::post()))){
                return $this -> response -> redirect('advertisement/position');
            }

            $model = new AdPosition();

            $model -> create(self::post())
                ? $msg = '新增成功!'
                : $msg = '新增失败!';

            return \helpers::redirect('advertisement/position',$msg);
        }

        return $this -> view -> pick('ad/add-position');
    }

    //编辑广告位
    public function editPositionAction($id)
    {

        $orm = AdPosition::findFirst($id);

        if(self::$isPost){

            $data = [
                'position_name' => self::post('position_name'),
                'height' => self::post('height'),
                'width' => self::post('width'),
                'state' => self::post('state',0)
            ];
            $orm -> update($data,"id = '{$id}'")
                ? $msg = '更改成功!'
                : $msg = '更改错误!';
            return \helpers::redirect('advertisement/position',$msg);
        }


        self::view([
            'data' => $orm,
            'checked' => [1 => 'checked']
        ]);

        return $this -> view -> pick('ad/edit-position');
    }

    //删除广告位或者广告
    public function handleAction()
    {
        $valid = new Validation();
        $valid -> add(['aid','t'],new Validation\Validator\PresenceOf());

        if(count($valid -> validate(self::post()))){
            $res = ['msg' => 'invalid'];
        }else{
            $res = ['msg' => 'valid'];
        }

        $table = [
            'p' => 'ste_ad_position',
            'a' => 'ste_ad'
        ];

        $id = self::post('aid');
        \Mysql::delete($table[self::post('t')],"id = '{$id}'");

        return $this -> response -> setJsonContent($res);
    }

    //广告列表
    public function adListAction()
    {

        if(self::has('position_name')){

            $name = self::get('position_name');
            $where = "position_name = '$name'";
        }else{
            $where = "";
        }

        $builder = $this -> modelsManager -> createBuilder()
            -> addFrom(self::$m['a'],'a')
            -> orderBy("sort desc")
            -> where($where);
        $data = \helpers::queryBuilder($builder,self::get('page',1),20);

        self::view([
            'data' => $data,
            'position' => self::position(),
            'append' => \helpers::builderAppend(self::get())
        ]);

        return $this -> view -> pick('ad/ad-list');
    }


    //新增广告
    public function adCreateAction()
    {
        if(self::$isPost){
            return self::postHandle(true);
        }

        self::view([
            'position' => self::position()
        ]);

        return $this -> view -> pick('ad/ad-create');
    }

    //获取所有广告位
    private static function position()
    {
        $position = AdPosition::find([
            'columns' => 'position_name,id'
        ]);
        return $position;
    }


    //编辑广告
    public function adEditAction($id = null)
    {
        $valid = new Validation();

        $valid -> add(['id'],new Validation\Validator\PresenceOf());

        if(count($valid -> validate(['id' => $id]))){
            return \helpers::redirect('参数错误!','advertisement/adList');
        }

        $orm = Ad::findFirst($id);

        if(empty($orm)){
            return \helpers::redirect('无此广告!','advertisement/adList');
        }

        //post动作
        if(self::$isPost){ //处理修改广告的数据
            return self::postHandle(false,$orm);
        }

        self::view([
            'data' => $orm,
            'position' => self::position(),
            'selected' => [$orm -> position_name => 'selected'],
            'checked' => [1 => 'checked']
        ]);

        return $this -> view -> pick('ad/ad-edit');
    }


    /**
     * @param bool|true $create
     * @param null $orm
     * @return \Phalcon\Http\Response
     * 新增和编辑在一起操作
     */
    private  function postHandle($create = true,$orm = null)
    {
        $valid = new Validation();


        $valid -> add(['time_between','ad_image','position_name','link','sort'],new Validation\Validator\PresenceOf());

        if(count($valid -> validate(self::post()))){
            return \helpers::redirect('参数错误!','advertisement/adList');
        }

        list($start,$end) = explode('到',self::post('time_between'));

        $data = [
            'position_name' => self::post('position_name'),
            'image' => self::post('ad_image'),
            'link' => self::post('link'),
            'sort' => self::post('sort',1),
            'start_time' => strtotime(trim($start)),
            'invalid_time' => strtotime(trim($end)),
            'state' => self::post('state',0)
        ];

        $model = new Ad();

        ($create)
            ? ($model -> create($data)
                ? $msg = '添加成功!'
                : $msg = '添加失败!')
            : ($orm -> save($data)
                ? $msg = '编辑成功!'
                : $msg = '修改错误!');

        return \helpers::redirect('advertisement/adList',$msg);
    }
}
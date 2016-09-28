<?php

namespace App\Manager\Controllers;


use App\Manager\Models\GoodsType;
use Phalcon\Validation;

class TypeController extends ControllerBase
{
    //商品分类列表
    public function goodsTypeAction()
    {
        $data = GoodsType::find([
            "order" => "concat(path,id)"
        ]);

        self::view([
            'data' => $data
        ]);
        return $this -> view -> pick('type/goods-type');
    }

    //添加分类
    public function addGoodsTypeAction()
    {

        if(self::$isPost){
            $valid = new Validation();
            $valid -> add(['name','parent'],new Validation\Validator\PresenceOf());

            if(count($valid -> validate(self::post()))){
                return \helpers::redirect('type/goodsType','参数错误!');
            }

            $pid = self::post('parent');
            $model = new GoodsType();

            $data = [
                'name' => self::post('name','','trim'),
                'create_time' => time(),
                'state' => self::post('state',0),
                'image' => '',
                'sort' => self::post('sort',0)
            ];
            if($pid == 0){ //添加为1级分类

                $data['pid'] = 0;
                $data['path'] = '0,';
            }else{

                $orm = $model -> findFirst($pid); //获取上级ID的路径信息

                $path = $orm -> path;
                $data['pid'] = $pid;
                $data['path'] = $path.$pid.',';
            }

            $model -> create($data)
                ? $msg = '添加成功!'
                : $msg = '添加失败!';

            return \helpers::redirect('type/goodsType',$msg);
        }

        self::view([
            'types' => \common::allGoodsType()
        ]);
        return $this -> view -> pick('type/add-goods-type');
    }


    //删除分类
    public function handleAction($id)
    {
        return $this -> response -> setJsonContent(\Mysql::delete('ste_goods_type',"id = '{$id}'"));
    }
}
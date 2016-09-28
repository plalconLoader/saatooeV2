<?php

namespace App\Manager\Controllers;

use App\Manager\Models\Market;
use Phalcon\Validation;

class MarketController extends ControllerBase
{

    public static $m = [
        'm' => Market::class
    ];


    //营销的类型
    public static $classify = [
        1 => '满M元少N元',
        2 => '满M元少N折',
        3 => '满M元增送券',
        4 => '充M元送N元',
        6 => '单品或套餐促销'
    ];


    /**
     * 商品促销
     * 就是在一段时间内商品的价格以定于平时的售价进行出售
     */
    public function GoodsAction()
    {

        self::view([
            'data' => (object)[],
            'append' => ''
        ]);

        return $this -> view -> pick('market/market-goods');
    }

    /**
     * 满减营销列表等
     * 1.满M元少N元
     * 2.满M元送N元优惠券
     * 3.充值M元送N元
     */
    public function saleAction()
    {

        $builder = $this -> modelsManager -> createBuilder()
            -> addFrom(self::$m['m'],'m')
            -> where("is_del = '0'")
            -> inWhere('id',range(1,4))
            -> orderBy('id desc');
        $data = \helpers::queryBuilder($builder,self::get('page',1),20);

        self::view([
            'data' => $data,
            'append' => \helpers::builderAppend(self::get()),
            'classify' => self::$classify
        ]);
        return $this -> view -> pick('market/market-sale');
    }

    //新增营销活动
    public function addSaleAction()
    {
        if(self::$isPost){
            return self::saleHandle();
        }

        self::view([
            'classify' => self::$classify
        ]);

        return $this -> view -> pick('market/add-sale');
    }


    //更改营销活动
    public function editSaleAction($id = null)
    {
        $valid = new Validation();
        $valid -> add(['id'],new Validation\Validator\PresenceOf());
        if(count($valid -> validate(['id' => $id]))){
            return \helpers::redirect('market/sale','参数错误!');
        }

        $orm = Market::findFirst($id);

        if(empty($orm)){
            return \helpers::redirect('market/sale','无此营销活动!');
        }

        if(self::$isPost){
            return self::saleHandle(false,$orm);
        }

        self::view([
            'data' => $orm,
            'selected' => [ $orm -> classify => 'selected'],
            'classify' => self::$classify,
            'checked' => self::$checked
        ]);

        return $this -> view -> pick('market/edit-sale'); //视图展示
    }

    /**
     * @param bool|true $create
     * @param null $orm
     * @return \Phalcon\Http\Response
     * 更新 | 修改活动营销
     */
    private function saleHandle($create = true,$orm = null)
    {
        $valid = new Validation();
        $valid -> add(['name','between_time','classify','condition','value'],new Validation\Validator\PresenceOf());
        if(count($valid -> validate(self::post()))){
            return \helpers::redirect('market/sale','参数错误!');
        }

        list($start,$end) = \helpers::formatDate(self::post('between_time'));

        $data = [
            'name' => self::post('name'),
            'begin_time' => $start,
            'invalid_time' => $end,
            'condition' => trim(self::post('condition')),
            'value' => trim(self::post('value')),
            'classify' => self::post('classify'),
            'market_type' => '1',
            'state' => self::post('state',0),
        ];

        if($create){

            $model = new Market();
            $model -> create($data)
                ? $msg = '添加成功!'
                : $msg = '添加错误!';
        }else{
            $orm -> save($data)
                ? $msg = '更新成功'
                : $msg = '更新错误!';
        }

        return \helpers::redirect('market/sale',$msg);
    }


    //订单优惠券
    public function couponAction()
    {
        return $this -> view -> pick('market/market-coupon');
    }

    //商品兑换券
    public function ticketAction()
    {
        return $this -> view -> pick('market/market-ticket');
    }


    //软删除统一管理
    public function stateHandleAction()
    {
        $id = self::post('mid');

        \Mysql::update('ste_market',[
            'is_del' => 1
        ],['id' => $id])
            ? $res = ['msg' => 'valid']
            : $res = ['msg' => 'invalid'];

        return $this -> response -> setJsonContent($res);
    }
}
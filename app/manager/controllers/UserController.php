<?php

namespace App\Manager\Controllers;


use App\Manager\Models\User;
use App\Manager\Models\UserAddress;
use App\Manager\Models\UserCoupon;
use App\Manager\Models\UserGrade;
use App\Manager\Models\UserInfo;
use App\Manager\Models\UserMoney;
use App\Manager\Models\UserWater;
use Mysql as mysql;
use Phalcon\Validation;

class UserController extends ControllerBase
{
    public static $m = [
        'u' => User::class,//用户主表
        'm' => UserMoney::class,//用户余额
        'i' => UserInfo::class,//用户附表
        'c' => UserCoupon::class,//会员优惠券
        'g' => UserGrade::class, //会员等级
    ];

    //用户列表
    public function listAction()
    {
        $columns = [
            'u.id','u.mobile','u.state','u.create_time','u.last_time','m.balance','m.saapay'
        ];
        $page = self::get('page',1,'int');
        $filter = ['_url','page']; //过滤的数据
        //获取用户关联的数据
        list($where,$bind,$append) = \helpers::builderUserWhere(self::get(),$filter);

        $builder = $this -> modelsManager -> createBuilder()
            -> addFrom(self::$m['u'],'u')
            -> join(self::$m['m'],'u.id = m.user_id','m')
            -> where($where,$bind)
            -> columns($columns)
            -> orderBy('u.id desc');

        $data = \helpers::queryBuilder($builder,$page,18);

        self::view([
            'data' => $data,
            'append' => $append,
            'selected' => [self::get('primary') => 'selected']
        ]);
    }

    //编辑用户
    public function editAction($uid = false)
    {
        $valid = new Validation();
        $valid -> add('user_id',new Validation\Validator\PresenceOf());

        if(count($valid -> validate(['user_id' => $uid]))){
            return \helpers::redirect('user/list','参数错误!');
        }


        if(self::$isPost){ //post动作修改用户信息

            //开启事物
            mysql::begin();

            mysql::update('ste_user',['state' => self::post('state',0)],['id' => $uid]);

            //获取用户等级和优惠的额度
            list($grade,$gradeSale) = explode('#',self::post('grade'));

            $info = [
                'email' => self::post('email',''),
                'real_name' => self::post('real_name',''),
                'qq' => self::post('qq',''),
                'weixin' => self::post('weixin',''),
                'birthday' => self::post('birthday'),
                'grade' => $grade,
                'grade_sale' => $gradeSale,
            ];

            mysql::update('ste_user_info',$info,['user_id' => $uid]);


            $moneyOrm = UserMoney::findFirst([
                "user_id = '{$uid}'"
            ]);

            $newBalance = self::post('balance'); //新的钱
            $newSaapay = self::post('saapay'); //善贝

            //记录用户流水日志
            if($newBalance != $moneyOrm -> balance){ //新的钱不等于原来的钱
                \helpers::writeUserWater($uid,$newBalance,$moneyOrm -> balance);
            }

            if($newSaapay != $moneyOrm -> saapay){
                \helpers::writeUserWater($uid,$newSaapay,$moneyOrm -> saapay,'1','善贝');
            }

            $moneyOrm -> save([
                'balance' => $newBalance,
                'saapay' => $newSaapay
            ]);

            mysql::commit();

            return \helpers::redirect('user/list','更新成功!');
        }

        $columns = [
            'u.mobile','u.id','u.create_time','u.create_ip','u.state','u.last_time',
            'i.grade','i.grade_sale','i.real_name','i.sex','i.qq','i.weixin','i.birthday','i.email','m.balance','m.saapay','m.exp','i.grade_sale'
        ];
        $data = $this -> modelsManager -> createBuilder()
            -> addFrom(self::$m['u'],'u')
            -> join(self::$m['i'],"u.id = i.user_id",'i')
            -> join(self::$m['m'],'u.id = m.user_id','m')
            -> where("u.id = '{$uid}'")
            -> columns($columns)
            -> getQuery()
            -> execute()
            -> getFirst();

        if(empty($data)){
            return \helpers::redirect('user/list','用户不存在');
        }

        //获取所有等级
        $grades = UserGrade::find([
            "columns" => 'id,grade,sale'
        ])
            -> toArray();

        //获取用户的优惠券
        $coupons = UserCoupon::find([
            "user_id = '{$uid}'",
            'order' => 'id desc',
            'columns' => 'id,ticket,is_use,value,condition,goods_limit,invalid_time'
        ]);

        //收货地址
        $address = UserAddress::find([
            "user_id = '{$uid}'",
            'order' => 'id desc',
            'columns' => 'id,real_name,mobile,province,city,area,address,create_time,defaults'
        ]);

        //视图中的数据
        self::view([
            'user' => $data,
            'grades' => $grades,
            'state' => ['1' => 'checked'],
            'sex' => ['女','男','未知'],
            'coupons' => $coupons,
            'address' => $address
        ]);
    }


    /**
     * todo 设置用户的状态到回收站中
     */
    public function recycleAction()
    {
        $valid = new Validation();

        $valid -> add(['uid','is_del'],new Validation\Validator\PresenceOf([
            'message' => 'ID不能为空'
        ]));
        $valid -> add('uid',new Validation\Validator\Regex([
            'pattern' => '/^\d+$/'
        ]));

        $valid -> add('is_del',new Validation\Validator\Between([
            'minimum' => 0,
            'maximum' => 1
        ]));

        $messages = $valid -> validate(self::post());
        $res = [
            'msg' => 'invalid'
        ];
        if (!count($messages)) { //没有错误信息
            $uid = self::post('uid'); //用户ID
            $orm = User::findFirst($uid);
            if(!empty($orm)){

                $orm -> is_del = self::post('is_del');
                $orm -> save();
                $res = [
                    'msg' => 'valid'
                ];
            }
        }
        return $this -> response -> setJsonContent($res);
    }


    //用户回收站列表
    public function recycleListAction()
    {
        $columns = [
            'u.id','u.mobile','u.state','u.create_time','u.last_time','m.balance','m.saapay'
        ];
        $page = self::get('page',1,'int');
        $filter = ['_url','page']; //过滤的数据
        //获取用户关联的数据
        list($where,$bind,$append) = \helpers::builderUserWhere(self::get(),$filter,'1');

        $builder = $this -> modelsManager -> createBuilder()
            -> addFrom(self::$m['u'],'u')
            -> join(self::$m['m'],'u.id = m.user_id','m')
            -> where($where,$bind)
            -> columns($columns)
            -> orderBy('u.id desc');

        $data = \helpers::queryBuilder($builder,$page,18);

        self::view([
            'data' => $data,
            'append' => $append,
            'selected' => [self::get('primary') => 'selected']
        ]);
        //展示视图了。
        $this -> view -> pick('user/recycle-list');
    }


    //会员等级列表
    public function gradeAction()
    {
        $builder = $this -> modelsManager -> createBuilder()
            -> addFrom(self::$m['g'],'g')
            -> orderBy("g.id desc");

        $data = \helpers::queryBuilder($builder,self::get('page'),18);

        self::view([
            'data' => $data,
            'append' => ''
        ]);

        $this -> view -> pick('user/user-grade');
    }


    /**
     * @return \Phalcon\Http\Response
     * 新增用户等级
     */
    public function addGradeAction()
    {
        if(self::$isPost){ //POST动作
            $valid = new Validation();

            //验证数据
            $valid -> add(['grade','start_exp','end_exp','sale'],new Validation\Validator\PresenceOf());

            if(count($valid -> validate(self::post()))){
                return \helpers::redirect('user/grade','数据错误!');
            }

            $grade = self::post('grade');
            $startExp = self::post('start_exp');
            $endExp = self::post('end_exp');
            $sale = self::post('sale');

            if($endExp <= $startExp){
                return \helpers::redirect('user/grade','结束经验不能少于开始经验!');
            }

            $orm = new UserGrade();

            $add = $orm -> create([
                'grade' => $grade,
                'start_exp' => $startExp,
                'end_exp' => $endExp,
                'sale' => $sale,
                'create_time' => time(),
            ]);

            if($add){
                $msg = '添加成功!';
            }else{
                $msg = '添加失败!';
            }
            return \helpers::redirect('user/grade',$msg);
        }

        //展示视图
        return $this -> view -> partial('user/add-user-grade');
    }

    /**
     * @param null $id
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface|\Phalcon\Mvc\View
     * 修改用户等级
     */
    public function editGradeAction($id = null)
    {
        $valid = new Validation();

        $valid -> add(['id'],new Validation\Validator\PresenceOf());

        if(count($valid -> validate(['id' => $id]))){
            return $this -> response -> redirect('user/grade');
        }

        $orm = UserGrade::findFirst($id);
        if(self::$isPost){ //POST动作修改等级

            $orm -> save(self::post())
                ? $msg = '修改成功!'
                : $msg = '修改失败！';

            return \helpers::redirect('user/grade',$msg);
        }


        if(empty($orm)){
            return $this -> response -> redirect('user/grade');
        }
        self::view([
            'data' => $orm,
            'disabled' => [1 => 'disabled']
        ]);

        return $this -> view -> pick('user/edit-user-grade');
    }

}
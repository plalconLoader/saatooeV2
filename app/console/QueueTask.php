<?php

use Phalcon\Cli\Task as Console;

class QueueTask extends Console
{

    public static $queue;
    public static $exchange = 'change';
    public static $router = 'router-key';
    public static $conn;
    public static $qName = 'queue';

    public function initialize()
    {

    }

    /**
     * @param array $params
     * todo subscribe模式
     */
    public function runAction()
    {
        $config = [
            'host' => '127.0.0.1',
            'port' => '5672',
            'login' => 'guest',
            'password' => 'guest',
            'vhost' => '/'
        ];

        self::$conn = new AMQPConnection($config);

        self::$conn -> connect() or die('connection invalid');


        $channel = new AMQPChannel(self::$conn); //获取频道

        $exchange = new AMQPExchange($channel);




//        $channel ->setPrefetchCount(1);  平均分发
        self::$queue = new AMQPQueue($channel);
        self::$queue -> setName(self::$qName);
        self::$queue -> setFlags(AMQP_DURABLE);

        $count = self::$queue -> declareQueue(); //声明队列
        self::$queue -> bind(self::$exchange,self::$router);
        echo "count:" . $count."\r\n";

        self::$queue -> consume([$this,'subscribe']);



        //阻塞了。
        self::$conn -> disconnect();
    }

    /**
     * @param AMQPEnvelope $envelope
     * @param AMQPQueue $queue
     * todo 处理消息
     */
    public function subscribe(AMQPEnvelope $envelope, AMQPQueue $queue)
    {
        echo "收到消息:".$envelope -> getBody()."\r\n";
        $queue -> ack($envelope -> getDeliveryTag()); //完成
        $queue -> nack($envelope -> getDeliveryTag());
    }


}
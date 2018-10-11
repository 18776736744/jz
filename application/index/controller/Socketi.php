<?php
// namespace app\wxapi\controller; 
namespace app\index\controller;
use think\Request;
use think\Db;
use Workerman\Worker;
use think\worker\Server;
 
class Socketi extends \think\Controller {
    public function index()
    {
      require_once ROOT_PATH.'/vendor/workerman/Autoloader.php';
        vendor('Workerman.Worker');
 
        // 创建一个Worker监听2346端口，使用websocket协议通讯
        $ws_worker = new Worker("websocket://0.0.0.0:2345");
        
        // 启动4个进程对外提供服务
        $ws_worker->count = 4;
         global $mm;
        $mm = $ws_worker;
        $global_uid = 0;
        $ws_worker->onConnect = function ($connection) {
        global $global_uid;
        $connection->uid = ++$global_uid;

         // 开启一个内部端口，方便内部系统推送数据，Text协议格式 文本+换行符
        // $inner_text_worker = new Worker('Text://0.0.0.0:5678');
        // $inner_text_worker->onMessage = function($connection, $buffer)
        // {

        //      global $mm;
        //     // $data数组格式，里面有uid，表示向那个uid的页面推送数据
        //     $data = json_decode($buffer, true);
        //     // 通过workerman，向uid的页面推送数据

        //      $info =  db('info')->where("id=1")->find();
        //     foreach ($mm->connections as $conn) {
        //              $conn->send($buffer);
        //     } 
        // };
        // $inner_text_worker->listen();


    };
   
    $ws_worker->onMessage = function ($connection,$data) {
        global $mm;
      	$found = explode('|',$data);
        $uid = $found['0'];
        $text = $found['1'];
      	$him_id=$found['2'];
      	db('send')->insert([
        		'uid'=> $uid,
          		'text'=>$text,
          		'him_id'=>$him_id,
          		'time' =>date("Y-m-d h:i:s")
          		
        ]);
        foreach ($mm->connections as $conn) {
                 $conn->send(json_encode(['uid'=>$uid,'text'=>$text,'him_id'=>$him_id]));
        }
    };
        // 运行worker
        Worker::runAll();
    
    }

    public function test()
    {
        // 建立socket连接到内部推送端口
        $client = stream_socket_client('tcp://127.0.0.1:5678',  $errno, $errstr, 1);
        // 推送的数据，包含uid字段，表示是给这个uid推送
       $info =  db('info')->where("id=1")->find();
        // 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
        fwrite($client, json_encode($info)."\n");
    }
}
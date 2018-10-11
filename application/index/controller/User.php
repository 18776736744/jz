<?php
 
namespace app\index\controller;
use think\Db;

 

class User extends \think\Controller
{
   public function index()
   {
    
       // print_r( $_COOKIE);
   }
   

    public function login()
    {
              // return json(['status'=>1,'uid'=> 3,'openid'=>3333333]);die;
      
          //开发者使用登陆凭证 code 获取 session_key 和 openid
        $APPID =  'wx2061ea8e3d1ca0f2';
        $AppSecret = 'ec82a92bfe6488a062c78afc5fabd7a6';

        $code = input('code');
        $url="https://api.weixin.qq.com/sns/jscode2session?appid=".$APPID."&secret=".$AppSecret."&js_code=".$code."&grant_type=authorization_code";
        $arr = file_get_contents($url);  // 一个使用curl实现的get方法请求
        $arr = json_decode($arr,true);
        $openid = $arr['openid'];
        $session_key = $arr['session_key'];
      
        
        // 数据签名校验
        $signature = input('signature');
        $signature2 = sha1($_GET['rawData'].$session_key);  //记住不应该用TP中的I方法，会过滤掉必要的数据
        if ($signature != $signature2) {
           return json(['msg'=>'数据签名验证失败！','status'=>-1]);die;
        }

          $ruser = db('user')->where(array('openid'=>$openid))->find();
        if(empty($ruser)){
               $rawData = json_decode(input('rawData'),true) ;
            $ruser['id'] = db('user')->insertGetId(['openid'=>$openid,'uname'=>filterEmoji($rawData['nickName']),'avatarUrl'=>$rawData['avatarUrl']]);

              
        } 
           
             return json(['status'=>1,'uid'=> $ruser['id'] ,'openid'=>$arr['openid']]);die;
       
      

    }

   
   

}
function filterEmoji($str)
{
    $str = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);

     return $str;
 }
 function vget($url){
    $info=curl_init();
    curl_setopt($info,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($info,CURLOPT_HEADER,0);
    curl_setopt($info,CURLOPT_NOBODY,0);
    curl_setopt($info,CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($info,CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($info,CURLOPT_URL,$url);
    $output= curl_exec($info);
    curl_close($info);
    return $output;
}

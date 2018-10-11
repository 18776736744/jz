<?php 
namespace app\index\controller;
use think\Db;


class Send extends \think\Controller
{
		public function getmysendData(){
			$senddata = db('send')->where(['uid'=>input('uid'),'him_id'=>input('him_id')])
			            ->field('text,time')
						->select();
						return json_encode($senddata);
		}
		public function gethimsendData(){
			$sendata = db('send')->where(['uid'=>input('uid'),'him_id'=>input('him_id')])
			            ->field('text,time')
						->select();
						return json_encode($sendata);
		}
        public function getsendData(){
             $sendata = db('send')->where(['uid'=>input('uid'),'him_id'=>input('him_id')])
			            ->field('text,time,uid')
						->select();
			$senddata = db('send')->where(['uid'=>input('him_id'),'him_id'=>input('uid')])
			            ->field('text,time,uid')
						->select();
						$d = json_encode($sendata);
						$c = json_encode($senddata);
						$data = array_merge_recursive($sendata,$senddata);
          				
                        $arrSort = array();
          				$arr = array();
          				if($sendata&&$senddata){
          					foreach($data AS $key => $value){
                            foreach($value AS $k=>$v){
                                $arrSort[$k][$key] = $v;
	                            }
	                        }
							array_multisort($arrSort['time'], SORT_ASC, $data);
							return json($data);
          				}else{
          					return;
          				}
                        
						
              }
}
 ?>
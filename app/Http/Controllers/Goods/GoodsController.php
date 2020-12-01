<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redis;
use App\Xiao\User;
use App\Xiao\GoodsModel;
use App\Api\Cart;
use DB;
class GoodsController extends Controller
{
	  public function __construct()
    {
        app('debugbar')->disable();     //关闭调试
    }
    public function goods(){
    	$arr=GoodsModel::limit(10)->get();
    	// dd($GoodsModel);
    	return $arr;
    }
    public function login(){
    	// echo "111";
		$code=Request()->code;
		// echo  $code;
		$Appid="wx9b596c72a217fdea";
		$AppSecret="547f346bc24d38d0d946742284595176";
		$url="https://api.weixin.qq.com/sns/jscode2session?appid=$Appid&secret=$AppSecret&js_code=$code&grant_type=authorization_code";
		$res=json_decode(file_get_contents($url),true);
		$openid=$res['openid'];
		// print_r($res);
		if (isset($res['errcode'])) {
			$open=[
					'erron'=>4399,
					'msg'=>'登录失败',
					];
		}else{

			$token=md5($res['openid'].$res['session_key']);
			// dd($token);
			$userInfo=[
				'user_id'=>123,
				'user_name'=>'张三',
				'login_time'=>time(),
				'login_ip'=>Request()->getClientIp(),
				'access_token'=>$token
			];

			$key="h:xcx:token";
			$hass=Redis::hMset($key,$userInfo);
			// dd($hass);
			$times=Redis::expire($hass,7200);
			$opens=User::where('openid',$openid)->first();
			if (empty($opens)) {
				$data=[
					'openid'=>$openid
				];
				$data=User::insert($res);
			}
			echo json_encode(['code'=>0000,'msg'=>'登录成功','token'=>$token]);
		}
		
    }
    public function goodslogin(){
    	// echo "111";
		$code=Request()->code;
		// echo  $code;
		$Appid="wx9b596c72a217fdea";
		$AppSecret="547f346bc24d38d0d946742284595176";
		$url="https://api.weixin.qq.com/sns/jscode2session?appid=$Appid&secret=$AppSecret&js_code=$code&grant_type=authorization_code";
		$res=json_decode(file_get_contents($url),true);
		$openid=$res['openid'];
		// print_r($res);
		if (isset($res['errcode'])) {
			$open=[
					'erron'=>4399,
					'msg'=>'登录失败',
					];
		}else{

			$token=md5($res['openid'].$res['session_key']);
			// dd($token);
			$userInfo=[
				'user_id'=>123,
				'user_name'=>'张三',
				'login_time'=>time(),
				'login_ip'=>Request()->getClientIp(),
				'access_token'=>$token
			];

			$key="h:xcx:token";
			$hass=Redis::hMset($key,$userInfo);
			// dd($hass);
			$times=Redis::expire($hass,7200);
			$opens=User::where('openid',$openid)->first();
			if (empty($opens)) {
				$data=[
					'openid'=>$openid
				];
				$data=User::insert($res);
			}
			echo json_encode(['code'=>0000,'msg'=>'登录成功','token'=>$token]);
		}
		
    }
    public function cate(){
    	$goods_id=Request()->goods_id;
    	$token=Request()->token;
    	$userToken=Redis::hget('h:xcx:token','access_token');
    	if ($userToken!=$token) {
    		return json_encode(['code'=>1111,'msg'=>'重新登录']);exit;
    	}
    	// return $goods_id;
    	$res=GoodsModel::where('goods_id',$goods_id)->first();
    	$res['goods_image']=explode('|',$res['goods_image']);
    	// dd($res);
    	return $res;
    }

    public function goodslist(){
    	$page_size=Request()->get('ps');
    	// echo $page_size;
    	$zbc=GoodsModel::select('goods_id','goods_name','shop_price','goods_img')->paginate($page_size);
    	$res=[
    		'erron'=>0,
    		'msg'=>'ok',
    		'data'=>[
    			'list'=>$zbc->items()
    		]
    	];
    	return $res;
    }	
    //加入购物车添加
    public function detaildo(){
    	$goods_id=Request()->goods_id;
    	$res=GoodsModel::where('goods_id',$goods_id)->select('goods_id','goods_name','shop_price','goods_img')->get()->toArray();
    	// dd($res);
    	$data=Cart::insert($res);
    	dd($data);
    }
    //购物车
    public function cart(){
    	// $goods_id=Request()->goods_id;
    	// echo $goods_id;
    	$data=Cart::get();
    	// dd($data);
    	return $data;
    }
}
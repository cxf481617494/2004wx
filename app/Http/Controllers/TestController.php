<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use DB;
use Log;
use App\WeiXin\WeixinModel;
use App\WeiXin\User;
use GuzzleHttp\Client;
class TestController extends Controller
{
  public function index(){
        $res=$this->checkSignature();
        if ($res) {
            echo $_GET['echostr'];
        }
        // $res=$this->text();
        //   //创建菜单
        // $res1=$this->create_moun();
        // echo $res1;
    } 
      //自动回复
    public function  text()
    {
        //接收数据
        $data = file_get_contents("php://input");
        Log::info("=====接收数据====" . $data);
        //转换成对象
        $postarray = simplexml_load_string($data);
        $access_token = $this->access();//获取token
        if($postarray->MsgType=="text"){
                if($postarray->Content=="天气"){
                    $Content = $this->getweather();
                    $this->info($postarray,$Content);
                }
            }
            $data = [];
        if($postarray->MsgType=="image"){
            $data[] = [
                "FromUserName" => $postarray->FromUserName,
                "CreateTime" => $postarray->CreateTime,
                "MsgType" => $postarray->MsgType,
                "PicUrl" => $postarray->PicUrl,
                "MediaId" => $postarray->MediaId,
            ];
            $image = new User();
            $image->insert($data);
            $this->med($postarray->MediaId);
        }else if($postarray->MsgType=="text"){
            $data[] = [
                "FromUserName" => $postarray->FromUserName,
                "CreateTime" => $postarray->CreateTime,
                "MsgType" => $postarray->MsgType,
                "Content" => $postarray->Content,
                ];
            $image = new User();
            $image->insert($data);
        }else if($postarray->MsgType=="video"){
            $data[] = [
                "FromUserName" => $postarray->FromUserName,
                "CreateTime" => $postarray->CreateTime,
                "MsgType" => $postarray->MsgType,
                "MediaId" => $postarray->MediaId,
                "ThumbMediaId" =>$postarray->ThumbMediaId,
            ];
            $image = new User();
            $image->insert($data);
            $this->med($postarray->MediaId);
        }else if($postarray->MsgType=="voice"){
            $data[] = [
                "FromUserName" => $postarray->FromUserName,
                "CreateTime" => $postarray->CreateTime,
                "MsgType" => $postarray->MsgType,
                "MediaId" => $postarray->MediaId,
                "Format" => $postarray->Format,
                "ThumbMediaId" =>$postarray->ThumbMediaId,
            ];
            $image = new User();
            $image->insert($data);
            $this->med($postarray->MediaId);
        }else if ($postarray->EventKey=="V1001_TODAY_weather") {
             $city = $this->geo();
             $Content = $this->getweather($city);
             $this->info($postarray,$Content);
        }
        $openid = $postarray->FromUserName;//获取发送方的 openid
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        // Log::info("123456",$url);
        $user = json_decode($this->http_get($url),true);
        $WexiinModel = new WeixinModel;
        $first = WeixinModel::where("openid",$user["openid"])->first();
        
        if ($first) {
            $array = ["欢迎回来!!!!"];
            $Content = $array[array_rand($array,1)];
            $this->info($postarray,$Content);die;
            
        } else {

            if ($postarray->MsgType == "event") {
                if ($postarray->Event == "subscribe") {
                    $array = ["你好啊", "欢迎关注!!!"];
                    $Content = $array[array_rand($array, 1)];
                    $this->info($postarray, $Content);
                    //入库
                    $data = [
                        "openid" => $user["openid"],
                        "city" => $user["city"],
                        "sex" => $user["sex"],
                        "language" => $user["language"],
                        "province" => $user["province"],
                        "country" => $user["country"],
                        "subscribe_time" => $user["subscribe_time"],
                        "subscribe" => $user["subscribe"],
                        "subscribe_scene" => $user["subscribe_scene"],
                    ];
                    $WexiinModel->insert($data);
                }
            }
        }
    }
    public function geo(){
        $data = '{
                "resultcode":"200",
                "reason":"success",
                "result":
                {
                "lat":"40.144161",
                "lng":"116.284241",
                "type":"1","address":"北京市昌平区X032(于善街)",
                "business":"沙河",                "citycode":131,
                "ext":
                {
                "country":"中国",
                "country_code":0,
                "country_code_iso":"CHN",
                "country_code_iso2":"CN",
                "province":"北京市",
                "city":"北京",
                "city_level":2,
                "district":"昌平区",
                "town":"",
                "town_code":"",
                "adcode":"110114",
                "street":"X032(于善街)",
                "street_number":"",
                "direction":"",
                "distance":""
                }
                },"error_code":0
             }';
        $data = json_decode($data,true);
        if($data["resultcode"]==200){
            if($data["reason"]=="success"){
                $content =  $data["result"]["ext"]["city"];
            }
        }
        return $content;
        Log::info("=========城市1=============".$content);
    }

    public function med($MediaId){
        $token=$this->access();
        $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$token.'&media_id='.$MediaId;
       $client = new Client();
        $response = $client->get($url);
        // 得到头部信息
        // 从头部信息中取出文件名 将文件名处理为字符串
        $file_name = $response->getHeader('Content-disposition')[0];

        $file_type = 'static/'.$response->getHeader('Content-Type')[0];
//        Log::info("=====file_type=====".$file_type);
        // 判断有无 文件夹 没有 则创建多层文件夹
        $adddir=$file_type.date("/Ymd/",time());
        if(!is_dir($adddir)){
            mkdir($adddir, 0777,true);
            chmod($adddir, 0777);
        }
        $file_name = ltrim($file_name,"attachment; filename=\"");
        $file_name = rtrim($file_name,'"');
        $file_path = $adddir.$file_name;
        $client->get($url,['save_to'=>$file_path]);


    }
    public function info($postarray,$Content){
        $ToUserName=$postarray->FromUserName;
        $FromUserName=$postarray->ToUserName;
        $CreateTime=time();
        $MsgType="text";
            $xml="<xml>
          <ToUserName><![CDATA[%s]]></ToUserName>
          <FromUserName><![CDATA[%s]]></FromUserName>
          <CreateTime>%s</CreateTime>
          <MsgType><![CDATA[%s]]></MsgType>
          <Content><![CDATA[%s]]></Content>
        </xml>";
    $info=sprintf($xml,$ToUserName,$FromUserName,$CreateTime,$MsgType,$Content);
    Log::info($info);
    echo $info;
    }
    public function xinwen($Content){
        $key="04f4d3a7b600d4507956005d77a1c62e";
        $top=$Content;
        $url="http://v.juhe.cn/toutiao/index?type=$top&key=$key";
        $xml=file_get_contents($url);
        Log::info("===================",$xml);
    }
    public function create_moun(){
    $access_token=$this->access();
    $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;
    $menu='{
                 "button":[
                 {
                      "type":"click",
                      "name":"新闻",
                      "key":"V1001_TODAY_QQ"
                  },
                  {
                       "name":"giao",
                       "sub_button":[
                       {
                           "type":"view",
                           "name":"新闻",
                           "url":"http://www.baidu.com/"
                        },
                        {
                           "type":"view",
                           "name":"商城",
                           "url":"http://2004weixin.259775.top/"
                        },
                        {
                           "type":"click",
                           "name":"天气",
                           "key":"V1001_TODAY_weather"
                        }]
                   }]
             }';
    $moun=$this->curl($url,$menu);
    return $moun;
    }
    //获取token
    public function access(){
        $token=Redis::get("token");
        if (!$token) {
        // $stream_opts = [
        //     "ssl" => [
        //         "verify_peer"=>false,
        //         "verify_peer_name"=>false,
        //     ]
        // ]; 

        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx0698605a1ca84bf6&secret=4f806f9e3a01e61d063e175aaa103ee4";
        // $token=file_get_contents($url,false,stream_context_create($stream_opts));
            // $token=file_get_contents($url);  
        $Client=new Client();
        $response=$Client->request('GET',$url,['verify'=>false]);
        $json_str=$response->getBody();
        // dd($json_str); 
        // dd($token);
        $token=json_decode($json_str,true);
        $token=$token['access_token'];
        // dd($token);
        Redis::setex("token",3600,$token);
        }
        return $token;
    }

     public function curl($url,$menu){
        //1.初始化
        $ch = curl_init();
        //2.设置
        curl_setopt($ch,CURLOPT_URL,$url);//设置提交地址
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);//设置返回值返回字符串
        curl_setopt($ch,CURLOPT_POST,1);//post提交方式
        curl_setopt($ch,CURLOPT_POSTFIELDS,$menu);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        //3.执行
        $output = curl_exec($ch);
        //关闭
        curl_close($ch);
        return $output;
    }
        //回复天气模板
    public function getweather(){
        $url = "http://api.k780.com:88/?app=weather.future&weaid=beijing&&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4&format=json";
        $weather = file_get_contents($url);
        $weather = json_decode($weather,true);
        //dd($weather);
        // $aa = $weather["result"];
        // dd($aa);
        if($weather["success"]){
            $content = "";
            foreach($weather["result"] as $v){
                $content .= "地区:".$v['citynm']."日期:".$v['days']."温度:".$v['temperature']."风速:".$v['winp']."天气:".$v['weather'];
            }
        }
        return $content;
        Log::info("============".$weather);
    }
    public function http_get($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);//向那个url地址上面发送
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//设置发送http请求时需不需要证书
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置发送成功后要不要输出1 不输出，0输出
        $output = curl_exec($ch);//执行
        curl_close($ch);    //关闭
        return $output;
 }
 private function checkSignature()
{
    $signature = $_GET["signature"];
    $timestamp = $_GET["timestamp"];
    $nonce = $_GET["nonce"];
    
    $token = 'cxf481617494';
    $tmpArr = array($token, $timestamp, $nonce);
    sort($tmpArr, SORT_STRING);
    $tmpStr = implode( $tmpArr );
    $tmpStr = sha1( $tmpStr );
    
    if( $tmpStr == $signature ){
        return true;
    }else{
        return false;
    }
}
}
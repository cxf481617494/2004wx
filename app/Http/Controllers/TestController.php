<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//引入 Redis
use Illuminate\Support\Facades\Redis;
//引用Gueele
use GuzzleHttp\Client;
use DB;
use Log;
use App\UserModel;
use App\WeixinModel;
use App\MediaModel;
use App\wx_mediaModel;
use App\MenueUser;
class TestController extends Controller
{
	public function index(){
    echo "123";
       $res = $this->checkSignature();
       if($res){
          echo  $_GET["echostr"];
       }
           //微信接入
  
        //回复消息
        // $ress = $this->infocode();
        //创建标签
//        $tags = $this->tags();
        //自定义菜单
       // $menu = $this->msgeon();
//       print_r($menu);
       //下载临时素材
//        $menuxiazai = $this->menuxiazai();
//        print_r($menuxiazai);

        //获取token
//        $access_token = $this->token();
//      print_r($access_token);
        //临时素材管理
//        $mediia = $this->mediia();
//        print_r($access_token);
//        $getuserinfo = $this->getuserinfo();
//        $resmsg = $this->resmsg();
//        print_r($resmsg);
    }
      private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = env("WX_TOKEN");
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
    //自动回复
//     public function  infocode()
//     {
//         //接收数据
//         $data = file_get_contents("php://input");
//         Log::info("=====接收数据====" . $data);
//         //转换成对象
//         $postarray = simplexml_load_string($data);
//         $access_token = $this->token();//获取token
// //        $lat = $postarray->Latitude;
// //        //经度
// //        $lng = $postarray->Longitude;
// //        if($postarray->MsgType=="text"){
// //            if($postarray->Content=="天气"){
// //                $Content = $this->getweather();
// //                $this->info($postarray,$Content);
// //            }
// //        }
//         //判断是否是图片信息
//         $data = [];
//         if($postarray->MsgType=="image"){
//             $data[] = [
//                 "FromUserName" => $postarray->FromUserName,
//                 "CreateTime" => $postarray->CreateTime,
//                 "MsgType" => $postarray->MsgType,
//                 "PicUrl" => $postarray->PicUrl,
//                 "MediaId" => $postarray->MediaId,
//             ];
//             $image = new MenueUser();
//             $image->insert($data);
// //            $insertId =DB::table("menueuser")->max("m_id");
//             $this->menuxiazai($postarray->MediaId);
//         }else if($postarray->MsgType=="text"){
//             $data[] = [
//                 "FromUserName" => $postarray->FromUserName,
//                 "CreateTime" => $postarray->CreateTime,
//                 "MsgType" => $postarray->MsgType,
//                 "Content" => $postarray->Content,
//                 ];
//             $image = new MenueUser();
//             $image->insert($data);
//         }else if($postarray->MsgType=="video"){
//             $data[] = [
//                 "FromUserName" => $postarray->FromUserName,
//                 "CreateTime" => $postarray->CreateTime,
//                 "MsgType" => $postarray->MsgType,
//                 "MediaId" => $postarray->MediaId,
//                 "ThumbMediaId" =>$postarray->ThumbMediaId,
//             ];
//             $image = new MenueUser();
//             $image->insert($data);
// //            $insertId =DB::table("menueuser")->max("m_id");
//             $this->menuxiazai($postarray->MediaId);
//         }else if($postarray->MsgType=="voice"){
//             $data[] = [
//                 "FromUserName" => $postarray->FromUserName,
//                 "CreateTime" => $postarray->CreateTime,
//                 "MsgType" => $postarray->MsgType,
//                 "MediaId" => $postarray->MediaId,
//                 "Format" => $postarray->Format,
//                 "ThumbMediaId" =>$postarray->ThumbMediaId,
//             ];
//             $image = new MenueUser();
//             $image->insert($data);
// //            $insertId =DB::table("menueuser")->max("m_id");
// //            Log::info("=====insertId=======".$insertId);
//             $this->menuxiazai($postarray->MediaId);
//         }else if($postarray->Event=="CLICK"){
//             if($postarray->EventKey=="V1001_TODAY_QQ"){
//                 $key = "qiandao";
//                 $openid = (string)$postarray->FromUserName;
//                 $slsMember = Redis::sismember($key,$openid);
//                 if($slsMember=="1"){
//                     $Content = "已签到";
//                     $this->info($postarray,$Content);
//                 }else{
//                     $Content = "签到成功";
//                     Redis::sAdd($key,$openid);
//                     $this->info($postarray,$Content);
//                 }
//            }else if($postarray->EventKey=="V1001_TODAY_weather"){
// //                $city = $this->geo($lat,$lng);
//                 $city = $this->geo();
//                 $Content = $this->getweather($city);
//                 $this->info($postarray,$Content);
//             }else if($postarray->EventKey=="V1001_TODAY_bd"){
// //                $url = "http://www.259775.top/getuserinfo";
// //                redirect($url);
//                 $code = $this->getuserinfo();
//                 Log::info("=========调用授权============".$code);
//                 if(!empty($code)){
//                     $Content = "绑定成功";
//                     $this->info($postarray,$Content);
//                 }else{
//                     $Content = "绑定失败";
//                     $this->info($postarray,$Content);
//                 }
//             }else if($postarray->EventKey=="V1001_TODAY_tj"){
//                 $picUrl =DB::table("wx_media")->max("picUrl");
//                 $Content = [
//                     "title"=>"客官,您好啊!!!",
//                     "description1"=>"今日推荐的商品哦",
//                     "picurl"=>$picUrl,
//                     "url"=>"http://www.259775.top"
//                 ];
//                 $this->tuwen($postarray,$Content);
//             }
//         }

//         $openid = $postarray->FromUserName;//获取发送方的 openid
//         $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $access_token . "&openid=" . $openid . "&lang=zh_CN";
//         $user = json_decode($this->http_get($url), true);
//         $WexiinModel = new WeixinModel;
//         $first = WeixinModel::where("openid", $user["openid"])->first();
//         if ($first) {
//             $Content = "欢迎回来哦";
//             $this->info($postarray, $Content);
//         } else {
//                 if ($postarray->Event == "subscribe") {
//                     $array = ["你好啊", "欢迎关注!!!"];
//                     $Content = $array[array_rand($array, 1)];
//                     $this->info($postarray, $Content);
//                     //入库
//                     $data = [
//                         "openid" => $user["openid"],
//                         "city" => $user["city"],
//                         "sex" => $user["sex"],
//                         "language" => $user["language"],
//                         "province" => $user["province"],
//                         "country" => $user["country"],
//                         "subscribe_time" => $user["subscribe_time"],
//                         "subscribe" => $user["subscribe"],
//                         "subscribe_scene" => $user["subscribe_scene"],
//                     ];
//                     $WexiinModel->insert($data);
//                 }
//             }
//          }
//          //调用用户地理位置
//     public function geo(){
// //        $url = "http://apis.juhe.cn/geo/?key=2862c30552f4698fd7e65baf5b6a3302&lat=$lat&lng=$lng&type=1";
// //        $data = file_get_contents($url);
// //        Log::info("=========用户地理位置=============".$data);
//         $data = '{
//                 "resultcode":"200",
//                 "reason":"success",
//                 "result":
//                 {
//                 "lat":"40.144161",
//                 "lng":"116.284241",
//                 "type":"1","address":"北京市昌平区X032(于善街)",
//                 "business":"沙河",
//                 "citycode":131,
//                 "ext":
//                 {
//                 "country":"中国",
//                 "country_code":0,
//                 "country_code_iso":"CHN",
//                 "country_code_iso2":"CN",
//                 "province":"北京市",
//                 "city":"北京",
//                 "city_level":2,
//                 "district":"昌平区",
//                 "town":"",
//                 "town_code":"",
//                 "adcode":"110114",
//                 "street":"X032(于善街)",
//                 "street_number":"",
//                 "direction":"",
//                 "distance":""
//                 }
//                 },"error_code":0
//              }';
//         $data = json_decode($data,true);
//         if($data["resultcode"]==200){
//             if($data["reason"]=="success"){
//                 $content =  $data["result"]["ext"]["city"];
//             }
//         }
//         return $content;
//         Log::info("=========城市1=============".$content);


//     }
//     //下载临时素材
//     public function menuxiazai($media_id){
//         $access_token = $this->token();
//         $insertId =DB::table("menueuser")->max("m_id");
//         $data =   new MenueUser();
//         $url = "https://api.weixin.qq.com/cgi-bin/media/get?access_token=$access_token&media_id=$media_id";
//         Log::info("======临时素材=======".$url);
// //        $data = file_get_contents($url);
// //        $res = file_put_contents("cat.jpg",$data);
//         $client = new client();
//         $response = $client->get($url);
//         // 得到头部信息
//         // 从头部信息中取出文件名 将文件名处理为字符串
//         $file_name = $response->getHeader('Content-disposition')[0];

//         $file_type = 'static/'.$response->getHeader('Content-Type')[0];
// //        Log::info("=====file_type=====".$file_type);
//         // 判断有无 文件夹 没有 则创建多层文件夹
//         $adddir=$file_type.date("/Ymd/",time());
//         if(!is_dir($adddir)){
//             mkdir($adddir, 0777,true);
//             chmod($adddir, 0777);
//         }
//         $file_name = ltrim($file_name,"attachment; filename=\"");
//         $file_name = rtrim($file_name,'"');
//         $file_path = $adddir.$file_name;
//         $client->get($url,['save_to'=>$file_path]);
//         $image = new MenueUser();
//         $image->where("m_id",$insertId)->update(["local_file"=>$file_path]);

//     }
//     //临时素材管理
//     public function mediia(){
//         $access_token = $this->token();
//         $type = "image";
//         $url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=$access_token&type=$type";
//         //使用Guzzle发送请求
//         $client = new Client();//实例化客户端
//         $response = $client->request("POST",$url,[
//             "multipart"=>[
//                 [
//                     'name'=>"media",
//                     'contents'=>fopen('demo-1-bg.jpg','r')//上传的图片地址,并且只有读的权限
//                 ],
//             ]
//         ]);
//         $data = $response->getBody();
//        $json = json_decode($data,true);
//        $data = [
//             "media_id" => $json["media_id"],
//             "created_at" => $json["created_at"],
//             "type" =>$json["type"],
//        ];
//        $MediaModel = MediaModel::insert($data);
//     }
//     //3.回复天气模板
//     public function getweather($city){
// //        $city = $city;
//         $url = "http://api.k780.com:88/?app=weather.future&weaid=$city&&appkey=10003&sign=b59bc3ef6191eb9f747dd4e83c99f2a4&format=json";
//         $weather = file_get_contents($url);
//         Log::info("==============weather=================".$url);
//         $weather = json_decode($weather,true);
//         if($weather["success"]) {
//             $content = "";
//             foreach ($weather["result"] as $v) {
//                 $content .= "\n"."地区:" . $v['citynm'] .","."日期:" . $v['days'] . $v['week'] .","."温度:" . $v['temperature'] .","."风速:" . $v['winp'] .","."天气:" . $v['weather'];
//             }
//         }else if($weather["success"]==0){
//             $content = "";
//             $content .= "暂无数据";
//         }
//         return $content;
//         Log::info("============".$content);
//     }
//     //调用qq测吉凶的接口
//     public function qqjx($Content){
//         $appkey = "3c1ce1d84f6aebe7f439593fcdac9520";
//         $qq = $Content;
//         $url = "http://japi.juhe.cn/qqevaluate/qq?key=3c1ce1d84f6aebe7f439593fcdac9520&qq=$qq";
//         $data  = file_get_contents($url);
//         $jsons = json_decode($data,true);
//         if($jsons["reason"]=="success"){
//             $content = "";
//             foreach ($jsons["result"] as $v) {
//                 $content .= "\n"."结果:" . $v['conclusion'] .","."期望:" . $v['analysis'];
//             }
//         }
//         return $content;
//         Log::info("======qq吉凶======".$data);


//     }
//     //自定义菜单
//     public function msgeon(){
//         $access_token = $this->token();
//         $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$access_token";
//         $menu = '{
//                  "button":[
//                      {
//                          "name":"菜单1",
//                        "sub_button":[
//                        {
//                            "type":"click",
//                       "name":"点击绑定账号",
//                       "key":"V1001_TODAY_bd"
//                          },
//                         {
//                             "type":"click",
//                            "name":"今日推荐",
//                            "key":"V1001_TODAY_tj"
//                         }, 
//                          {
//                           "type":"click",
//                           "name":"签到",
//                           "key":"V1001_TODAY_QQ"
//                      }]
//                    },
                 
//                   {
//                        "name":"菜单2",
//                        "sub_button":[
//                        {
//                            "type":"view",
//                            "name":"商城",
//                            "url":"http://www.259775.top"
//                         },
//                         {
//                            "type":"click",
//                            "name":"天气",
//                            "key":"V1001_TODAY_weather"
//                         },
//                         {
//                            "type":"view",
//                            "name":"qq测试吉凶",
//                            "url":"http://www.259775.top/qqjixiong"
//                         }]
//                    }]
//              }';
//         $data = $this->curl($url,$menu);
//      }
//     //qq测吉凶首页
//     public function qqjixiong(){
//         return view("qqjixiong/index");
//     }
//     public function qqjixiongstore(){
//         $q_name = request()->q_name;
// //        dd($q_name);
//         $data = $this->qqjx($q_name);
//     return json_encode(["msg"=>"执行成功","code"=>"0000","data"=>$data]);
//     }
//     //获取code
//     public function getuserinfo(){
//         //加密
//         $redirect_uri = urlencode("http://www.259775.top/getuserid");
//         $str = "ABCDEFGHIGKLMNOPQRSTUVWN1234567890qwertyuiopasdfghjklzxcvbnm";
//         $str = substr(str_shuffle($str),0,3);
//         $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('WX_APPID')."&redirect_uri=".$redirect_uri."&response_type=code&scope=snsapi_userinfo&state=".$str."#wechat_redirect";
//         header("location:".$url);
//     }
//     //静默授权/普通授权
//     public function getuserid(){
//         $code = request()->code;
//         $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env('WX_APPID')."&secret=".env('WX_APPSE')."&code=$code&grant_type=authorization_code";
//         $refresh_token = file_get_contents($url);
//         Log::info("==个人信息==".$refresh_token);
//         $res = json_decode($refresh_token,true);
//         $access_token = $res["access_token"];
//         $openid = $res["openid"];
//         if($res["scope"]=="snsapi_base"){
//             echo $res["openid"];exit;
//         }else{
//             $users = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
//             $userss = file_get_contents($users);
//             $url = "";
//             Log::info("============普通授权============".$userss);
//             $users = json_decode($userss,true);
//             return $users;
// //            return RedirectToAction("weixinlogin", "UserController");
// //            header("location:".$url);
// //            return redirect('/'.$users["name"]);

// //            return view("home.index",["users",$users]);
//         }




//     }
//     //发送文字消息
//     public function info($postarray,$Content){
//         $ToUserName = $postarray->FromUserName;
//         $FromUserName = $postarray->ToUserName;
//         $CreateTime = time();
//         $MsgType = "text";
//         $xml = "<xml>
//                   <ToUserName><![CDATA[%s]]></ToUserName>
//                   <FromUserName><![CDATA[%s]]></FromUserName>
//                   <CreateTime>%s</CreateTime>
//                   <MsgType><![CDATA[%s]]></MsgType>
//                   <Content><![CDATA[%s]]></Content>
//                 </xml>";
//         $info = sprintf($xml,$ToUserName,$FromUserName,$CreateTime,$MsgType,$Content);
//         Log::info("===!!!===".$info);
//         echo    $info;
//     }

//     //获取token
//     public function token(){
//         $tokens = Redis::get("token");
//         if(!$tokens){
//             $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WX_APPID')."&secret=".env("WX_APPSE");
//             //使用file_get_contents发送请求
// //            $token = file_get_contents($url);
//             //使用Guzzle发送get请求
//             $client = new Client();//实例化客户端
//             $response = $client->request("GET",$url);//发起请求并接收响应
//             $token = $response->getBody();//服务器响应数据
//             $token = json_decode($token,true);
// //            dd($token);
//             $tokens = $token["access_token"];
// //            dd($tokens);
//             Redis::setex("token",3600,$tokens);
//         }
//         return $tokens;
//     }
//     //过滤https请求(1)
//     public function curl($url,$menu){
//         //1.初始化
//         $ch = curl_init();
//         //2.设置
//         curl_setopt($ch,CURLOPT_URL,$url);//设置提交地址
//         curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);//设置返回值返回字符串
//         curl_setopt($ch,CURLOPT_POST,1);//post提交方式
//         curl_setopt($ch,CURLOPT_POSTFIELDS,$menu);
//         curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
//         curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
//         //3.执行
//         $output = curl_exec($ch);
//         //关闭
//         curl_close($ch);
//         return $output;
//     }
//     //过滤https请求(2)
//     public function http_get($url){
// //        Log::info("--------------------123");
//         $ch = curl_init();
//         curl_setopt($ch, CURLOPT_URL, $url);//向那个url地址上面发送
//         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//设置发送http请求时需不需要证书
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//设置发送成功后要不要输出1 不输出，0输出
//         $output = curl_exec($ch);//执行
//         curl_close($ch);    //关闭
//         return $output;
//  }
//     //用户标签
//     public function tags(){
//         $access_token = $this->token();
//         $url = "https://api.weixin.qq.com/cgi-bin/tags/create?access_token=$access_token";
//         $data = '{
//            "tag" : {
//                     "name" : "北京"
//                     }
//                 } ';
//         $curl = $this->curl($url,$data);
//         Log::info("========用户标签=========".$curl);
//     }
//     //生成临时二维码
//     public function ticket(){
//         $access_token  = $this->token();
//        $url  =  "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token$access_token";
//        $data = [
//             "expire_seconds" => 604800,
//             "action_name" => "QR_SCENE",
//             "action_info" =>[
//             "scene" =>[
//               "scene_id" => 123
//             ]
//           ],
//         ];
//     }
//     //发送图文消息
//     public function tuwen($postarray,$Content){
//         $ToUserName = $postarray->FromUserName;
//         $FromUserName = $postarray->ToUserName;
//         $CreateTime = time();
//         $MsgType = "news";
//         $ArticleCount  = 1;
//         $xml = "<xml>
//                   <ToUserName><![CDATA[%s]]></ToUserName>
//                   <FromUserName><![CDATA[%s]]></FromUserName>
//                   <CreateTime>%s</CreateTime>
//                   <MsgType><![CDATA[%s]]></MsgType>
//                   <ArticleCount>%s</ArticleCount>
//                   <Articles>
//                     <item>
//                       <Title><![CDATA[%s]]></Title>
//                       <Description><![CDATA[%s]]></Description>
//                       <PicUrl><![CDATA[%s]]></PicUrl>
//                       <Url><![CDATA[%s]]></Url>
//                     </item>
//                   </Articles>
//                 </xml>";
//         $info = sprintf($xml,$ToUserName,$FromUserName,$CreateTime,$MsgType,$ArticleCount,$Content["title"],$Content["description1"],$Content["picurl"],$Content["url"]);
//         Log::info("===!!!===".$info);
//         echo    $info;
//     }



}

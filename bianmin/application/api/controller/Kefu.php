<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/7 0007
 * Time: 下午 1:33
 */

namespace app\api\controller;


use app\api\service\AccessToken;
use think\Exception;

//header("Content-type: text/html; charset=utf-8");


class Kefu
{

    // 客服消息，上线后测试......
    // http://www.cnblogs.com/objects/p/7889088.html


    public function index()
    {
        return 'index';
    }

    public function qwe()
    {
        return 'qwe1';
    }

    // 未成功，是不是要发布后才有效？待测试。

    public function getkefu()
    {     //校验服务器地址URL
        if (isset($_GET['echostr'])) {
            $this->valid();
        } else {
            $this->responseMsg();
        }
    }


    public function valid()
    {
        $echoStr = $_GET["echostr"];
        if ($this->checkSignature()) {
//            header('content-type:text');
            ob_clean();
//            header('content-type:text');
            echo $echoStr;
            exit;
        }
    }


    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = 'dixia2haomi';
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }


    public function responseMsg()
    {
        // 测试
//        cache('kefu','kefu');

//        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postStr = file_get_contents("php://input");

        if (!empty($postStr) && is_string($postStr)) {
            //禁止引用外部xml实体
            //libxml_disable_entity_loader(true);

            //$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $postArr = json_decode($postStr, true);
            if (!empty($postArr['MsgType']) && $postArr['MsgType'] == 'text') {   //文本消息
                $fromUsername = $postArr['FromUserName'];   //发送者openid
                $toUserName = $postArr['ToUserName'];       //小程序id
                $textTpl = array(
                    "ToUserName" => $fromUsername,
                    "FromUserName" => $toUserName,
                    "CreateTime" => time(),
                    "MsgType" => "transfer_customer_service",
                );
                exit(json_encode($textTpl));
            } elseif (!empty($postArr['MsgType']) && $postArr['MsgType'] == 'image') { //图文消息
                $fromUsername = $postArr['FromUserName'];   //发送者openid
                $toUserName = $postArr['ToUserName'];       //小程序id
                $textTpl = array(
                    "ToUserName" => $fromUsername,
                    "FromUserName" => $toUserName,
                    "CreateTime" => time(),
                    "MsgType" => "transfer_customer_service",
                );
                exit(json_encode($textTpl));
            } elseif ($postArr['MsgType'] == 'event' && $postArr['Event'] == 'user_enter_tempsession') { //进入客服动作
                $fromUsername = $postArr['FromUserName'];   //发送者openid
//                $content = '您好，有什么能帮助你?';
//                $data=array(
//                    "touser"=>$fromUsername,
//                    "msgtype"=>"text",
//                    "text"=>array("content"=>$content)
//                );

                $data = array(
                    "touser" => $fromUsername,
                    "msgtype" => "link",
                    "link" => array(
                        "title" => '你来啦?',
                        "description" => '我有一些乱七八糟的东西想跟你说，还有一些福利.',
                        //   "url"=>"http://mp.weixin.qq.com/s/lKAs-czQAdEvex6WAPsGRw",
                        "url" => "http://mp.weixin.qq.com/s?__biz=MzU5NTM1MjY0Mw==&mid=100000104&idx=1&sn=638a1c4bafbee3a8ae10533b8dbb66e8&chksm=7e720c44490585529ad10aee2dfbafceb5cdea6bf9671e9ae8a93a3cad465c80dd9c8c58641c#rd",
                        "thumb_url" => "http://wx.qlogo.cn/mmhead/Q3auHgzwzM4xLNDUnYwb9PVDMDxwxKC4631OxxChgPsBPGh7yHUYYQ/0"
                    )
                );

//                {
//                    "touser": "OPENID",
//                    "msgtype": "link",
//                    "link": {
//                            "title": "Happy Day",
//                            "description": "Is Really A Happy Day",
//                            "url": "URL",
//                            "thumb_url": "THUMB_URL"
//               }
//}
                $json = json_encode($data, JSON_UNESCAPED_UNICODE);  //php5.4+

                // 获取access_token
                $Access = new AccessToken();
                $access_token = $Access->get();

                /*
                 * POST发送https请求客服接口api
                 */
                $url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=" . $access_token;
                //以'json'格式发送post的https请求

                // -------------------- 原来的请求 ---------------------------
//                $curl = curl_init();
//                curl_setopt($curl, CURLOPT_URL, $url);
//                curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
//                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
//                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
//                if (!empty($json)){
//                    curl_setopt($curl, CURLOPT_POSTFIELDS,$json);
//                }
//                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//                //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers );
//                $output = curl_exec($curl);
//                if (curl_errno($curl)) {
//                    echo 'Errno'.curl_error($curl);//捕抓异常
//                }
//                curl_close($curl);
//                if($output == 0){
//                    echo 'success';exit;
//                }

                // ------------------------------------------------
                curl_post_raw($url, $json);

            } else {
                exit('aaa');
            }
        } else {
            echo "";
            exit;
        }
    }
    /* 调用微信api，获取access_token，有效期7200s -xzz0704 */
//    public function get_accessToken(){
//        /* 在有效期，直接返回access_token */
//        if(S('access_token')){
//            return S('access_token');
//        }
//        /* 不在有效期，重新发送请求，获取access_token */
//        else{
//            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx6056****&secret=30e46f3ef07b****';
//            $result = curl_get_https($url);
//            $res = json_decode($result,true);   //json字符串转数组
//
//            if($res){
//                S('access_token',$res['access_token'],7100);
//                return S('access_token');
//            }else{
//                return 'api return error';
//            }
//        }
//    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/1 0001
 * Time: 下午 10:39
 */

namespace app\api\service;


use app\exception\WeChatException;

class BaseWeChat
{

    /**
     *  微信基础接口
     *  用wx.login换取openid,sessionKey
     *
     */

    // code换取openid,sessionKey
    public function loginCode($code){

        $appid = config('wx_config.appid');
        $secret = config('wx_config.secret');

        // 拼接url
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$secret."&js_code=".$code."&grant_type=authorization_code";

        // 发送请求
        $result = curl_get($url);
        $wxResult = json_decode($result,true);
        if(empty($wxResult)){
            throw new WeChatException(['msg'=>'微信内部错误，获取openid和session_key异常']);   //如果微信没有返回任何数据，抛出异常记录日志
        }else {
            if (array_key_exists('errcode', $wxResult)) {
                //如果微信返回的数据中包含errcode就抛出异常告诉客户端
                throw new WeChatException(['msg' => $wxResult['errmsg'], 'errorCode' => $wxResult['errcode']]);
            }
            // 返回openid和sessionKey
            return $wxResult;
        }
    }
}
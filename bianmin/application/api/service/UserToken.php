<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28 0028
 * Time: 下午 6:22
 */

namespace app\api\service;


use app\exception\Success;
use app\exception\TokenException;
use app\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;

class UserToken
{
//    protected $code;
//    protected $wxAppID;
//    protected $wxAppSecret;
//    protected $loginUrl;
//
//    // 构造函数-组织微信的url
//    public function __construct($code)
//    {
//        $this->code = $code;
//        $this->wxAppID = config('wx_config.appid');
//        $this->wxAppSecret = config('wx_config.secret');
//        $this->loginUrl = sprintf(config('wx_config.login_url'),$this->wxAppID,$this->wxAppSecret,$this->code);
//    }
//
//
//    //用户登录
//    //获取token
//    //发送网络请求，获取微信返回的结果,生成token缓存
//    //返回token缓存的key
//    public function get_Token_Service(){
//        $result = curl_get($this->loginUrl);
//        $wxResult = json_decode($result,true);
//        if(empty($wxResult)){
//            throw new WeChatException(['msg'=>'微信内部错误，获取openid和session_key异常']);   //如果微信没有返回任何数据，抛出异常记录日志
//        }else{
//            if(array_key_exists('errcode',$wxResult)){
//                //如果微信返回的数据中包含errcode就抛出异常告诉客户端
//                throw new WeChatException([
//                    'msg' => $wxResult['errmsg'],
//                    'errorCode' => $wxResult['errcode']
//                ]);
//            }else{
//                //授予Token，接受微信返回的数据，操作数据库.
////                return $this->grantToken($wxResult);
//                throw new Success(['data'=>$this->grantToken($wxResult)]);
//            }
//        }
//    }
//
//
//    //授予Token
//    //接受微信返回的数据
//    //查询用户数据，没有则新增，获取用户id
//    //返回缓存的key
//    public function grantToken($wxResult){
//        //拿到openid
//        //查看数据库有没有这个openid,没有就新增数据，
//        //有->生成令牌，缓存令牌数据.
//        //返回令牌给客户端
//        $openid = $wxResult['openid'];
//        $user = UserModel::getByOpenid($openid);
//        if($user){
//            $uid = $user->id;
//        }else{
//            $uid = UserModel::create_user($openid); // 携带openid和用户信息去创建用户
//        }
//        return $this->save_Cache_Token($wxResult,$uid);    //保存token缓存，返回token的key
//    }
//
//
//    //保存token缓存
//    //接受微信返回的数据；用户id等等..，封装后写入缓存.
//    //返回缓存的key
//    public function save_Cache_Token($wxResult,$uid){
//        $tokenKey = BaseToken::prepare_Token_Key();     //获取token_key
//        $tokenValue = BaseToken::prepare_Token_Value($wxResult,$uid);         //获取token_value
//        $token_expire = config('wx_config.token_expire');  //获取token过期时间
//
//        $token = cache($tokenKey,$tokenValue,$token_expire);    //缓存token
//        if(!$token){
//            throw new TokenException(['msg' => '缓存token时异常，来自save_Cache_Token']);
//        }
//        return $tokenKey;
//    }

}
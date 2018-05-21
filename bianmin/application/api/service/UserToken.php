<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28 0028
 * Time: 下午 6:22
 */

namespace app\api\service;


use app\exception\QueryDbException;
use app\exception\Success;
use app\exception\TokenException;
use app\exception\WeChatException;
use think\Exception;
use app\api\model\User as UserModel;

class UserToken
{
    protected $code;
    protected $wxAppID;
    protected $wxAppSecret;
    protected $loginUrl;

    // 构造函数-组织微信的url
    public function __construct($code)
    {
        $this->code = $code;
        $this->wxAppID = config('wx_config.appid');
        $this->wxAppSecret = config('wx_config.secret');
        $this->loginUrl = sprintf(config('wx_config.login_url'), $this->wxAppID, $this->wxAppSecret, $this->code);
    }

    /**
     * API 最终获得token的key
     */

    // 用wx.login的code换取openid和session_key并
    public function get_Token_Service()
    {
        $result = curl_get($this->loginUrl);
        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            throw new WeChatException(['msg' => '微信内部错误，获取openid和session_key异常']);   //如果微信没有返回任何数据，抛出异常记录日志
        } else {
            if (array_key_exists('errcode', $wxResult)) {
                //如果微信返回的数据中包含errcode就抛出异常告诉客户端
                throw new WeChatException(['msg' => $wxResult['errmsg']]);
            } else {
                // 返回客户端缓存后的token的key
                return $this->grantToken($wxResult);
            }
        }
    }


    // 创建用户并返回缓存后的token的key
    public function grantToken($wxResult)
    {
        //拿到openid
        //查看数据库有没有这个openid,没有就新增数据，
        //有->生成令牌，缓存令牌数据.
        //返回令牌给客户端
        $openid = $wxResult['openid'];
        // 处理用户数据
        $user = UserModel::getByOpenid($openid);
        if ($user) {
            // 增加访问量
            $inc = $user->setInc('fangwen',1);
            if($inc === false){
                throw new QueryDbException(['msg'=>'grantToken增加访问量']);
            }

            $uid = $user->id;
        } else {
            $uid = UserModel::create_user($openid); // 携带openid和用户信息去创建用户
        }
        return $this->save_Cache_Token($wxResult, $uid);    //保存token缓存，返回token的key
    }


    //保存token缓存
    //接受微信返回的数据；用户id等等..，封装后写入缓存.
    //返回缓存的key
    public function save_Cache_Token($wxResult, $uid)
    {
        $tokenKey = BaseToken::prepare_Token_Key();     //获取token_key
        $tokenValue = BaseToken::prepare_Token_Value($wxResult, $uid);         //获取token_value
        $token_expire = config('wx_config.token_expire');  //获取token过期时间

        $token = cache($tokenKey, $tokenValue, $token_expire);    //缓存token
        if (!$token) {
            throw new TokenException(['msg' => 'token写入缓存时失败，来自save_Cache_Token']);
        }

        // 检查并返回登录态(2018/04/30添加的登陆态检查)
        $loginState = $this->checkformId($uid);

        return ['token_key' => $tokenKey, 'loginstate' => $loginState];
    }

    // 2018/04/30添加的登陆态检查
    // 如果有form_id且没有过期返回登陆态正常，用户不需要重复登陆（formID是登陆的时候和userinfo一起写入用户表的，登陆频率取决于form_id失效频率）
    // 检查并返回登录态
    private function checkformId($uid)
    {
        $userModel = new UserModel();
        $user = $userModel->where('id', $uid)->find();
        if ($user === false) {
            throw new QueryDbException(['msg'=>'checkformId']);
        }
        // 设置登录态
        if($user['form_id'] === ''){
            $loginState = false;
        }else{
            $loginState = true;
        }
        return $loginState;
    }

}
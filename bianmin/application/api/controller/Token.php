<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28 0028
 * Time: 下午 6:17
 */

namespace app\api\controller;


use app\api\service\UserToken;
use app\exception\Success;
use app\exception\TokenException;
use think\Cache;
use app\api\model\User as UserModel;

class Token
{

    // 获取token接口
    public function getToken($code)
    {
        $usertoken = new UserToken($code); //接受code，在构造函数中封装微信获取openid的url

        // 这个方法2018/04/30添加了登陆态检查返回
        // $token = ['token_key' => $tokenKey, 'loginstate' => $loginState];
        $token = $usertoken->get_Token_Service();

        throw new Success(['data' => $token]); //返回一个json对象（直接返回$token是json字符串）
    }


    /**
     *  API
     *  检查token是否有效接口
     *  接受：客户端缓存中的token_key
     *  返回：布尔
     */

    public function verifyToken($token)
    {
        if (!$token) {
            throw new TokenException(['msg' => '检查token时token为空']);
        }

        $cache = Cache::get($token);
        if (!$cache) {
            // token失效
            throw new Success(['data' => ['token' => false]]);
        } else {
            // token有效，检查登录态
            $loginState = $this->checkformId($cache);
            throw new Success(['data' => ['token' => true, 'loginstate' => $loginState]]);
        }
    }

    // 2018/04/30添加的登陆态检查
    // 如果有form_id且没有过期返回登陆态正常，用户不需要重复登陆（formID是登陆的时候和userinfo一起写入用户表的，登陆频率取决于form_id失效频率）
    // 检查并返回登录态
    private function checkformId($cache)
    {
        $cache = json_decode($cache, true);

        $userModel = new UserModel();
        $user = $userModel->where('id', $cache['uid'])->find();
        if ($user === false) {
            //
        }

        // 检查formID
        if (empty($user['form_id'])) {
            // form_id为空，设置登陆态为false
            $loginState = false;
        } else {
            // 有form_id，检查是否过期
            $dt = time();           // 当前时间
            $update_time = strtotime($user['update_time']);
            $ShiJianCha = $dt - $update_time;

            if ($ShiJianCha > 604800) {
                // 大于7天过期，设置登陆态为false
                $loginState = false;
            } else {
                // form_id有效，设置登陆态为true
                $loginState = true;
            }
        }
        return $loginState;
    }


    // 获取第三方令牌（*）
    /**
     * API
     * 第三方应用获取令牌
     * @url /app_token?
     * @POST ac=:ac se=:secret
     */
//    public function getAppToken($ac = '', $se = '')
//    {
////        header('Access-Control-Allow-Origin: *');
////        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
////        header('Access-Control-Allow-Methods: GET');
////        (new AppTokenValidate())->goCheck();
//
//        $app = new AppToken();
//        $token = $app->getThirdAppTokenService($ac, $se);
//
//        // 返回客户端
//        throw new Success(['data' => $token]);
//    }


//    public function getToken()
//    {
//
//        // 进入 -》
//
//
//        // 获得客户端getuserinfo数据，包含wx.longin的code
//        $post = input('post.');
//
//        $code = $post['code'];
//        $encryptedData = $post['encryptedData'];
//        $iv = $post['iv'];
//
//        // 解密userinfo
//        $userinfo = (new GetUserInfo())->jiemi_UserInfo($code, $encryptedData, $iv, $sessionKey);
//
//        // 新增或更新用户信息并返回uid
//        $uid = User::create_or_Update_User($userinfo);
//
//        // 生成token并缓存
//        $tokenKey = BaseToken::save_Cache_Token($userinfo['openId'], $uid, $sessionKey);
//
//        // 返回token，昵称头像给客户端
//        $arr = ['avatar_url' => $userinfo['avatarUrl'], 'nick_name' => $userinfo['nickName']];
//        throw new Success([
//            'data' => ['token' => $tokenKey, 'userinfo' => $arr],
//            'msg' => 'login:ok'
//        ]);
//    }
}
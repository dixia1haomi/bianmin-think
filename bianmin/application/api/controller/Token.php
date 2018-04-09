<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28 0028
 * Time: 下午 6:17
 */

namespace app\api\controller;


use app\api\model\User;
use app\api\service\AppToken;
use app\api\service\BaseToken;
use app\api\service\userinfo\GetUserInfo;
use app\api\service\userinfo\JiemiUserInfo;
use app\api\service\UserToken;
use app\exception\Success;
use app\exception\TokenException;
use think\Cache;

class Token
{

    // 获取token接口
//    public function getToken($code){
//
//        $ut = new UserToken($code); //接受code，在构造函数中封装微信获取openid的url
//        $token = $ut->get_Token_Service();
//
//        return ['token_key' => $token]; //返回一个json对象（直接返回$token是json字符串）
//    }


    /**
     *  API
     *  登陆
     */

    public function getToken()
    {

        // 进入 -》


        // 获得客户端getuserinfo数据，包含wx.longin的code
        $post = input('post.');
        $code = $post['code'];
        $encryptedData = $post['encryptedData'];
        $iv = $post['iv'];

        // 解密userinfo
        $userinfo = (new GetUserInfo())->jiemi_UserInfo($code, $encryptedData, $iv);

        // 新增或更新用户信息并返回uid
        $uid = User::create_or_Update_User($userinfo);

        // 生成token并缓存
        $tokenKey = BaseToken::save_Cache_Token($userinfo['openId'], $uid);

        // 返回token，昵称头像给客户端
        $arr = ['avatar_url' => $userinfo['avatarUrl'], 'nick_name' => $userinfo['nickName']];
        throw new Success(['data' => ['token' => $tokenKey, 'userinfo' => $arr]]);
    }


    /**
     *  API
     *  检查token是否有效接口
     *  接受：客户端缓存中的token_key
     *  返回：布尔
     */

    public function verifyToken($token = '')
    {
        if (!$token) {
            throw new TokenException(['msg' => '检查token时token为空']);
        }

        $exist = Cache::get($token);
        if (!$exist) {
            throw new Success(['data'=>false]);
        }else{
            throw new Success(['data'=>true]);
        }
    }


    // 获取第三方令牌（*）
    /**
     * API
     * 第三方应用获取令牌
     * @url /app_token?
     * @POST ac=:ac se=:secret
     */
    public function getAppToken($ac = '', $se = '')
    {
//        header('Access-Control-Allow-Origin: *');
//        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
//        header('Access-Control-Allow-Methods: GET');
//        (new AppTokenValidate())->goCheck();

        $app = new AppToken();
        $token = $app->getThirdAppTokenService($ac, $se);

        // 返回客户端
        throw new Success(['data' => $token]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28 0028
 * Time: 下午 6:17
 */

namespace app\api\controller;


use app\api\service\UserToken;
use app\exception\QueryDbException;
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
            throw new QueryDbException(['msg'=>'checkformId']);
        }

        // 检查formID
        if (empty($user['form_id'])) {
            // form_id为空，设置登陆态为false
            $loginState = false;
        } else {
            // 这里formid直接返回不做检查、检查formid的任务改成计划任务自动检查删除
            $loginState = true;
        }
        return $loginState;
    }

}
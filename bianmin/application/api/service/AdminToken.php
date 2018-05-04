<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/4 0004
 * Time: 下午 6:54
 */

namespace app\api\service;


use app\api\model\Admin;
use app\exception\Success;

class AdminToken extends BaseToken
{
    //第三方app登录检测并且缓存token
    public function getThirdAppTokenService($zhanghao, $mima)
    {
        $check = Admin::check($zhanghao, $mima);
        if (!$check) {
            throw new Success(['msg' => '授权失败']);
        } else {
            $values = [
                'scope' => $check->scope,
                'uid' => $check->id
            ];
            // 生成并缓存Token
            $token = $this->saveToCache($values);
            return $token;
        }
    }

    // admin登录缓存token
    private function saveToCache($values)
    {
        $token = self::prepare_Token_Key();
        $expire_in = config('wx_config.token_expire');
        $cache = cache($token, json_encode($values), $expire_in);
        if (!$cache) {
            throw new Success(['msg' => 'admin登录缓存token失败,来自Admin -- saveToCache']);
        }
        return $token;
    }
}
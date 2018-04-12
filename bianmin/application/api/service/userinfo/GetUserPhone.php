<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/12 0012
 * Time: 上午 6:18
 */

namespace app\api\service\userinfo;


use app\api\service\BaseToken;
use app\api\service\BaseWeChat;
use app\exception\WeChatException;

class GetUserPhone
{

    /**
     *  用户登陆，获取解密后的userinfo
     */

    public function jiemi_UserPhone($encryptedData, $iv)
    {
        // 获取APPID
        $appid = config('wx_config.appid');

        // 缓存中获取sessionKey
        $sessionKey = BaseToken::get_Token_Value_Vars('session_key');

        // 解密userPhone
        $jiemi = new JiemiUserInfo($appid, $sessionKey);
        $errCode = $jiemi->decryptData($encryptedData, $iv, $data);   // $data == 解密后的数据

        if ($errCode == 0) {
            return json_decode($data, true);
        } else {
            // 异常
            throw new WeChatException(['msg' => '解密userPhone失败，service/userinfo/GetUserPhone/jiemi_UserPhone']);
        }
    }

}
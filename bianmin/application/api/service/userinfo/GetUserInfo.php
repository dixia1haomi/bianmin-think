<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/1 0001
 * Time: 下午 7:29
 */

namespace app\api\service\userinfo;


use app\api\model\User;
use app\api\service\BaseWeChat;
use app\exception\WeChatException;
use think\Exception;

class GetUserInfo
{

    /**
     *  用户登陆，获取解密后的userinfo
     */

    public function jiemi_UserInfo($code, $encryptedData, $iv)
    {

        $appid = config('wx_config.appid');

        // 用code换取sessionKey
        $wxRes = (new BaseWeChat())->loginCode($code);
        $sessionKey = $wxRes['session_key'];


        // 解密userinfo
        $jiemi = new JiemiUserInfo($appid, $sessionKey);
        $errCode = $jiemi->decryptData($encryptedData, $iv, $data);   // $data == 解密后的数据
        if ($errCode == 0) {
            return json_decode($data, true);
        } else {
            // 异常
            throw new WeChatException(['msg' => '解密userinfo失败，service/userinfo/GetUserInfo/jiemi_UserInfo']);
        }
    }


}
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
use app\exception\Success;
use app\exception\WeChatException;
use think\Log;

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
        } else if($errCode == -41003){
            // 记录日志、这里可能是token到期引发的session_key对不起来返回-41003、抛出Success让用户再试一次
            Log::init([
                'type' => 'File',
                'path' => LOG_PATH_WECHAT_EXCEPTION,   // 自定义的日志文件路径
            ]);
            Log::record('解密电话-41003', 'jiemi_UserPhone');
            throw new Success(['data' => $errCode]);
        }else {
            throw new WeChatException(['msg' => '解密userPhone失败，service/userinfo/GetUserPhone/jiemi_UserPhone', 'data' => $errCode]);
        }
    }

}
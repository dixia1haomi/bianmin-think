<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/7 0007
 * Time: 上午 10:59
 */

namespace app\api\controller;

use app\api\model\User as userModel;
use app\api\service\BaseToken;
use app\api\service\userinfo\GetUserPhone;
use app\exception\QueryDbException;
use app\exception\Success;
use think\Log;

class Index
{

    // -------------------------------------------- 电话解密 --------------------------------------------
    public function getPhone()
    {
        $params = input('post.');
        $encryptedData = $params['encryptedData'];
        $iv = $params['iv'];

        // 解密userphone
        $phone = (new GetUserPhone())->jiemi_UserPhone($encryptedData, $iv);
        /**
         * $phone
         * countryCode:"86"                 区号
         * phoneNumber:"15987419288"        用户绑定的手机号（国外手机号会有区号）
         * purePhoneNumber:"15987419288"    没有区号的手机号
         */
        // 把电话号码写入数据库
        $uid = BaseToken::get_Token_Uid();
        $model = new userModel();

        $updatephone = $model->where(['id' => $uid])->setField('phone', $phone["purePhoneNumber"]);

        if ($updatephone === false) {
            throw new QueryDbException(['msg' => '电话解密getPhone']);
        }
        throw new Success(['data' => $phone["purePhoneNumber"]]);
    }


    // -------------------------------------------- 记录app.onError错误 ----------------------------------------------------
    public function create_App_onError($msg)
    {
        // 开启日志记录
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH_APPJS_ONERROR,   // 自定义的日志文件路径
        ]);
        Log::record($msg);
    }


}
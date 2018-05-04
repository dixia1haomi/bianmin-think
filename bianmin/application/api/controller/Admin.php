<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/4 0004
 * Time: 下午 6:17
 */

namespace app\api\controller;

use app\api\model\Admin as adminModel;
use app\api\service\AdminToken;
use app\exception\Success;
use think\Cache;

use app\api\model\User as userModel;

class Admin
{

    // 获取admin令牌
    public function getAdminToken()
    {
        $zhanghao = input('post.zhanghao');
        $mima = input('post.mima');

        $admin = new AdminToken();
        $token = $admin->getThirdAppTokenService($zhanghao, $mima);

        // 返回客户端
        throw new Success(['data' => $token]);
    }

    // 检查adminToken是否有效
    public function verifyAdminToken($token)
    {
        if (!$token) {
            throw new Success(['msg' => '检查token时token为空']);
        }

        $cache = Cache::get($token);
        if (!$cache) {
            // token失效
            throw new Success(['data' => false]);
        } else {
            // token有效
            throw new Success(['data' => true]);
        }
    }


    // 查询formId有效的用户
    public function selectFormId_User()
    {
        // 1.formid不为空
        // 2.更新时间不超过7天
        $guoqishijian = time() - (60 * 60 * 24 * 7);
        $userModel = new userModel();
        $user = $userModel->where('form_id', '<>', '')->where('update_time', '>', $guoqishijian)->select();
        if ($user === false) {
            //
        }
        throw new Success(['data' => $user]);
    }
}
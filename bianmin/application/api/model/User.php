<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28 0028
 * Time: 下午 6:39
 */

namespace app\api\model;


use app\api\service\BaseToken;
use app\exception\QueryDbException;
use think\Model;

class User extends Model
{
    // 通过openid查询用户是否存在
    public static function getByOpenid($openid)
    {
        $user = self::where('openid', $openid)->find();
        if ($user === false) {
            throw new QueryDbException(['msg' => 'getByOpenid']);
        }
        return $user;
    }

    // 没得用户，新增用户
    public static function create_user($openid)
    {
        $user = self::create(['openid' => $openid]);
        if ($user === false) {
            throw new QueryDbException(['msg' => 'create_user']);
        }
        return $user->id;
    }


    // 登陆
    public static function saveUserinfoModel($info, $uid)
    {
        $res = self::update([
            'nick_name' => $info['nickName'],
            'avatar_url' => $info['avatarUrl'],
            'city' => $info['city'],
            'gender' => $info['gender'],
            'province' => $info['province'],
            'form_id' => $info['form_id']
        ], ['id' => $uid]);

        if($res === false){
            throw new QueryDbException(['msg' => 'saveUserinfoModel']);
        }
        return $res;
    }

}
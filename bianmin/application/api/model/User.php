<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28 0028
 * Time: 下午 6:39
 */

namespace app\api\model;


use app\exception\QueryDbException;
use think\Model;

class User extends Model
{
    // 通过openid查询用户是否存在
    public static function getByOpenid($openid){
        return self::where('openid',$openid)->find();
    }

    //新增用户,返回id
//    public static function create_user($openid){
//        $user = self::create(['openid'=>$openid]);
//        return $user->id;
//    }



    // 关联->userhuati表
//    public function userToUserhuati()
//    {
//        return $this->hasMany('userhuati', 'user_id', 'id');
//    }


    // ---------------------------------------------- 重构登陆解密userinfo --------------------------------

    // 新增或更新用户并返回uid
    public static function create_or_Update_User($data){

        // 准备userinfo数据
        $arr = [
            'openid' => $data['openId'],
            'nick_name' => $data['nickName'],
            'avatar_url' => $data['avatarUrl'],
            'city' => $data['city'],
            'gender' => $data['gender'],
            'province' => $data['province']
        ];

        // 通过openid查询用户是否存在
        $user = self::getByOpenid($data['openId']);     // 没有返回null
        if($user){
            // user表中存在此用户，对比是否需要更新并取uid
            self::checkUser($arr,$user);
            $uid = $user->id;
        }else{
            // 没有，新增用户并后取uid
            $new_user = self::create($arr);
            if($new_user === false){
                throw new QueryDbException(['msg'=>'新增用户写入失败，User/createUser']);
            }
            $uid = $new_user->id;
        }
        return $uid;
    }


    // 检查新获取的用户信息是否和已有的一样
    public static function checkUser($arr,$user){
        if($arr['nick_name'] != $user['nick_name'] || $arr['avatar_url'] != $user['avatar_url'] || $arr['city'] != $user['city'] || $arr['gender'] != $user['gender'] || $arr['province'] != $user['province']){
            // 不一样，更新用户信息
            $update = self::update($arr,['id'=>$user['id']]);
            if($update === false){
                // *这里更新失败不要终止，只需要记录日志,成功也不需要返回
            }
        }
        return true;    // 继续执行
    }


}
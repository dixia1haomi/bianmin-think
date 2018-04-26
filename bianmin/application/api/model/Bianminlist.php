<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/8 0008
 * Time: 上午 9:11
 */

namespace app\api\model;


use think\Model;

class Bianminlist extends Model
{
    // 关联->user
    public function withUser()
    {
        return $this->hasOne('user', 'id', 'user_id')->bind(['nick_name','avatar_url']);
    }

    // 关联->user
    public function withImg()
    {
        return $this->hasMany('img', 'list_id', 'id');
    }

    // 关联->liuyan
    public function withLiuyan()
    {
        return $this->hasMany('liuyan', 'bmxx_id', 'id')->with(['liuyanwithUser'])->with(['liuyanwithHuifu']);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28 0028
 * Time: 上午 9:16
 */

namespace app\api\model;


use think\Model;

class Huodongcanyu extends Model
{
    // 关联->user表
    public function withUser()
    {
        return $this->hasOne('user', 'id', 'user_id')->bind(['avatar_url','nick_name']);
    }

    // 关联->助力表
    public function withZhuli()
    {
        return $this->hasMany('huodongzhuli','canyu_id','id')->with(['withZhuliuser']);
    }

    // 关联->领取表
//    public function withLingqu()
//    {
//        return $this->hasOne('huodonglingqu','canyu_id','id');
//    }

    // 关联->活动表
    public function withHuodong()
    {
        return $this->hasOne('huodong','id','huodong_id');
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/24 0024
 * Time: 上午 11:46
 */

namespace app\api\model;


use think\Model;

class Huifu extends Model
{
    protected $hidden = ['create_time','delete_time', 'update_time'];

    // 回复关联->回复人
    public function huifuwithHfuser()
    {
        return $this->hasOne('user', 'id', 'user_id')->bind(['user'=>'nick_name']);
    }

    // 回复关联->被回复人
    public function huifuwithBhfuser()
    {
        return $this->hasOne('user', 'id', 'huifu_user_id')->bind(['hf_user'=>'nick_name']);
    }

    // 回复关联->留言
    public function huifuwithLiuyan()
    {
        return $this->hasOne('liuyan', 'id', 'liuyan_id');
    }
}
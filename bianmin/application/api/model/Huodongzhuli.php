<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28 0028
 * Time: 下午 4:55
 */

namespace app\api\model;


use think\Model;

class Huodongzhuli extends Model
{
    // 关联->user表
    public function withZhuliuser()
    {
        return $this->hasOne('user', 'id', 'user_id')->bind(['avatar_url','nick_name']);
    }
}
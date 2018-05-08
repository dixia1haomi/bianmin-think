<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/6 0006
 * Time: 上午 8:31
 */

namespace app\api\model;


use think\Model;

class Bmxxdingzhijilu extends Model
{

    // 关联->bangdinguser
    public function withDingzhiuser()
    {
        return $this->hasOne('user', 'id', 'user_id')->bind(['avatar_url','nick_name']);
    }
}
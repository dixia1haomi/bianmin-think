<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 上午 9:36
 */

namespace app\api\model;


use think\Model;

class Huodong extends Model
{
    // 关联->活动IMG
    public function withhuodongImg()
    {
        return $this->hasMany('huodongimg', 'huodong_id', 'id');
    }
}
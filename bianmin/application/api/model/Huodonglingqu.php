<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/31 0031
 * Time: 下午 3:39
 */

namespace app\api\model;


use think\Model;

class Huodonglingqu extends Model
{
    // 关联->活动表
    public function withHuodong()
    {
        return $this->hasOne('huodong','id','huodong_id');
    }

    // 关联->商家表
    public function withShangjia()
    {
        return $this->hasOne('shangjia','id','shangjia_id');
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/18 0018
 * Time: 上午 10:23
 */

namespace app\api\model;


use think\Model;

class Shangjia extends Model
{
    // 关联->商家IMG
    public function withshangjiaImg()
    {
        return $this->hasMany('shangjiaimg', 'shangjia_id', 'id');
    }

    // 关联->已发布的活动
    public function withFabuhuodong()
    {
        return $this->hasMany('huodong', 'shangjia_id', 'id')->where('state',1);
    }
}
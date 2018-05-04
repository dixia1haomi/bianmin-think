<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/4 0004
 * Time: 下午 6:18
 */

namespace app\api\model;


use think\Model;

class Admin extends Model
{
    public static function check($zhanghao, $mima)
    {
        $app = self::where('zhanghao', $zhanghao)->where('mima', $mima)->find();
        if ($app === false) {
            //
        }
        return $app;
    }
}
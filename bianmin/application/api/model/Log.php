<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/19 0019
 * Time: 下午 4:12
 */

namespace app\api\model;


use app\exception\QueryDbException;
use think\Model;

class Log extends Model
{

    // 写入日志并且抛出errorCode 不等于 0
    public static function mysql_log($laiyuan,$miaoshu){
        // 写入数据库
        self::create(['laiyuan'=>$laiyuan,'miaoshu'=>$miaoshu]);

        // 抛出errorCode等于1
//        throw new QueryDbException();
    }


    // redis异常
    public static function redis_log($laiyuan,$miaoshu){
        // 写入数据库
        self::create(['laiyuan'=>$laiyuan,'miaoshu'=>$miaoshu]);

    }
}
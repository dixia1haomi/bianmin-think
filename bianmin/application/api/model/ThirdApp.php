<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/6 0006
 * Time: 上午 10:40
 */

namespace app\api\model;


use app\exception\Success;
use think\Model;

class ThirdApp extends Model
{
    public static function check($ac, $se)
    {
        $app = self::where('app_id','=',$ac)->where('app_secret', '=',$se)->find();

        if($app === false){
            Log::mysql_log('mysql/ThirdApp/check','admin检查未通过');
        }

        return $app;
    }
}
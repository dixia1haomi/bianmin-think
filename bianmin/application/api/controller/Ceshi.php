<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/12 0012
 * Time: 下午 8:42
 */

namespace app\api\controller;


use app\api\service\userinfo\GetUserInfo;
use think\Cache;

class Ceshi
{

    public function index(){
//        $post = input('post.');
//        return $post;
        $getinfo = new GetUserInfo();
        return $getinfo->jiemi();
    }
}
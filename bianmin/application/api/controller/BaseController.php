<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/1 0001
 * Time: 下午 5:39
 */

namespace app\api\controller;


use app\api\service\BaseToken;
use think\Controller;

class BaseController extends Controller
{

    // 管理员身份验证
    protected function checkAdmin()
    {
        BaseToken::checkAdminScope();
    }
}
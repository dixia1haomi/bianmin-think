<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11 0011
 * Time: 上午 8:51
 */

namespace app\exception;


class QueryDbException extends BaseException
{
    // 数据库查询错误
    public $code = 200;

    public $msg = 'QueryDbException';

    public $errorCode = 30000;
}


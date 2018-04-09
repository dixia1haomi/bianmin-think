<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11 0011
 * Time: 上午 11:11
 */

namespace app\exception;


class CheckParamException extends BaseException
{
    public $code = 200;

    public $msg = '查询数据库传入的参数验证错误，CheckParamException';

    public $errorCode = 10000;
}

// 参数错误 $Code = 10001

// 数据库错误 $Code = 10002

// 微信方面错误 $Code = 10003

// Token错误 $Code = 10004

// ------------------------------------

// 参数错误 $errorCode = 10000

// 数据库错误 $errorCode = 20000

// 微信方面错误 $errorCode = 30000

// Token错误 $errorCode = 40000
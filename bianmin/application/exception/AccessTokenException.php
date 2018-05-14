<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/14 0014
 * Time: 上午 12:32
 */

namespace app\exception;


class AccessTokenException extends BaseException
{
    public $code = 200;

    public $msg = 'AccessTokenException';

    public $errorCode = 40000;
}
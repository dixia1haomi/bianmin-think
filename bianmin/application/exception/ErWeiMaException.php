<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/14 0014
 * Time: 上午 12:58
 */

namespace app\exception;


class ErWeiMaException extends BaseException
{
    public $code = 200;

    public $msg = 'ErWeiMaException';

    public $errorCode = 50000;
}
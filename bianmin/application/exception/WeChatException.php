<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28 0028
 * Time: 下午 6:33
 */

namespace app\exception;


class WeChatException extends BaseException
{
    public $code = 200;

    public $msg = 'WeChatException';

    public $errorCode = 20000;
}


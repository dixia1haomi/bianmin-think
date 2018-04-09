<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/12 0012
 * Time: 下午 2:17
 */

namespace app\exception;


class Success extends BaseException
{
    public $code = 200;

    public $msg = 'ok';

    public $errorCode = 0;
}
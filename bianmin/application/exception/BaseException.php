<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11 0011
 * Time: 上午 8:49
 */

namespace app\exception;


use think\Exception;
use think\Log;

class BaseException extends Exception
{
    // TokenException -> errorCode = 10000
    // WeChatException -> errorCode = 20000
    // QueryDbException -> errorCode = 30000
    // AccessTokenException -> errorCode = 40000
    // ErWeiMaException -> errorCode = 50000
    // MoBanXiaoXiException -> errorCode = 60000

    public $code = 200;
    public $msg = 'BaseMsg';
    public $errorCode = 0;
    public $data = 'no';


    //构造函数->用于让外面的异常可以接受自定义的参数：如：throw new testException(['msg'=>'查询数据不存在']);
    public function __construct($params = [])
    {
        if (!is_array($params)) {
            //return ; 可以直接return让传入的参数无效，使用默认的参数,跟下面的抛出异常二选一，都可以
            throw new Exception('自定义异常抛出的参数必须是数组，来自BaseException');
        }

        //如果传进来的数组中有code，就覆盖code
        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }

        //如果传进来的数组中有msg，就覆盖msg
        if (array_key_exists('msg', $params)) {
            $this->msg = $params['msg'];
        }

        //如果传进来的数组中有errorCode，就覆盖errorCode
        if (array_key_exists('errorCode', $params)) {
            $this->errorCode = $params['errorCode'];
        }

        //如果传进来的数组中有data，就覆盖data
        if (array_key_exists('data', $params)) {
            $this->data = $params['data'];
        }

        $this->log();
    }

    // 抛出错误是记录日志
    private function log()
    {
        // 如果是TokenException、记录日志
        if ($this->errorCode == 10000) {
            Log::init([
                'type' => 'File',
                'path' => LOG_PATH_TOKEN_EXCEPTION,   // 自定义的日志文件路径
            ]);
            Log::record($this->msg, 'TokenException');
        }

        // 如果是WeChatException、记录日志
        if ($this->errorCode == 20000) {
            Log::init([
                'type' => 'File',
                'path' => LOG_PATH_WECHAT_EXCEPTION,   // 自定义的日志文件路径
            ]);
            Log::record($this->msg, 'WeChatException');
        }

        // 如果是QueryDbException、记录日志
        if ($this->errorCode == 30000) {
            Log::init([
                'type' => 'File',
                'path' => LOG_PATH_QUERYDB_EXCEPTION,   // 自定义的日志文件路径
            ]);
            Log::record($this->msg, 'QueryDbException');
        }

        // 如果是AccessTokenException、记录日志
        if ($this->errorCode == 40000) {
            Log::init([
                'type' => 'File',
                'path' => LOG_PATH_ACCESS_TOKEN_EXCEPTION,   // 自定义的日志文件路径
            ]);
            Log::record($this->msg, 'AccessTokenException');
        }

        // 如果是ErWeiMaException、记录日志
        if ($this->errorCode == 50000) {
            Log::init([
                'type' => 'File',
                'path' => LOG_PATH_ERWEIMA_EXCEPTION,   // 自定义的日志文件路径
            ]);
            Log::record($this->msg, 'ErWeiMaException');
        }

    }

}
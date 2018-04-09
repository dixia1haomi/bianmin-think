<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11 0011
 * Time: 上午 8:49
 */

namespace app\exception;




use think\Exception;

class BaseException extends Exception
{
    public $code = 200;
    public $msg = 'BaseMsg';
    public $errorCode = 10000;
    public $data = 'no';


    //构造函数->用于让外面的异常可以接受自定义的参数：如：throw new testException(['msg'=>'查询数据不存在']);
    public function __construct($params=[])
    {
        if(!is_array($params)){
            //return ; 可以直接return让传入的参数无效，使用默认的参数,跟下面的抛出异常二选一，都可以
            throw new Exception('自定义异常抛出的参数必须是数组，来自BaseException');
        }

        //如果传进来的数组中有code，就覆盖code
        if(array_key_exists('code',$params)){
            $this->code = $params['code'];
        }

        //如果传进来的数组中有code，就覆盖code
        if(array_key_exists('msg',$params)){
            $this->msg = $params['msg'];
        }

        //如果传进来的数组中有code，就覆盖code
        if(array_key_exists('errorCode',$params)){
            $this->errorCode = $params['errorCode'];
        }

//        //如果传进来的数组中有code，就覆盖code
        if(array_key_exists('data',$params)){
            $this->data = $params['data'];
        }
    }

}
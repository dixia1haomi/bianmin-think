<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/16 0016
 * Time: 下午 11:27
 */
//use Qcloud_cos\Auth;
//use Qcloud_cos\Cosapi;


namespace app\api\controller;

require('../cossdk/include.php');

use app\exception\Success;
use QCloud\Cos\Auth;
use QCloud\Cos\Api;


class Cos
{


    public function config()
    {
        $config = array(
            'app_id' => '1253443226',
            'secret_id' => 'AKIDfDZjhT7PabTbZLuLZaP1ReeS8cu0AZZO',
            'secret_key' => 'lKhsqcCZmqjQM3f5IK9oHYdVBf1B9nGX',
            'region' => 'cd',   // bucket所属地域：华北 'tj' 华东 'sh' 华南 'gz'
            'timeout' => 60
        );
        $cosApi = new Api($config);//实例化对象
        return $cosApi;
    }

    // 签名-单次
    public function cosQianMingDanci($cospath)
    {
        // 接受签名路径
        $auth = new Auth($appId = '1253443226', $secretId = 'AKIDfDZjhT7PabTbZLuLZaP1ReeS8cu0AZZO', $secretKey = 'lKhsqcCZmqjQM3f5IK9oHYdVBf1B9nGX');
        $bucket = 'cosceshi';
        $sign = $auth->createNonreusableSignature($bucket, $cospath);
        throw new Success(['data' => $sign]);
    }


    // 删除(可用)
    public function cosdelete($wenjianjia,$fileName)
    {
        $cosApi = $this->config();//调用配置文件的内容
        $bucketName = "cosceshi";
        $path = $wenjianjia.$fileName;  // $wenjianjia = "bianmin/"
        $result = $cosApi->delFile($bucketName, $path);
        return $result;
    }



}
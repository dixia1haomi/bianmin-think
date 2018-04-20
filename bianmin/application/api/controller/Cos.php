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

        //        $cosApi=$this->config();//调用配置文件的内容
    }

    // 签名-单次
    public function cosQianMingDanci($cospath)
    {
        // post 灵活传入签名路径
//        $filepath = input('post.filepath');
        $auth = new Auth($appId = '1253443226', $secretId = 'AKIDfDZjhT7PabTbZLuLZaP1ReeS8cu0AZZO', $secretKey = 'lKhsqcCZmqjQM3f5IK9oHYdVBf1B9nGX');
        $bucket = 'cosceshi';
//        $filepath = "/bianmin";
        $sign = $auth->createNonreusableSignature($bucket, $cospath);

        throw new Success(['data' => $sign]);
    }

    // 签名-多次
//    public function cosQianMingDuoci()
//    {
//        $auth = new Auth($appId = '1253443226', $secretId = 'AKIDfDZjhT7PabTbZLuLZaP1ReeS8cu0AZZO', $secretKey = 'lKhsqcCZmqjQM3f5IK9oHYdVBf1B9nGX');
//        $expiration = time() + 3600;    // 过期时间，当前时间+1小时
//        $bucket = 'cosceshi';
//        $filepath = "/";    // 不是必填
//        $sign = $auth->createReusableSignature($expiration, $bucket);
//        return $sign;
//    }

    // 实验删除(可用)
    public function cosdelete($wenjianjia,$fileName)
    {
        $cosApi = $this->config();//调用配置文件的内容

        $bucketName = "cosceshi";
        $path = $wenjianjia.$fileName;  // $wenjianjia = "bianmin/"
        $result = $cosApi->delFile($bucketName, $path);
        return $result;
    }

    // 实验上传(不可用，以后再linux上就找不到C:/xxx)
    public function cosupdate()
    {
        $cosApi = $this->config();//调用配置文件的内容
        $data = input('post.');
//        {
//            filePath:'C:/xxx'
//        }
        $bucketName = "cosceshi";
        $srcPath = $data["filePath"];
        $dstPath = "/canting/test.png";
        $bizAttr = "";
        $insertOnly = 0;
        $sliceSize = 3 * 1024 * 1024;
        $result = $cosApi->upload($bucketName, $srcPath, $dstPath);
        return $result;
    }

    // 上传文件
//$ret = $cosApi->upload($bucket, $src, $dst);
//var_dump($ret);


//'app_id' => '1253443226',
//'secret_id' => 'AKIDfDZjhT7PabTbZLuLZaP1ReeS8cu0AZZO',
//'secret_key' => 'lKhsqcCZmqjQM3f5IK9oHYdVBf1B9nGX',
//'region' => 'cd',   // bucket所属地域：华北 'tj' 华东 'sh' 华南 'gz'
//'timeout' => 60

### 有签名了，准备上传文件

}
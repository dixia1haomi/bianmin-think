<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28 0028
 * Time: 下午 12:35
 */

namespace app\api\service\shengchengerweima;


use app\api\service\AccessToken;
use app\exception\ErWeiMaException;
use think\Image;

class XinxiDingZhiErweima
{
    // 生成可带参数的小程序码接口B，不限制数量
    private $sendUrl = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?" . "access_token=%s";

    // 获得AccessToken
    function __construct()
    {
        $accessToken = new AccessToken();
        $token = $accessToken->get();
        $this->sendUrl = sprintf($this->sendUrl, $token);
    }

    // 获得小程序码
    public function getCanShuMa($scene)
    {
        // header('content-type:image/jpg');
        $data = array();
        $data['scene'] = $scene;//自定义信息，可以填写诸如识别用户身份的字段，注意用中文时的情况
        $data['page'] = "pages/index/index1";//扫描后对应的path
        $data['width'] = 200;//自定义的尺寸
        // $data['auto_color'] = false;
        // $data['line_color'] = '{"r":"28","g":"157","b":"240"}';

        // 发送请求
        $da = curl_post($this->sendUrl, $data);

        // 如果$da里面包含errcode证明出错了 strpos:检查字符串中是否包含另一个字符串，没有返回false
        $nda = strpos($da, "errcode");
        if ($nda !== false) {
            // 可能有错误,生成二维码出现errcode
            throw new ErWeiMaException(['msg' => '向微信请求的二维码返回的二进制流值中包含errcode,XinxiDingZhiErweima']);
        }

        // 把数据流转成图片保存到本地
        file_put_contents("/data/wwwroot/default/bianmin/public/erweima/" . $scene . ".jpeg", $da);
        // Image类处理图片
        $haibao_base = Image::open('/data/wwwroot/default/bianmin/public/erweimamoban/bmxxfenxianghaibao.jpg');

        $haibao_base->water("/data/wwwroot/default/bianmin/public/erweima/" . $scene . ".jpeg", 5)
            ->save("/data/wwwroot/default/bianmin/public/erweima/" . $scene . ".jpeg");

        return '/erweima/' . $scene . '.jpeg';
    }
}
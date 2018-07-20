<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/29 0029
 * Time: 下午 1:19
 */

namespace app\api\service\mobanxiaoxi;


use app\api\service\AccessToken;
use app\exception\Success;

class MobanXiaoxi
{

    private $sendUrl = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?" . "access_token=%s";
    private $touser;
    //不让子类控制颜色
    private $color = 'black';

    protected $tplID;
    protected $page;
    protected $formID;
    protected $data;
    protected $emphasisKeyWord;

    function __construct()
    {
        $accessToken = new AccessToken();
        $token = $accessToken->get();
        $this->sendUrl = sprintf($this->sendUrl, $token);
    }

    //
    protected function sendMessage($openID)
    {

        $data = [
            'touser' => $openID,
            'template_id' => $this->tplID,
            'page' => $this->page,
            'form_id' => $this->formID,
            'data' => $this->data,
            'color' => $this->color,
//            'emphasis_keyword' => $this->emphasisKeyWord
        ];
        $result = curl_post($this->sendUrl, $data);
        $result = json_decode($result, true);
        return $result;
    }
}
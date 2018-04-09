<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/9 0009
 * Time: 下午 2:06
 */

namespace app\api\service\kajuan;


use think\Exception;

class CheckCardDetail
{

    // 调用微信接口查询卡劵详细信息（包含库存）
    // 接受卡劵ID

    public function go($card_id)
    {

        // 需要公众号accsee_token,card_id
        // 返回数据查 https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1451025272

        // 获取公众号accsee_token
        $gzh_accsee = new GzhAccseeToken();
        $gzh_accsee_token = $gzh_accsee->get();

        // 微信接口POST
        $url = "https://api.weixin.qq.com/card/get?access_token=".$gzh_accsee_token;

        // 发送POST请求
        $card_detail = curl_post($url,['card_id'=>$card_id]);
        $card_detail = json_decode($card_detail,true);

        if($card_detail['errcode'] != 0){
            throw new Exception('调用微信接口查询卡劵详细信息失败,kajuan/CheckCardDetail类');
        }

        return $card_detail;
    }



}
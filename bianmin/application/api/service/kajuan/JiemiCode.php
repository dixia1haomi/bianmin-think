<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/9 0009
 * Time: 下午 1:37
 */

namespace app\api\service\kajuan;


use think\Exception;

class JiemiCode
{

    // 解密领取卡劵后的code，接受加密的code
    public function go($code){

        // **先获取储存的加密code和card_id

        // 获取公众号AccseeToken
        $GzhAccseeTokenM = new GzhAccseeToken();
        $gzh_acc = $GzhAccseeTokenM->get();

        // 发送请求换取解密后的code
        $url = "https://api.weixin.qq.com/card/code/decrypt?access_token=".$gzh_acc;

        $jiemi_code = curl_post($url,['encrypt_code'=>$code]);
        $jiemi_code = json_decode($jiemi_code,true);

        if($jiemi_code['errcode'] != 0){
            throw new Exception('解密Card_code失败,kajuan/jiemiCode');
        }

        return $jiemi_code["code"];
    }
}
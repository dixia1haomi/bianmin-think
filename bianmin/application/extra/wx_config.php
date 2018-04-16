<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/15 0015
 * Time: 下午 6:00
 */

return [

    // 小程序微信获取openid配置信息 https://zhaopin.qujingdaishuyanxuan.org/api/kefu/getkefu
    // 便民+
    'appid' => 'wx4b5c6d5c3c25c237',
    'secret' => 'dd486a1111c75cf91ed5beb1299d8df0',

    // 课外
//    'appid' => 'wx4db8544fbae664b1',
//    'secret' => '44089888e037bbda9f1d38a4d54a686d',
    'login_url' => 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',

    //token过期时间
    'token_expire' => 7100,

    //token->key的加密 盐
    'token_salt' => 'dixia2haomi',

    // 微信获取access_token的url地址
    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s",


    // 公众号-卡卷签名使用
//    'gzh_appid' => 'wx121b23c02de6a537',
//    'gzh_secret' => '2e9850212ad015f3b4a6b86bc208ca3a',
//    'gzh_access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s",

    // 卡卷
//    'ticket_url' => "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=wx_card",

    // 卡卷ID - 生成sha1加密时要用(这个是测试用的)
    // 'card_id' => 'pQ7pM1gccLWeQjOBkDN60PxClnFQ'

];
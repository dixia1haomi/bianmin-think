<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/15 0015
 * Time: 下午 6:00
 */

return [

    // ----------------------------------- 基础配置项 -----------------------------------
    // 小程序微信获取openid配置信息 https://zhaopin.qujingdaishuyanxuan.org/api/kefu/getkefu
    // 便民+
    'appid' => 'wx4b5c6d5c3c25c237',
    'secret' => 'dd486a1111c75cf91ed5beb1299d8df0',

    // login
    'login_url' => 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code',

    //token过期时间
    'token_expire' => 7100,

    //token->key的加密 盐
    'token_salt' => 'dixia2haomi',

    // 微信获取access_token的url地址
    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s",


    // ----------------------------------- 补充配置项 -----------------------------------

    // 计划任务项

    // 用户离开7天提醒回访
//    'useri_guoqi_time' => 60 * 60 * 30,

    // 便民信息超过7天提醒刷新
//    'bmxx_guoqi_shuaxin_time' => 60 * 60 * 30,

    // 便民信息超过30天删除
//    'bmxx_guoqi_delete_time' => 60 * 60 * 2,



    // 公众号-卡卷签名使用
//    'gzh_appid' => 'wx121b23c02de6a537',
//    'gzh_secret' => '2e9850212ad015f3b4a6b86bc208ca3a',
//    'gzh_access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s",

    // 卡卷
//    'ticket_url' => "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=wx_card",

    // 卡卷ID - 生成sha1加密时要用(这个是测试用的)
    // 'card_id' => 'pQ7pM1gccLWeQjOBkDN60PxClnFQ'

];
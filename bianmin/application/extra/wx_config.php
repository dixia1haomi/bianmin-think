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


    'leimu' => [

        ['id' => 0, 'name' => '招聘求职', 'value' =>
            [
                [
                    'name' => '招聘', 'value2' => [['moban' => '招聘1'], ['moban' => '招聘2']]
                ],
                [
                    'name' => '求职', 'value2' => [['moban' => '求职1'], ['moban' => '求职2']]
                ]
            ]
        ],

        ['id' => 1, 'name' => '房产交易', 'value' =>
            [
                [
                    'name' => '房屋出售', 'value2' => [['moban' => '房屋出售模板1'], ['moban' => '房屋出售模板2'], ['moban' => '房屋出售模板3'], ['moban' => '房屋出售模板4']]
                ],
                [
                    'name' => '房屋求购', 'value2' => [['moban' => '房屋求购模板1'], ['moban' => '房屋求购模板2']]
                ],
                [
                    'name' => '房屋出租', 'value2' => [['moban' => '房屋出租模板1'], ['moban' => '房屋出租模板2'], ['moban' => '房屋出租模板3'], ['moban' => '房屋出租模板4']]
                ],
                [
                    'name' => '房屋求租', 'value2' => [['moban' => '房屋求租模板1'], ['moban' => '房屋求租模板2'], ['moban' => '房屋求租模板3'], ['moban' => '房屋求租模板4']]
                ],
            ]
        ],


        ['id' => 2, 'name' => '车辆交易', 'value' =>
            [
                [
                    'name' => '车辆出售', 'value2' => [['moban' => '车辆出售模板1'], ['moban' => '车辆出售模板2'], ['moban' => '车辆出售模板3'], ['moban' => '车辆出售模板4']]
                ],
                [
                    'name' => '车辆求购', 'value2' => [['moban' => '车辆求购模板1'], ['moban' => '车辆求购模板2']]
                ],
                [
                    'name' => '车辆出租', 'value2' => [['moban' => '车辆出租模板1'], ['moban' => '车辆出租模板2'], ['moban' => '车辆出租模板3'], ['moban' => '车辆出租模板4']]
                ],
                [
                    'name' => '车辆求租', 'value2' => [['moban' => '车辆求租模板1'], ['moban' => '车辆求租模板2'], ['moban' => '车辆求租模板3'], ['moban' => '车辆求租模板4']]
                ],
            ]
        ],

        ['id' => 3, 'name' => '物品交易', 'value' =>
            [
                [
                    'name' => '物品出售', 'value2' => [['moban' => '物品出售1'], ['moban' => '物品出售2']]
                ],
                [
                    'name' => '物品求购', 'value2' => [['moban' => '物品求购1'], ['moban' => '物品求购2']]
                ]
            ]
        ],

        ['id' => 4, 'name' => '生意转让', 'value' =>
            [
                [
                    'name' => '生意转让', 'value2' => [['moban' => '生意转让1'], ['moban' => '生意转让2']]
                ],
                [
                    'name' => '店铺转让', 'value2' => [['moban' => '店铺转让1'], ['moban' => '店铺转让2']]
                ]
            ]
        ],

        ['id' => 5, 'name' => '顺风车', 'value' =>
            [
                [
                    'name' => '人找车', 'value2' => [['moban' => '人找车1'], ['moban' => '人找车2']]
                ],
                [
                    'name' => '车找人', 'value2' => [['moban' => '车找人1'], ['moban' => '车找人2']]
                ]
            ]
        ],

        ['id' => 6, 'name' => '寻人寻物', 'value' =>
            [
                [
                    'name' => '寻人', 'value2' => [['moban' => '寻人1'], ['moban' => '寻人2']]
                ],
                [
                    'name' => '寻物', 'value2' => [['moban' => '寻物1'], ['moban' => '寻物2']]
                ],
                [
                    'name' => '打听', 'value2' => [['moban' => '打听1'], ['moban' => '打听2']]
                ]
            ]
        ],

        ['id' => 7, 'name' => '打折优惠', 'value' =>
            [
                [
                    'name' => '打折优惠', 'value2' => [['moban' => '打折优惠1'], ['moban' => '打折优惠2']]
                ]
            ]
        ],
    ],
//        [
//      {
//          id: 0,
//        name: '房产交易',
//        value: [
//          {
//              name: '房屋出售',
//            value2: [{
//              moban: '房屋出售模板1'
//            }, {
//              moban: '房屋出售模板2'
//            }, {
//              moban: '房屋出售模板3'
//            }, {
//              moban: '房屋出售模板4'
//            }]
//          },
//          {
//              name: '房屋求购',
//            value2: [{
//              moban: '房屋求购模板1'
//            }]
//          }
//        ]
//      },
//      {
//          id: 1,
//        name: '招聘求职',
//        value: [
//          {
//              name: '招聘',
//            value2: [{
//              moban: '招聘模板1'
//            }, {
//              moban: '招聘模板2'
//            }, {
//              moban: '招聘模板3'
//            }]
//          },
//          {
//              name: '求职',
//            value2: [{
//              moban: '求职模板1'
//            }, {
//              moban: '求职模板2'
//            }]
//          }
//        ]
//      }
//    ];

    // 公众号-卡卷签名使用
//    'gzh_appid' => 'wx121b23c02de6a537',
//    'gzh_secret' => '2e9850212ad015f3b4a6b86bc208ca3a',
//    'gzh_access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s",

    // 卡卷
//    'ticket_url' => "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=wx_card",

    // 卡卷ID - 生成sha1加密时要用(这个是测试用的)
    // 'card_id' => 'pQ7pM1gccLWeQjOBkDN60PxClnFQ'

];
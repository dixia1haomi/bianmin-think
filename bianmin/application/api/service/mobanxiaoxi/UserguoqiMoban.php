<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/8 0008
 * Time: 上午 2:39
 */

namespace app\api\service\mobanxiaoxi;


class UserguoqiMoban extends MobanXiaoxi
{
    // 服务进度通知
    // 服务状态 {{keyword1.DATA}}
    // 温馨提示 {{keyword2.DATA}}

    const MOBANXIAOXI_ID = "lRaOsAwfC_sKNd9_zC6uJIVr_7v3_09Y6cIpvchGudQ";

    // 发给谁（openid）                  便民信息user_id查询获取
    // form_id                          便民信息直接获取
    // 进入小程序的路径（信息ID）          便民信息id
    // 留言内容                          留言表数据
    // 留言人                            留言表数据

    public function sendUserGuoQiMessage($value)
    {
        // 需要openid和form_id

        // 获得openid
        $openid = $value['openid'];
        $form_id = $value['form_id'];
//        $nick_name = $value['nick_name'];

        $this->tplID = self::MOBANXIAOXI_ID;                            // 模板消息ID
        $this->formID = $form_id;                                       // formID
        $this->page = 'pages/index/index1';                            // 进入路径
        $this->createMessageData();                           // 创建模板消息的data数组
        $backmsg = parent::sendMessage($openid);                         // 条送发送模板消息携带openid

        return $backmsg;
    }

    private function createMessageData()
    {
        // 服务进度通知
        // 服务状态 {{keyword1.DATA}}
        // 温馨提示 {{keyword2.DATA}}

        $data = [
            'keyword1' => [
                'value' => '访问邀请',
            ],

            'keyword2' => [
                'value' => '亲，袋鼠同城邀请您访问、或发布信息。',
                'color' => '#27408B'
            ]
        ];
        $this->data = $data;
    }

}
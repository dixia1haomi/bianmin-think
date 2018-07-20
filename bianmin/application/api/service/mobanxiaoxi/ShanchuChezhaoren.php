<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/29 0029
 * Time: 下午 12:57
 */

namespace app\api\service\mobanxiaoxi;


use app\exception\QueryDbException;
use think\Log;
use app\api\model\User as userModel;

class ShanchuChezhaoren extends MobanXiaoxi
{
    // 删除车找人信息通知

    // 服务进度通知
    // 服务状态 {{keyword1.DATA}}
    // 温馨提示 {{keyword2.DATA}}

    const MOBANXIAOXI_ID = "lRaOsAwfC_sKNd9_zC6uJIVr_7v3_09Y6cIpvchGudQ";

    // 发给谁（openid）                  便民信息user_id查询获取
    // form_id                          便民信息直接获取

    public function sendShanchuChezhaorenMessage($value)
    {

        // 获得openid
        $openid = $this->getOpenID($value['user_id']);
        $form_id = $value['form_id'];

        $this->tplID = self::MOBANXIAOXI_ID;                            // 模板消息ID
        $this->formID = $form_id;                                       // formID
        $this->page = 'pages/index/index1';                            // 进入路径
        $this->createMessageData();                           // 创建模板消息的data数组
        $backmsg = parent::sendMessage($openid);                         // 条送发送模板消息携带openid

        // 发送失败记录日志
        if ($backmsg['errcode'] != 0) {
            // 发送失败
            Log::init([
                'type' => 'File',
                'path' => LOG_PATH_CTONTAB_SHANCHUCHEZHAOREN,   // 车找人日志文件路径
            ]);
            Log::record('删除车找人信息通知失败，' . $backmsg['errmsg'], 'info');
        }
    }

    private function createMessageData()
    {
        // 服务进度通知
        // 服务状态 {{keyword1.DATA}}
        // 温馨提示 {{keyword2.DATA}}

        $data = [
            'keyword1' => [
                'value' => '信息删除通知',
            ],

            'keyword2' => [
                'value' => '亲，您发布的车找人信息已过出发时间，系统已为你自动删除，感谢您使用袋鼠同城。',
                'color' => '#27408B'
            ]
        ];
        $this->data = $data;
    }

    // 获得openid
    private function getOpenID($uid)
    {
        // 先获得user表数据
        $userModel = new userModel();
        $user = $userModel->where('id',$uid)->find();
        if ($user === false) {
            throw new QueryDbException(['msg'=>'模板消息删除车找人getOpenID出错']);
        }
        return $user->openid;
    }
}
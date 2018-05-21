<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/21 0021
 * Time: 上午 5:41
 */

namespace app\api\service\mobanxiaoxi;

use app\api\model\User as userModel;
use app\exception\QueryDbException;
class ShangjiaShuaXinMoban extends MobanXiaoxi
{
    // 信息处理提醒
    // 温馨提示 {{keyword1.DATA}}
    // 信息详情 {{keyword2.DATA}}

    const MOBANXIAOXI_ID = "4YFl2gLqZ6IE-JBiosPxzFM6cz3jgq6CPc8XC5ukeWI";


    public function sendShangjiaShuaxinMessage($value)
    {

        $form_id = $value['form_id'];

        $this->tplID = self::MOBANXIAOXI_ID;                            // 模板消息ID
        $this->formID = $form_id;                                       // formID
        $this->page = 'pages/index/index1';                             // 进入路径
        $this->createMessageData();                                     // 创建模板消息的data数组
        $backmsg = parent::sendMessage($this->getOpenID($value['user_id']));                        // 条送发送模板消息携带openid

        return $backmsg;
    }

    private function createMessageData()
    {
        // 信息处理提醒
        // 温馨提示 {{keyword1.DATA}}
        // 信息详情 {{keyword2.DATA}}

        $data = [
            // 温馨提示
            'keyword1' => [
                'value' => '亲、您获得了刷新店铺的权限。'
            ],

            // 信息详情
            'keyword2' => [
                'value' => '刷新店铺可以让您的店铺重新排序、展示在前排、长时间不刷新会导致店铺排序靠后，点击卡片、快去刷新吧。',
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
            throw new QueryDbException(['msg'=>'getOpenID']);
        }
        return $user->openid;
    }

}
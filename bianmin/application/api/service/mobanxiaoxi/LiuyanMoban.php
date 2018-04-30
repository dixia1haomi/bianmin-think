<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/29 0029
 * Time: 下午 1:33
 */

namespace app\api\service\mobanxiaoxi;


use app\api\model\User as userModel;

class LiuyanMoban extends MobanXiaoxi
{


    const MOBANXIAOXI_ID = "2d3BnglG-SWZPEmWz4otzLUH2DPiQ1yu5Aa3eGhCPwo";

    // 发给谁（openid）                  便民信息user_id查询获取
    // form_id                          便民信息直接获取
    // 进入小程序的路径（信息ID）          便民信息id
    // 留言内容                          留言表数据
    // 留言人                            留言表数据

    public function sendLiuyanMessage($lyxx, $bmxx)
    {
        // 检查from_id是否还在有效期内
        $user = $this->checkFormID($bmxx->user_id);

        if ($user !== false) {
            $this->tplID = self::MOBANXIAOXI_ID;                                    // 模板消息ID
            $this->formID = $user->form_id;                                         // 信息表formID
            $this->page = '/pages/index/index1';                                    // 进入路径
            $this->createMessageData($lyxx);                                             // 创建模板消息的data数组
            $msgres = parent::sendMessage($user->openid);       // 条送发送模板消息携带openid

            if ($msgres['errcode'] == 0) {
                // 模板消息发送成功，清空user下的form_id
                $this->deleteFormID($user->id);
                $msg = '模板消息发送成功';
            } else {
                $msg = '模板消息发送失败,' . $msgres['errmsg'];
            }
        } else {
            $msg = '没有formID或者已过期';
        }
        return $msg;
    }

    private function createMessageData($lyxx)
    {
        // 当前时间
        $dt = new \DateTime();
        // 留言人昵称
        $name = $this->getLiuyanUserName($lyxx['user_id']);

        $data = [
            // 留言内容
            'keyword1' => [
                'value' => $lyxx['neirong'],
            ],
            // 留言人
            'keyword2' => [
                'value' => $name,
                'color' => '#27408B'
            ],
            // 留言时间
            'keyword3' => [
                'value' => $dt->format("Y-m-d H:i")
            ]
        ];
        $this->data = $data;
    }

    // 根据id查询user表并检查form_id是否有效
    private function checkFormID($bmxxuserid)
    {
        // 先获得user表数据
        $userModel = new userModel();
        $user = $userModel->find($bmxxuserid);
        if ($user === false) {
            //
        }

        // 检查user表是否有form_id
        if (empty($user['form_id'])) {
            // form_id为空
            return false;
        } else {
            // 检查form_id是否有效
            $dt = time();           // 当前时间
            $update_time = strtotime($user['update_time']);
            $ShiJianCha = $dt - $update_time;

            // 大于7天过期
            if ($ShiJianCha > 604800) {
                return false;
            } else {
                return $user;
            }
        }
    }


    private function getLiuyanUserName($lyuid)
    {
        $userModel = new userModel();
        $lyuser = $userModel->find($lyuid);
        if ($lyuser === false) {
            //
        }
        return $lyuser->nick_name;
    }

    // 清空user下的form_id
    private function deleteFormID($uid)
    {
        $userModel = new userModel();
        $user = $userModel->where('id',$uid)->setField('form_id','');
        if ($user === false) {
            //
        }
        return true;
    }
}
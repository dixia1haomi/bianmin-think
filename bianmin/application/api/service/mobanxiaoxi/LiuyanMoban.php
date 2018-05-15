<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/29 0029
 * Time: 下午 1:33
 */

namespace app\api\service\mobanxiaoxi;


use app\api\model\User as userModel;
use app\api\model\Bianminlist as bianminlistModel;
use app\exception\QueryDbException;
use think\Log;

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
        // 检查便民信息里的from_id是否还在有效期内
         $checkformId = $this->checkFormID($bmxx);

        if($checkformId === true){
            // 获得openid
            $openid = $this->getOpenID($bmxx->user_id);

            $this->tplID = self::MOBANXIAOXI_ID;                                    // 模板消息ID
            $this->formID = $bmxx->form_id;                                         // 信息表formID
            $this->page = "pages/bmxx/myfabu";                                      // 进入路径
            $this->createMessageData($lyxx);                                             // 创建模板消息的data数组
            $msgres = parent::sendMessage($openid);       // 条送发送模板消息携带openid

            if ($msgres['errcode'] == 0) {
                $this->deleteFormID($bmxx->id);
                $msg = '模板消息发送成功';
            } else {
                $msg = '模板消息发送失败,' . $msgres['errmsg'];
                Log::init([
                    'type' => 'File',
                    'path' => LOG_PATH_MOBANXIAOXI_EXCEPTION,   // 自定义的日志文件路径
                ]);
                Log::record('留言时'.$msg, 'MoBanXiaoXiException');
            }
        }else{
            $msg = $checkformId;
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

    // 获得openid
    private function getOpenID($huifu_user_id)
    {
        // 先获得user表数据
        $userModel = new userModel();
        $user = $userModel->find($huifu_user_id);
        if ($user === false) {
            throw new QueryDbException(['msg'=>'getOpenID-liuyanmoban']);
        }
        return $user->openid;
    }

    // 根据id查询user表并检查form_id是否有效
    private function checkFormID($bmxx)
    {
        // 检查是否有form_id
        if (empty($bmxx->form_id)) {
            // form_id为空
            return 'form_id为空';
        } else {
            // 检查form_id是否有效
            $dt = time();           // 当前时间
            $update_time = strtotime($bmxx['update_time']);
            $ShiJianCha = $dt - $update_time;

            // 大于7天过期
            if ($ShiJianCha > 604800) {
                return 'form_id已过期';
            } else {
                return true;
            }
        }
    }


    private function getLiuyanUserName($lyuid)
    {
        $userModel = new userModel();
        $lyuser = $userModel->find($lyuid);
        if ($lyuser === false) {
            throw new QueryDbException(['msg'=>'getLiuyanUserName-liuyanmoban']);
        }
        return $lyuser->nick_name;
    }

    // 清空便民信息下的form_id
    private function deleteFormID($id)
    {
        $bianminModel = new bianminlistModel();
        $bianmin = $bianminModel->where('id', $id)->setField('form_id', '');
        if ($bianmin === false) {
            throw new QueryDbException(['msg'=>'deleteFormID-liuyanmoban']);
        }
        return true;
    }
}
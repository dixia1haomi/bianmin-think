<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/29 0029
 * Time: 下午 10:44
 */

namespace app\api\service\mobanxiaoxi;

use app\api\model\Liuyan;
use app\api\model\User as userModel;
use app\api\model\Liuyan as liuyanModel;
use app\exception\QueryDbException;
use think\Log;

class HuifuMoban extends MobanXiaoxi
{

    const MOBANXIAOXI_ID = "FZ8y0U2gi3sfw7mPMfy-5RqY8TkMn52vW3-n_oeiUVA";

    // 发给谁（openid）                  便民信息user_id查询获取
    // form_id                          便民信息直接获取
    // 进入小程序的路径（信息ID）          便民信息id
    // 留言内容                          留言表数据
    // 留言人                            留言表数据

    public function sendHuifuMessage($hfxx)
    {
        // 查询留言表，获得留言表中的便民信息ID、内容、form_id
        $liuyan = $this->findLiuyan($hfxx['liuyan_id']);
        // 检查from_id是否还在有效期内
        $checkformId = $this->checkFormID($liuyan);

        if($checkformId === true){
            // 获得openid
            $openid = $this->getOpenID($hfxx->huifu_user_id);

            $this->tplID = self::MOBANXIAOXI_ID;                                    // 模板消息ID
            $this->formID = $liuyan->form_id;                                         // 信息表formID
            $this->page = 'pages/bmxx/detail?id=' . $liuyan->bmxx_id;   // 进入路径
            $this->createMessageData($hfxx, $liuyan->neirong);                                             // 创建模板消息的data数组
            $msgres = parent::sendMessage($openid);       // 条送发送模板消息携带openid

            if ($msgres['errcode'] == 0) {
                $msg = '模板消息发送成功';
            } else {
                $msg = '模板消息发送失败,' . $msgres['errmsg'];
                Log::init([
                    'type' => 'File',
                    'path' => LOG_PATH_MOBANXIAOXI_EXCEPTION,   // 自定义的日志文件路径
                ]);
                Log::record('回复时'.$msg, 'MoBanXiaoXiException');
            }
        }else{
            $msg = $checkformId;
        }

        return $msg;
    }

    private function createMessageData($hfxx, $liuYanNeiRong)
    {
        // 当前时间
        $dt = new \DateTime();
        // 留言人昵称
        $name = $this->getHuifuUserName($hfxx['user_id']);

        $data = [
            // 留言内容
            'keyword1' => [
                'value' => $liuYanNeiRong,
            ],
            // 回复者
            'keyword2' => [
                'value' => $name,
                'color' => '#27408B'
            ],
            // 回复内容
            'keyword3' => [
                'value' => $hfxx['neirong'],
                'color' => '#27408B'
            ],
            // 回复时间
            'keyword4' => [
                'value' => $dt->format("Y-m-d H:i")
            ]
        ];
        $this->data = $data;
    }

    // 留言内容
    private function findLiuyan($liuyan_id)
    {
        $liuyanModel = new liuyanModel();
        $liuyan = $liuyanModel->where('id', $liuyan_id)->find();
        if ($liuyan === false) {
            throw new QueryDbException(['msg'=>'findLiuyan-huifumoban']);
        }
        return $liuyan;
    }

    // 根据id查询user表并检查form_id是否有效
    private function checkFormID($liuyan)
    {
        // 检查是否有form_id
        if (empty($liuyan->form_id)) {
            // form_id为空
            return 'form_id为空';
        } else {
            // 检查form_id是否有效
            $dt = time();           // 当前时间
            $update_time = strtotime($liuyan['update_time']);
            $ShiJianCha = $dt - $update_time;

            // 大于7天过期
            if ($ShiJianCha > 604800) {
                return 'form_id已过期';
            } else {
                return true;
            }
        }
    }

    // 获得openid
    private function getOpenID($huifu_user_id)
    {
        // 先获得user表数据
        $userModel = new userModel();
        $user = $userModel->find($huifu_user_id);
        if ($user === false) {
            throw new QueryDbException(['msg'=>'getOpenID-huifumoban']);
        }
        return $user->openid;
    }


    private function getHuifuUserName($fhuid)
    {
        $userModel = new userModel();
        $fhuser = $userModel->find($fhuid);
        if ($fhuser === false) {
            throw new QueryDbException(['msg'=>'getHuifuUserName']);
        }
        return $fhuser->nick_name;
    }


}
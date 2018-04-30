<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/29 0029
 * Time: 下午 10:44
 */

namespace app\api\service\mobanxiaoxi;

use app\api\model\User as userModel;
use app\api\model\Liuyan as liuyanModel;
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
        // 检查from_id是否还在有效期内
        $user = $this->checkFormID($hfxx->huifu_user_id);
        if($user !== false){
            $this->tplID = self::MOBANXIAOXI_ID;                                    // 模板消息ID
            $this->formID = $user->form_id;                                         // 信息表formID
            $this->page = '/pages/index/index1';                                    // 进入路径
            $this->createMessageData($hfxx);                                             // 创建模板消息的data数组
//        $this->emphasisKeyWord = 'keyword2.DATA';                             // 放大字体
            $msg = parent::sendMessage($user->openid);       // 条送发送模板消息携带openid
        }else{
            $msg = 'formID过期';
        }
        return $msg;
    }

    private function createMessageData($hfxx)
    {
        // 当前时间
        $dt = new \DateTime();
        // 留言内容
        $liuyan_neirong = $this->liuyanNeirong($hfxx['liuyan_id']);
        // 留言人昵称
        $name = $this->getHuifuUserName($hfxx['user_id']);

        $data = [
            // 留言内容
            'keyword1' => [
                'value' => $liuyan_neirong,
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
    private function liuyanNeirong($liuyan_id){
        $liuyanModel = new liuyanModel();
        $liuyan = $liuyanModel->where('id',$liuyan_id)->find();
        if($liuyan === false){
            //
        }
        return $liuyan->neirong;
    }

    // 根据id查询user表并检查form_id是否有效
    private function checkFormID($uid)
    {
        $userModel = new userModel();
        $user = $userModel->find($uid);
        if ($user === false) {
            //
        }

        // 检查form_id是否有效
        // 当前时间
        $dt = time();
        $update_time = strtotime($user['update_time']);
        $ShiJianCha = $dt - $update_time;

        // 7天
        if ($ShiJianCha < 604800) {
            return $user;
        } else {
            return false;
        }
    }


    private function getHuifuUserName($fhuid)
    {
        $userModel = new userModel();
        $fhuser = $userModel->find($fhuid);
        if ($fhuser === false) {
            //
        }
        return $fhuser->nick_name;
    }
}
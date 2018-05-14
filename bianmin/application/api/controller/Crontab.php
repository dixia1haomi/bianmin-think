<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/7 0007
 * Time: 下午 8:01
 */

namespace app\api\controller;

use app\api\model\Bianminlist as bianminlistModel;
use app\api\model\User as userModel;
use app\api\model\Liuyan as liuyanModel;
use app\api\model\UserGuoqiJilu as userguoqijiluModel;
use app\api\model\BmxxShuaxinJilu as bmxxshuaxinjiluModel;

use app\api\service\mobanxiaoxi\BianminShuaXinMoban;
use app\api\service\mobanxiaoxi\UserguoqiMoban;
use app\exception\QueryDbException;
use app\exception\Success;
use think\Log;

class Crontab
{
    // * 服务器计划任务：1. 更改便民信息表顶置状态为0 = 当前时间 > 顶置时间
    //                 2. 删除便民信息数据 = update_time超过30天 （有顶置状态的不删除）
    //                 3. 检查formId过期

    // -------------------------------------------------- 更改便民信息表顶置状态 --------------------------------------------------

    // 1. 更改便民信息表顶置状态为0 = 当前时间 > 顶置时间
    public function crontab_Check_BianMin_Dingzhi_state()
    {
        // 查询所有已顶置的信息判断dingzhi_time是否到期，到期的更改顶置状态并清空dingzhi_time
        $bianminlistModel = new bianminlistModel();
        $dingzhi = $bianminlistModel->where('dingzhi_state', 1)->select();
        if ($dingzhi === false) {
            throw new QueryDbException(['msg'=>'crontab更改便民信息表顶置状态crontab_Check_BianMin_Dingzhi_state']);
        }

        // 如果有数据
        if (count($dingzhi) > 0) {
            // 遍历顶置信息
            foreach ($dingzhi as $key => $value) {
                // 遍历检查
                $this->check_Dingzhi_time($value);
            }
        }
    }

    private function check_Dingzhi_time($value)
    {
        // 开启日志记录
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH_CTONTAB_DINGZHI_STATE,   // 自定义的日志文件路径
        ]);

        // 当前时间
        $time = time();
        // 如果：当前时间 > 顶置时间 取消这条信息的顶置状态
        if ($time > $value['dingzhi_time']) {
            $bianminlistModel = new bianminlistModel();
            $over = $bianminlistModel->where('id', $value['id'])->setField(['dingzhi_state' => 0, 'dingzhi_time' => 0]);
            if ($over === false) {
                throw new QueryDbException(['msg'=>'crontab更改便民信息表顶置状态check_Dingzhi_time']);
            }
            Log::record('更改了ID为' . $value['id'] . '信息顶置状态', 'info');
        }
    }


    // -------------------------------------------------- 超过30天删除便民信息 --------------------------------------------------

    // 2. 删除便民信息数据 = update_time超过30天 （有顶置状态的不删除）
    public function crontab_Delete_BianMin()
    {
        // 查询没有顶置并且update_time在一个月之后的便民信息
        $bianminlistModel = new bianminlistModel();
        // not between 不在 当前时间至30天以前的这个范围 查询 （只查询30天以外的数据）
        $time = time();
        $time_30 = $time - (60 * 60 * 24 * 30);
        $nodingzhi = $bianminlistModel->where('dingzhi_state', '<>', 1)->whereTime('update_time', 'not between', [$time_30, $time])->select();
        if ($nodingzhi === false) {
            throw new QueryDbException(['msg'=>'crontab超过30天删除便民信息crontab_Delete_BianMin']);
        }

        // 如果有数据
        if (count($nodingzhi) > 0) {
            // 遍历顶置信息
            foreach ($nodingzhi as $key => $value) {
                // 遍历检查
                $this->check_Dingzhi_updatetime($value);
            }
        }
    }

    private function check_Dingzhi_updatetime($value)
    {
        // 开启日志记录
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH_CTONTAB_DELETE_BIANMIN,   // 自定义的日志文件路径
        ]);

        $XinXi = new Xinxi();
        $XinXi->deleteMyfabu($value['id']);

        Log::record('删除了ID为' . $value['id'] . '的信息', 'info');
    }

    // ----------------------------------------------- 用户7天回访提醒 -----------------------------------------------

    // 3. 检查formId过期,有form_id的表3张：user - bianminlist - liuyan | 3个计划任务分别检查3张表

    public function crontab_CheckUserFormId()
    {
        // 查询有formid的用户
        $userModel = new userModel();
        // not between 不在 当前时间至6天以前的这个范围 查询 （只查询6天以外的数据）
        $time = time();
        $time_6 = $time - (60 * 60 * 24);   // 6天以外
        $user = $userModel->where('form_id', '<>', '')->whereTime('update_time', 'not between', [$time_6, $time])->select();
        if ($user === false) {
            throw new QueryDbException(['msg'=>'crontab用户7天回访提醒crontab_CheckUserFormId']);
        }


        if (count($user) > 0) {
            foreach ($user as $key => $value) {
                $mobanxiaoxi = new UserguoqiMoban();
                // 发送消息
                $backMsg = $mobanxiaoxi->sendUserGuoQiMessage($value);     // $backMsg返回成功或失败的字符串
                // 记录执行情况并删除formid
                $this->userGuoQiJiLu($backMsg, $value);
            }
        }
    }

    private function userGuoQiJiLu($backMsg, $value)
    {
        // 开启日志记录
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH_CTONTAB_USER_FORMID,   // 自定义的日志文件路径
        ]);

        $userguoqijiluModel = new userguoqijiluModel();

        // 模板消息成功失败判断
        if ($backMsg['errcode'] == 0) {
            $msg = '模板消息发送成功';
            // 记录到数据库
            $user = $userguoqijiluModel->create(['user_id' => $value['id'], 'state' => 1]);
            if ($user === false) {
                throw new QueryDbException(['msg'=>'crontab用户7天回访提醒userGuoQiJiLu-1']);
            }
        } else {
            $user = $userguoqijiluModel->create(['user_id' => $value['id'], 'state' => 0]);
            if ($user === false) {
                throw new QueryDbException(['msg'=>'crontab用户7天回访提醒userGuoQiJiLu-0']);
            }
            $msg = '模板消息发送失败,' . $backMsg['errmsg'];
        }

        //
        Log::record($value['nick_name'] . '---' . $msg . '---' . '用户ID' . '---' . $value['id'], 'info');

        // 清空user表用户的form_id
        $this->deleteUserFormID($value['id']);
    }

    // 清空user表用户的form_id
    private function deleteUserFormID($id)
    {
        $userModel = new userModel();
        $user = $userModel->where('id', $id)->setField('form_id', '');
        if ($user === false) {
            throw new QueryDbException(['msg'=>'crontab用户7天回访提醒清空user表用户的form_id']);
        }
        return;
    }


    // ------------------------------------- 便民信息刷新提醒 -------------------------------------

    // 便民信息消息推送场景：1.form_id到期提醒刷新推送 |  2.留言推送（留言接口处已实现）
    public function crontab_CheckBianMinListFormId()
    {
        // form_id到期提醒刷新推送，查询有formid的便民信息
        $bianminlistModel = new bianminlistModel();

        // 查询formid不为空并且是7天以内更新的
        $time = time();
        $time_6 = $time - (60 * 60 * 24);    // 6天前以外
        $bianmin = $bianminlistModel->where('form_id', '<>', '')->whereTime('update_time', 'not between', [$time_6, $time])->select();
        if ($bianmin === false) {
            throw new QueryDbException(['msg'=>'crontab便民信息刷新提醒crontab_CheckBianMinListFormId']);
        }

        if (count($bianmin) > 0) {
            foreach ($bianmin as $key => $value) {
                $mobanxiaoxi = new BianminShuaXinMoban();
                // 发送消息
                $backMsg = $mobanxiaoxi->sendBianminXinxiShuaxinMessage($value);     // $backMsg返回成功或失败的字符串
                // 记录执行情况并删除formid
                $this->bianminShuaxinJiLu($backMsg, $value);
            }
        }

    }

    private function bianminShuaxinJiLu($backMsg, $value)
    {
        // 开启日志记录
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH_CTONTAB_BIANMINSHUAXIN_FORMID,   // 自定义的日志文件路径
        ]);

        $bmxxshuaxinjiluModel = new bmxxshuaxinjiluModel();

        // 模板消息成功失败判断
        if ($backMsg['errcode'] == 0) {
            $msg = '模板消息发送成功';

            // 记录到数据库
            $bianmin = $bmxxshuaxinjiluModel->create(['user_id' => $value['user_id'], 'state' => 1]);
            if ($bianmin === false) {
                throw new QueryDbException(['msg'=>'crontab便民信息刷新提醒bianminShuaxinJiLu-1']);
            }
        } else {
            $msg = '模板消息发送失败,' . $backMsg['errmsg'];

            $bianmin = $bmxxshuaxinjiluModel->create(['user_id' => $value['user_id'], 'state' => 0]);
            if ($bianmin === false) {
                throw new QueryDbException(['msg'=>'crontab便民信息刷新提醒bianminShuaxinJiLu-0']);
            }
        }

        //
        Log::record('信息ID' . '---' . $value['user_id'] . '---' . $msg, 'info');

        // 清空便民信息表的form_id
        $this->deleteBianminFormID($value['id']);
    }

    // 清空便民信息表的form_id
    private function deleteBianminFormID($id)
    {
        $bianminlistModel = new bianminlistModel();
        $bianmin = $bianminlistModel->where('id', $id)->setField('form_id', '');
        if ($bianmin === false) {
            throw new QueryDbException(['msg'=>'crontab便民信息刷新提醒deleteBianminFormID']);
        }
        return;
    }


    // ------------------------------------- 检查liuyan表 -------------------------------------
    public function crontab_CheckLiuYanFormId()
    {

    }

}
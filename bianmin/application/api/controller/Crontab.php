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
use app\api\model\UserGuoqiJilu as userguoqijiluModel;
use app\api\model\BmxxShuaxinJilu as bmxxshuaxinjiluModel;
use app\api\model\Shangjia as shangjiaModel;

use app\api\service\mobanxiaoxi\BianminShuaXinMoban;
use app\api\service\mobanxiaoxi\ShangjiaShuaXinMoban;
use app\api\service\mobanxiaoxi\UserguoqiMoban;
use app\exception\QueryDbException;
use app\exception\Success;
use think\Log;

class Crontab
{
    // * 服务器计划任务：1. 更改便民信息表顶置状态为0 = 当前时间 > 顶置时间
    //                 2. 删除便民信息数据 = update_time超过30天 （有顶置状态的不删除）
    //                 3. 检查formId过期


    // -------------------------------------------------- 用户 --------------------------------------------------
    // -------------------------------------------------- 用户 --------------------------------------------------
    // -------------------------------------------------- 用户 --------------------------------------------------

    // ---------------------- 6天回访提醒/活动提醒 ----------------------

    public function crontab_CheckUserFormId()
    {
        // 查询有formid的用户
        $userModel = new userModel();
        // not between 不在 当前时间至6天以前的这个范围 查询 （只查询6天以外的数据）

        $time_6 = time() - (60 * 60 * 24 * 6);   // 6天以前
        $user = $userModel->where('form_id', '<>', '')->whereTime('update_time', '<', $time_6)->limit(3)->select();
        if ($user === false) {
            throw new QueryDbException(['msg' => 'crontab用户7天回访提醒crontab_CheckUserFormId']);
        }

        if (count($user) > 0) {
            foreach ($user as $key => $value) {
                $this->sendUserMoban($value);
            }
        }
    }

    private function sendUserMoban($value)
    {
        // 发送模板消息
        $mobanxiaoxi = new UserguoqiMoban();
        $backMsg = $mobanxiaoxi->sendUserGuoQiMessage($value);
        // 记录
        $userModel = new userModel();

        if ($backMsg['errcode'] == 0) {
            // 发送成功
            $user = $userModel->where('id', $value['id'])->update(['form_state' => 1, 'form_id' => '']);
            if ($user === false) {
                throw new QueryDbException(['msg' => 'crontab用户7天回访提醒发送成功记录数据库失败sendUserMoban']);
            }
        } else {
            // 发送失败记录日志
            Log::init([
                'type' => 'File',
                'path' => LOG_PATH_CTONTAB_USER_FORMID,   // 自定义的日志文件路径
            ]);
            Log::record('用户回访提醒发送失败，用户ID---' . $value['id'] . '错误原因---' . $backMsg['errmsg'], 'info');
            // 记录数据库
            $user = $userModel->where('id', $value['id'])->update(['form_state' => -1, 'form_id' => '']);
            if ($user === false) {
                throw new QueryDbException(['msg' => 'crontab用户7天回访提醒发送失败记录数据库失败sendUserMoban']);
            }
        }
    }




    // -------------------------------------------------- 信息 --------------------------------------------------
    // -------------------------------------------------- 信息 --------------------------------------------------
    // -------------------------------------------------- 信息 --------------------------------------------------


    // ---------------------- 检查顶置状态 ----------------------

    public function crontab_Check_BianMin_Dingzhi_state()
    {
        // 查询所有已顶置的信息判断dingzhi_time是否到期，到期的更改顶置状态并清空dingzhi_time
        $bianminlistModel = new bianminlistModel();
        $dingzhi = $bianminlistModel->where('dingzhi_state', 1)->select();
        if ($dingzhi === false) {
            throw new QueryDbException(['msg' => 'crontab更改便民信息表顶置状态crontab_Check_BianMin_Dingzhi_state']);
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
                throw new QueryDbException(['msg' => 'crontab更改便民信息表顶置状态check_Dingzhi_time']);
            }
            Log::record('更改了ID为' . $value['id'] . '信息顶置状态', 'info');
        }
    }


    // ---------------------- 超过30天删除信息(每小时执行) ----------------------

    public function crontab_Delete_BianMin()
    {
        // 删除便民信息数据 = update_time超过30天 （有顶置状态的不删除）
        $bianminlistModel = new bianminlistModel();
        // 30天以前的一个时间
        $time_30 = time() - (60 * 60 * 24 * 30);
        $nodingzhi = $bianminlistModel->where('dingzhi_state', '<>', 1)->whereTime('update_time', '<', $time_30)->limit(1)->select();
        if ($nodingzhi === false) {
            throw new QueryDbException(['msg' => 'crontab超过30天删除便民信息crontab_Delete_BianMin']);
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


    //  ---------------------- 3天后提醒信息刷新(每10分钟执行) ----------------------

    public function crontab_CheckBianMinListFormId()
    {
        // form_id到期提醒刷新推送，查询有formid的便民信息
        $bianminlistModel = new bianminlistModel();

        $time3 = time() - (60 * 60 * 24 * 3);    // 3天前以外
        $bianmin = $bianminlistModel->where('form_id', '<>', '')->whereTime('update_time', '<', $time3)->limit(3)->select();
        if ($bianmin === false) {
            throw new QueryDbException(['msg' => 'crontab便民信息刷新提醒查询失败crontab_CheckBianMinListFormId']);
        }

        if (count($bianmin) > 0) {
            foreach ($bianmin as $key => $value) {
                $this->sendXinXiShuaxin($value);
            }
        }

    }

    private function sendXinXiShuaxin($value)
    {
        // 发送模板消息
        $mobanxiaoxi = new BianminShuaXinMoban();
        $backMsg = $mobanxiaoxi->sendBianminXinxiShuaxinMessage($value);
        // 记录
        $bianminlistModel = new bianminlistModel();

        if ($backMsg['errcode'] == 0) {
            // 发送成功
            $bianmin = $bianminlistModel->where('id', $value['id'])->update(['form_state' => 1, 'form_id' => '']);
            if ($bianmin === false) {
                throw new QueryDbException(['msg' => 'crontab信息刷新模板消息发送成功后写入数据库时失败sendXinXiShuaxin']);
            }
        } else {
            // 发送失败记录日志
            Log::init([
                'type' => 'File',
                'path' => LOG_PATH_CTONTAB_BIANMINSHUAXIN_FORMID,   // 自定义的日志文件路径
            ]);
            Log::record('信息刷新模板消息发送失败，信息ID---' . $value['id'] . '错误原因---' . $backMsg['errmsg'], 'info');
            // 更新信息表
            $bianmin = $bianminlistModel->where('id', $value['id'])->update(['form_state' => -1, 'form_id' => '']);
            if ($bianmin === false) {
                throw new QueryDbException(['msg' => 'crontab信息刷新模板消息发送失败后写入数据库时失败sendXinXiShuaxin']);
            }
        }
    }





    // -------------------------------------------------- 商家 --------------------------------------------------
    // -------------------------------------------------- 商家 --------------------------------------------------
    // -------------------------------------------------- 商家 --------------------------------------------------

    //  ---------------------- 3天后提醒商家刷新(每30分钟执行) ----------------------
    public function crontab_ShangJia_ShuaXin()
    {
        // 查询3天前更新的商家
//        $time3 = time() - (60 * 60 * 24 * 3);
        $time3 = time() - (60 * 1);

        $shangjiaModel = new shangjiaModel();
        $shangjia = $shangjiaModel->where('form_id', '<>', '')->whereTime('update_time', '<', $time3)->limit(3)->select();
        if ($shangjia === false) {
            //
        }

        // 如果有数据
        if (count($shangjia) > 0) {
            foreach ($shangjia as $key => $value) {
                // 发送模板消息
                $this->seedShangjiaShuaxinMoban($value);
            }
        }
    }

    private function seedShangjiaShuaxinMoban($value)
    {
        // 发送模板消息
        $mobanxiaoxi = new ShangjiaShuaXinMoban();
        $backMsg = $mobanxiaoxi->sendShangjiaShuaxinMessage($value);
        // 记录
        $shangjiaModel = new shangjiaModel();

        if ($backMsg['errcode'] == 0) {
            // 发送成功
            $res = $shangjiaModel->where('id', $value['id'])->update(['form_state' => 1, 'form_id' => '']);
            if ($res === false) {
                throw new QueryDbException(['msg' => 'crontab商家刷新模板消息发送成功后写入数据库时失败seedShangjiaShuaxinMoban']);
            }
        } else {
            // 发送失败记录日志
            Log::init([
                'type' => 'File',
                'path' => LOG_PATH_SHANGJIA_SHUAXIN,   // 自定义的日志文件路径
            ]);
            Log::record('商家刷新模板消息发送失败，商家ID---' . $value['id'] .'用户ID---'.$value['user_id']. '错误原因---' . $backMsg['errmsg'], 'info');

            // 更新商家表
            $res = $shangjiaModel->where('id', $value['id'])->update(['form_state' => -1, 'form_id' => '']);
            if ($res === false) {
                throw new QueryDbException(['msg' => 'crontab商家刷新模板消息发送失败后写入数据库时失败seedShangjiaShuaxinMoban']);
            }
        }
    }


}
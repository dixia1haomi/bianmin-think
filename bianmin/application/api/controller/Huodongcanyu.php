<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/28 0028
 * Time: 上午 9:17
 */

namespace app\api\controller;

use app\api\model\Huodongcanyu as huodongcanyuModel;
use app\api\model\Huodongzhuli as huodongzhuliModel;
use app\api\model\Huodonglingqu as huodonglingquModel;
use app\api\model\Huodong as huodongModel;
use app\api\service\BaseToken;
use app\api\service\shengchengerweima\HuoDongZhuLiErweima;
use app\exception\QueryDbException;
use app\exception\Success;

class Huodongcanyu
{

    // 根据canyuID查 -- 参与表 (用来查别人的参与数据)
    private function find_Canyu_canyuId($id)
    {
        //
        $canyuModel = new huodongcanyuModel();
        $canyu = $canyuModel->where(['id' => $id])->with(['withHuodong', 'withZhuli', 'withUser'])->find();
        if ($canyu === false) {
            throw new QueryDbException(['msg' => '查-参与表失败，find_Canyu']);
        }
        return $canyu;
    }

    // 根据huodong_id查 -- 参与表 (用来查自己的参与数据)
    private function find_Canyu_huodongId($huodong_id)
    {
        // 获取uid
        $uid = BaseToken::get_Token_Uid();
        //
        $canyuModel = new huodongcanyuModel();
        $canyu = $canyuModel->where(['huodong_id' => $huodong_id, 'user_id' => $uid])->with(['withHuodong', 'withZhuli', 'withUser'])->find();
        if ($canyu === false) {
            throw new QueryDbException(['msg' => '查-参与表失败，find_Canyu']);
        }
        return $canyu;
    }


    // 查-助力表
    private function find_Zhuli($canyu_id)
    {
        // 获取uid
        $uid = BaseToken::get_Token_Uid();
        //
        $zhuliModel = new huodongzhuliModel();
        $zhuli = $zhuliModel->where(['canyu_id' => $canyu_id, 'user_id' => $uid])->find();
        if ($zhuli === false) {
            throw new QueryDbException(['msg' => '查-助力表失败，find_Zhuli']);
        }
        return $zhuli;
    }

    // 查-领取表
    private function find_Lingqu($huodong_id)
    {
        // 获取uid
        $uid = BaseToken::get_Token_Uid();
        //
        $lingquModel = new huodonglingquModel();
        $lingqu = $lingquModel->where(['huodong_id' => $huodong_id, 'user_id' => $uid])->find();
        if ($lingqu === false) {
            throw new QueryDbException(['msg' => '查-领取表失败，find_Lingqu']);
        }
        return $lingqu;
    }



    // ------------ API ------------
    // 检查无canyuId的参与结果（查自己的参与数据）
    public function checkZiji($params)
    {
        $ziji_canyu = $this->find_Canyu_huodongId($params['huodong_id']);
        if ($ziji_canyu) {
            // 有自己参与的数据、自己已参与
            // 检查活动条件是否达到
            $tiaojian = $ziji_canyu['with_huodong']['tiaojian'];
            $zhulishu = count($ziji_canyu['with_zhuli']);
            if ($zhulishu >= $tiaojian) {
                // 已达到领取条件、（没有别人的数据只有自己的数据、已达到领取条件）
                // 检查是否已领取
                $lingqu = $this->find_Lingqu($params['huodong_id']);
                if ($lingqu) {
                    // 已领取、显示查看卡卷
                    $ziji_canyu['kajuan'] = $lingqu;
                    throw new Success(['msg' => '跳转', 'data' => $ziji_canyu]);
                } else {
                    // 未领取、显示领取
                    throw new Success(['msg' => '领取', 'data' => $ziji_canyu]);
                }
            } else {
                // 未达到领取条件、（没有别人的数据只有自己的数据、未达到领取条件、只能显示自己的数据+邀请）
                throw new Success(['msg' => '邀请', 'data' => $ziji_canyu]);
            }
        } else {
            // 无自己参与的数据、自己未参与、（没有别人的数据也没有自己的数据、只能显示参与活动）
            throw new Success(['data' => $ziji_canyu]);
        }
    }

    // ------------ API ------------
    // 携带canyuID进入、检查有canyuId的参与结果（查别人的参与数据）
    public function checkBieren($params)
    {
        // $params = { canyu_id，huodong_id }
        // 查别人的参与数据
        $bieren_canyu = $this->find_Canyu_canyuId($params['canyu_id']);
        if ($bieren_canyu) {
            // 有别人的参与数据、用户已参与邀请我为他助力
            // 检查我是否助力过
            $checkzhulifor = $this->checkzhuli_for($bieren_canyu['with_zhuli']);
            if ($checkzhulifor) {
                // 已助力过、（后续检查自己是否参与过）
                throw new Success(['msg' => '已助', 'data' => $bieren_canyu]);
            } else {
                // 未助力过、显示别人的数据+助力按钮
                throw new Success(['msg' => '未助', 'data' => $bieren_canyu]);
            }
        }
        // 无别人的参与数据、用户未参与直接分享给我
        throw new Success(['data' => $bieren_canyu]);
    }


    // 检查我是否助力过
    private function checkzhuli_for($zhuli)
    {
        if (count($zhuli) > 0) {
            $uid = BaseToken::get_Token_Uid();
            foreach ($zhuli as $key => $value) {
                if ($value['user_id'] == $uid) {
                    // 已助力过
                    return true;
                }
            }
        }
        // 未助力过
        return false;
    }


    // AIP
    // 写-参与表 { shangjia_id , huodong_id }
    public function create_Canyu($params)
    {
        // 检查重复
        $canyu = $this->find_Canyu_huodongId($params['huodong_id']);
        if (!$canyu) {
            // 写入
            $params['user_id'] = BaseToken::get_Token_Uid();
            $canyuModel = new huodongcanyuModel();
            $create = $canyuModel->create($params);
            if ($create === false) {
                throw new QueryDbException(['msg' => '写-参与表失败，create_Canyu']);
            }
            throw new Success(['data' => $create]);
        }
    }


    // API
    // 写-助力表 { canyu_id }
    public function create_Zhuli($params)
    {
        // 检查重复
        $zhuli = $this->find_Zhuli($params['canyu_id']);
        if (!$zhuli) {
            // 写入
            $params['user_id'] = BaseToken::get_Token_Uid();
            $zhuliModel = new huodongzhuliModel();
            $create = $zhuliModel->create($params);
            if ($create === false) {
                throw new QueryDbException(['msg' => '写-助力表失败，create_Zhuli']);
            }
            // 再关联用户查出来
            $res = $zhuliModel->where(['canyu_id' => $params['canyu_id'], 'user_id' => $params['user_id']])->with(['withZhuliuser'])->find();
            if ($res === false) {
                throw new QueryDbException(['msg' => '写-助力表后再关联用户查出来时失败，create_Zhuli']);
            }
            throw new Success(['data' => $res]);
        }
    }


    // 写-领取表(新增活动劵) { shangjia_id , huodong_id }
    public function create_Lingqu($params)
    {
        // 检查重复
        $lingqu = $this->find_Lingqu($params['huodong_id']);
        if (!$lingqu) {
            // 写入
            $params['user_id'] = BaseToken::get_Token_Uid();
            $lingquModel = new huodonglingquModel();
            $create = $lingquModel->create($params);
            if ($create === false) {
                throw new QueryDbException(['msg' => '写-领取表失败，create_Lingqu']);
            }
            throw new Success(['data' => $create]);
        }
    }

    // API
    // 查询活动劵详情
    public function get_Lingqu_Detail($params)
    {
        //
        $lingquModel = new huodonglingquModel();
        $lingqu = $lingquModel->where(['id' => $params['id'], 'state' => 0])->with(['withShangjia', 'withHuodong'])->find();
        if ($lingqu === false) {
            throw new QueryDbException(['msg' => '查询领取detail失败，get_Lingqu_Detail']);
        }
        throw new Success(['data' => $lingqu]);
    }


    // API
    // 查询活动劵列表
    public function get_Lingqu_List()
    {
        // 获取uid
        $uid = BaseToken::get_Token_Uid();
        $lingquModel = new huodonglingquModel();
        $lingqu = $lingquModel->where(['user_id' => $uid])->with(['withShangjia', 'withHuodong'])->select();
        if ($lingqu === false) {
            throw new QueryDbException(['msg' => '查询活动劵列表失败，get_Lingqu_List']);
        }
        throw new Success(['data' => $lingqu]);
    }


    // API
    // 核销活动劵
    public function hexiaoHuodongJuan($params)
    {
        // $params = { state , shangjia_id , huodong_id , id }
        // 对于用户自行核销、state = 0、需检查此劵是不是此用户的、uid === 劵里的user_id
        // 对于商家扫码核销、state = 1、需检查核销此劵的是不是活动创建人、uid === 此劵关联的活动的user_id
        // 获取uid
        $uid = BaseToken::get_Token_Uid();
        // 判断是自行核销还是商户核销
        if ($params['state'] === 0) {
            // 自行核销
            $lingquModel = new huodonglingquModel();
            $lingqu = $lingquModel->where('id', $params['id'])->find();
            if ($lingqu === false) {
                //
            }
            if ($lingqu) {
                // 劵存在、检查状态
                if ($lingqu['state'] == 0) {
                    // 未核销
                    if ($lingqu['user_id'] == $uid) {
                        // 是自己的、可以核销
                        $update = $lingqu->update(['state' => 1], ['id' => $params['id']]);
                        if ($update === false) {
                            //
                        }
                        // 核销成功
                        throw new Success(['msg' => '核销成功']);
                    } else {
                        // 不是自己的、出了大问题
                        throw new Success(['msg' => '尝试核销别人的劵']);
                    }
                } else {
                    // 已核销
                    throw new Success(['msg' => '此劵已核销']);
                }
            } else {
                // 不存在的劵
                throw new Success(['msg' => '此劵不存在']);
            }
        } else {
            // 商户核销、查询活动数据对比
            $huodongModel = new huodongModel();
            $huodong = $huodongModel->where(['id' => $params['huodong_id']])->find();
            if ($huodong === false) {
                //
            }
            if ($huodong) {
                if ($huodong['user_id'] == $uid) {
                    // 核销此劵的是活动创建人、查询劵数据
                    $lingquModel = new huodonglingquModel();
                    $lingqu = $lingquModel->where('id', $params['id'])->find();
                    if ($lingqu === false) {
                        //
                    }
                    if ($lingqu) {
                        // 劵存在、检查状态
                        if($lingqu['state'] == 0){
                            // 未核销、可以核销
                            $update = $lingqu->update(['state' => 1], ['id' => $params['id']]);
                            if ($update === false) {
                                //
                            }
                            // 核销成功
                            throw new Success(['msg' => '核销成功']);
                        }else{
                            // 已核销
                            throw new Success(['msg' => '此劵已核销']);
                        }
                    } else {
                        // 不存在的劵
                        throw new Success(['msg' => '此劵不存在']);
                    }
                } else {
                    // 尝试核销别的商家的劵
                    throw new Success(['msg' => '此劵与商家对比失败']);
                }
            }
        }
    }


    // 获得活动助力二维码
    public function get_HuoDong_ZhuLi_erweima($scene)
    {
        $huodongzhulierweima = new HuoDongZhuLiErweima();
        $erweima = $huodongzhulierweima->getCanShuMa($scene);
        if ($erweima === false) {
            // * 这里应该醒目提示，分享失败很伤！！
            throw new Success(['msg' => '生成失败', 'data' => null]);
        }
        throw new Success(['msg' => 'ok', 'data' => $erweima]);
    }


}
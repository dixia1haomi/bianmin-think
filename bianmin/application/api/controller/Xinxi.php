<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/13 0013
 * Time: 上午 8:19
 */

namespace app\api\controller;

use app\api\model\Bianminlist as bianminlistModel;
use app\api\model\Bianminkuozhan as bianminkuozhanModel;
use app\api\model\Img as imgModel;
use app\api\model\Liuyan as liuyanModel;
use app\api\model\Huifu as huifuModel;
use app\api\model\Bmxxdingzhijilu as bmxxdngzhijiluModel;
use app\api\service\shengchengerweima\canshuma;
use app\api\service\mobanxiaoxi\HuifuMoban;
use app\api\service\mobanxiaoxi\LiuyanMoban;

use app\api\service\BaseToken;
use app\api\service\shengchengerweima\XinxiDingZhiErweima;
use app\exception\ExceptionHandler;
use app\exception\QueryDbException;
use app\exception\Success;
use app\api\controller\Cos as cosCon;
use think\Db;

class Xinxi
{

    /**
     * 删除信息：使用事务
     * 新增信息：使用事务
     */

    // ---------------------------------------------------------------- 增删改查 ----------------------------------------------------------------

    // 获取信息列表
    public function getList()
    {
        $page = input('post.page');
        $leibie = input('post.leibie');

        $model = new bianminlistModel();
        if ($leibie == '全部') {
            $data = $model->with(['withUser', 'withImg', 'withLiuyan'])->order(['dingzhi_state' => 'desc', 'update_time' => 'desc'])->page($page, 20)->select();
        } else {
            $data = $model->where(['leibie' => $leibie])->with(['withUser', 'withImg', 'withLiuyan'])->order(['dingzhi_state' => 'desc', 'update_time' => 'desc'])->page($page, 20)->select();
        }

        if ($data === false) {
            throw new QueryDbException(['msg' => 'getList']);
        }

        // 添加hid = false,用于客户端对应信息展开折叠,$data是数组并且不为空
        if (is_array($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                // 处理时间
                $value['time'] = format_date($value['update_time']);
                // 处理扩展
                if ($value['kuozhan']) {
                    $value['kuozhan'] = json_decode($value['kuozhan']);
                }
                // 增加所有查询的流浪次数
                // $model->where('id',$value['id'])->setInc('liulangcishu');
            }
        }
        throw new Success(['data' => $data]);
    }

    // 查询单个便民信息（接受信息ID）
    public function findBianmin($id)
    {
        $bianminModel = new bianminlistModel();
        $bianmin = $bianminModel->where('id', $id)->with(['withUser', 'withImg', 'withLiuyan', 'withBangdinguser'])->find();
        if ($bianmin === false) {
            throw new QueryDbException(['msg' => 'findBianmin']);
        }

        // 添加hid = false,用于客户端对应信息展开折叠,不判断有可能是null而又没有update_time字段报错
        if ($bianmin) {
            $bianmin['time'] = format_date($bianmin['update_time']);
            $bianmin['kuozhan'] = json_decode($bianmin['kuozhan']);
            // 添加顶置时间输出
            if ($bianmin['dingzhi_state'] == 1) {
                $bianmin['dingzhi_time'] = date("Y/m/d H:i:s", $bianmin['dingzhi_time']);
            }
        }
        throw new Success(['data' => $bianmin]);
    }

    // -- API -- 创建便民信息
    public function createList($params)
    {
        // params = { leibie:'xx' , neirong:'xx' , kuozhan:{}  }

        // params中可能有img,需要删除
        // 数组中是否包含某个key
        if (array_key_exists('img', $params)) {
            // 根据key删除指定元素
            unset($params['img']);
        }

        // * 单独类目处理
        if ($params['leibie'] == '人找车') {
            // 人找车 || 车找人 检查出发日期 如果不是每天,需要在biaoji字段加入删除时间,用于模板推送提醒,客户端日期为:X月X日,时间为XX:XX
            if ($params['kuozhan']['renzhaoche_riqi'] != '每天') {
                // 获取日期-时间 转成时间戳 写入biaoji字段
                $str = date('Y') . '年' . $params['kuozhan']['renzhaoche_riqi'] . $params['kuozhan']['renzhaoche_zuiwanshijian'];
                $arr = date_parse_from_format('Y年m月d日H:i', $str);
                $time = mktime($arr['hour'], $arr['minute'], 0, $arr['month'], $arr['day'], $arr['year']);
                // 把时间戳加入到$params里
                $params['biaoji'] = $time;
            }
        }elseif($params['leibie'] == '车找人'){
            if ($params['kuozhan']['chezhaoren_riqi'] != '每天') {
                // 获取日期-时间 转成时间戳 写入biaoji字段
                $str = date('Y') . '年' . $params['kuozhan']['chezhaoren_riqi'] . $params['kuozhan']['chezhaoren_zuiwanshijian'];
                $arr = date_parse_from_format('Y年m月d日H:i', $str);
                $time = mktime($arr['hour'], $arr['minute'], 0, $arr['month'], $arr['day'], $arr['year']);
                // 把时间戳加入到$params里
                $params['biaoji'] = $time;
            }
        }

        // 处理扩展
        $params['kuozhan'] = json_encode($params['kuozhan']);
        // 获取用户ID
        $uid = BaseToken::get_Token_Uid();
        $params['user_id'] = $uid;

        // 写入便民表
        $model = new bianminlistModel();
        $res = $model->create($params);
        if ($res === false) {
            throw new QueryDbException(['msg' => 'createList-base']);
        }
        throw new Success(['data' => $res]);
    }


    // 创建信息图片
    public function createImg()
    {
        $params = input('post.');

        $model = new imgModel();
        $res = $model->create($params);
        if ($res === false) {
            throw new QueryDbException(['msg' => 'createImg']);
        }
        throw new Success(['data' => $res]);
    }

    // 获取我的发布的信息
    public function getMyfabu()
    {
        $uid = BaseToken::get_Token_Uid();

        $model = new bianminlistModel();
        $res = $model->where('user_id', $uid)->with(['withUser', 'withImg', 'withLiuyan', 'withBangdinguser'])->select();
        if ($res === false) {
            throw new QueryDbException(['msg' => 'getMyfabu']);
        }

        if (count($res) > 0) {
            foreach ($res as $value) {
                $value['time'] = format_date($value['update_time']);
                $value['kuozhan'] = json_decode($value['kuozhan']);
                if ($value['dingzhi_state'] == 1) {
                    $value['dingzhi_time'] = date("Y/m/d H:i:s", $value['dingzhi_time']);
                }
            }
        }
        throw new Success(['data' => $res]);
    }

    // 修改便民信息内容
//    public function edit_Bianmin_Neirong()
//    {
//        $id = input('post.id');
//        $neirong = input('post.neirong');
//
//        $model = new bianminlistModel();
//        $res = $model->where('id', $id)->setField('neirong', $neirong);
//
//        if ($res === false) {
//            throw new QueryDbException(['msg' => 'edit_Bianmin_Neirong']);
//        }
//        throw new Success(['data' => $res]);
//    }


    // 增加便民信息点击量
    public function incLiulangcishu()
    {
        $id = input('post.id');
        $model = new bianminlistModel();
        $liulangcishu = $model->where(['id' => $id])->setInc('liulangcishu', rand(1, 3));

        if ($liulangcishu === false) {
            // 增加流浪次数失败了
            throw new QueryDbException(['msg' => 'incLiulangcishu']);
        }
        throw new Success(['data' => $liulangcishu]);
    }


    // 刷新信息， 更新update_time
//    public function updateTime()
//    {
//        $id = input('post.id');
//        $model = new bianminlistModel();
//
//        // 限制刷新时间
//        // 获取数据库上次更新时间对比当前时间，大于24小时更新，否则返回提示
//        $updatetime = $model->where(['id' => $id])->field('update_time')->find();
//        if ($updatetime === false) {
//            throw new QueryDbException(['msg' => 'updateTime']);
//        }
//
//        $now_time = date("Y-m-d H:i:s", time());
//        $now_time = strtotime($now_time);                       // 当前时间
//        $show_time = strtotime($updatetime['update_time']);     // 数据库时间
//        $t = $show_time + 21600;                                // 数据库时间 + 6小时（可刷新时间）
//
//        // 当前时间大于可刷新时间就更新
//        if ($now_time > $t) {
//            $updatetime = $model->where(['id' => $id])->setField('update_time', time());
//            if ($updatetime === false) {
//                // 数据库问题刷新更新时间失败
//                throw new QueryDbException(['msg' => 'updateTime1']);
//            }
//            throw new Success(['data' => '刷新成功']);
//        } else {
//            // 不到24小时，提示还有多长时间能刷新
//            $t = date('H:i', $t);
//            throw new Success(['data' => '这条信息需要' . $t . '以后才能刷新']);
//        }
//    }


    // 删除我的发布
    public function deleteMyfabu($list_id)
    {
        // 根据list_id查询IMG表
        $imgModel = new imgModel();
        $imgArray = $imgModel->where('list_id', $list_id)->select();
        if ($imgArray === false) {
            throw new QueryDbException(['msg' => 'deleteMyfabu']);
        }


        // 删除关联的COS图片和IMG表数据
        $this->forDelete($imgArray);

        Db::startTrans();
        // 删除关联的回复
        $huifuModel = new huifuModel();
        $huifu = $huifuModel->where('bmxx_id', $list_id)->delete();
        if ($huifu === false) {
            Db::rollback();
            throw new QueryDbException(['msg' => 'deleteMyfabu删除关联的回复']);
        }

        // 删除关联的留言
        $liuyanModel = new liuyanModel();
        $liuyan = $liuyanModel->where('bmxx_id', $list_id)->delete();
        if ($liuyan === false) {
            Db::rollback();
            throw new QueryDbException(['msg' => 'deleteMyfabu删除关联的留言']);
        }

        // 删除信息扩展
        $kuozhanModel = new bianminkuozhanModel();
        $kuozhan = $kuozhanModel->where('bmxx_id', $list_id)->delete();
        if ($kuozhan === false) {
            Db::rollback();
            throw new QueryDbException(['msg' => 'deleteMyfabu删除信息扩展']);
        }

        // 删除帮顶数据
        $bmxxdingzhijiluModel = new bmxxdngzhijiluModel();
        $dingzhijilu = $bmxxdingzhijiluModel->where('bmxx_id', $list_id)->delete();
        if ($dingzhijilu === false) {
            Db::rollback();
            throw new QueryDbException(['msg' => 'deleteMyfabu删除帮顶数据']);
        }

        // 删除这条信息的分享码
        $path = "/data/wwwroot/default/bianmin/public/erweima/" . $list_id . ".jpeg";
        if (is_file($path)) {
            $unlink = unlink($path);
            if ($unlink === false) {
                // 删除失败、不能抛出要继续执行
            }
        }

        // 删除信息
        $bianminModel = new bianminlistModel();
        $bianmin = $bianminModel->where('id', $list_id)->delete();
        if ($bianmin === false) {
            Db::rollback();
            throw new QueryDbException(['msg' => 'deleteMyfabu删除信息']);
        }
        Db::commit();
        throw new Success(['data' => ['liuyan' => $liuyan, 'huifu' => $huifu, 'bianmin' => $bianmin]]);
    }


    // 遍历删除信息COS图片和IMG表数据
    public function forDelete($imgArray)
    {
        if (count($imgArray) > 0) {
            // 有图片，准备删除COS
            $cos = new cosCon();
            $fileName = trim(strrchr($imgArray[0]['url'], '/'), '/');   // 截取最后一个斜杠后面的内容
            $wenjianjia = "bianmin/";
            $cosdelete = $cos->cosdelete($wenjianjia, $fileName);

//            if ($cosdelete['code'] == 0) {
            // COS返回成功，继续删除IMG表

            $imgModel = new imgModel();
            $imgres = $imgModel->where('id', $imgArray[0]['id'])->delete();
            if ($imgres === false) {
                throw new QueryDbException(['msg' => 'forDelete']);
            }


            unset($imgArray[0]);                    // 删除$imgArray[0]
            $imgArray = array_values($imgArray);    // 使用 unset 并未改变数组的原有索引。如果打算重排索引（让索引从0开始，并且连续），可以使用 array_values

            // 循环调用自己
            $this->forDelete($imgArray);
//            } else {
//                // 删除COS失败 // * 抛给客户端并且记录日志
//            }
        } else {
            // 没有图片，直接删除数据
            return true;
        }
    }


    // ------------------------------------------------------------ 便民信息帮顶 ------------------------------------------------------------
    // 创建便民信息帮顶用户(接受信息ID)
    // 返回 msg = ok || 已经顶过了
    // * 2和3操作两张表，应该使用事务！！！
    public function createBmxxDingZhi_User($id)
    {
        // 1.防止重复顶置
        // 2.新增顶置用户表数据
        // 3.更新便民信息表信息顶置时间，顶置状态

        // 1.防止重复顶置--根据信息ID查询便民信息顶置表中是否存在此用户
        $uid = BaseToken::get_Token_Uid();
        $bmxxdngzhijilumodel = new bmxxdngzhijiluModel();
        $dingzhi = $bmxxdngzhijilumodel->where(['bmxx_id' => $id, 'user_id' => $uid])->find();
        if ($dingzhi === false) {
            throw new QueryDbException(['msg' => 'createBmxxDingZhi_User1']);
        }

        // 如果用户存在，已经顶过了，抛出
        if ($dingzhi) {
            // 顶过了
            throw new Success(['msg' => '您已经顶置过了', 'data' => $dingzhi]);
        } else {
            // 开启事务
            Db::startTrans();
            // 没顶过，新增顶置数据
            $res = $bmxxdngzhijilumodel->create(['bmxx_id' => $id, 'user_id' => $uid]);
            if ($res === false) {
                Db::rollback();
                throw new QueryDbException(['msg' => 'createBmxxDingZhi_User2']);
            }
            // 3.更新便民信息表信息顶置时间，顶置状态 (使用实例化后的防止update_time时间被更新导致from_id有效期出现误差)
            $bianminlistModel = new bianminlistModel();
            // 总顶置时间 = 已有顶置时间 + 24小时，所以提前判断已有顶置时间是否有（字段是int类型默认0）
            $dingzhi_time = $bianminlistModel->where('id', $id)->value('dingzhi_time');
            if ($dingzhi_time === false) {
                Db::rollback();
                throw new QueryDbException(['msg' => 'createBmxxDingZhi_User3']);
            }
            // 如果没有顶置过，取当前时间+24小时，否则取已有的顶置时间+24小时
            if ($dingzhi_time === 0) {
                $dztime = time() + (60 * 60 * 24);
            } else {
                $dztime = $dingzhi_time + (60 * 60 * 24);
            }
            // 更新顶置状态和顶置时间
            $bianmin = $bianminlistModel->where('id', $id)->setField(['dingzhi_state' => 1, 'dingzhi_time' => $dztime]);
            if ($bianmin === false) {
                Db::rollback();
                throw new QueryDbException(['msg' => 'createBmxxDingZhi_User4']);
            }
            Db::commit();
            throw new Success(['msg' => '帮顶成功', 'data' => $res]);
        }
    }



    // ------------------------------------------------------------ 留言回复 ------------------------------------------------------------

    // 新增便民留言
    public function create_Bianmin_Liuyan()
    {
        $uid = BaseToken::get_Token_Uid();

        $id = input('post.bmxx_id');
        $neirong = input('post.neirong');
        $form_id = input('post.form_id');

        $model = new liuyanModel();
        // 新增便民留言数据
        $lyxx = $model->create([
            'bmxx_id' => $id,
            'neirong' => $neirong,
            'user_id' => $uid,
            'form_id' => $form_id
        ]);
        if ($lyxx === false) {
            throw new QueryDbException(['msg' => 'create_Bianmin_Liuyan']);
        }

        // 查询新信息返回客户端（局部刷新）
        $bmxx = $this->findBianminXinxi($id);

        // 发送留言模板消息给信息主人(留言数据,便民数据)
        $message = new LiuyanMoban();
        $bmMsg = $message->sendLiuyanMessage($lyxx, $bmxx);

        throw new Success(['data' => $bmxx, 'msg' => $bmMsg]);
    }

    // 私有查询单个便民信息（接受信息ID，用于留言回复局部刷新）
    private function findBianminXinxi($id)
    {
        $bianminModel = new bianminlistModel();
        $bianmin = $bianminModel->where('id', $id)->with(['withUser', 'withImg', 'withLiuyan', 'withKuozhan', 'withBangdinguser'])->find();
        if ($bianmin === false) {
            throw new QueryDbException(['msg' => 'findBianmin']);
        }

        // 添加hid = false,用于客户端对应信息展开折叠,不判断有可能是null而又没有update_time字段报错
        if ($bianmin) {
            $bianmin['time'] = format_date($bianmin['update_time']);
            $bianmin['kuozhan'] = json_decode($bianmin['kuozhan']);
            // 添加顶置时间输出
            if ($bianmin['dingzhi_state'] == 1) {
                $bianmin['dingzhi_time'] = date("Y/m/d H:i:s", $bianmin['dingzhi_time']);
            }
        }
        return $bianmin;
    }


    // 新增回复
    public function create_Bianmin_Huifu()
    {
        $uid = BaseToken::get_Token_Uid();
        $liuyan_id = input('post.liuyan_id');
        $huifu_user_id = input('post.huifu_user_id');
        $neirong = input('post.neirong');
        $form_id = input('post.form_id');
        $bmxx_id = input('post.bmxx_id');


        // 防止自己回复自己（不要改动！！前段是判断str == 不要自己回复自己）
//        if ($uid == $huifu_user_id) {
//            throw new Success(['data' => '不要自己回复自己']);
//        }

        // 新增回复
        $model = new huifuModel();
        $hfxx = $model->create([
            'bmxx_id' => $bmxx_id,
            'liuyan_id' => $liuyan_id,
            'user_id' => $uid,
            'huifu_user_id' => $huifu_user_id,
            'neirong' => $neirong
        ]);
        if ($hfxx === false) {
            throw new QueryDbException(['msg' => 'create_Bianmin_Huifu']);
        }

        // 更新便民信息的form_id
        $bianminModel = bianminlistModel::update(['form_id' => $form_id], ['id' => $bmxx_id]);
        if ($bianminModel === false) {
            throw new QueryDbException(['msg' => 'create_Bianmin_Huifu1']);
        }

        // 发送回复模板消息给留言人(留言数据,便民数据)
        $message = new HuifuMoban();
        $bmMsg = $message->sendHuifuMessage($hfxx);

        throw new Success(['data' => $hfxx, 'msg' => $bmMsg]);
    }


    // 刷新，我的发布页更新formId,接受信息ID
    public function updateBianMinXinXiFormId()
    {
        $bianmin = bianminlistModel::update(['form_id' => input('post.form_id')], ['id' => input('post.id')]);
        if ($bianmin === false) {
            throw new QueryDbException(['msg' => 'updateBianMinXinXiFormId']);
        }
        throw new Success(['data' => $bianmin]);
    }


    // ------------------------------------------------------------ 获得类目分类数据 ------------------------------------------------------------
    public function leimuMoBan()
    {
        $leimu = config('wx_config.leimu');
        throw new Success(['msg' => 'ok', 'data' => $leimu]);
    }

    // ---------------------------------------------------------------- 获得信息分享二维码 ----------------------------------------------------------------
    public function get_XinXi_DingZhi_erweima($scene)
    {
        $xinxierweima = new XinxiDingZhiErweima();
        $erweima = $xinxierweima->getCanShuMa($scene);
        if ($erweima === false) {
            // * 这里应该醒目提示，分享失败很伤！！
            throw new Success(['msg' => '生成失败', 'data' => null]);
        }
        throw new Success(['msg' => 'ok', 'data' => $erweima]);
    }
}
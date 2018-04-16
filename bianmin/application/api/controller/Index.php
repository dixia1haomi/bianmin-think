<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/7 0007
 * Time: 上午 10:59
 */

namespace app\api\controller;



use app\api\model\Bianminlist as bianminlistModel;
use app\api\model\Img as imgModel;
use app\api\model\User as userModel;
use app\api\service\BaseToken;
use app\api\service\userinfo\GetUserPhone;

use app\exception\Success;
use app\api\controller\Cos as cosCon;

class Index
{


    // 获取信息列表
    public function getList($page = 1)
    {
        // 接受分页参数
//        $page = input('post.page');
        $model = new bianminlistModel();
        $data = $model->with(['withUser', 'withImg'])->order('update_time desc')->page($page,10)->select();

        // 添加hid = false,用于客户端对应信息展开折叠
        foreach ($data as $key => $value) {
            $value['hid'] = false;
            $value['time'] = format_date($value['update_time']);
        }

        throw new Success(['data' => $data]);
    }


    // 创建信息
    public function createList()
    {
        $params = input('post.');

        // 获取用户ID
        $uid = BaseToken::get_Token_Uid();
        $params['user_id'] = $uid;

        $model = new bianminlistModel();
        $res = $model->create($params);

        throw new Success(['data' => $res]);
    }

    // 创建图片
    public function createImg()
    {
        $params = input('post.');

        $model = new imgModel();
        $res = $model->create($params);

        throw new Success(['data' => $res]);
    }

    // 获取我的发布
    public function getMyfabu()
    {
        $uid = BaseToken::get_Token_Uid();

        $model = new bianminlistModel();
        $res = $model->where('user_id', $uid)->with(['withUser', 'withImg'])->select();

        // 添加hid = false,用于客户端对应信息展开折叠
        foreach ($res as $key => $value) {
            $value['hid'] = false;
            $value['time'] = format_date($value['update_time']);
        }

        throw new Success(['data' => $res]);
    }

    // 删除我的发布
    public function deleteMyfabu()
    {
        // 接受信息ID先删除关联的图片
        $list_id = input('post.list_id');
        // 根据list_id查询IMG表
        $imgModel = new imgModel();
        $imgArray = $imgModel->where('list_id', $list_id)->select();

        // 删除COS图片和IMG表数据
        $this->forDelete($imgArray);

        // 删除list数据
        $model = new bianminlistModel();
        $delete_bianmin_res = $model->where('id', $list_id)->delete();
        if ($delete_bianmin_res === false) {
            return '出问题了,deleteMyfabu';                             // * 抛给客户端并且记录日志
        }
        throw new Success(['data' => $delete_bianmin_res]);
    }


    // 遍历删除COS图片和IMG表数据
    public function forDelete($imgArray)
    {
        if (count($imgArray) > 0) {
            // 有图片，准备删除COS
            $cos = new cosCon();
            $fileName = trim(strrchr($imgArray[0]['url'], '/'), '/');   // 截取最后一个斜杠后面的内容
            $cosdelete = $cos->cosdelete($fileName);

            if ($cosdelete['code'] == 0) {
                // COS返回成功，继续删除IMG表
                $imgModel = new imgModel();
                $imgres = $imgModel->where('id', $imgArray[0]['id'])->delete();
                if ($imgres === false) {
                    return '出问题了';                  // * 抛给客户端并且记录日志
                }

                unset($imgArray[0]);                    // 删除$imgArray[0]
                $imgArray = array_values($imgArray);    // 使用 unset 并未改变数组的原有索引。如果打算重排索引（让索引从0开始，并且连续），可以使用 array_values

                // 循环调用自己
                $this->forDelete($imgArray);
            } else {
                // 删除COS失败 // * 抛给客户端并且记录日志
            }
        } else {
            // 没有图片，直接删除数据
            return true;
        }
    }

    // 增加指定数据点击量
    public function incLiulangcishu()
    {
        $id = input('post.id');
        $model = new bianminlistModel();
        $liulangcishu = $model->where(['id' => $id])->setInc('liulangcishu');

        if ($liulangcishu === false) {
            // 增加流浪次数失败了
        }
        throw new Success(['data' => $liulangcishu]);
    }


    // 刷新， 更新update_time
    public function updateTime()
    {
        $id = input('post.id');
        $model = new bianminlistModel();

        // 限制刷新时间
        // 获取数据库上次更新时间对比当前时间，大于24小时更新，否则返回提示
        $updatetime = $model->where(['id' => $id])->field('update_time')->find();

        $now_time = date("Y-m-d H:i:s", time());
        $now_time = strtotime($now_time);                       // 当前时间
        $show_time = strtotime($updatetime['update_time']);     // 数据库时间
        $t = $show_time + 43200;                                // 数据库时间 + 12小时（可刷新时间）

        // 当前时间大于可刷新时间就更新
        if ($now_time > $t) {
            $updatetime = $model->where(['id' => $id])->setField('update_time', time());
            if ($updatetime === false) {
                // 数据库问题刷新更新时间失败
            }
            throw new Success(['data' => '刷新成功']);
        }else{
            // 不到24小时，提示还有多长时间能刷新
            $t = date('H:i',$t);
            throw new Success(['data' => '这条信息需要'.$t.'以后才能刷新']);
        }
    }


    // ----- 电话解密 -----
    public function getPhone()
    {
        $params = input('post.');
        $encryptedData = $params['encryptedData'];
        $iv = $params['iv'];

        // 解密userphone
        $phone = (new GetUserPhone())->jiemi_UserPhone($encryptedData, $iv);

        /**
         * $phone
         * countryCode:"86"                 区号
         * phoneNumber:"15987419288"        用户绑定的手机号（国外手机号会有区号）
         * purePhoneNumber:"15987419288"    没有区号的手机号
         */

        // 把电话号码写入数据库
        $uid = BaseToken::get_Token_Uid();
        $model = new userModel();

        $updatephone = $model->where(['id' => $uid])->setField('phone', $phone["purePhoneNumber"]);

        if ($updatephone === false) {
            // 更新电话失败了
        }
        throw new Success(['data' => $phone["purePhoneNumber"]]);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/7 0007
 * Time: 上午 10:59
 */

namespace app\api\controller;


use app\api\controller\Cos;
use app\api\model\Bianminlist as bianminlistModel;
use app\api\model\Img as imgModel;
use app\api\model\User as userModel;
use app\api\service\BaseToken;
use app\api\service\userinfo\GetUserPhone;
use app\exception\QueryDbException;
use app\exception\Success;
use app\api\controller\Cos as cosCon;

class Index
{

    public function index()
    {
        return "aaaa";
    }


    // 获取信息列表
    public function getList()
    {
        $model = new bianminlistModel();
        $data = $model->with(['withUser', 'withImg'])->order('update_time desc')->select();

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
        $res = $model->where('user_id', $uid)->with(['withImg'])->select();

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
        $updatetime = $model->where(['id' => $id])->setField('update_time', time());

        if ($updatetime === false) {
            // 刷新更新时间失败了
        }
        throw new Success(['data' => $updatetime]);
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
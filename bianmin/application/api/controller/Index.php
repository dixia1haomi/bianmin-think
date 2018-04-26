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

use app\api\model\Shangjia as shangjiaModel;
use app\api\model\Shangjiaimg as shangjiaimgModel;

class Index
{


    // 获取信息列表
    public function getList($page = 1)
    {
        // 接受分页参数
//        $page = input('post.page');
        $model = new bianminlistModel();
        $data = $model->with(['withUser', 'withImg','withLiuyan'])->order('update_time desc')->page($page, 10)->select();

        // 添加hid = false,用于客户端对应信息展开折叠
        foreach ($data as $key => $value) {
            $value['hid'] = false;
            $value['time'] = format_date($value['update_time']);
        }

        throw new Success(['data' => $data]);
    }

    // 查询单个便民信息（接受信息ID，用于留言回复局部刷新）
    public function findBianmin($id)
    {
        $bianminModel = new bianminlistModel();
        $bianmin = $bianminModel->where('id', $id)->with(['withUser', 'withImg', 'withLiuyan'])->find();
        if ($bianmin === false) {
            //
        }

        // 添加hid = false,用于客户端对应信息展开折叠
        $bianmin['hid'] = false;
        $bianmin['time'] = format_date($bianmin['update_time']);

        throw new Success(['data' => $bianmin]);
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
        $res = $model->where('user_id', $uid)->with(['withUser', 'withImg','withLiuyan'])->select();

        // 添加hid = false,用于客户端对应信息展开折叠
        foreach ($res as $key => $value) {
            $value['hid'] = false;
            $value['time'] = format_date($value['update_time']);
        }

        throw new Success(['data' => $res]);
    }

    // 修改便民信息内容
    public function edit_Bianmin_Neirong()
    {
        $id = input('post.id');
        $neirong = input('post.neirong');

        $model = new bianminlistModel();
        $res = $model->where('id', $id)->setField('neirong', $neirong);

        if ($res === false) {
            // 失败了
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
            $wenjianjia = "bianmin/";
            $cosdelete = $cos->cosdelete($wenjianjia, $fileName);

//            if ($cosdelete['code'] == 0) {
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
//            } else {
//                // 删除COS失败 // * 抛给客户端并且记录日志
//            }
        } else {
            // 没有图片，直接删除数据
            return true;
        }
    }

    // 增加便民信息点击量
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
        $t = $show_time + 21600;                                // 数据库时间 + 12小时（可刷新时间）

        // 当前时间大于可刷新时间就更新
        if ($now_time > $t) {
            $updatetime = $model->where(['id' => $id])->setField('update_time', time());
            if ($updatetime === false) {
                // 数据库问题刷新更新时间失败
            }
            throw new Success(['data' => '刷新成功']);
        } else {
            // 不到24小时，提示还有多长时间能刷新
            $t = date('H:i', $t);
            throw new Success(['data' => '这条信息需要' . $t . '以后才能刷新']);
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


    // ---------------------------------------------------------- 商家 ---------------------------------------------------
    // ---------------------------------------------------------- 商家 ---------------------------------------------------

    // 新增商家
    public function createShangjia()
    {
        // 获取user_id
        $uid = BaseToken::get_Token_Uid();
        // 获取商家参数:name, toutu, phone, dizhi, longitude, latitude, miaoshu
        $params = input('post.');
        // 加入用户ID
        $params['user_id'] = $uid;

        $model = new shangjiaModel();
        $res = $model->create($params);
        if ($res === false) {
            //
        }
        throw new Success(['data' => $res]);
    }

    // 创建商家图片
    public function createShangjiaImg()
    {
        $params = input('post.');

        $model = new shangjiaimgModel();
        $res = $model->create($params);
        if ($res === false) {
            //
        }
        throw new Success(['data' => $res]);
    }

    // 查询商家详情
    public function findShangjia($id)
    {
        $model = new shangjiaModel();

        $data = $model->with(['withshangjiaImg'])->find($id);
        if ($data === false) {
            //
        }

        // 查询商家详情就增加点击量
        $liulangcishu = $model->where(['id' => $id])->setInc('liulangcishu');
        if ($liulangcishu === false) {
            // 增加流浪次数失败了
        }

        throw new Success(['data' => $data]);
    }

    // 查询商家列表
    public function selectShangjia()
    {
        $model = new shangjiaModel();

        $page = input('post.page');
        // 如果传了page分页就不限制limit,limit限制用于首页展示10个商家，不想再分一个API了
        if ($page) {
            $data = $model->select();
        } else {
            $data = $model->limit(10)->select();
        }

        if ($data === false) {
            //
        }
        throw new Success(['data' => $data]);
    }

    // 查询我的店铺
    public function getMyShangjia()
    {
        $uid = BaseToken::get_Token_Uid();

        $model = new shangjiaModel();
        $res = $model->where('user_id', $uid)->with(['withshangjiaImg'])->find();

        throw new Success(['data' => $res]);
    }

    // ------------------ 修改店铺 -----------------
    // ------------------ 修改店铺 -----------------

    // 删除一张店铺详情图
    public function deleteMyShangjia_xiangqingtu_item()
    {
        $id = input('post.id');
        $url = input('post.url');

        // 准备删除COS
        $cos = new cosCon();
        $fileName = trim(strrchr($url, '/'), '/');   // 截取最后一个斜杠后面的内容
        $wenjianjia = "shangjia/";
        $cosdelete = $cos->cosdelete($wenjianjia, $fileName);
        if ($cosdelete['code'] != 0) {
            // COS有问题
        }

        // 删除shangjiaimg表数据
        $shangjiaimgModel = new shangjiaimgModel();
        $delete = $shangjiaimgModel->where('id', $id)->delete();
        if ($delete === false) {
            //
        }

        throw new Success(['data' => $delete]);
    }

    // 修改商家头图
    public function xiugaiMyShangjia_toutu()
    {
        $shangjia_id = input('post.shangjia_id');   // 商家ID
        $url = input('post.url');                   // 上传的新图url
        $jiu_toutu = input('post.jiu_toutu');       // 旧图url

        // 更新商家表头图url
        $model = new shangjiaModel();
        $res = $model->where('id', $shangjia_id)->setField('toutu', $url);
        if ($res === false) {
            //
        }

        // 删除COS旧图
        $cos = new cosCon();
        $fileName = trim(strrchr($jiu_toutu, '/'), '/');   // 截取最后一个斜杠后面的内容
        $wenjianjia = "shangjia/";
        $cosdelete = $cos->cosdelete($wenjianjia, $fileName);
        if ($cosdelete['code'] != 0) {
            // COS有问题
        }
        throw new Success(['data' => $res]);
    }

    // 修改商家名称
    public function xiugaiMyShangjia_name()
    {
        $shangjia_id = input('post.shangjia_id');   // 商家ID
        $name = input('post.name');                 // 新名称

        // 更新商家表头图url
        $model = new shangjiaModel();
        $update_name = $model->where('id', $shangjia_id)->setField('name', $name);
        if ($update_name === false) {
            //
        }
        throw new Success(['data' => $update_name]);
    }

    // 修改商家描述
    public function xiugaiMyShangjia_miaoshu()
    {
        $shangjia_id = input('post.shangjia_id');   // 商家ID
        $miaoshu = input('post.miaoshu');                 // 新名称

        // 更新商家表头图url
        $model = new shangjiaModel();
        $update_miaoshu = $model->where('id', $shangjia_id)->setField('miaoshu', $miaoshu);
        if ($update_miaoshu === false) {
            //
        }
        throw new Success(['data' => $update_miaoshu]);
    }

    // 修改商家地址
    public function xiugaiMyShangjia_dizhi()
    {
        $shangjia_id = input('post.shangjia_id');   // 商家ID
        $dizhi = input('post.dizhi');               // 新地址
        $longitude = input('post.longitude');       // 新经度
        $latitude = input('post.latitude');         // 新纬度

        // 更新商家地址,经度，纬度
        $model = new shangjiaModel();
        $update_dizhi = $model->where('id', $shangjia_id)->update(['dizhi' => $dizhi, 'longitude' => $longitude, 'latitude' => $latitude]);
        if ($update_dizhi === false) {
            //
        }
        throw new Success(['data' => $update_dizhi]);
    }

    // 删除我的店铺
    public function deleteMyShangjia()
    {
        // 接受ID，删除COS详情图，删除COS头图，删除数据
        $id = input('post.id');
        // 根据商家ID查询商家图片表
        $imgModel = new shangjiaimgModel();
        $imgArray = $imgModel->where('shangjia_id', $id)->select();

        // 删除COS详情图
        $this->forDelete_ShangjiaCos($imgArray);

        // 查询商家表
        $shangjiaModel = new shangjiaModel();
        $shangjia = $shangjiaModel->where('id', $id)->find();

        // 删除COS头图
        $cos = new cosCon();
        $fileName = trim(strrchr($shangjia['toutu'], '/'), '/');   // 截取最后一个斜杠后面的内容
        $wenjianjia = "shangjia/";
        $cosdelete = $cos->cosdelete($wenjianjia, $fileName);
        if ($cosdelete['code'] == 0) {
            // 删除头图成功
        }

        // 删除商家数据
        $deleteShangjia = $shangjiaModel->where('id', $id)->delete();
        if ($deleteShangjia === false) {
            // 删除失败
        }

        throw new Success(['data' => $deleteShangjia]);
    }

    // 遍历删除商家COS图片和IMG表数据
    public function forDelete_ShangjiaCos($imgArray)
    {
        if (count($imgArray) > 0) {
            // 有图片，准备删除COS
            $cos = new cosCon();
            $fileName = trim(strrchr($imgArray[0]['url'], '/'), '/');   // 截取最后一个斜杠后面的内容
            $wenjianjia = "shangjia/";
            $cosdelete = $cos->cosdelete($wenjianjia, $fileName);

            // COS返回成功，继续删除商家IMG表
            $imgModel = new shangjiaimgModel();
            $imgres = $imgModel->where('id', $imgArray[0]['id'])->delete();
            if ($imgres === false) {
                // 删除数据失败
            }

            unset($imgArray[0]);                    // 删除$imgArray[0]
            $imgArray = array_values($imgArray);    // 使用 unset 并未改变数组的原有索引。如果打算重排索引（让索引从0开始，并且连续），可以使用 array_values

            // 循环调用自己
            $this->forDelete_ShangjiaCos($imgArray);

        } else {
            // 没有图片，直接删除数据
            return true;
        }
    }


}
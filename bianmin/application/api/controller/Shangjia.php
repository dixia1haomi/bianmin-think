<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/13 0013
 * Time: 上午 8:29
 */

namespace app\api\controller;


class Shangjia
{

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
        if ($res === false) {
            //
        }

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
        if ($imgArray === false) {
            //
        }

        // 删除COS详情图
        $this->forDelete_ShangjiaCos($imgArray);

        // 查询商家表
        $shangjiaModel = new shangjiaModel();
        $shangjia = $shangjiaModel->where('id', $id)->find();
        if ($shangjia === false) {
            //
        }

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
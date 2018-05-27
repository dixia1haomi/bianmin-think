<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/25 0025
 * Time: 上午 9:37
 */

namespace app\api\controller;

use app\api\model\Huodong as huodongModel;
use app\api\model\Huodongimg as huodongimgModel;
use app\api\service\BaseToken;
use app\exception\QueryDbException;
use app\exception\Success;

use app\api\controller\Cos as cosCon;

class Huodong
{
    // 新增活动
    public function create_Huodong($params)
    {
        // 加入uid
        $uid = BaseToken::get_Token_Uid();
        $params['user_id'] = $uid;
        // 创建活动
        $huodong = huodongModel::create($params);
        if ($huodong === false) {
            throw new QueryDbException(['msg' => '创建商家活动失败，create_Huodong']);
        }
        throw new Success(['data' => $huodong]);
    }


    // 查询活动详情(关联huodong图文表)
    public function find_Huodong($id)
    {
        $huodongModel = new huodongModel();
        $huodong = $huodongModel->with(['withhuodongImg'])->find($id);
        if ($huodong === false) {
            throw new QueryDbException(['msg' => '查询活动详情，find_Huodong']);
        }
        // 增加点击量
        if ($huodong != null) {
            $liulangcishu = $huodong->setInc('liulangcishu');
            if ($liulangcishu === false) {
                // 增加流浪次数失败了
            }
        }
        throw new Success(['data' => $huodong]);
    }


    // 查询活动列表
    public function select_Huodong($shangjia_id)
    {
        $huodongModel = new huodongModel();
        $huodong = $huodongModel->where('shangjia_id', $shangjia_id)->select();
        if ($huodong === false) {
            throw new QueryDbException(['msg' => '查询活动列表失败，select_Huodong']);
        }
        throw new Success(['data' => $huodong]);
    }


    // 删除活动
    public function delete_Huodong($id)
    {
        // 接受ID，删除COS详情图，删除COS头图，删除数据
        // 根据活动ID查询活动IMG表
        $imgModel = new huodongimgModel();
        $imgArray = $imgModel->where('huodong_id', $id)->select();
        if ($imgArray === false) {
            throw new QueryDbException(['msg' => '删除我的活动查询活动IMG表失败，delete_Huodong']);
        }

        // 删除COS详情图
        $this->forDelete_HuodongCos($imgArray);

        // 查询活动表
        $huodongModel = new huodongModel();
        $huodong = $huodongModel->where('id', $id)->find();
        if ($huodong === false) {
            throw new QueryDbException(['msg' => '查询活动表失败，delete_Huodong']);
        }

        // 删除COS头图
        $cos = new cosCon();
        $fileName = trim(strrchr($huodong['toutu'], '/'), '/');   // 截取最后一个斜杠后面的内容
        $wenjianjia = "huodong/";
        $cosdelete = $cos->cosdelete($wenjianjia, $fileName);
        if ($cosdelete['code'] != 0) {
            //
        }

        // 删除活动数据
        $delete = $huodong->delete();
        if ($delete === false) {
            throw new QueryDbException(['msg' => '删除活动失败，delete_Huodong']);
        }

        throw new Success(['data' => $delete]);
    }


    // 遍历删除商家COS图片和IMG表数据
    private function forDelete_HuodongCos($imgArray)
    {
        if (count($imgArray) > 0) {
            // 有图片，准备删除COS
            $cos = new cosCon();
            $fileName = trim(strrchr($imgArray[0]['url'], '/'), '/');   // 截取最后一个斜杠后面的内容
            $wenjianjia = "huodongimg/";
            $cosdelete = $cos->cosdelete($wenjianjia, $fileName);
            if ($cosdelete["code"] != 0) {
                //
            }

            // COS返回成功，继续删除活动IMG表
            $imgModel = new huodongimgModel();
            $imgres = $imgModel->where('id', $imgArray[0]['id'])->delete();
            if ($imgres === false) {
                throw new QueryDbException(['msg' => '删除活动IMG表失败，delete_Huodong']);
            }

            unset($imgArray[0]);                    // 删除$imgArray[0]
            $imgArray = array_values($imgArray);    // 使用 unset 并未改变数组的原有索引。如果打算重排索引（让索引从0开始，并且连续），可以使用 array_values

            // 循环调用自己
            $this->forDelete_HuodongCos($imgArray);

        } else {
            return true;
        }
    }


    // ----------------------------------- 编辑活动基本资料 ----------------------------------

    // 修改活动头图
    public function xiugaiHuodong_toutu($params)
    {
        /**
         * $params = { huodong_id:huodong_id, toutu:toutu }
         */
        // 先删除以前的图片，再更新新图片
        $huodongModel = new huodongModel();
        $toutu = $huodongModel->where('id', $params['huodong_id'])->value('toutu');
        if ($toutu === false) {
            throw new QueryDbException(['msg' => '修改活动头图查询旧图失败，xiugaiHuodong_toutu']);
        }
        // 删除COS旧图
        $cos = new cosCon();
        $fileName = trim(strrchr($toutu, '/'), '/');   // 截取最后一个斜杠后面的内容
        $wenjianjia = "huodong/";
        $cosdelete = $cos->cosdelete($wenjianjia, $fileName);
        if ($cosdelete['code'] != 0) {
            // COS有问题
        }

        // 更新新图片
        $res = $huodongModel->where('id', $params['huodong_id'])->setField('toutu', $params['toutu']);
        if ($res === false) {
            throw new QueryDbException(['msg' => '修改活动头图更新新图失败，xiugaiHuodong_toutu']);
        }
        throw new Success(['data' => $res]);
    }


    // 修改活动标题
    public function xiugaiHuodong_biaoti()
    {
        $huodong_id = input('post.huodong_id');   // 活动ID
        $biaoti = input('post.biaoti');           // 新标题

        // 修改活动标题
        $model = new huodongModel();
        $res = $model->where('id', $huodong_id)->setField('biaoti', $biaoti);
        if ($res === false) {
            throw new QueryDbException(['msg' => '修改活动标题失败，xiugaiHuodong_biaoti']);
        }
        throw new Success(['data' => $res]);
    }


    // 修改活动原价
    public function xiugaiHuodong_yuanjia()
    {
        $huodong_id = input('post.huodong_id');   // 活动ID
        $yuanjia = input('post.yuanjia');

        // 修改活动原价
        $model = new huodongModel();
        $res = $model->where('id', $huodong_id)->setField('yuanjia', $yuanjia);
        if ($res === false) {
            throw new QueryDbException(['msg' => '修改活动标题失败，xiugaiHuodong_yuanjia']);
        }
        throw new Success(['data' => $res]);
    }

    // 修改活动活动价
    public function xiugaiHuodong_huodongjia()
    {
        $huodong_id = input('post.huodong_id');   // 活动ID
        $huodongjia = input('post.huodongjia');

        // 修改活动活动价
        $model = new huodongModel();
        $res = $model->where('id', $huodong_id)->setField('huodongjia', $huodongjia);
        if ($res === false) {
            throw new QueryDbException(['msg' => '修改活动活动价失败，xiugaiHuodong_huodongjia']);
        }
        throw new Success(['data' => $res]);
    }


    // 修改活动数量
    public function xiugaiHuodong_shuliang()
    {
        $huodong_id = input('post.huodong_id');   // 活动ID
        $shuliang = input('post.shuliang');

        // 修改活动数量
        $model = new huodongModel();
        $res = $model->where('id', $huodong_id)->setField('shuliang', $shuliang);
        if ($res === false) {
            throw new QueryDbException(['msg' => '修改活动数量失败，xiugaiHuodong_shuliang']);
        }
        throw new Success(['data' => $res]);
    }


    // 修改活动条件
    public function xiugaiHuodong_tiaojian()
    {
        $huodong_id = input('post.huodong_id');   // 活动ID
        $tiaojian = input('post.tiaojian');

        // 修改活动数量
        $model = new huodongModel();
        $res = $model->where('id', $huodong_id)->setField('tiaojian', $tiaojian);
        if ($res === false) {
            throw new QueryDbException(['msg' => '修改活动条件失败，xiugaiHuodong_tiaojian']);
        }
        throw new Success(['data' => $res]);
    }


    // 修改活动结束时间
    public function xiugaiHuodong_time()
    {
        $huodong_id = input('post.huodong_id');   // 活动ID
        $jieshu_time = input('post.jieshu_time');

        // 修改活动数量
        $model = new huodongModel();
        $res = $model->where('id', $huodong_id)->setField('jieshu_time', $jieshu_time);
        if ($res === false) {
            throw new QueryDbException(['msg' => '修改活动结束时间失败，xiugaiHuodong_time']);
        }
        throw new Success(['data' => $res]);
    }

    // 修改活动说明
    public function xiugaiHuodong_shuoming()
    {
        $huodong_id = input('post.huodong_id');   // 活动ID
        $shuoming = input('post.shuoming');

        // 修改活动数量
        $model = new huodongModel();
        $res = $model->where('id', $huodong_id)->setField('shuoming', $shuoming);
        if ($res === false) {
            throw new QueryDbException(['msg' => '修改活动说明失败，xiugaiHuodong_shuoming']);
        }
        throw new Success(['data' => $res]);
    }



    // ----------------------------------- 编辑活动图文详情 ----------------------------------

    // 新增一条活动图文 (img\text)
    public function createHuodongImg($params)
    {
        /**
         * $params = { huodong_id:huodong_id, url:url, text:text }
         */
        $huodongimgModel = new huodongimgModel();
        $res = $huodongimgModel->create($params);
        if ($res === false) {
            throw new QueryDbException(['msg' => '新增一条活动图文失败，createHuodongImg']);
        }
        throw new Success(['data' => $res]);
    }


    // 删除一条活动IMG
    public function deleteHuodongImg($id)
    {
        $huodongimgModel = new huodongimgModel();
        $delete = $huodongimgModel->where('id', $id)->delete();
        if ($delete === false) {
            throw new QueryDbException(['msg' => '删除一条活动IMG失败，deleteHuodongImg']);
        }
        throw new Success(['data' => $delete]);
    }


    // 单独修改活动IMG表里的text字段
    public function updateHuodongImg_text($params)
    {
        /**
         * $params = { id:id, text:text }
         */
        $huodongimgModel = new huodongimgModel();
        $res = $huodongimgModel->where('id', $params['id'])->setField('text', $params['text']);
        if ($res === false) {
            throw new QueryDbException(['msg' => '单独修改活动IMG表里的text字段失败，updateHuodongImg_text']);
        }
        throw new Success(['data' => $res]);
    }


    // 单独修改活动IMG表里的url字段 | 修改图片
    public function updateHuodongImg_url($params)
    {
        /**
         * $params = { id:id, url:url }
         */
        // 先删除以前的图片，再更新新图片
        $huodongimgModel = new huodongimgModel();
        $huodongimg = $huodongimgModel->where('id', $params['id'])->value('url');
        if ($huodongimg === false) {
            throw new QueryDbException(['msg' => '单独修改活动IMG表里的url字段删除以前的图片失败，updateHuodongImg_url']);
        }
        // 删除COS图片
        $cos = new cosCon();
        $fileName = trim(strrchr($huodongimg, '/'), '/');   // 截取最后一个斜杠后面的内容
        $wenjianjia = "huodongimg/";
        $cosdelete = $cos->cosdelete($wenjianjia, $fileName);
        if ($cosdelete['code'] != 0) {
            // COS有问题
        }
        // 更新新图片
        $url = $huodongimgModel->where('id', $params['id'])->setField('url', $params['url']);
        if ($url === false) {
            throw new QueryDbException(['msg' => '单独修改活动IMG表里的url字段更新新图片失败，updateHuodongImg_url']);
        }
        throw new Success(['data' => $url]);
    }
}
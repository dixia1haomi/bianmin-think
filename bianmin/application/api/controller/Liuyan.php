<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/11 0011
 * Time: 上午 11:34
 */

namespace app\api\controller;

use app\api\model\Bianminlist;
use app\api\model\Liuyan as liuyanModel;
use app\api\model\Huifu as huifuModel;
use app\api\model\Bianminlist as bianminlistModel;
use app\api\service\BaseToken;
use app\api\service\mobanxiaoxi\HuifuMoban;
use app\api\service\mobanxiaoxi\LiuyanMoban;
use app\exception\Success;


class Liuyan
{

    // 新增便民留言
    public function create_Bianmin_Liuyan()
    {
        $uid = BaseToken::get_Token_Uid();

        $bmxx_id = input('post.bmxx_id');
        $neirong = input('post.neirong');
        $form_id = input('post.form_id');

        $model = new liuyanModel();
        // 新增便民留言数据
        $lyxx = $model->create([
            'bmxx_id' => $bmxx_id,
            'neirong' => $neirong,
            'user_id' => $uid,
            'form_id' => $form_id
        ]);
        if ($lyxx === false) {
            // res =
            // bmxx_id:"86"
            // id:"2"
            // neirong:"qwe"
            // user_id:2
        }

        // 查询新信息返回客户端（局部刷新）
        $bmxx = $this->findBianminXinxi($bmxx_id);

        // 发送留言模板消息给信息主人(留言数据,便民数据)
        $message = new LiuyanMoban();
        $bmMsg = $message->sendLiuyanMessage($lyxx,$bmxx);

        throw new Success(['data' => $bmxx, 'msg' => $bmMsg]);
    }



    // ------------------------------------------------- 回复留言 -------------------------------------------

    // 新增回复
    public function create_Bianmin_Liuyan_Huifu()
    {
        $uid = BaseToken::get_Token_Uid();
        $liuyan_id = input('post.liuyan_id');
        $huifu_user_id = input('post.huifu_user_id');
        $neirong = input('post.neirong');

        $bmxx_id = input('post.bmxx_id');

        // 防止自己回复自己（不要改动！！前段是判断str == 不要自己回复自己）
//        if ($uid == $huifu_user_id) {
//            throw new Success(['data' => '不要自己回复自己']);
//        }

        // 新增回复
        $model = new huifuModel();
        $hfxx = $model->create([
            'liuyan_id' => $liuyan_id,
            'user_id' => $uid,
            'huifu_user_id' => $huifu_user_id,
            'neirong' => $neirong,
        ]);
        if ($hfxx === false) {
            //
        }

        // 查询新信息返回客户端（局部刷新）
        $bmxx = $this->findBianminXinxi($bmxx_id);

        // 发送回复模板消息给留言人(留言数据,便民数据)
        $message = new HuifuMoban();
        $bmMsg = $message->sendHuifuMessage($hfxx);

        throw new Success(['data' => $bmxx, 'msg' => $bmMsg]);
    }


    // 查询单个便民信息（接受信息ID，用于留言回复局部刷新）
    public function findBianminXinxi($bmxx_id)
    {
        $bianminModel = new bianminlistModel();
        $bianmin = $bianminModel->where('id', $bmxx_id)->with(['withUser', 'withImg', 'withLiuyan'])->find();
        if ($bianmin === false) {
            //
        }

        // 添加hid = false,用于客户端对应信息展开折叠
        $bianmin['hid'] = false;
        $bianmin['time'] = format_date($bianmin['update_time']);

        return $bianmin;
    }



    // ---------------------------------------------- 删除留言、回复 ---------------------------------------

    // 查询我的留言
    public function selectMyLiuyan()
    {
        $uid = BaseToken::get_Token_Uid();

        $liuyanModel = new liuyanModel();
        $res = $liuyanModel->where('user_id', $uid)->with(['liuyanwithUser'])->select();
        if ($res === false) {
            //
        }
        throw new Success(['data' => $res]);
    }

    // 回复我的
    public function huifuWode()
    {
        $uid = BaseToken::get_Token_Uid();

        $huifuModel = new huifuModel();
        $res = $huifuModel->where('huifu_user_id', $uid)->with(['huifuwithHfuser', 'huifuwithBhfuser', 'huifuwithLiuyan'])->select();
        if ($res === false) {
            //
        }
        throw new Success(['data' => $res]);
    }





    // ------------------ 以前美食的 -------------------------
//    // 根据餐厅ID查询留言列表（客户端餐厅详情页-查看全部留言）
//    public function liuyanList(){
//        $post_id = input('post.canting_id');
//        $post_page = input('post.page');
//        liuyanModel::liuyanList_Model($post_id,$post_page);
//    }
//    // 新增留言（客户端餐厅详情页-写留言） || redis
//    public function createLiuyan(){
//        $uid = BaseToken::get_Token_Uid();
//        $params = input('post.');               // 接受餐厅ID，Uid，内容.
//        $params['user_id'] = $uid;
//        liuyanModel::createLiuyan_Model($params);
//    }
//    // 删除留言
//    public function deleteLiuyan(){
//        $uid = BaseToken::get_Token_Uid();
//        $post_id = input('post.id');
//        $liuyanModel = new liuyanModel();
//        // 自己才可以删除自己的数据（uid=user_id,证明是自己的，防止直接恶意调用api）
//        $data = $liuyanModel->get($post_id);
//        if($data === false){
//            Log::mysql_log('mysql/Liuyan/deleteLiuyan','查询留言失败');
//        }
//        // 如果留言数据的user_id不等于uid，不是自己的，可能是恶意调用，抛出异常.
//        if($data['user_id'] != $uid){
//            Log::mysql_log('mysql/Liuyan/deleteLiuyan','删除留言时恶意调用，数据不是该用户的');
//        }
//        // 执行删除
//        $delete = $liuyanModel->destroy($post_id);
//        if($delete === false){
//            Log::mysql_log('mysql/Liuyan/deleteLiuyan','删除留言失败');
//        }
//        // 这里要删除redis 餐厅详情
////        $cache = Cache::rm('cantingdetail'.$data['canting_id']);
////        if(!$cache){
////            // 删除redis数据失败，记录日志，返回错误码
////            Log::redis_log('redis/Liuyan/deleteLiuyan','redis删除指定餐厅失败');
////        }
//        throw new Success(['data'=>$delete]);
//    }
}
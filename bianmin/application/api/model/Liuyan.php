<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/11 0011
 * Time: 上午 9:30
 */

namespace app\api\model;


use think\Model;

class Liuyan extends Model
{
    protected $hidden = ['create_time','delete_time', 'update_time'];

    // 留言关联->user
    public function liuyanwithUser()
    {
        return $this->hasOne('user', 'id', 'user_id')->bind(['nick_name']);
    }

    // 留言关联->huifu
    public function liuyanwithHuifu()
    {
        return $this->hasMany('huifu', 'liuyan_id', 'id')->with(['huifuwithHfuser','huifuwithBhfuser']);
    }

    // --------- 回复我的 -- 留言表关联被恢复的用户 ----------
//    public function liuyanwithHuifuUser()
//    {
//        return $this->hasMany('huifu', 'huifu_user_id', 'user_id');
//    }

    // 留言关联便民信息
//    public function liuyanwithBianmin()
//    {
//        return $this->hasOne('bianminlist', 'id', 'bmxx_id');
//    }







    // ---------------------- 以前美食的 -----------------------------
//    protected $hidden = ['delete_time', 'update_time'];
    // 关联->userinfo(只要昵称，头像绑定到调用它的表上-餐厅Model/liuyan（）调用)
//    public function liuyanuserinfo()
//    {
//        return $this->hasOne('user', 'id', 'user_id')->bind(['nick_name','avatar_url']);  // ->bind(['nick_name','avatar_url'])
//    }
//    // 关联->canting(只要餐厅名)
//    public function cantingname()
//    {
//        return $this->hasOne('canting', 'id', 'canting_id')->bind(['name']);
//    }
//    // 新增留言 || redis
//    public static function createLiuyan_Model($params){
//
//        $data = self::create($params);
//        if($data === false){
//            // mysql新增留言失败，日志，返回
//            Log::mysql_log('mysql/Liuyan/createLiuyan','新增留言失败');
//        }
//
//        // 删除redis中餐厅详情
//        $id = $params['canting_id'];
////        $cantingDetail = Cache::rm('cantingdetail'.$id);
////        if(!$cantingDetail){
////            // redis删除餐厅详情失败，记录日志
////            Log::redis_log('redis/Liuyan/createLiuyan','redis删除指定留言失败');
////        }
//
//        throw new Success(['data'=>$data]);
//    }
//    // 查询留言分页列表（根据餐厅ID,客户端餐厅详情页-查看全部留言）
//    public static function liuyanList_Model($post_id,$post_page){
//
//        $data = self::where('canting_id',$post_id)->with(['liuyanuserinfo'])->order('create_time desc')->page($post_page,20)->select();
//        if($data === false){
//            // ****** 查询留言分页列表失败，日志，返回
//            Log::mysql_log('mysql/Liuyan/liuyanList','查询留言分页列表失败');
//        }
//        throw new Success(['data'=>$data]);
//    }
//    // 查询我的留言（接受uid,分页20条）
//    public static function getMyLiuyan_Model($uid,$post_page){
//
//        // 根据uid查留言分页20条关联餐厅名
//        $data = self::where('user_id',$uid)->with(['cantingname'])->order('create_time desc')->page($post_page,20)->select();
//        // 根据uid统计有多少条留言
//        $count = self::where('user_id',$uid)->count();
//        // 查询失败
//        if($data === false || $count === false){
//            Log::mysql_log('mysql/Liuyan/getMyLiuyan','查询我的留言失败');
//        }
//        // 拼接数据
//        $res['data'] = $data;
//        $res['count'] = $count;
//        // 成功返回
//        throw new Success(['data' => $res]);
//    }
}
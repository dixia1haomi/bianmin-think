<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/25 0025
 * Time: 上午 1:27
 */

namespace app\api\controller;

use app\api\model\Log;
use app\api\model\Wenzhang as wenzhangModel;
use app\exception\QueryDbException;
use app\exception\Success;
use think\Cache;

class Wenzhang extends BaseController
{

    // -------------------------------------------------------- Admin ----------------------------------------------------------------
    // -------------------------------------------------------- Admin ----------------------------------------------------------------
    // -------------------------------------------------------- Admin ----------------------------------------------------------------

    // ----------------------前置方法定义，验证管理员身份----------------------
    protected $beforeActionList = [
        'checkAdmin' => ['only' => 'createWenzhang,updateWenzhang,deleteWenzhang'],
    ];



    // 新增文章
    public function createWenzhang()
    {
        // canting_id由后台组织到post里一起传来
        $param = input('post.');
        // 参数验证（*）
        $data = wenzhangModel::createWenzhang($param);
        if($data === false){
            Log::mysql_log('mysql/Wenzhang/createWenzhang','新增文章失败');
        }

        // 更新成功后删除redis相应的数据，cantingDetail，cantingList
        $id = $param['canting_id'];

        // 删除对应的餐厅详情
//        $cantingDetail = Cache::rm('cantingdetail'.$id);
//        if(!$cantingDetail){
//            // *删除餐厅详情缓存失败，记录日志
//            Log::redis_log('redis/Canting/updateCanting','redis删除餐厅详情失败');
//        }

        // 删除餐厅列表
        $cantingList = Cache::rm('cantingList');
        if(!$cantingList){
            // *删除餐厅列表缓存失败，记录日志
            Log::redis_log('redis/Canting/updateCanting','redis删除餐厅列表失败');
        }

        throw new Success(['data'=>$data]);
    }



    // 更新文章
    public function updateWenzhang(){
        $param = input('post.');
        // 参数验证（*）

        // 参数中必须有id
        $data = wenzhangModel::updateWenzhang($param);
        if ($data === false) {
            Log::mysql_log('mysql/Wenzhang/updateWenzhang','更新文章失败');
        }

        // 更新成功后删除redis相应的数据，cantingDetail，cantingList
        $id = $param['canting_id'];

        // 删除对应的餐厅详情
//        $cantingDetail = Cache::rm('cantingdetail'.$id);
//        if(!$cantingDetail){
//            // *删除餐厅详情缓存失败，记录日志
//            Log::redis_log('redis/Canting/updateCanting','redis删除餐厅详情失败');
//        }

        // 删除餐厅列表
        $cantingList = Cache::rm('cantingList');
        if(!$cantingList){
            // *删除餐厅列表缓存失败，记录日志
            Log::redis_log('redis/Canting/updateCanting','redis删除餐厅列表失败');
        }

        throw new Success(['data'=>$data]);
    }


    // 删除文章
    public function deleteWenzhang(){
        $id = input('post.id');
        $canting_id = input('post.canting_id');
        // 参数验证（*）

        $data = wenzhangModel::destroy($id);
        if ($data === false) {
            Log::mysql_log('mysql/Wenzhang/deleteWenzhang','删除文章失败');
        }

        // 删除成功后删除redis相应的数据，cantingDetail，cantingList
        // 删除对应的餐厅详情
//        $cantingDetail = Cache::rm('cantingdetail'.$canting_id);
//        if(!$cantingDetail){
//            // *删除餐厅详情缓存失败，记录日志
//            Log::redis_log('redis/Canting/updateCanting','redis删除餐厅详情失败');
//        }

        // 删除餐厅列表
        $cantingList = Cache::rm('cantingList');
        if(!$cantingList){
            // *删除餐厅列表缓存失败，记录日志
            Log::redis_log('redis/Canting/updateCanting','redis删除餐厅列表失败');
        }

        throw new Success(['data'=>$data]);
    }
}
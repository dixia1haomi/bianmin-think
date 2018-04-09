<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/27 0027
 * Time: 下午 4:20
 */

namespace app\api\controller;

use app\api\model\Canting as cantingModel;
use app\api\model\Log;
use app\api\service\BaseToken;
use app\exception\QueryDbException;

// ------------
use app\exception\Success;
use think\Cache;
use think\cache\driver\Redis;

class Canting extends BaseController
{

    /**
     *  删除餐厅未完成
     *  验证admin权限未完成
     */



    // 获取餐厅列表(where条件) || redis
    public function getList()
    {
        $post = input('post.');
        cantingModel::cantingList($post);
    }


    // 获取收藏的餐厅列表(我的页面-我的收藏使用)
    public function shoucangCantingList()
    {
        // list 客户端缓存的收藏数组，里面是餐厅ID
        $list = input('post.list');
        $cantingModel = new cantingModel();
        $data = $cantingModel->select($list);
        if ($data === false) {
            // *查询失败，返回错误码，并记录日志
            Log::mysql_log('mysql/Canting/shoucangCantingList','获取收藏的餐厅列表失败');
        }
        throw new Success(['data'=>$data]);
    }


    // 获取餐厅详细信息,接受餐厅表ID  || redis
    public function getDetail()
    {
        $id = input('post.id');
        cantingModel::cantingDetail($id);
    }


    // 餐厅点赞（由于不对用户做要求，所以没有放到user控制器中，仅餐厅表点赞 +1 ）
    public function dianzanCanting()
    {
        // 接受餐厅ID
        $post_id = input('post.id');
        $cantingModel = new cantingModel();

        $data = $cantingModel->where(['id'=>$post_id])->setInc('zan');
        if($data === false){
            Log::mysql_log('mysql/Canting/dianzanCanting','餐厅点赞失败');
        }

        // 删除成功后删除redis相应的数据，cantingDetail，cantingList
        // 删除对应的餐厅详情
//        $cantingDetail = Cache::rm('cantingdetail'.$post_id);
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



    // -------------------------------------------------------- Admin ----------------------------------------------------------------
    // -------------------------------------------------------- Admin ----------------------------------------------------------------
    // -------------------------------------------------------- Admin ----------------------------------------------------------------

    // ----------------------前置方法定义，验证管理员身份----------------------
    protected $beforeActionList = [
        'checkAdmin' => ['only' => 'createCanting,updateCanting,deleteCanting'],
    ];



    // 新增餐厅 || redis
    public function createCanting()
    {
        // *验证admin权限


        $param = input('post.');
        // 参数验证（*）
        cantingModel::createCanting($param);
    }


    // 更新餐厅 || resdis(更新时删除redis缓存)
    public function updateCanting()
    {
        // *验证admin权限


        // 获取参数
        $param = input('post.');
        // 参数中必须有id
        cantingModel::updateCanting($param);
    }


    // 删除餐厅 -> 先删除餐厅关联的菜品，环境，文章
    public function deleteCanting()
    {
        // *验证admin权限


        $id = input('post.id');
        // 参数验证（*）

//        $data = cantingModel::destroy($id);
//        if ($data === 0) {
//            throw new QueryDbException();
//        }
//        return $data;
    }



}
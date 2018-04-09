<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/24 0024
 * Time: 下午 5:17
 */

namespace app\api\model;


use app\exception\Success;
use think\Cache;
use think\Model;

class Canting extends Model
{
//    protected $hidden = ['delete_time', 'update_time', 'create_time'];

    // 关联->文章
    public function wenzhang()
    {
        return $this->hasOne('wenzhang', 'canting_id', 'id');
    }

    // 关联:查询餐厅详情使用->取留言limit5条，再关联userinfo取头像和昵称
    public function liuyan()
    {
        return $this->hasMany('liuyan', 'canting_id', 'id')->order('create_time desc')->limit(5)->with(['liuyanuserinfo']);
    }

    // 关联->餐厅下所有留言的数量
    public function liuyanCount()
    {
        return $this->hasMany('liuyan', 'canting_id', 'id');
    }


    // 获取餐厅列表(where条件) || redis
    public static function cantingList($post)
    {

        // 客户端没有传where条件的先从redis获取
        if (!$post) {
            $cantingList = cache('cantingList');
            // 缓存中没有餐厅列表,去数据库获取后再缓存
            if (!$cantingList) {
                $data = self::where('state', 1)->order('create_time desc')->select();
                if ($data === false) {
                    // *如果从数据库获取失败，记录日志,写入日志并且抛出errorCode等于1
                    Log::mysql_log('mysql/Canting/cantingList', '获取餐厅列表失败');
                }
                // 获取成功，缓存
                $cache = cache('cantingList', json_encode($data));
                if (!$cache) {
                    // *如果缓存失败，记录日志
                    Log::redis_log('redis/Canting/cantingList', 'redis写入餐厅列表失败');
                }
                // 返回客户端
                throw new Success(['data' => $data]);
            } else {
                // 缓存中有cantingList，直接返回
                throw new Success(['data' => json_decode($cantingList, true)]);
            }
        } else {
            // 客户端传了where条件，查数据库
            $data = self::where('state', 1)->order('create_time desc')->where($post)->select();
            if ($data === false) {
                // *如果从数据库获取失败，记录日志返回错误码
                Log::mysql_log('mysql/Canting/cantingList', '获取餐厅列表失败');
            }
            throw new Success(['data' => $data]);
        }
    }


    // 获取餐厅详细信息：关联->留言，文章 || Redis *(缓存出现问题的地方，二次查询有缓存依然走没有缓存会再次生成cantingdetail并且没有带ID后缀)
    public static function cantingDetail($id)
    {
        // 先从redis获取
//        $cantingDetail = Cache('cantingdetail' . $id);
//        $cantingDetail = Cache::get('cantingdetail' . $id);
        // 缓存中没有,去数据库获取后再缓存
//        if (!$cantingDetail) {
        // 从数据库获取
        $data = self::with(['liuyan', 'wenzhang'])->withCount(['liuyanCount'])->find($id);   //withCount(['liuyan_count'])->
        if ($data === false) {
            // *如果从数据库获取失败，记录日志
            Log::mysql_log('mysql/Canting/cantingDetail', '获取餐厅详细信息失败');
        }
        // 缓存
//            $name = 'cantingdetail' . $id;
//            $cache = cache($name, $data);
//            if (!$cache) {
//                // *如果缓存失败，记录日志
//                Log::redis_log('redis/Canting/cantingDetail', 'redis获取餐厅详细信息失败');
//            }
        throw new Success(['data' => $data]);
//        } else {
//            throw new Success(['data' => $cantingDetail]);
//        }

    }

    // 新增餐厅 || redis
    public static function createCanting($param)
    {
        // 新增餐厅也要删除redis餐厅列表缓存
        $data = self::create($param);
        if ($data === false) {
            // *新增餐厅到数据库失败，记录日志
            Log::mysql_log('mysql/Canting/createCanting', '新增餐厅失败');
        }
        // 新增成功后删除缓存
        $cantingList = Cache::rm('cantingList');
        if (!$cantingList) {
            // *删除餐厅列表缓存失败，记录日志
            Log::redis_log('redis/Canting/createCanting', 'redis删除餐厅列表失败');
        }
        throw new Success(['data' => $data]);
    }


    // 更新餐厅 || redis *(不知道为什么删不掉缓存，会留下cantingdetail,只删了后面的ID)
    public static function updateCanting($param)
    {
        // 更新数据库
        $data = self::update($param);
        // *是不是这样判断更新成功还是失败
        if ($data === false) {
            // *数据库更新失败，记录日志
            Log::mysql_log('mysql/Canting/updateCanting', '更新餐厅失败');
        }
        // 更新成功后删除redis相应的数据，cantingDetail，cantingList
//        $id = $param['id'];
//        $rm = "cantingdetail" . "-" . $param["id"];
        // 删除对应的餐厅详情
//        $cantingDetail = cache('cantingdetail' . $param["id"], NULL);
////        $cantingDetail = Cache::pull($rm);
//        if (!$cantingDetail) {
//            // *删除餐厅详情缓存失败，记录日志
//            Log::redis_log('redis/Canting/updateCanting', 'redis删除餐厅详情失败');
//        }

        // 删除餐厅列表
        $cantingList = Cache::rm('cantingList');
        if (!$cantingList) {
            // *删除餐厅列表缓存失败，记录日志
            Log::redis_log('redis/Canting/updateCanting', 'redis删除餐厅列表失败');
        }

//        Cache::rm('cantingdetail');

        throw new Success(['data' => $data]);
    }


    // 删除餐厅


    // 关联->菜品
//    public function caipin()
//    {
//        return $this->hasMany('caipin', 'canting_id', 'id');
//    }
//
//    // 关联->环境
//    public function huanjing()
//    {
//        return $this->hasMany('huanjing', 'canting_id', 'id');
//    }

    // 关联->文章
//    public function wenzhang()
//    {
//        return $this->hasOne('wenzhang', 'canting_id', 'id');
//    }
//
//    // 关联->留言->关联userinfo
//    public function liuyan()
//    {
//        return $this->hasMany('liuyan', 'canting_id', 'id')->order('create_time desc')->limit(5)->with(['liuyanuserinfo']);
//    }

}





<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/9 0009
 * Time: 上午 9:02
 */

namespace app\api\model;


use app\exception\Success;
use think\Cache;
use think\Model;

class Kajuan extends Model
{

    // 查询卡劵列表(客户端index页用)
    public static function select_Kajuan_Model(){

        // 先查redis，没有则查数据库
        $kajuanList = cache('kajuanList');
        if(!$kajuanList){
            // 查mysql并写入redis
            $data = self::where('state',1)->select();
            if($data === false){
                // 查询失败，记录日志，返回错误码
                Log::mysql_log('mysql/Kajuan/select_Kajuan','查询卡劵列表失败');
            }
            $cache = cache('kajuanList',$data);
            if(!$cache){
                // 缓存失败
                Log::redis_log('redis/Kajuan/select_Kajuan','redis写入卡劵列表失败');
            }
            // 返回
            throw new Success(['data'=>$data]);
        }else{
            // redis中有则直接返回
            throw new Success(['data'=>$kajuanList]);
        }
    }


    // 查询指定卡劵(客户端卡劵页用，接受卡劵ID(表id))
    public static function find_Kajuan_Model($id){

        // 先查redis，没有则查数据库
//        $kajuanDetail = cache('kajuanDetail-'.$id);
//        if(!$kajuanDetail){
            // 查mysql并写入redis
            $data = self::where('id',$id)->find();
            if($data === false){
                // 查询失败，记录日志，返回错误码
                Log::mysql_log('mysql/Kajuan/find_Kajuan','查询指定卡劵失败');
            }
//            $cache = cache('kajuanDetail-'.$id,$data);
//            if(!$cache){
//                // 缓存失败
//                Log::redis_log('redis/Kajuan/find_Kajuan','redis写入指定卡劵失败');
//            }
            // 返回
            throw new Success(['data'=>$data]);
//        }else{
//            // redis中有则直接返回
//            throw new Success(['data'=>$kajuanDetail]);
//        }

    }



    // 更新剩余数量(需要删除redis相应的数据，卡劵详情和卡劵列表)
    public static function update_Kajuan_Shengyushuliang_Model($id,$shengyushuliang){
        // 更新数据库
        $data = self::update(['id'=>$id,'shengyushuliang'=>$shengyushuliang]);
        if($data === false){
            // 更新失败，记录日志，返回错误码
            Log::mysql_log('mysql/Kajuan/update_Kajuan_Shengyushuliang','更新卡劵剩余数量失败');
        }
        // 更新成功删除redis相应数据（防止mysql更新了,但是redis没有更新，删除后用户再次进入卡劵详情页redis找不到数据就会重新写入，此时写入的是更新后的卡劵详情数据了）
        // 删除redis卡劵详情数据
//        $cache = Cache::rm('kajuanDetail-'.$id);
//        if(!$cache){
//            // 删除redis数据失败，记录日志，返回错误码
//            Log::redis_log('redis/Kajuan/update_Kajuan_Shengyushuliang','redis删除指定卡劵失败');
//        }
        // 删除redis卡劵列表数据
        $cache = Cache::rm('kajuanList');
        if(!$cache){
            // 删除redis数据失败，记录日志，返回错误码
            Log::redis_log('redis/Kajuan/update_Kajuan_Shengyushuliang','redis删除卡劵列表失败');
        }
        // 返回
        throw new Success(['data'=>$data]);
    }

}
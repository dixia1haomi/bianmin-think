<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/28 0028
 * Time: 下午 6:39
 */

namespace app\api\controller;

use app\api\model\Liuyan;
use app\api\model\User as userModel;
use app\api\service\BaseToken;
use app\exception\QueryDbException;
use app\exception\Success;


class User
{

    // 根据uid检查userinfo表中是否有用户信息,(有：返回errorCode = 0和查到的用户信息,没有：返回errorCode=1)（app.js初始化时调用）
//    public function uidCheckInfo(){
//
//        $uid = BaseToken::get_Token_Uid();
//
//        userinfoModel::uidCheckInfo($uid);
//    }


    // 用户登陆(检查是否有info信息，有再对比，不一样更新，没有新增)（app.js appData.loginState为false时调用）
//    public function userLogin()
//    {
//        $uid = BaseToken::get_Token_Uid();
//        $post = input('post.');
//
//        userinfoModel::login($uid,$post);
//    }



    // 查询我的留言（根据uid-客户端我的页-我的留言）(关联餐厅名)（排序-根据创建时间，分页-20条）
    public function getMyLiuyan(){
        $uid = BaseToken::get_Token_Uid();
        $post_page = input('post.page');

        // 根据uid查所有留言
        Liuyan::getMyLiuyan_Model($uid,$post_page);
    }

    // 查询我的话题（根据uid-客户端我的页-我的话题）(关联话题名)（排序-根据创建时间，分页-20条）
//    public function getMyHuati(){
//        $uid = BaseToken::get_Token_Uid();
//        $post_page = input('post.page');
//
//        // 根据uid查所有留言
//        Userhuati::getMyHuati_Model($uid,$post_page);
//    }

    // 查询用户参与发表的话题（接受uid，客户端-我的-话题）
//    public function userHuatiList()
//    {
//        $uid = BaseToken::get_Token_Uid();
//        $userModel = new userModel();
//
//        $data = $userModel->with(['userToUserhuati'=>function($query){
//            $query->with(['userhuatiToHuati']);
//        }])->find($uid);
//        if (!$data) {
//            throw new QueryDbException(['msg' => '查询用户参与发表的话题失败，User/userHuatiList']);
//        }
//        return $data;
//    }


    // 查询用户名下的所有信息，包含用户参与的话题，用户对餐厅的留言 （前置登陆）
//    public function userWithAll(){
//        // uid
//        $uid = BaseToken::get_Token_Uid();
//        // 关联查询
//        userinfoModel::userWithAll_Model($uid);
//    }


}
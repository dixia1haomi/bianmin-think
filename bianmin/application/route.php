<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//Route::rule('路由表达式','路由地址','请求类型','路由参数（数组）','变量规则（数组）');


use think\Route;


// 客服
Route::get('api/kefu/getkefu', 'api/kefu/getKefu');             // 客服接口  gzh_accsee_token

// ---------------------------------------------------------- Token ----------------------------------------------------------

Route::post('api/token/gettoken', 'api/token/getToken');   // 获取Token
Route::post('api/token/verify', 'api/token/verifyToken');  // 检查Token是否有效


// ---------------------------------------------------------- cos ----------------------------------------------------------

Route::post('api/cos/qianmingdanci', 'api/cos/cosQianMingDanci');   // COS签名-单次


// ---------------------------------------------------------- 登陆 ----------------------------------------------------------

Route::post('api/user/saveuserinfo', 'api/User/saveUserInfo');


// ---------------------------------------------------------- 便民信息 ----------------------------------------------------------

//增删改查
Route::post('api/xinxi/getlist', 'api/Xinxi/getList');                       // 获取列表
Route::post('api/xinxi/findbianmin', 'api/Xinxi/findBianmin');               // 查询单个便民信息
Route::post('api/xinxi/create', 'api/Xinxi/createList');                     // 创建信息
Route::post('api/xinxi/createimg', 'api/Xinxi/createImg');                   // 创建信息图片
Route::post('api/xinxi/incliulangcishu', 'api/Xinxi/incLiulangcishu');       // 增加浏览次数
Route::post('api/xinxi/xiugaineirong', 'api/Xinxi/edit_Bianmin_Neirong');    // 修改信息内容

// 留言回复
Route::post('api/xinxi/createbianminliuyan', 'api/Xinxi/create_Bianmin_Liuyan');           // 新增便民信息留言
Route::post('api/xinxi/createbianminhuifu', 'api/Xinxi/create_Bianmin_Huifu');             // 新增便民信息留言回复
Route::post('api/xinxi/updateformid', 'api/Xinxi/updateBianMinXinXiFormId');               // 更新便民信息formId,我的发布页，新留言提醒

// 我的
Route::post('api/xinxi/myfabu', 'api/Xinxi/getMyfabu');                 // 我的发布
Route::post('api/xinxi/deletemyfabu', 'api/Xinxi/deleteMyfabu');        // 删除我的发布
Route::post('api/xinxi/updatetime', 'api/Xinxi/updateTime');            // 刷新
Route::post('api/index/getphone', 'api/Index/getPhone');                // 获取电话

// 获取类目分类模板
//Route::post('api/xinxi/leimu', 'api/Xinxi/leimuMoBan');

// 分享信息二维码
Route::post('api/xinxi/erweima', 'api/Xinxi/get_XinXi_DingZhi_erweima');            // 获取信息顶置二维码
Route::post('api/xinxi/createbmxxdingzhi', 'api/Xinxi/createBmxxDingZhi_User');


// ---------------------------------------------------------- 商家 ----------------------------------------------------------

//Route::post('api/shangjia/createshangjia', 'api/Shangjia/createShangjia');                         // 新增商家
//Route::post('api/shangjia/findshangjia', 'api/Shangjia/findShangjia');                             // 查询商家详情
//Route::post('api/shangjia/selectshangjia', 'api/Shangjia/selectShangjia');                         // 查询商家列表
//Route::post('api/shangjia/myshangjia', 'api/Shangjia/getMyShangjia');                              // 我的店铺
//Route::post('api/shangjia/deleteshangjia', 'api/Shangjia/deleteMyShangjia');                       // 删除店铺
//Route::post('api/shangjia/shuaxin', 'api/Shangjia/shangJiaShuaXin');                               // 刷新
//
//
//// ----- 编辑商家基本资料 -----
//Route::post('api/shangjia/xiugaishangjiatoutu', 'api/Shangjia/xiugaiMyShangjia_toutu');            // 修改商家头图
//Route::post('api/shangjia/xiugaishangjianame', 'api/Shangjia/xiugaiMyShangjia_name');              // 修改商家名称
//Route::post('api/shangjia/xiugaishangjiadizhi', 'api/Shangjia/xiugaiMyShangjia_dizhi');            // 修改商家地址
//
//// ----- 编辑商家图文详情 -----
//Route::post('api/shangjia/createshangjiaimg', 'api/Shangjia/createShangjiaImg');                   // 新增商家IMG数据（新增商家img表一条数据）
//Route::post('api/shangjia/updateshangjiaimgtext', 'api/Shangjia/updateShangjiaImg_text');          // 修改商家IMG里的text字段
//Route::post('api/shangjia/updateshangjiaimgurl', 'api/Shangjia/updateShangjiaImg_url');            // 修改商家IMG里的url字段
//Route::post('api/shangjia/deleteshangjiaimg', 'api/Shangjia/deleteShangjiaImg');                   // 删除商家IMG数据（删除商家img表一条数据）


// ---------------------------------------------------------- 商家活动 ----------------------------------------------------------
//Route::post('api/huodong/createhuodong', 'api/Huodong/create_Huodong');                         // 新增活动
//Route::post('api/huodong/findhuodong', 'api/Huodong/find_Huodong');                             // 查询活动详情
//Route::post('api/huodong/selecthuodong', 'api/Huodong/select_Huodong');                         // 查询活动列表
//Route::post('api/huodong/deletehuodong', 'api/Huodong/delete_Huodong');                         // 删除活动
//
//
//// ----- 编辑活动基本资料 -----
//Route::post('api/huodong/xiugaihuodongtoutu', 'api/Huodong/xiugaiHuodong_toutu');           // 修改活动头图
//Route::post('api/huodong/xiugaihuodongbiaoti', 'api/Huodong/xiugaiHuodong_biaoti');         // 修改活动标题
//Route::post('api/huodong/xiugaihuodongyuanjia', 'api/Huodong/xiugaiHuodong_yuanjia');         // 修改活动原价
//Route::post('api/huodong/xiugaihuodonghuodongjia', 'api/Huodong/xiugaiHuodong_huodongjia');    // 修改活动价
//Route::post('api/huodong/xiugaihuodongshuliang', 'api/Huodong/xiugaiHuodong_shuliang');         // 修改活动数量
//Route::post('api/huodong/xiugaihuodongtiaojian', 'api/Huodong/xiugaiHuodong_tiaojian');         // 修改活动条件
//Route::post('api/huodong/xiugaihuodongtime', 'api/Huodong/xiugaiHuodong_time');         // 修改活动结束时间
//Route::post('api/huodong/xiugaihuodongshuoming', 'api/Huodong/xiugaiHuodong_shuoming');         // 修改活动说明
//
//
//// ----- 编辑活动图文详情 -----
//Route::post('api/huodong/createhuodongimg', 'api/Huodong/createHuodongImg');                 // 新增活动IMG数据（新增活动img表一条数据）
//Route::post('api/huodong/updatehuodongimgtext', 'api/Huodong/updateHuodongImg_text');          // 修改活动IMG里的text字段
//Route::post('api/huodong/updatehuodongimgurl', 'api/Huodong/updateHuodongImg_url');            // 修改活动IMG里的url字段
//Route::post('api/huodong/deletehuodongimg', 'api/Huodong/deleteHuodongImg');                   // 删除活动IMG数据（删除活动img表一条数据）


// ---------------------------------------- 参与活动 ------------------------------------
//Route::post('api/huodongcanyu/checkziji', 'api/Huodongcanyu/checkZiji');                 // 检查自己的参与结果
//Route::post('api/huodongcanyu/checkbieren', 'api/Huodongcanyu/checkBieren');             // 检查别人的参与结果
//Route::post('api/huodongcanyu/canyu', 'api/Huodongcanyu/create_Canyu');                  // 参与活动
//Route::post('api/huodongcanyu/zhuli', 'api/Huodongcanyu/create_Zhuli');                  // 为别人助力
//Route::post('api/huodongcanyu/lingqu', 'api/Huodongcanyu/create_Lingqu');                // 领取
//Route::post('api/huodongcanyu/erweima', 'api/Huodongcanyu/get_HuoDong_ZhuLi_erweima');   // 获取活动助力二维码
//Route::post('api/huodongcanyu/juandetail', 'api/Huodongcanyu/get_Lingqu_Detail');         // 查询活动劵详情
//Route::post('api/huodongcanyu/juanlist', 'api/Huodongcanyu/get_Lingqu_List');         // 查询活动劵列表
//Route::post('api/huodongcanyu/hexiao', 'api/Huodongcanyu/hexiaoHuodongJuan');            // 核销劵


//Route::post('api/huodongcanyu/ziji', 'api/Huodongcanyu/find_Canyu_ziji');                      // 查询自己的参与表数据
//Route::post('api/huodongcanyu/bieren', 'api/Huodongcanyu/find_Canyu_bieren');                  // 查询别人的参与表数据
//Route::post('api/huodongcanyu/lingqu', 'api/Huodongcanyu/create_Lingquhuodong');



// ---------------------------------------------------------- app.onError ----------------------------------------------------------

Route::post('api/index/onerror', 'api/index/create_App_onError');                               // 记录app.js的错误


// ---------------------------------------------------------- Admin ----------------------------------------------------------

Route::post('api/admin/getadmintoken', 'api/Admin/getAdminToken');                      // admin登陆验证
Route::post('api/admin/verifyadmintoken', 'api/Admin/verifyAdminToken');                // 检查adminToken是否有效
Route::post('api/admin/selectformiduser', 'api/Admin/selectFormId_User');               // 查询formid有效的用户


// ---------------------------------------------------------- crontab ----------------------------------------------------------

Route::post('api/crontab/checkbianmindingzhi', 'api/Crontab/crontab_Check_BianMin_Dingzhi_state');  // 更改便民信息表顶置状态
Route::post('api/crontab/deletebianmin', 'api/Crontab/crontab_Delete_BianMin');                     // 删除超过30天的便民信息
Route::post('api/crontab/checkuserformid', 'api/Crontab/crontab_CheckUserFormId');                  // 检查user表formId
Route::post('api/crontab/checkbianminlistformid', 'api/Crontab/crontab_CheckBianMinListFormId');    // 检查便民信息表formId
Route::post('api/crontab/checkliuyanformid', 'api/Crontab/crontab_CheckLiuYanFormId');              // 检查留言表formId
Route::post('api/crontab/renzhaoche', 'api/Crontab/crontab_delete_renzhaoche');                     // 删除人找车
Route::post('api/crontab/chezhaoren', 'api/Crontab/crontab_delete_chezhaoren');                     // 删除车找人

// 商家
//Route::post('api/crontab/shangjiashuaxin', 'api/Crontab/crontab_ShangJia_ShuaXin');

// ------------
//Route::post('api/ceshi/renzhaoche', 'api/Crontab/crontab_delete_renzhaoche');                     // 删除人找车


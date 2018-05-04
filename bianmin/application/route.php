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

// 餐厅
//Route::post('api/canting/list', 'api/canting/getList');                       // 获取餐厅列表 shoucangCantingList
//Route::post('api/canting/shoucanglist', 'api/canting/shoucangCantingList');   // 获取收藏的餐厅列表（我的-我的收藏使用）
//Route::post('api/canting/detail', 'api/canting/getDetail');                   // 获取餐厅详细信息，接受餐厅表ID
//Route::post('api/canting/createCanting', 'api/canting/createCanting');        // 新增餐厅
//Route::post('api/canting/updateCanting', 'api/canting/updateCanting');        // 更新餐厅
//Route::post('api/canting/deleteCanting', 'api/canting/deleteCanting');        // 删除餐厅
//Route::post('api/canting/zan', 'api/canting/dianzanCanting');                 // 点赞餐厅+1
//
//
//// 留言
//Route::post('api/liuyan/list', 'api/liuyan/liuyanList');                // 查询留言列表（根据餐厅ID）
//Route::post('api/liuyan/create', 'api/liuyan/createLiuyan');            // 新增留言（接受餐厅id,留言内容,uid内部获取）
////Route::post('api/liuyan/myliuyan', 'api/liuyan/getMyLiuyan');           // 查询我的留言（根据uid-客户端我的页-我的留言）
//Route::post('api/liuyan/delete', 'api/liuyan/deleteLiuyan');            // 删除留言（内部获取uid，接受id-客户端我的页-我的留言）
//
//
//
//// 文章
//Route::post('api/wenzhang/createwenzhang', 'api/wenzhang/createWenzhang');    // 新增文章
//Route::post('api/wenzhang/updatewenzhang', 'api/wenzhang/updateWenzhang');    // 更新文章
//Route::post('api/wenzhang/deletewenzhang', 'api/wenzhang/deleteWenzhang');    // 删除文章
//
//

//
//

//
//
//// User
////Route::post('api/user/login', 'api/user/userLogin');             // 用户登陆（获取userInfo）
////Route::post('api/user/huati', 'api/user/userHuatiList');         // 获取用户参与的话题列表
////Route::post('api/user/check', 'api/user/uidCheckInfo');          // 根据uid检查userinfo表中是否有用户信息
////Route::post('api/user/all', 'api/user/userWithAll');             // userWithAll
//Route::post('api/user/myliuyan', 'api/user/getMyLiuyan');        // 查询我的留言（根据uid-客户端我的页-我的留言）
////Route::post('api/user/myhuati', 'api/user/getMyHuati');          // 查询我的话题（根据uid查询）（我的页-我的话题）
//
//
//
//// 客服
////Route::get('api/kefu/getkefu', 'api/kefu/getKefu');             // *客服接口  gzh_accsee_token
////Route::post('api/kefu/gzhacc', 'api/Kefu/gzh_accsee_token');
////Route::post('api/kefu/code', 'api/Kefu/jiemi_opencard_code');
//
//// 卡劵
//Route::post('api/kajuan/select', 'api/Kajuan/select_Kajuan');                             // 查询优惠商家列表
//Route::post('api/kajuan/find', 'api/Kajuan/find_Kajuan');                                 // 查询指定卡劵(客户端卡劵页用，接受卡劵ID)
//Route::post('api/kajuan/shengyushuliang', 'api/Kajuan/update_shengyushuliang');           // 更新卡劵剩余数量
//Route::post('api/kajuan/code', 'api/Kajuan/jiemi_code');                                  // 解密wx.addCard成功后返回的code(接受加密code,用于wx.openCard)
//Route::post('api/kajuan/signature', 'api/Kajuan/get_kajuan_signature');                   // 获取卡劵signature（后续用于调用wx.addcard）
//Route::post('api/kajuan/log', 'api/Kajuan/user_card_log');                                // 卡劵领取记录(接受卡劵ID)
//
//Route::post('api/kajuan/create', 'api/Kajuan/createKajuan');                              // 新增卡劵
//Route::post('api/kajuan/update', 'api/Kajuan/updateKajuan');                              // 更新卡劵
//Route::post('api/kajuan/delete', 'api/Kajuan/deleteKajuan');                              // 删除卡劵
//
//
//
//// 测试专用
//Route::post('api/ceshi/index', 'api/ceshi/index');

// 客服
Route::get('api/kefu/getkefu', 'api/kefu/getKefu');             // 客服接口  gzh_accsee_token

// Token
Route::post('api/token/gettoken', 'api/token/getToken');   // 获取Token
Route::post('api/token/verify', 'api/token/verifyToken');  // 检查Token是否有效
//Route::post('api/token/app', 'api/token/getAppToken');     //第三方登录获取token

// cos
Route::post('api/cos/qianmingdanci', 'api/cos/cosQianMingDanci');   // COS签名-单次
//Route::post('api/cos/qianmingduoci', 'api/cos/cosQianMingDuoci');   // COS签名-多次
//Route::post('api/cos/delete', 'api/cos/cosdelete');                 // 删除

// 登陆
Route::post('api/user/saveuserinfo', 'api/User/saveUserInfo');

// 列表
Route::post('api/index/getlist', 'api/Index/getList');                       // 获取列表
Route::post('api/index/findbianmin', 'api/Index/findBianmin');               // 查询单个便民信息
Route::post('api/index/create', 'api/Index/createList');                     // 创建信息
Route::post('api/index/createimg', 'api/Index/createImg');                   // 创建图片
Route::post('api/index/incliulangcishu', 'api/Index/incLiulangcishu');       // 增加浏览次数
Route::post('api/index/xiugaineirong', 'api/Index/edit_Bianmin_Neirong');    // 修改便民信息内容

// 我的
Route::post('api/index/myfabu', 'api/Index/getMyfabu');                 // 我的发布
Route::post('api/index/deletemyfabu', 'api/Index/deleteMyfabu');        // 删除我的发布  updateTime
Route::post('api/index/updatetime', 'api/Index/updateTime');            // 刷新
Route::post('api/index/getphone', 'api/Index/getPhone');                // 获取电话

// 文章
//Route::post('api/index/wenzhanglist', 'api/Index/getWenzhangList');      // 获取文章列表
//Route::post('api/index/wenzhangdetail', 'api/Index/getWenzhangDetail');      // 获取文章列表

// 商家
Route::post('api/index/createshangjia', 'api/Index/createShangjia');                         // 新增商家
Route::post('api/index/createshangjiaimg', 'api/Index/createShangjiaImg');                   // 新增商家图片
Route::post('api/index/findshangjia', 'api/Index/findShangjia');                             // 查询商家详情
Route::post('api/index/selectshangjia', 'api/Index/selectShangjia');                         // 查询商家列表
Route::post('api/index/myshangjia', 'api/Index/getMyShangjia');                              // 我的店铺
Route::post('api/index/deleteshangjia', 'api/Index/deleteMyShangjia');                       // 删除店铺
Route::post('api/index/deleteshangjiaimg', 'api/Index/deleteMyShangjia_xiangqingtu_item');   // 删除商家详情图（每次1张）
Route::post('api/index/xiugaishangjiatoutu', 'api/Index/xiugaiMyShangjia_toutu');            // 修改商家头图
Route::post('api/index/xiugaishangjianame', 'api/Index/xiugaiMyShangjia_name');              // 修改商家名称
Route::post('api/index/xiugaishangjiamiaoshu', 'api/Index/xiugaiMyShangjia_miaoshu');        // 修改商家描述
Route::post('api/index/xiugaishangjiadizhi', 'api/Index/xiugaiMyShangjia_dizhi');            // 修改商家地址

// 留言
Route::post('api/liuyan/createbianminliuyan', 'api/Liuyan/create_Bianmin_Liuyan');           // 新增便民信息留言
Route::post('api/liuyan/createbianminhuifu', 'api/Liuyan/create_Bianmin_Huifu');             // 新增便民信息留言回复
//Route::post('api/liuyan/selectmyliuyan', 'api/Liuyan/selectMyLiuyan');
//Route::post('api/liuyan/huifuwode', 'api/Liuyan/huifuWode');
//Route::post('api/liuyan/xiaoxiapi', 'api/Liuyan/xiaoxiApi');                                  // 测试模板消息
Route::post('api/liuyan/updateformid', 'api/Liuyan/updateBianMinXinXiFormId');                // 更新便民信息formId,我的发布页，新留言提醒

//app.onError create_App_onError
Route::post('api/index/onerror', 'api/index/create_App_onError');                               // 记录app.js的错误

// Admin
Route::post('api/admin/getadmintoken', 'api/Admin/getAdminToken');                      // admin登陆验证
Route::post('api/admin/verifyadmintoken', 'api/Admin/verifyAdminToken');                // 检查adminToken是否有效
Route::post('api/admin/selectformiduser', 'api/Admin/selectFormId_User');               // 查询formid有效的用户
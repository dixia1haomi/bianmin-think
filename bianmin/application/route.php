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
Route::post('api/xinxi/leimu', 'api/Xinxi/leimuMoBan');

// 分享信息二维码
Route::post('api/xinxi/erweima', 'api/Xinxi/erweima');
Route::post('api/xinxi/createbmxxdingzhi', 'api/Xinxi/createBmxxDingZhi_User');


// ---------------------------------------------------------- 商家 ----------------------------------------------------------

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



//app.onError create_App_onError
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


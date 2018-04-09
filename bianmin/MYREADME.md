快捷键：
    格式化代码：Alt + Ctrl + L

起步流程：

    database返回Array,自动写入时间日期，'Y/m/d H:i:s',路由完整匹配，关闭日志记录.
    建立api模块..
    完成基本增上改查.
    完成各表IMG字段图片上传（*）


### 异常处理

        覆盖重写TP本身的异常处理类Handler里的render方法

        1.建立Exception目录

        2.创建ExceptionHandler类，继承Handler，创建3个自定义属性，实现render方法.
        render方法内部分为两块：
                     1.类型是BaseException的异常都是客户端传入了错误参数导致异常，需要返回客户端明确告知给用户的异常（如没有id=28的数据）。
                     2.否则就是服务器内部错误，不明确告诉用户的异常。错误码999（实现调用DEBUG作为开关在开发模式下返回原始的页面异常，便于开发）

        3.创建BaseException类，创建3个自定义属性，继承Exception，实现构造函数->
          用于让外面的异常可以接受自定义的参数：如：throw new testException(['msg'=>'查询数据不存在']);

        4.创建CheckParamException类，创建3个自定义属性，继承BaseException，用于参数验证错误抛出.

        5.创建QueryDbException类，创建3个自定义属性，继承BaseException，用于数据库查询错误抛出.

        6.配置config.异常处理Handle=>'app\exception\ExceptionHandler'


### 图片上传

        服务器只负责权鉴，主要实现都在小程序端.
        签名需要 SecretId 和 SecretKey，这两个信息不适合存放在客户端中，这也是我们单独部署一个鉴权服务器的主要原因。

        服务器端：引入phpSDK,实现签名接口.

        小程序端：https://github.com/tencentyun/wecos-ugc-upload-demo
        实现上传文件到COS

        删除图片：调用服务器PHPSDK删除接口.


### Token模块

        ## 生成token

         + 实现获取token接口
            (1).接受小程序code -> (2).拼接微信获取openid的url -> (3).发送网络请求获取微信返回的数据(openid) -> (4).数据库新增用户/返回uid -> (5).生成token缓存并返回客户端，详见代码（Token类，service）.

         + 实现检查token是否有效接口(详见代码，Token类)
         + 获取管理员token接口

        ## 从token中获取uid(*包含了检查用户权限*)(详见代码，BaseToken类)


### 星评

        ## 每个用户只能投票餐厅1次，用星评表记录已经投票过的餐厅，（投几星直接记录在餐厅表），客户端查询星评表实现功能
        user表关联 -> 星评表
        星评接口携带（餐厅ID，投几星，接口内部获取uid）(详见代码，Canting类星评接口)
        客户端取星评表数据缓存，遍历实现禁用


### 用户登陆

        *（以接口形式完成）调用接口之前，需要客户端授权.
        调用接口：检查userinfo表 -> 没有？新增，有？对比数据（用户修改过资料）.一样：返回，不一样：更新后返回

        自己对小程序登陆的理解（登陆接口不应该频繁调用）

         + 场景1：需要严格同步用户信息时每次调用
         + 场景2：按需调用一次就行，客户端设置登陆标识（App.js的全局data里），程序被销毁后标识也消失了（程序销毁：进入后台5分钟，或者用户主动删除）
         + 场景3：按需调用一次，缓存在客户端（除非用户主动删除，不然缓存一直在）

        # 美食小程序 采用场景3方案，用户发表话题时再次调用
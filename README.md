云印南开
=================
更方便的校园打印 [print.nkumstc.cn](http://print.nkumstc.cn)
----------------------------

## Bugs or Fault
* 缓存导致管理列表刷新的问题
* 打印列表翻页
* 文件删除无通知
* 上传页面UI
* 多文件不能一次
* 提前判断文件大小
* 低版本浏览器适配


## Features to add
* 打印店文件到达通知
* 邮箱绑定
* 状态更新通知提醒
* 打印店主页和自主管理
* 手机绑定
* 邮箱绑定和验证
* ...
* 神秘功能
* 更安全和开放的API接口（提供APP接入所有接口）
* ...
* 打印店客户端自动打印

##安全问题：
* 输入字段严格过滤
* https验证通道
* xss(httponly，反馈和打印店介绍)
* 隐私数据加密（手机号和邮箱保存加密）



## 团队主要人员及分工
#### 1.项目发起人
[李旭昇](https://github.com/jeffli678)
#### 2.指导和系统设计
[NewFuture](https://github.com/New-Future)
#### 3.数据库设计
[牛亮](https://github.com/wangxiaodiu) [梁崇军]()
#### 4.后端实现
[孙卓豪]() [牛亮](https://github.com/wangxiaodiu) [梁崇军]() [NewFuture](https://github.com/New-Future)
#### 5.前端实现
[杜晓唐]() [孙卓豪]() [王博]()
#### 6.打印店客户端
[宋剑超]()
#### 7.测试维护
[刘安]() [王雨晴]() [赵泽坤]()
#### 8.图形设计
[陈超]()
#### 9.运营推广
[崔金锐]()

##框架目录

使用是请将 `Common/Conf/secret.php.sample` 改成 `Common/Conf/secret.php` 修改相应配置

>
```
|─index.php    入口文件-->Print
|─api.php        api接口入口文件-->API
|
|─Common     后端公共模块目录
|    |─Common       公共库目录
|    |    |─Urp.class.php        urp验证
|    |    └─function.php        公共函数文件
|    └─Conf                公共配置目录
|         |─config.php             公共配置文件
|         |─secret.php             安全配置文件
|         └─config_sae.php     sae配置文件
|
|─Print            云印南开系统项目目录
|    |─Home            普通用户模块目录
|    |    |─Conf                  配置文件目录
|    |    |─Common        公共函数目录
|    |    |─Controller       控制器目录
|    |    |    |─IndexController.class.php      默认控制器（首页）
|    |    |    |─UserController.class.php        用户控制器
|    |    |    └─FileController.class.php          文件管理控制器
|    |    |─Model         模型目录
|    |    |    |─......                                                各种模型
|    |    |    └─UserModel.class.php               用户模型
|    |    └─View          模板视图目录
|    |         |─Index       默认模板目录
|    |         |─User        用户模板目录
|    |         └─File         文件模板目录
|    |
|    └─Printer     打印店管理模块目录
|         |─Conf           配置文件目录
|         |─Common        公共函数目录
|         |─Controller       控制器目录
|         |    |─IndexController.class.php         打印店控制器
|         |    |─PrinterController.class.php       打印店控制器
|         |    └─FileController.class.php              文件管理控制器
|         |─Model             模型目录
|         └─View                视图目录
|
|─API                  云印南开API模块
|    |─Conf                     配置文件目录
|    |─Common           公共函数目录
|    |─Controller          控制器目录
|    |    |─NotificationController.class.php    消息接口控制器
|    |    |─FileController.class.php                    文件接口控制器
|    |    └─IndexController.class.php               认证和令牌管理制器
|    └─Model                模型目录
|
|─Public             前端资源文件目录
|    |─css                      css文件目录
|    |─js                         javascript目录
|    |─images              图片目录
|    └─template          模板文件目录    
|
|─Uploads       上传文件目录（可写，不同步）
|─Runtime       运行时缓存目录（可写，不同步）
└─ThinkPHP    框架目录(框架核心资源不用修改)
```
>>



## 仓库分支说明
    包含web端，客户端，数据 三个稳定分支
1. [master](https://github.com/nkumstc/print/tree/master) web端源码仓库分支
2. [DB](https://github.com/nkumstc/print/tree/DB)     数据库设计源码仓库分支
3. [printer](https://github.com/nkumstc/print/tree/printer) 打印店客户端和源码仓库分支

##其他


API相关说明:[API.md](https://github.com/nkumstc/print/blob/master/API.md)

本项目由南开大学学生发起，免费开源，同时欢迎所有人贡献代码和想法

项目源码遵循apache2 开源协议

项目起步阶段由[南开大学微软技术俱乐部](http://nkumstc.cn)提供支持
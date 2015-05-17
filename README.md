云印南天
=================
创造更方便的校园服务[yunyin.org](http://yunyin.org)
-------------------------

Make a BETTER campus！

### 官方媒体

+ **微信公众号**：云印南天(`YunYinNanTian`) [点击查看](http://www.yunyin.org/assets/image/weixin_qrcode.png)
+ **官方网站**：[www.yunyin.org](http://www.yunyin.org) :heart:
+ **官方微博**：[云印南天](http://weibo.com/cloudPrint) (`cloudPrint`)
+ **人人主页**：@[云印南天](http://page.renren.com/602117408)
+ **联系邮箱**：contact#yunyin.org; join#yunyin.org(你懂的:smile:)

### Bugs or Fault
* 多文件不能一次
* 提前判断文件大小
* 未登录查看打印店等信息


### Features to add
* 打印店文件到达通知【doing】
* 图片打印支持
* 上传进度显示
* 逾期提醒
* 文件直传
* 打印店信息修改
* 状态更新通知提醒
* 文件分享
* 打印店主页和自主管理【done】
* 手机绑定 【done】
* 邮箱绑定和验证 【done】
* 校园卡招领功能 【done】
* 更安全和开放的API接口【done】
* ...
* 打印店客户端自动打印

### 安全问题：
* 输入字段严格过滤【always doing】
* xss(反馈和打印店介绍)【doing】
* API接口,token直接传递不够安全,时间戳加密(防止劫持)
* https验证通道【done】
* 涉及到修改密码的操作前端md5之后再传递【done】
* xss(httponly) 【done】
* 隐私数据加密（手机号和邮箱保存加密）【done】

### API相关说明:
采用`REST`风格的接口设计，所有接口文档和源码均对外开放

此处查看最新API文档[API.md](https://github.com/YunYinORG/print/blob/master/API.md)


##框架目录

使用是请将 `Common/Conf/secret.php.sample` 改成 `Common/Conf/secret.php` 修改相应配置

>
```
|─index.php    应用入口文件-->Print
|─api.php      api接口入口文件-->API
|
|─Common       后端公共模块目录
|    |─Common        公共库目录
|    |    |─Encrypt.php           加密函数库
|    |    └─function.php          公共函数文件
|    |─Model         公用模型
|    |    └─UserModel.class.php   用户模型（API和应用公用）
|    |─Verify        验证库
|    |    |─NankaiUrp.class.php   南开URP验证
|    |    └─TjuE.class.php        天大办公网验证
|    └─Conf          公共配置目录
|         |─config.php            公共配置文件
|         |─config_sae.php        SAE配置文件
|         └─secret.php            安全配置文件（不同步，根据secret.php.sample修改）
|
|─Print        云印南开系统项目目录
|    |-Admin         后台管理模块
|    |    |─Conf                  配置文件目录
|    |    |─Common                公共函数目录
|    |    |─Controller            后台控制器目录
|    |    |    |─IndexController.class.php      管理登录控制器
|    |    |    └─PrinterController.class.php    打印店注册管理控制器
|    |    |─Model                 模型目录
|    |    └─View                  模板视图目录
|    |         |─Index                          登录模板目录
|    |         └─Printer                        管理打印店模板目录
|    | 
|    |─Home          学生用户模块目录
|    |    |─Conf                  配置文件目录
|    |    |─Common                公共函数目录
|    |    |─Controller            用户控制器目录
|    |    |    |─IndexController.class.php       默认控制器（首页）
|    |    |    |─UserController.class.php        用户控制器
|    |    |    |─CardController.class.php        找回一卡通控制器
|    |    |    |─EmptyController.class.php       404控制器
|    |    |    |─PrintersController.class.php    打印店介绍控制器
|    |    |    └─FileController.class.php        文件操作控制器
|    |    |─Model                 模型目录
|    |    |    └─......                          各种模型
|    |    └─View                  用户模版目录
|    |         |─Index                           默认模板目录
|    |         |─User                            用户模板目录
|    |         |─Printers                        打印店介绍模板目录
|    |         |─Card                            找回一卡通模板目录
|    |         └─File                            文件模板目录
|    |
|    └─Printer       打印店管理模块
|         |─Conf                  配置文件目录
|         |─Common                公共函数目录
|         |─Controller            打印店控制器目录
|         |    |─IndexController.class.php       默认店控制器
|         |    |─PrinterController.class.php     打印店控制器
|         |    └─FileController.class.php        文件管理控制器
|         |─Model                 打印店模型目录
|         └─View                  打印店模版
|              |─Printer                         打印店模板目录
|              |─Index                           登录模板目录
|              └─File                            文件模板目录
|
|─API          云印南开API
|    |─Conf          配置文件目录
|    |─Common        公共函数目录
|    |-Model         公共模型目录
|    |-Verify        公共认证函数目录
|    |─Controller    控制器目录
|    |    |─NotificationController.class.php     消息接口控制器
|    |    |─FileController.class.php             文件接口控制器
|    |    |-IndexController.class.php            默认和连接测试制器
|    |    |-PrinterController.class.php          打印店接口控制器
|    |    |-TokenController.class.php            令牌接口控制器
|    |    └─UserController.class.php             学生用户接口控制器
|    |    
|    └─Model         模型目录
|
|─Public       前端资源文件目录[此目录对外开放]
|    |─css           css文件目录
|    |─js            javascript目录
|    |-images        图片资源目录
|    |─lib           引用的js以及css第三方通用库
|    |─fonts         字体目录
|    |─html          404等静态目录【待迁移】
|    └─template      模板文件目录【待迁移】 
|
|─Uploads      上传文件目录（可写，不同步，sae上不用）
|─Runtime      运行时缓存目录（可写，不同步，sae上不用）
└─ThinkPHP     框架目录(框架核心资源不用修改)
```
>>


### 仓库说明

包含`web服务器端`，`打印店客户端`，两个仓库

1. [print仓库](https://github.com/YunYinORG/print/) web端源码核心仓库分支
2. [DB分支](https://github.com/YunYinORG/print/tree/DB)     数据库设计源码仓库分支
3. [printer仓库](https://github.com/YunYinORG/printer) 打印店客户端和源码仓库分支
4. [yunyinorg.github.io](https://github.com/YunYinORG/yunyinorg.github.io)官方文档网站www.yunyin.org源码


### 支持和协议

云印项目由南开大学学生发起，收到南开和北洋两所学校共同支持，项目和平台免费开源，同时欢迎所有人贡献代码和想法

由南开微软俱乐部，天大微软俱乐部，南开机器人所，等组织和单位提高核心资源支持。由合作服务商提供重要服务资源支持。

由于对github稳定性的担忧和大家访问的便捷性，我们同时在国内两个git托管平台不定期推送最新代码镜像

* GitHub托管地址【主站,处理pull request和issues】[https://github.com/YunYinORG/print](https://github.com/YunYinORG/print)
* 开源中国oschina托管地址：[http://git.oschina.net/newfuture/print](http://git.oschina.net/newfuture/print)
* gitcafe代码托管地址：[https://gitcafe.com/NewFuture/print](https://gitcafe.com/NewFuture/print)

本项目源码遵循apache2 开源协议，同时尊重每一位贡献者的权益。

更多信息查看[www.yunyin.org](http://www.yunyin.org)
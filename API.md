API说明 v0.1
===========
api接口设计和调用方式
---------

# 接口设计

#### 模块设计
----
		Index 认证相关
		Notification 通知推送相关
		File 文件相关

#### REST设计风格
-----
	uri为资源地址
	对所有资源操作只有四种: 读取`get`，添加`post`，修改`put`，删除`delete`
	数据格式默认json，也支持xml
	认证方式uri?token=xxxxxxx;

# 接口调用方式
##### 入口为api.php

#### 1. 认证和令牌
------
##### * 令牌生成`/api.php` 
  **post** 生成: 
    示例url：`/api.php` 
    post参数：
     `type`=>用户类型1学生，2打印店
     `pwd`=>密码，
     `number`=>学号/`account`=>打印店账号
  返回令牌相关信息

##### * 令牌更新 `/api.php/Token`
  **put** 更新: 
     示例url：`/api.php/Token`
     put参数：
    `token`=>当前令牌
  返回新生成的令牌相关信息

#### 2. 消息 Notification
-----
##### * 获取消息列表 `/api.php/Notification/`
  **get**获取:
    示例url : `/api.php/Notification/?token=xxxxxxxxxxxx`
  返回未获取消息列表

##### * 获取消息 `/api.php/Notification/123`,
  **get**获取：
   示例url :`/api.php/Notification/123?token=xxxxxxxxxxxx`
  返回单条消息内容

  **delete**删除消息:
   示例url: `/api.php/Notification/123?token=xxxxxxxxxxxx`
  返回操作结果

#### 3. 文件 File
-----
##### * 文件列表 `/api.php/File/`
  **get**获取:
     示例url:`/api.php/File/?token=xxxxxxxxxxxx`
    参数
    `page`=>页数，可选参数,翻页使用如`/api.php/File/?page=2&token=xxxxxxxxxxxx`
  返回文件信息列表

##### * 文件操作 `/api.php/File/1234`
  **get** 文件详细信息
    示例url: `/api.php/File/1234?token=xxxxxxxxxxxx`
  返回文件信息信息

  **put** 文件状态修改
    示例url: `/api.php/File/1234?token=xxxxxxxxxxxx`
    参数：
    `status`=>文件状态'uploud','download','printing','printed','payed',
   返回操作结果


----
authored by [NewFuture]()
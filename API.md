API说明文档 *v1.3*
===========
API接口设计和调用方式
---------


# 一. API接口设计

### 模块
----
O.  API请求测试
1.  **Token** 认证登录相关
2.  **Notification** 通知推送相关
3.  **File** 文件相关
4.  **User** 用户信息相关
5.  **Printer** 打印店信息


### REST接口
-----
 uri为资源地址

 对所有资源操作只有四种: 
>
   读取`get`，
   添加`post`，
   修改`put`，
   删除`delete`
>

### 认证方式
-----
 从1.0版本之后,认证令牌需包含在uri请求header中;
 `"Token: replace_the_token_string_here"`
Token在通过API登录成功时，会得到一个32到48位的随机字符串作为访问令牌

### 返回数据（响应）
-----
返回数据格式默认`json`，也支持`xml`(均以json为例说明,根据实际请求格式返回）
数据格式包含在uri请求的header中的Accept参数

>
  请求返回json格式的数据 `"Accept: application/json"`
  请求返回xml格式的数据 `"Accept: application/xml"`
  未设置时返回json格式数据
>

 **操作失败**时,统一返回出错原因信息
 ````json
  {
    "err":"错误信息"
  }
 ````

常见错误信息
>
`"unauthored"` 未认证（使用的token无效）
`"unkown method"` 未知操作或不支持（如有的uri仅支持GET）
`"author failed"` 登录认证失败
>

# 二.接口调用方式
#### 入口为/api.php
````
baseURL="http://print.nkumstc.cn/api.php";
````

>
一下所有接口的的地址相对于此`baseURL`
如查询打印店的接口`/Printer/` ，则实际的`URI`是`http://print.nkumstc.cn/api.php/Printer/`
>


### 0. API请求测试
-----

0.1 API共有了六个公开的测试链接
数据格式支持`json`,`xml`和`html`

0.1.1 查看请求信息`/Index/test`
0.1.2 测试get 请求 `/Index/get`
0.1.3 测试post请求 `/Index/post`
0.1.4 测试put 请求 `/Index/put`
0.1.5 测试delete请求 `/Index/delete`
0.1.6 测试token传递 `/Index/token` 

0.2 请求请求举例，这里用curl操作，返回json为例

get测试
````cmd
curl -H "Accept: application/json" "baseURL/Index/get?data=testdata"
````
post测试
````cmd
curl -X POST -d "data=testdata" -H "Accept: application/json" "baseURL/Index/post"
````
put测试
````cmd
curl -X PUT -d "data=testdata" -H "Accept: application/json" "baseURL/Index/put"
````
get,post,put 测试有效时返回结果
````json
{"code":1,"param":{"data":"testdata"}}
````

delete 测试
````cmd
curl -X DELETE -H "Accept: application/json" "baseURL/Index/delete"
````
delete有效时的返回结果
````json
{"code":1}
````

token 测试 
````cmd
curl -H "Token: qLvF6BEhMHX0kNGTyxKQef6PjRNDS0dvz5xaKsTwyIDd5H0" -H "Accept: application/xml" "baseURL/Index/token"
````
token 读取成功时的返回结果（xml格式为例）
````xml
<?xml version="1.0" encoding="utf-8"?>
<think>
  <token>qLvF**********************S0dvz5xaKsTwyIDd5H0</token>
</think>
````


### 1. 登录和令牌 Token
------
##### 1.1 生成令牌(登录) `/Token`
 **post** 生成:
    URI操作示意：`POST /Token`
 
 post参数： 
 
  ````c
  type="类型";//type=4代表学生API,type=2代表打印店客户端API
  pwd="md5加密之后的密码";
  account="登录账号";//学生为学号(type为4),打印店为登录账号(type为2)
  ````

 登录成功时,返回令牌相关信息（json数组或者xml格式）

  ````json
  {
   "token"  : "生成的令牌",
   "name"   : "用户名称",
   "id"     : "用户id",
   "version": "API版本"
  }

  ````

##### 1.2 更新令牌 `/Token/xxxxxxxxxx` 
 **put** 更新: 
  
 URI操作示意：`PUT /Token/your_token_string_is_here`

操作成功,返回新生成的令牌相关信息
````json
{
  "token":"新生成的token"
}

````

##### 1.3 删除令牌（注销） `/Token/xxxxxxxxxx` 
 **delete** 删除: 
  
 URI操作示意：`DELETE /Token/your_token_string_is_here`

操作成功,返回
````json
{
  "msg":"删除成功"
}

````


### 2. 通知 Notification
-----

##### 2.1  获取通知列表 `/Notification/`
  **get**获取未删除通知列表:
  一次最多10条

  URI操作示意:

>
 `GET /Notification/`
 `GET /Notification/?page=2`
 `GET /Notification/?start=123&page=2`
>

  get参数:
  ````c
  start="起始id";//可选参数，从这一条之后开始读取通知列表

  page="读取页号";//可选参数，翻页操作，跳过之前页数，默认为1；
  ````

  获取成功,返回未获取通知列表（数组）
  ````json
  {
    "notices":
     [
        {
            "id": "通知id",
            "content": "通知内容"
        },
        {
            "id": "通知id",
            "content": "通知内容"
        }
    ]
  }
  ````

##### 2.2  单条通知操作

  2.2.1  **get**获取通知详情：

  URI操作示意 : `GET /Notification/123`

  读取成功，返回通知详细内容

````json
    {
      "id": "通知id",
      "content": "通知内容",
      "file": "关联文件id",
      "type": "int，消息类型和状态标识"
    }
 ````

  2.2.2  **delete**删除通知:

  URI操作示意: `DELETE /Notification/123`
  
  删除成功，返回操作消息

  ````json
  {
    "msg":"删除成功！"
  }
  ````

### 3. 文件 File
-----

##### 3.1  文件列表 `/File/`
  **get**获取文件列表:
     URI操作示意:`GET /File/`
   
   get参数
````c
  start="起始id";//可选参数，从这一条之后开始读取通知列表
  page="读取页号";//可选参数，翻页操作，跳过之前的页数，默认为1；

````

  获取成功，返回内容
  *打印店请求结果*  
````json
  {
    "files": 
    [
        {
           "id": "文件id",
           "use_id": "用户id",
           "name": "文件名",
           "url": "保存的url",
           "time": "上传时间",
           "status": "文件状态码",
           "copies": "份数",
           "double_side": "是否双面",
           "use_name": "用户名",
           "student_number": "学号"
        },
        {

          "id": "123",
          "pri_id": "1",
          "name": "文件.pdf",
          "url": "2015-02-05/54aa9abf29187.pdf",
          "time": "2015-02-05 22:08:00",
          "status": "1",
          "copies": "1",
          "double_side": "0"
          "use_name": "用户名",
          "student_number": "学号"
        }
    ]
  }
````
 *学生请求结果*
````json
{
  "files": 
  [
    {
        "id": "文件ID",
        "pri_id": "打印店ID",
        "name": "文件名",
        "time": "文件上传时间",
        "status": "文件状态码",
        "copies": "份数",
        "double_side": "是否双面"
    },
    {
        "id": "123",
        "pri_id": "1",
        "name": "文件.pdf",
        "time": "2015-02-05 22:08:00",
        "status": "1",
        "copies": "1",
        "double_side": "0"
    }
  ]
}
````

 *文件状态码对照*(status)
````
  1=>"已上传";//'uploud'
  2=>"已下载";//'download'
  3=>"正在打印";//'printing'
  4=>"已打印";//'printed'
  5=>"已付款";//'payed'
````

##### 3.2 单个文件操作 `/File/1234`
  
  3.2.1 **get** 文件详细信息
    URI操作示意: `GET /File/1234`
  
  获取成功返回文件详细信息
````json
{
    "id": "文件ID",
    "pri_id": "打印店ID",
    "use_id": "上传用户ID",
    "name": "文件名.pdf",
    "url": "文件地址",
    "time": "文件上传时间",
    "requirements": "文件备注说明",
    "copies": "份数",
    "double_side": "单双面",
    "status": "状态码"
}
````

  3.2.2 **put** 文件状态修改(**仅允许打印店操作**)
    更新文件状态，不可逆向修改，已删除或者已支付的文件不可修改
   
   URI操作示意: `PUT /File/1234`

   put参数：
   ````
    status="文件状态";//'download','printing','printed','payed'
                     //或者2,3,4,5
   ````

   操作成功，返回更新后的状态
   ````json
   {
      "status" : "修改后的状态码即2,3,4或5"
   }
   ````

  3.2.3 **delete** 删除文件(**仅允许学生用户操作**)
   文件删除操作，仅在文件刚上传(下载之前)和已支付的状态才允许删除
   
   URI操作示意: `DELETE /File/1234`

   操作成功，返回操作提示
   ````json
   {
      "msg" : "删除成功！"
   }
   ````

  3.2.4 **post** 上传文件(**仅允许学生用户操作**)
   尚未开放

### 4. 用户 User
-----

##### 4.1  获取自己的信息(仅限学生使用) `/User`
  **get**获取:
  
  URI操作示意: `GET /User`

  成功返回用户信息
  ````json
  
  {
    "id": "用户id",
    "student_number": "学号",
    "name": "姓名",
    "gender": "性别",
    "phone": "电话********(已打码)",
    "email": "邮***箱(已打码)"
  }

  ````

##### 4.2  查看用户详情(显示无码信息) `/User/123`

 **get** 获取
 
 URI操作示意: `GET /User/123`

 获取成功返回用户详细信息

 ````json
 {
    "id": "用户id",
    "name": "姓名",
    "student_number": "学号",
    "gender": "性别",
    "phone": "真实手机号",
    "email": "真实邮箱",
    "status": "用户状态标识码"
 }
 ````

### 5. 打印店 Printer
-----

##### 5.1  获取打印店列表 `/Printer/`
  
  **get**获取:(此接口公开,未登录亦可访问)
  
  URI操作示意: `GET /Printer/`

  get支持参数:
  ````c
  start="起始id";//可选参数，从这一编号之后开始读取打印店

  page="读取页号";//可选参数，翻页操作，跳过之前页数，默认为1；
  ````

  读取成功成功返回打印店列表
  
````json
  {
    "printers":
     [
        {
            "id": "打印店编号（ID）",
            "name": "打印店名称",
            "address": "打印店地址"
        },
        {
            "id": "id",
            "name": "名称",
            "address": "地址"
        }
     ]
  }

````

##### 5.2  查看打印店详情 `/Printer/1`

 **get** 获取
 
 URI操作示意: `GET /Printer/1`

 获取成功返回打印店详细信息(未完待续)

 ````json
  {
    "id": "1",
    "name": "打印店名称",
    "address": "打印店地址",
    "phone": "打印店手机号",
    "qq": "打印店QQ"
  }
 ````

----
authored by [NewFuture](https://github.com/New-Future)
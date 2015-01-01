<?php
// ===================================================================
// | FileName: 	/Common/Conf/config.php 全局配置
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014 云印南开团队 All rights reserved.
// +------------------------------------------------------------------
return array(
    //调试信息
	'SHOW_PAGE_TRACE' => 1,
    //验证方式
    'VERIFY_WAY'=>'Common.Urp',

    //URL设定
    'URL_PARAMS_BIND'       =>  TRUE,
    'URL_MODEL'=>2,
    'MODULE_ALLOW_LIST'    =>    array('Home','Printer',),
	'DEFAULT_MODULE'       =>    'Home',  // 默认模块

    //数据库配置
	'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'print',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  '',    // 数据库表前缀
   
    //模板渲染转义
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => '/Public',
        '__JS__' => '/Public/js',
        '__CSS__' => '/Public/css',
        '__IMG__' => '/Public/img',
        '__UPLOAD__' => '/Uploads',
    ) ,

    //用户登录类型
	'STUDENT' => 1,
	'PRINTER' => 2,
    'PRINTER_WEB' => 3,
    //文件状态
    'FILE_DELETED'=>0,
    'FILE_UPLOAD'=>1,
    'FILE_DOWNLOAD'=>2,
    'FILE_PRINT'=>3,
    'FILE_PRINTED'=>4,
    'FILE_PAID'=>5,
);

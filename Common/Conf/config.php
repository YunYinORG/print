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
	'SHOW_PAGE_TRACE' => 1,
	//'配置项'=>'配置值'
	// 'MODULE_ALLOW_LIST'    =>    array('Home','Printer'),

	'DEFAULT_MODULE'       =>    'Home',  // 默认模块
	 
	'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'print',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  '',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    //模板渲染转义
    'TMPL_PARSE_STRING' => array(
        '__PUBLIC__' => '/Public',
        '__JS__' => '/Public/js',
        '__CSS__' => '/Public/css',
        '__IMG__' => '/Public/img',
        '__UPLOAD__' => '/Uploads',
    ) ,
/*
    'DB_PREFIX'             =>  '',    // 数据库表前缀
    'DB_FIELDTYPE_CHECK'    =>  false,       // 是否进行字段类型检查
    'DB_FIELDS_CACHE'       =>  true,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8
    'DB_DEPLOY_TYPE'        =>  0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'DB_RW_SEPARATE'        =>  false,       // 数据库读写是否分离 主从式有效
    'DB_MASTER_NUM'         =>  1, // 读写分离后 主服务器数量
    'DB_SLAVE_NO'           =>  '', // 指定从服务器序号
    'DB_SQL_BUILD_CACHE'    =>  false, // 数据库查询的SQL创建缓存
    'DB_SQL_BUILD_QUEUE'    =>  'file',   // SQL缓存队列的缓存方式 支持 file xcache和apc
    'DB_SQL_BUILD_LENGTH'   =>  20, // SQL缓存的队列长度
    'DB_SQL_LOG'            =>  false, // SQL执行日志记录
    'DB_BIND_PARAM'         =>  false, // 数据库写入数据自动参数绑定
    'DB_DEBUG'              =>  false,  // 数据库调试模式 3.2.3新增 
*/
    'URL_PARAMS_BIND'       =>  TRUE,
	'URL_MODEL'=>2,

	'STUDENT' => 1,
	'PRINTER' => 2,
    'PRINTER_WEB' => 3,

    'FILE_DELETED'=>0,
    'FILE_UPLOAD'=>1,
    'FILE_DOWNLOAD'=>2,
    'FILE_PRINT'=>3,
    'FILE_PRINTED'=>4,
    'FILE_PAID'=>5,
);

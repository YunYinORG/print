<?php
// ===================================================================
// | FileName: 	/Common/Conf/config.php 全局配置
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014-2015 云印南开团队 All rights reserved.
// +------------------------------------------------------------------
return array(
	//安全配置文件，相同内容可以覆盖配置文件
	'LOAD_EXT_CONFIG' => 'secret',
	'COOKIE_HTTPONLY' => true, //开启httponly,禁止js读取cookie
	//调试信息
	'SHOW_PAGE_TRACE' => 1,

	//SQL生成缓存
	'DB_SQL_BUILD_CACHE' => true,

	'NKU_OPEN'   => true,

	//URL设定
	'URL_PARAMS_BIND' => TRUE,
	'URL_MODEL'       => 2,
	'URL_HTML_SUFFIX' => '',
	'MODULE_ALLOW_LIST' => array('Home', 'Printer', 'Admin'),
	'DEFAULT_MODULE' => 'Home', // 默认模块

	//数据库配置
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_PORT'   => '3306', // 端口
	'DB_PREFIX' => '', // 数据库表前缀

	//模板渲染转义
	'TMPL_PARSE_STRING' => array(
		'__PUBLIC__' => __ROOT__.'/Public',
		'__CDNLIB__' => __ROOT__.'/Public/lib',
		'__UPLOAD__' => __ROOT__.'/Uploads',
	),

	//用户登录类型
	'ADMIN'       => 0,
	'STUDENT'     => 1,
	'PRINTER'     => 2,
	'PRINTER_WEB' => 3,
	'STUDENT_API' => 4,

	//文件状态
	'FILE_DELETED'  => 0,
	'FILE_UPLOAD'   => 1,
	'FILE_DOWNLOAD' => 2,
	'FILE_PRINT'    => 3,
	'FILE_PRINTED'  => 4,
	'FILE_PAID'     => 5,

	//最大尝试次数
	'MAX_TRIES' => 10,

	//打印ppt格式
	'PPT_LAYOUT' => array(
		0 => '-',
		1 => '1X1',
		2 => '2X3',
		3 => '2X4',
		4 => '3X3',
	),

	//验证方式
	'VERIFY_NKU' => 'Verify.nankai_urp',
	'VERIFY_TJU' => 'Verify.tju_e',
	'VERIFY_TIFERT'=>'Verify.tifert_jw',
	//验证正则表达式
	'REGEX_NUMBER'     => '/^(\d{7}|\d{10})$/', //学号正则7或者10
	'REGEX_NUMBER_NKU' => '/^(([1][0-4]\d{5})|([1|2]1201[0-4]\d{4}))$/', //南开学号本科7硕博10
	'REGEX_NUMBER_TJU' => '/^[1-3]01[0-4]\d{6}$/', //天大学号10
	'REGEX_NUMBER_TIFERT'=>'/^((0[1-4][01][0-9])|(1[139][05][1-4]))1[2-5]0\d{3}$/',//天津商职10位
	'REGEX_ACCOUNT'    => '/^\w{3,16}$/', //打印店账号正则
	'REGEX_TOKEN'      => '/^\w{38,48}$/',
	'REGEX_PHONE'      => '/^1[34578]\d{9}$/',
	'REGEX_EMAIL'      => '/^[\w\.\-]{1,17}@[A-Za-z,0-9,\-,\.]{1,30}\.[A-Za-z]{2,6}$/',

	//文件上传相关配置
	'FILE_UPLOAD_TYPE' => 'QINIU', //默认存储提供商LOACL SAE QINIU
	'FILE_UPLAOD_CONFIG' => array( //默认配置
		'maxSize'  => 10485760, //10 * 1024 * 1024,//文件大小
		'rootPath' => './Uploads/',
		'exts' => array('pdf', 'doc', 'docx', 'wps', 'ppt', 'pptx'),
		'saveName' => array('uniqid', ''),
	),

	'LANG_SWITCH_ON' => true, // 开启语言包功能
	// 'LANG_AUTO_DETECT' => true, // 自动侦测语言 开启多语言功能后有效
	// 'LANG_LIST'        => 'zh-cn', // 允许切换的语言列表 用逗号分隔
	// 'VAR_LANGUAGE'     => 'l', // 默认语言切换变量

	/*HTTPS安全连接是否开启，Home/Conf/config.php有对应的safe_url为https*/
	'HTTPS_ON' => true, //本地调试开发时，在secret中设置'HTTPS_ON'=>false；
	'BASE_URL' => 'http://yunyin.org', //本地调试开发时，在secret中设置'BASE_URL'=>'http://localhost';

);

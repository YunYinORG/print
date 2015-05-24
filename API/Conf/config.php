<?php
// ===================================================================
// | FileName: 	/API/Conf/config.php api配置
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014-2015 云印南天团队 All rights reserved.
// +------------------------------------------------------------------

return array(
	
	//'配置项'=>'配置值'
	'URL_ROUTER_ON' => true,
	
	'URL_ROUTE_RULES' => array(
		array('File/:id','File/read','',array('method'=>'GET')),
		array('File/:id','File/set','',array('method'=>'PUT')),
		array('File/:id','File/del','',array('method'=>'DELETE')),
		array('File','File/upload','',array('method'=>'POST')),
		
		'Notification/:id' => 'Notification/id',
		'User/:id' => 'User/id',
		'Printer/:id' => 'Printer/id',
		'Token/:token'=>'Token/token',
	) ,

	'API_VERSION'=>1.53,
);


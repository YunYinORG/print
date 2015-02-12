<?php

// ===================================================================
// | FileName: 		NotificationController.class.php
// ===================================================================
// | Discription：	NotificationController 消息查询接口
//		<命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014 云印南开团队 All rights reserved.
// +------------------------------------------------------------------

/**
 * Class and Function List:
 * Function list:
 * - auth()
 * Classes list:
 */

/**
 *get_token()
 *尝试从请求中获取token
 *@return string token
 *@version 1.1
 */
function get_token() 
{
	return I('server.HTTP_TOKEN',false,'/^\w{33,50}$/');
}

/**
 *auth_token()
 *验证信息
 *@return array  $info 验证失败返回空值null
 *					$info['id']用户id
 *					$info['type']用户类型
 *@version 1.1
 *@author NewFuture
 */
function auth() 
{
	$token = get_token();
	return ($token?auth_token($token):false);
}

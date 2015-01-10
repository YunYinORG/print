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
 *auth_token()
 *验证信息
 *@return array  $info 验证失败返回空值null
 *					$info['id']用户id
 *					$info['type']用户类型
 *@version 1.0
 *@author NewFuture
 */
function auth() 
{
	$token = I('get.token',null,'/^\w{32,63}$/');
	return ($token?auth_token($token):false);
}

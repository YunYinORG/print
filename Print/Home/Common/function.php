<?php

// ===================================================================
// | FileName: 		/Print/Printer/Common/function.php
// ===================================================================
// | Printer 打印管理端公用函数库
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014 云印南开团队 All rights reserved.
// +------------------------------------------------------------------

/**
 * Class and Function List:
 * Function list:
 * - use_id()
 * Classes list:
 */

/**
 *pri_id()
 *获取打印店id，后端验证
 *如果未登录使用cookie自动登陆并更新session
 *@param $redirect_url 重定向url,空不跳转
 *@return int 打印店id
 */
function use_id($redirect_url = null) 
{
	$id           = session('use_id');
	if ($id) 
	{
		return $id;
	} else
	{
		$token = I('cookie.token', null, '/^\w{32,63}$/');
		if ($token) 
		{
			$info  = auth_token($token);
			if ($info['type'] == C('STUDENT')) 
			{
				session('use_id', $info['id']);
				return $info['id'];
			}
		}
	}
	
	if ($redirect_url) 
	{
		redirect($redirect_url);
	} else
	{
		return 0;
	}
}

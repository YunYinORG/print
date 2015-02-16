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
 * - student_number()
 * Classes list:
 */

/**
 *use_id()
 *获取用户id，后端验证
 *如果未登录使用cookie自动登陆并更新session
 *@param $redirect_url 重定向url,空不跳转
 *@return int 用户id
 */
function use_id($redirect_url = null) 
{
	$id           = session('use_id');
	if ($id) 
	{
		return $id;
	} else
	{
		$token = I('cookie.token', null, C('REGEX_TOKEN'));
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

/**
 *student_number()
 *获取学号，后端验证
 *如果未登录使用cookie自动登陆并更新session
 *@param $redirect_url 重定向url,空不跳转
 *@return string 用户学号
 */
function student_number($redirect_url = null) 
{
	$sid          = session('student_number');
	if ($sid) 
	{
		return $sid;
	} else
	{
		$uid = use_id($redirect_url);
		if ($uid) 
		{
			$sid = M('User')->getFieldById($uid, 'student_number');
			if ($sid) 
			{
				session('student_number', $sid);
				return $sid;
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

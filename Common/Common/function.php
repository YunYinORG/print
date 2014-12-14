<?php

// ===================================================================
// | FileName: 	/Common/Common.function.php 公共函数库
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014 云印南开团队 All rights reserved.
// +------------------------------------------------------------------


/**
* Class and Function List:
* Function list:
* - token()
* - encode()
* Classes list:
*/


/**
 * token($id)
 *生成唯一的token令牌
 *@param 验证对象id
 *@return 字符串
 *@author NewFuture
 */
function token($id) 
{
	$str = md5(str_shuffle($id . time()));
	return $id . $str;
}

/**
 *encode($pwd, $id)
 *密码加密
 *@param $pwd 原始密码
 *@param $id验证对象id
 *@return 字符串
 *@author NewFuture
 */
function encode($pwd, $id) 
{
	return crypt($pwd, $id);
}

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
 * - update_token()
 * - delete_token()
 * - auth_token()
 * - encode()
 * - delete_file()
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
 *update_token($id, $type)
 *生成和更新token
 *@param  int $id 用户id
 *@param  int $type用户类型，读取配置
 *@return mixed
 *			stinrg  生成或者更新的token字符串
 *			bool flase 生成失败
 *@version 1.0
 *@author NewFuture
 */
function update_token($id, $type) 
{
	
	switch ($type) 
	{
	case C('STUDENT'):
	case C('PRINTER'):
	case C('PRINTER_WEB'):
		
		// code...
		break;

	default:
		
		return false;
	}
	
	$data['to_id']       = $id;
	$data['type']       = $type;
	$Token = M('token');
	$Token->where($data)->delete();
	$data['token'] = token($id);
	if ($Token->add($data)) 
	{
		return $data['token'];
	} else
	{
		return false;
	}
}

/**
 *delete_token($id, $type)
 *删除token
 *@param  mixed 用户id 或者token值
 *@param  int $type=null用户类型，读取配置
 *						缺省，直接删除token
 *@return int/false  删除失败返回false
 *@version 1.0
 *@author NewFuture
 */
function delete_token($info, $type = null) 
{
	
	switch ($type) 
	{
	case C('STUDENT'):
	case C('PRINTER'):
		$data['to_id']      = $info;
		$data['type']      = $type;
		break;

	default:
		$data['token'] = $info;
		break;
	}
	return M('token')->where($data)->delete();
}

/**
 *auth_token($token)
 *验证token信息
 *@param  string $token token值
 *@return array  $info 验证失败返回空值null
 *					$info['id']用户id
 *					$info['type']用户类型
 *@version 1.0
 *@author NewFuture
 */
function auth_token($token) 
{
	return M('token')->cache(true, 60)->field(array('type', 'to_id' => 'id'))->getByToken($token);
}

/**
 *encode($pwd, $id)
 *密码加密
 *@param $pwd 原始密码
 *@param $id验证对象账号
 *@return 字符串
 *@author NewFuture
 */
function encode($pwd, $id) 
{
	return crypt($pwd, $id);
}

/**
 *delete_file($path)
 *删除上传文件
 *@param $path 文件路径
 *@author NewFuture
 */
function delete_file($path) 
{
	
	switch (C('FILE_UPLOAD_TYPE')) 
	{
	case 'Sae':
		$arr      = explode('/', ltrim($path, './'));
		$domain   = array_shift($arr);
		$filePath = implode('/', $arr);
		$s        = Think\Think::instance('SaeStorage');
		return $s->delete($domain, $filePath);
		break;

	default:
		return @unlink($path);
		break;
	}
}

/**
 *cache_name($type,$id)
 *缓存key生成，用于对打印店和用户的
 *@param $path 文件路径
 *@author NewFuture
 */
function cache_name($type,$id)
{
	switch ($type) {
		case 'printer':
			return 'PFV_'.$id;
			break;
		case 'user':
			return 'UFV_'.$id;
		default:
			return $type.'_'.$id;
			break;
	}
}

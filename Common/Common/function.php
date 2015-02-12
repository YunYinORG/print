<?php

// ===================================================================
// | FileName: 	/Common/Common.function.php 公共函数库
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014-2015 云印南开团队 All rights reserved.
// +------------------------------------------------------------------

/**
 * Class and Function List:
 * Function list:
 * - token()
 * - update_token()
 * - delete_token()
 * - auth_token()
 * - encode_old()
 * - encode()
 * - delete_file()
 * - cache_name()
 * - send_mail()
 * - send_sms()
 * - random()
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
	return $id . random(1, 'W') . random(30) . random(1, 'W') . time();
}

/**
 *update_token($info, $type=null)
 *生成和更新token
 *@param  mixed $info 用户id或者token值
 *@param  int $type用户类型，读取配置
 *@return mixed
 *			stinrg  生成或者更新的token字符串
 *			bool flase 生成失败
 *@version 1.1
 *@author NewFuture
 */
function update_token($info, $type  = null) 
{
	$Token = M('token');
	switch ($type) 
	{
	case C('STUDENT'):
	case C('PRINTER'):
	case C('PRINTER_WEB'):
	case C('STUDENT_API'):
		$data['to_id']       = $info;
		$data['type']       = $type;
		
		//删除之前token；再更新token
		$Token->where($data)->delete();
		$token = token($info);
		$data['token']       = md5($token);
		if (!$Token->add($data)) 
		{
			return false;
		}
		break;

	default:
		if (!preg_match('/^\d+/', $info, $result)) 
		{
			return false;
		}
		$id    = $result[0];
		$data['to_id']       = $id;
		$data['token']       = md5($info);
		$token = token($id);
		if (!$Token->where($data)->save(array('token' => md5($token)))) 
		{
			return false;
		}
		break;
	}
	
	return $token;
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
		$data['token'] = md5($info);
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
 *@version 2.0
 *@author NewFuture
 */
function auth_token($token) 
{
	return M('token')->field(array('type', 'to_id' => 'id'))->getByToken(md5($token));
}

/**
 *encode_old($pwd, $id)
 *原始密码加密
 *过渡使用，全部更新后，去掉此函数
 *@param $pwd 原始密码
 *@param $id验证对象账号
 *@return 字符串
 *@author NewFuture
 */
function encode_old($pwd, $id) 
{
	return crypt($pwd, $id);
}

/**
 *encode($pwd, $id)
 *密码加密
 * 新密码加密过程 md5(pwd)——>crypt()->md5()
 *@param $pwd 原始密码
 *@param $id验证对象账号
 *@return 字符串
 *@author NewFuture
 */
function encode($pwd, $id) 
{
	return md5(crypt($pwd, $id));
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
 *@return string 缓存字段名
 *@author NewFuture
 */
function cache_name($type, $id) 
{
	switch ($type) 
	{
	case 'printer':
		return 'PFV_' . $id;
		break;

	case 'user':
		return 'UFV_' . $id;
	default:
		return $type . '_' . $id;
		break;
	}
}

/**
 *send_mail($toMail,$content,$mailType)
 *发送邮件
 *@param $toMail 收件人邮箱
 *@param $content string 邮件主要内容
 *@param $mailType 邮件类型
 *@return bool 是否发送成功
 */
function send_mail($toMail, $content, $mailType) 
{
}

/**
 *send_sms($toMail,$content,$mailType)
 *发送短信
 *@param $toPhone 接收手机号
 *@param $content mixed 信息主要内容
 *@param $smsType 短信类型
 *@return bool 是否发送成功
 */
function send_sms($toPhone, $content, $smsType) 
{
}

/**
 *random($n,$mode='')
 *生成n位随机字符串
 *@param int $n 字符个数
 *@param string $mode='' 生成方式
 *				''默认快速生成不重复字符串
 *				包含'N':Number数字，
 *				包含'W':Word包含所有字母（=L+U）,
 *				包含'L':Low小写字母，
 *				包含'U':Up大写字母
 *@return string n位随机字符串
 *
 *@example  $str=random(4，'N');快速生成4位随机数字
 *			$str=random(16,'NU');生成由数字和大写字母组成的16位字符串
 *			$str=random(32);生成32位任意随机字符串
 */
function random($n, $mode = '') 
{
	
	//10位一下的数字使用随机数快速生成
	if ($n < 10 && $mode == 'N') 
	{
		$max  = pow(10, $n);
		return substr($max + rand(1, $max), -$n);
	}
	
	$str = '';
	
	//是否含有数字
	if (strstr($mode, 'N') != null) 
	{
		$str.= '1234567890';
	}
	
	//是否含由字母或者大小写
	if (strstr($mode, 'W') != null) 
	{
		$str.= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	} elseif (strstr($mode, 'L') != null) 
	{
		$str.= 'abcdefghijklmnopqrstuvwxyz';
	} elseif (strstr($mode, 'U') != null) 
	{
		$str.= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}
	
	//默认全部使用
	if (!$str) 
	{
		$str.= 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}
	
	$str = str_repeat($str, $n * 10 / strlen($str) + 1);
	return substr(str_shuffle($str), 0, $n);
}

function qiniu_encode($str) // URLSafeBase64Encode
{
    $find = array('+', '/');
    $replace = array('-', '_');
    return str_replace($find, $replace, base64_encode($str));
}
 
 
function qiniu_sign($url) 
{//$info里面的url
    $setting = C ( 'UPLOAD_SITEIMG_QINIU' );
    $duetime = NOW_TIME + 86400;//下载凭证有效时间
    $DownloadUrl = $url . '?e=' . $duetime;
    $Sign = hash_hmac ( 'sha1', $DownloadUrl, $setting ["driverConfig"] ["secrectKey"], true );
    $EncodedSign = Qiniu_Encode ( $Sign );
    $Token = $setting ["driverConfig"] ["accessKey"] . ':' . $EncodedSign;
    $RealDownloadUrl = $DownloadUrl . '&token=' . $Token;
    return $RealDownloadUrl;
}

?>

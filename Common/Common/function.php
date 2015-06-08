<?php
// ===================================================================
// | FileName: 	/Common/Common/function.php 公共函数库
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
 * - upload_file()
 * - delete_file()
 * - download_file()
 * - get_user_by_phone()
 * - get_user_by_email()
 * - get_phone_by_id()
 * - random()
 * - send_mail()
 * - send_sms_code()
 * - check_sms_code()
 * Classes list:
 */
/**
 * 生成唯一的token令牌
 * @method token
 * @param  [int/string] $id                [对象id]
 * @return [string]     [token字符串]
 */
function token($id)
{
	return $id.random(1, 'W').random(30).random(1, 'W').time();
}

/**
 * 生成和更新token 并保持到数据库
 * @method update_token
 *
 * @author 云小印[xxx@yunyin.org]
 *
 * @param  mixed $info                        用户id或者token值
 * @param  int   $type                        	用户类型，读取配置
 * @return mixed 操作成功返回token值
 */
function update_token($info, $type = null)
{
	$Token = M('token');
	switch ($type)
	{
		case C('ADMIN'):
		case C('STUDENT'):
		case C('PRINTER'):
		case C('PRINTER_WEB'):
		case C('STUDENT_API'):
			$data['to_id'] = $info;
			$data['type'] = $type;
			$Token->where($data)->delete(); 	//删除之前的token；再更新token
			$token = token($info);
			$data['token'] = md5($token);
			if ( ! $Token->add($data))
			{
				return false;
			}
			break;
		default:
			if ( ! preg_match('/^\d+/', $info, $result))
			{
				return false;
			}
			$id = $result[0];
			$data['to_id'] = $id;
			$data['token'] = md5($info);
			$token = token($id);
			if ( ! $Token->where($data)->save(array('token' => md5($token))))
			{
				return false;
			}
			break;
	}

	return $token;
}

/**
 * 删除token
 * @method delete_token
 *
 * @author NewFuture[NewFuture@yunyin.org]
 *
 * @param  mixed 用户id         或者token值
 * @param  int   $type            用户类型，读取配置
 * @return [int] [删除结果]
 */
function delete_token($info, $type = null)
{
	switch ($type)
	{
		case C('STUDENT'):
		case C('PRINTER'):
		case C('STUDENT_API'):
		case C('PRINTER_WEB'):
			$data['to_id'] = $info;
			$data['type'] = $type;
			break;
		default:
			$data['token'] = md5($info);
			break;
	}
	return M('token')->where($data)->delete();
}

/**
 * 验证token信息
 * @method auth_token
 *
 * @author NewFuture[NewFuture@yunyin.org]
 *
 * @param  [string] $token
 * @return [array]  [验证信息包含用户id，和type]
 */
function auth_token($token)
{
	return M('token')->field(array('type', 'to_id' => 'id'))->getByToken(md5($token));
}

/**
 * 旧的加密函数
 * 过渡使用，全部更新后，去掉此函数
 * @method encode_old
 * @param  string      $pwd                    原始密码
 * @param  string      $id验证对象账号
 * @return 字符串
 */
function encode_old($pwd, $id)
{
	return crypt($pwd, $id);
}

/**
 * 密码加密
 * 新密码加密过程 md5(pwd)—>crypt()->md5()
 * @method  encode
 * @param  string $pwd                    md5之后的密码
 * @param  int    $id                     验证对象账号
 * @return string 加密之后的密码
 */
function encode($pwd, $id)
{
	return md5(crypt($pwd, $id));
}

/**
 * 获取上传token
 * @method upload_token
 * @param  [string]       $save_name [保存的文件名]
 * @return [string]                  [上传token]
 * @author NewFuture[newfuture@yunyin.org]
 */
function upload_token($save_name)
{
	$config = C('UPLOAD_CONFIG_QINIU');
	$timeout= 300;
	$setting = array(
		'scope' => $config['bucket'].':'.$save_name,
		 'deadline' => $timeout + time(),
	  	);
	$token = \Think\Upload\Driver\Qiniu\QiniuStorage::SignWithData($config['secretKey'], $config['accessKey'], json_encode($setting));
	return $token;
}


function get_thumbnail_url($name)
{
    $config = C('UPLOAD_CONFIG_QINIU');
    $key    = str_replace('/', '_', $name);
    $qiniu   = new \Think\Upload\Driver\Qiniu\QiniuStorage($config);
    $fops = 'odconv/jpg/page/1/density/150/quality/80/resize/800';
    $url = "http://".$config['domain'].$key."?".$fops;
    $deadline = time() + 3600;
    $pos = strpos($url, '?');
    if ($pos !== false) {
        $url .= '&e=';
    } else {
        $url .= '?e=';
    }
    $url .= $deadline;
    $token = $qiniu->sign($config['secretKey'],$config['accessKey'],$url);
    return $url."&token=".$token;
}

/**
 * 上传文件
 * upload_file($storage='')
 * @param  $storage=''存储服务商 默认读取FILE_UPLOAD_TYPE
 * @param  $config=array()            自定义配置，可以覆盖默认配置
 * @return mixed                      上传信息
 */
function upload_file($storage = '', $config = array())
{
	if (empty($_FILES))
	{
		return false;
	}
	$config = array_merge(C('FILE_UPLAOD_CONFIG'), $config); //基本设置
	$driver_config = ''; //驱动配置
	if ( ! $storage)
	{
		$storage = C('FILE_UPLOAD_TYPE');
	}
	switch (strtoupper($storage))
	{
		case 'QINIU':
			$driver = 'QINIU';
			$driver_config = C('UPLOAD_CONFIG_QINIU'); 	//上传驱动的配置
			break;
		case 'SAE':
			$driver = 'SAE';
			break;
		case 'LOCAL':
			$driver = 'LOCAL';
			break;
		default:
			$driver = C('FILE_UPLOAD_TYPE'); 	//读取默认上传类型和配置
			break;
	}

	$Upload = new \Think\Upload($config, $driver, $driver_config);
	return $Upload->upload();
}

/**
 * 文件重命名
 * @method rename_file
 * @param  [type]      $old_name [原文件位置]
 * @param  [type]      $new_name [新文件位置]
 * @return [bool]                [重名结果]
 * @author NewFuture[newfuture@yunyin.org]
 */
function rename_file($old_name,$new_name)
{
	$config = C('UPLOAD_CONFIG_QINIU');
	$old_url    = str_replace('/', '_', $old_name);
	$new_url = str_replace('/', '_', $new_name);
	$qiniu   = new \Think\Upload\Driver\Qiniu\QiniuStorage($config);
	return $qiniu->rename($old_url, $new_url);
}


/**
 *  删除上传文件
 * @method delete_file($path)
 *
 * @author NewFuture
 *
 * @param $path       文件路径(url)
 * @param $storage='' 存储驱动        LOCAL QINIU SAE 等
 */
function delete_file($path, $storage = '')
{
	if ( ! $storage)
	{
		$storage = C('FILE_UPLOAD_TYPE');
	}
	switch ($storage)
	{
		case 'Sae':
			$arr       = explode('/', ltrim($path, './'));
			$domain    = array_shift($arr);
			$file_path = implode('/', $arr);
			$s         = Think\Think::instance('SaeStorage');
			return $s->delete($domain, $file_path);
			break;
		case 'QINIU':
			$setting = C('UPLOAD_CONFIG_QINIU');
			$setting['timeout'] = 300;
			$url    = str_replace('/', '_', $path);
			$qiniu  = new Think\Upload\Driver\Qiniu\QiniuStorage($setting);
			$result = $qiniu->del($url);
			return true;
			break;
		default:
			return @unlink('./Uploads/'.$path);
			break;
	}
}

/**
 * 下载文件
 * @method download_file
 *
 * @param  string $url                         [保存的URL]
 * @param  string $param                       [额外参数，可为空]
 * @param  string $storage                     [指定存储驱动存储空间 LOCAL QINIU SAE]
 * @return string [用于下载文件的URL]
 */
function download_file($url, $param = '', $storage = '')
{
	if ( ! $storage)
	{
		$storage = C('FILE_UPLOAD_TYPE');
	}
	switch ($storage)
	{
		case 'QINIU':
			$setting = C('UPLOAD_CONFIG_QINIU');
			$url = 'http://'.$setting['domain'].str_replace('/', '_', $url);
			$duetime = NOW_TIME + 86400; 	//下载凭证有效时间
			$download_url = $url.'?'.$param.'&e='.$duetime;
			$sign  = hash_hmac('sha1', $download_url, $setting['secretKey'], true);
			$encoded_sign = str_replace(array('+', '/'), array('-', '_'), base64_encode($sign));
			$token = $setting['accessKey'].':'.$encoded_sign;
			$real_download_url = $download_url.'&token='.$token;
			return $real_download_url;
			break;
		case 'LOCAL':
			return '/Uploads/'.$url;
			break;
		default:
			return '/Uploads/'.$url;
			break;
	}
}

/**
 * 根据手机号查找用户
 * @method get_user_by_phone($phone)
 * @param  $phone 电话号码
 * @return array  返回一个或者全部查找结果
 */
function get_user_by_phone($phone)
{
	import('Common.Encrypt', COMMON_PATH, '.php');
	$tail = encrypt_end(substr($phone, -4));
	$where['phone'] = array('LIKE', '%%'.$tail);
	$info = M('User')
		->where($where)
		->field('id,student_number,phone')
		->select();
	if ( ! $info)
	{
		return false;
	}
	foreach ($info as $user)
	{
		if ($phone == decrypt_phone($user['phone'], $user['student_number'], $user['id']))
		{
			return $user;
		}
	}
	return false;
}

/**
 * 根据邮箱查找用户
 * @method get_user_by_email($email)
 * @param  $email 邮箱
 * @return int    返回对应用户的id
 */
function get_user_by_email($email)
{
	if (strpos($email, '@') == 1)
	{
		$q1 = '`email` LIKE "'.substr_replace($email, '%', 1, 0).'"';
		$q2 = 'length(`email`)<'.(strlen($email) + 23);
		$condition = $q1.' AND '.$q2;
		$id = M('User')
			->where($condition)->getField('id');
	}
	else
	{
		import('Common.Encrypt', COMMON_PATH, '.php');
		$en_email = encrypt_email($email);
		$id = M('User')->getFieldByEmail($en_email, 'id');
	}
	return $id;
}

/**
 * 根据id查找用户
 * @method get_phone_by_id($id)
 * @param  $id    电话号码
 * @return string 返回号码
 */
function get_phone_by_id($id)
{
	if ( ! $id)
	{
		return false;
	}
	$user = M('User')->field('student_number,phone')->getById($id);
	if ($user)
	{
		import('Common.Encrypt', COMMON_PATH, '.php');
		return decrypt_phone($user['phone'], $user['student_number'], $id);
	}
	return false;
}

/**
 * 生成n位随机字符串
 * 				''默认快速生成不重复字符串
 * 				包含'N':Number数字，
 * 				包含'W':Word包含所有字母（=L+U）,
 * 				包含'L':Low小写字母，
 * 				包含'U':Up大写字母
 * @method random($n,$mode='')
 *
 * @author NewFuture
 *
 * @param  int    $n                     字符个数
 * @param  string $mode=''               生成方式
 * @return string n位随机字符串
 */
function random($n, $mode = '')
{
	if ($n < 10 && $mode == 'N') //10位一下的数字使用随机数快速生成
	{
		$max = pow(10, $n);
		return substr($max + rand(1, $max), -$n);
	}

	$str = '';
	if (strstr($mode, 'N') != null) //是否含有数字
	{
		$str .= '1234567890';
	}
	if (strstr($mode, 'W') != null) //是否含由字母或者大小写
	{
		$str .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}
	elseif (strstr($mode, 'L') != null)
	{
		$str .= 'abcdefghijklmnopqrstuvwxyz';
	}
	elseif (strstr($mode, 'U') != null)
	{
		$str .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}
	if ( ! $str) //默认全部使用
	{
		$str .= 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}

	$str = str_repeat($str, $n * 10 / strlen($str) + 1);
	return substr(str_shuffle($str), 0, $n);
}

/**
 * 发送邮件
 * 如果sae环境会先尝试sae发送然后调用phpmailer
 * @method send_mail
 * @version 2.0
 *
 * @author NewFutre[newfuture@yunyin.org]
 *
 * @param  [string/array] $to          [收件人邮箱]
 * @param  [array]  $info             [邮箱信息'title'标题,'content'内容]
 * @param  [array]  $config           [邮件发送配置]
 * @return [bool]   [发送结果]
 */
function send_mail($to, $info, $config)
{
	/*判断是否包含收件人姓名*/
	if(is_array($to))
	{
		$to_mail=$to['email'];
		$to_name=$to['name'];
	}else{
		$to_mail=$to;
		$to_name=$to;
	}

	/*发邮件*/
	$Mail = new \Vendor\Mail(C('MAIL_SMTP'));
	$Mail->addTo($to_mail,$to_name)
	     ->setLogin($config['email'], $config['pwd'])
	     ->setFrom($config['email'], $config['name'])
	     ->setSubject($info['title'])
	     ->setMessage($info['content'].L('MAIL_SIGN'), true);
	return $Mail->send();
}

/**
 * 给用户发送验证码
 * @method send_sms_code($phone,$code,$type)
 *
 * @author NewFuture
 *
 * @param  $phone 手机码
 * @param  $type  类型
 * @return string 返回号码
 */
function send_sms_code($phone, $type)
{
	$info = S($type.$phone);
	if ($info)
	{
		if ($info['times'] > 5)
		{
			\Think\Log::record('手机号验证发送失败：ip:'.get_client_ip().',phone:'.$phone);
			return 0;
		}
		else
		{
			$code = $info['code'];
			$info['times'] = $info['times'] + 1;
		}
	}
	else
	{
		$code = random(6, 'N');
		$info['code'] = $code;
		$info['times'] = 0;
		$info['tries'] = 0;
	}
	S($type.$phone, $info, 600);
	$SMS = new \Vendor\Sms();
	switch ($type)
	{
		case 'bind':
			return $SMS->bindPhone($phone, $code);
			break;
		case 'findPwd':
			return $SMS->findPwd($phone, $code);
			break;
		default:
			E('unknow sms type ');

	}
}

/**
 * 验证手机验证码
 * @method check_sms_code
 *
 * @author NewFuture
 *
 * @param  $phone 手机码
 * @param  $code  	验证码
 * @param  $type  类型
 * @return bool   true 验证成功	 false 验证失败	 尝试次数达到限制	 null 验证信息不存在
 */
function check_sms_code($phone, $code, $type)
{
	$info = S($type.$phone);
	if ($info)
	{
		if ($info['code'] == $code)
		{
			S($type.$phone, null);
			return true;
		}
		elseif ($info['tries'] >= 5)
		{
			S($type.$phone, null);
			return 0;
		}
		else
		{
			$info['tries'] = $info['tries'] + 1;
			S($type.$phone, $info, 600);
			return false;
		}
	}
	else
	{
		return null;
	}
}

?>

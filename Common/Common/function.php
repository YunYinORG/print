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
 * - send_mail()
 * - send_sms()
 * - random()
 * - download()
 * - get_user_by_phone()
 * - get_user_by_email()
 * - get_phone_by_id()
 * - send_sms_code()
 * - check_sms_code()
 * Classes list:
 */

/**
 * token($id)
 *生成唯一的token令牌
 *@param 验证对象id
 *@return 字符串
 *@author NewFuture
 */
function token($id) {
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
function update_token($info, $type = null) {
	$Token = M('token');
	switch ($type) {
		case C('ADMIN'):
		case C('STUDENT'):
		case C('PRINTER'):
		case C('PRINTER_WEB'):
		case C('STUDENT_API'):
			$data['to_id'] = $info;
			$data['type'] = $type;

			//删除之前token；再更新token
			$Token->where($data)->delete();
			$token = token($info);
			$data['token'] = md5($token);
			if (!$Token->add($data)) {
				return false;
			}
			break;

		default:
			if (!preg_match('/^\d+/', $info, $result)) {
				return false;
			}
			$id = $result[0];
			$data['to_id'] = $id;
			$data['token'] = md5($info);
			$token = token($id);
			if (!$Token->where($data)->save(array('token' => md5($token)))) {
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
function delete_token($info, $type = null) {

	switch ($type) {
		case C('STUDENT'):
		case C('PRINTER'):
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
 *auth_token($token)
 *验证token信息
 *@param  string $token token值
 *@return array  $info 验证失败返回空值null
 *					$info['id']用户id
 *					$info['type']用户类型
 *@version 2.0
 *@author NewFuture
 */
function auth_token($token) {
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
function encode_old($pwd, $id) {
	return crypt($pwd, $id);
}

/**
 *encode($pwd, $id)
 *密码加密
 * 新密码加密过程 md5(pwd)—>crypt()->md5()
 *@param $pwd 原始密码
 *@param $id验证对象账号
 *@return 字符串
 *@author NewFuture
 */
function encode($pwd, $id) {
	return md5(crypt($pwd, $id));
}

/**
 *upload_file($storage='')
 *上传文件
 *@param $storage=''存储服务商 默认读取FILE_UPLOAD_TYPE
 *@param $config=array() 自定义配置，可以覆盖默认配置
 *@return mixed 上传信息
 */
function upload_file($storage = '', $config = array()) {
	if (empty($_FILES)) {
		return false;
	}
	//基本设置
	$config = array_merge(C('FILE_UPLAOD_CONFIG'), $config);
	//驱动配置
	$driverConfig = '';
    if(!$storage){
        $storage=C('FILE_UPLOAD_TYPE');
    }
	switch (strtoupper($storage)) {
		case 'QINIU':
			$driver = 'QINIU';
			//上传驱动的配置
			$driverConfig = C('UPLOAD_CONFIG_QINIU');
			break;

		case 'SAE':
			$driver = 'SAE';
			break;

		case 'LOCAL':
			$driver = 'LOCAL';
			break;
		default:
			//读取默认上传类型和配置
			$driver = C('FILE_UPLOAD_TYPE');
			break;
	}

	$Upload = new \Think\Upload($config, $driver, $driverConfig);
	return $Upload->upload();
}

/**
 *delete_file($path)
 *删除上传文件
 *@param $path 文件路径
 *@param $storage='' 存储服务商
 *@author NewFuture
 */
function delete_file($path, $storage = '') {
	if (!$storage) {
		$storage = C('FILE_UPLOAD_TYPE');
	}
	switch ($storage) {
		case 'Sae':
			$arr = explode('/', ltrim($path, './'));
			$domain = array_shift($arr);
			$filePath = implode('/', $arr);
			$s = Think\Think::instance('SaeStorage');
			return $s->delete($domain, $filePath);
			break;

		case 'QINIU':
			$setting = C('UPLOAD_CONFIG_QINIU');
			$setting['timeout'] = 300;
			$url = str_replace('/', '_', $path);
			$qiniu = new Think\Upload\Driver\Qiniu\QiniuStorage($setting);
			$result = $qiniu->del($url);
			return true;
			break;

		default:
			return @unlink("./Uploads/" . $path);
			break;
	}
}

/**
 *send_mail($toMail,$msg,$mailType)
 *发送邮件
 *@param $toMail 收件人邮箱
 *@param $msg string 邮件主要内容
 *@param $mailType 邮件类型
 *@return bool 是否发送成功
 */
function send_mail($toMail, $msg, $mailType) {
	switch ($mailType) {
		case 1:
			//绑定验证邮箱
			$title = '云印邮箱绑定验证';
			$content = '欢迎加入云印的大家庭哦！<br/>云小印专注于为您提供最方便快捷的校园打印体验，让您随时打印，随手可取，无需“忧”盘！<br/>请点击以下链接绑定此的邮箱：(' . $toMail . ") <a href='$msg'>$msg</a>";
			$mailConfig = C('MAIL_VERIFY');
			break;

		case 2:
			//找回密码
			$title = '云印找回密码邮件';
			$content = '您正在云印南天使用密码找回,<br>请点击以下链接重设密码：' . " <a href='$msg'>$msg</a>";
			$mailConfig = C('MAIL_VERIFY');
			break;

		case 3:
			$title = '云印校园卡认领通知';
			$content = $msg;
			$mailConfig = C('MAIL_NOTIFY');
			break;

		default:
			$title = '来自云印的通知'; //无论如何都应该有一个标题
			$content = $msg;
			$mailConfig = C('MAIL_NOTIFY');
			//直接发送
			break;
	}

	switch (C('MAIL_WAY')) {
		case 'sae':	// sae mail
			$mail = new SaeMail();
			$opt = array(
				'content_type' => 'HTML',
				'from' => $mailConfig['email'],
				'to' => $toMail,
				'content' => $content,
				'subject' => $title,
				'smtp_host' => C('MAIL_SMTP'),
				'smtp_username' => $mailConfig['email'],
				'smtp_password' => $mailConfig['pwd'],
				'nickname' => $mailConfig['name'],
			);
			$mail->setOpt($opt);
			$ret = $mail->send();
			if (!$ret) {
				\Think\Log::record("saemail error:" . $mail->errno() . ":" . $mail->errmsg(), 'WARN', true);
				unset($mail);
			} else {
				break;
			}

		case 'phpmailer':
		default:
			$mail = new \Vendor\PHPMailer();
			$mail->AddAddress($toMail);
			$mail->Subject = $title;
			$mail->Body = $content;
			$mail->IsSMTP();
			$mail->IsHTML(true);
			$mail->SMTPAuth = true;
			$mail->CharSet = 'UTF-8';
			$mail->Host = C('MAIL_SMTP');
			$mail->From = $mailConfig['email'];
			$mail->Username = $mailConfig['email'];
			$mail->Password = $mailConfig['pwd'];
			$mail->FromName = $mailConfig['name'];
			try
			{
				//no use
				$mail->Send();
			} catch (phpmailerException $e) {
				\Think\Log::record("phpmail error:" . $e, 'WARN', true);
				return 0;
			}
	}
	return 1;
}

/**
 *send_sms($toPhone,$content,$smsType)
 *发送短信
 *@param $toPhone 接收手机号
 *@param $content mixed 信息主要内容
 *@param $smsType 短信类型
 *@return bool 是否发送成功
 */
function send_sms($toPhone, $content, $smsType) {
	switch ($smsType) {
		case 1:

			//验证码
			if (C('SMS_SUPPORTER') == 'huyi') {
				$msg = rawurlencode('您的验证码是：' . $content . '。请不要把验证码泄露给其他人。');
				$tid = null;
			} else {
				$msg = 'yunyin.org,' . $content . ',5';
				$tid = 3620;
			}
			break;
		case 2:
			//找回密码
			if (C('SMS_SUPPORTER') == 'huyi') {
				$msg = rawurlencode('您的验证码是：' . $content . '。请不要把验证码泄露给其他人。');
				$tid = null;
			} else {
				$msg = 'yunyin.org,' . $content . ',5';
				$tid = 4003;
			}
			break;
		case 3:
			//通知学子卡丢失
			if (C('SMS_SUPPORTER') == 'huyi') {
				$msg = null;
				$tid = null;
			} else {
				$msg = $content['recv_name'] . ',' . $content['send_name'] . ',' . $content['send_phone'];
				$tid = 4134;
			}
		default:

			// code...
			break;
	}

	$SMS = new Common\Common\Sms();
	return $SMS->sendSms($toPhone, $msg, $tid);
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
function random($n, $mode = '') {

	//10位一下的数字使用随机数快速生成
	if ($n < 10 && $mode == 'N') {
		$max = pow(10, $n);
		return substr($max + rand(1, $max), -$n);
	}

	$str = '';

	//是否含有数字
	if (strstr($mode, 'N') != null) {
		$str .= '1234567890';
	}

	//是否含由字母或者大小写
	if (strstr($mode, 'W') != null) {
		$str .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	} elseif (strstr($mode, 'L') != null) {
		$str .= 'abcdefghijklmnopqrstuvwxyz';
	} elseif (strstr($mode, 'U') != null) {
		$str .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}

	//默认全部使用
	if (!$str) {
		$str .= 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}

	$str = str_repeat($str, $n * 10 / strlen($str) + 1);
	return substr(str_shuffle($str), 0, $n);
}

function download($url,$param='',$storage='') {
    if(!$storage){
        $storage=C('FILE_UPLOAD_TYPE');
    }
	switch ($storage) {
		case 'QINIU':
			$setting = C('UPLOAD_CONFIG_QINIU');
			$url = 'http://' . $setting['domain'] . str_replace('/', '_', $url);
			$duetime = NOW_TIME + 86400;

			//下载凭证有效时间
			$DownloadUrl = $url . '?' . $param . '&e=' .$duetime ;
			$Sign = hash_hmac('sha1', $DownloadUrl, $setting["secretKey"], true);
			$EncodedSign = str_replace(array('+', '/'), array('-', '_'), base64_encode($Sign));
			$Token = $setting["accessKey"] . ':' . $EncodedSign;
			$RealDownloadUrl = $DownloadUrl . '&token=' . $Token ;
			return $RealDownloadUrl;
			break;

		case 'LOCAL':
			return "/Uploads/" . $url;
			break;

		default:
			return "/Uploads/" . $url;
			break;
	}
}

/**
 *get_user_by_phone($phone)
 *根据手机号查找用户
 *@param $phone 电话号码
 *@return array 返回一个或者全部查找结果
 */
function get_user_by_phone($phone) {
	import('Common.Encrypt', COMMON_PATH, '.php');
	$tail = encrypt_end(substr($phone, -4));
	$where['phone'] = array('LIKE', '%%' . $tail);
	$info = M('User')->where($where)->field('id,student_number,phone')->select();
	if (!$info) {
		return false;
	}
	foreach ($info as $user) {
		if ($phone == decrypt_phone($user['phone'], $user['student_number'], $user['id'])) {
			return $user;
		}
	}
	return false;
}

/**
 *get_user_by_email($email)
 *根据邮箱查找用户
 *@param $email 邮箱
 *@return int 返回对应用户的id
 */
function get_user_by_email($email) {
	if (strpos($email, '@') == 1) {
		$q1 = '`email` LIKE "' . substr_replace($email, '%', 1, 0) . '"';
		$q2 = 'length(`email`)<' . (strlen($email) + 23);
		$condition = $q1 . ' AND ' . $q2;
		$id = M('User')->where($condition)->getField('id');
	} else {
		import('Common.Encrypt', COMMON_PATH, '.php');
		$en_email = encrypt_email($email);
		$id = M('User')->getFieldByEmail($en_email, 'id');
	}
	return $id;
}

/**
 *get_phone_by_id($id)
 *根据id查找用户
 *@param $id  电话号码
 *@return string 返回号码
 */
function get_phone_by_id($id) {
	if (!$id) {
		return false;
	}
	$user = M('User')->field('student_number,phone')->getById($id);
	if ($user) {
		import('Common.Encrypt', COMMON_PATH, '.php');
		return decrypt_phone($user['phone'], $user['student_number'], $id);
	}
	return false;
}

/**
 *send_sms_code($phone,$type)
 *给用户发送验证码
 *@param $phone  手机码
 *@param $type   类型
 *@return string 返回号码
 */
function send_sms_code($phone, $type) {
	$info = S($type . $phone);
	if ($info) {
		if ($info['times'] > 5) {
			\Think\Log::record('手机号验证发送失败：ip:' . get_client_ip() . ',phone:' . $phone);
			return 0;
		} else {
			$code = $info['code'];
			$info['times'] = $info['times'] + 1;
		}
	} else {
		$code = random(6, 'N');
		$info['code'] = $code;
		$info['times'] = 0;
		$info['tries'] = 0;
	}
	S($type . $phone, $info, 600);
	switch ($type) {
		case 'bind':
			return send_sms($phone, $code, 1);
			break;
		case 'findPwd':
			return send_sms($phone, $code, 2);
			break;
		default:
			//code..
	}
}

/**
 *check_sms_code($phone,$code,$type)
 *验证手机验证码
 *@param $phone  手机码
 *@param $code 	验证码
 *@param $type   类型
 *@return bool true 验证成功
 *			  false 验证失败
 *			  0 尝试次数达到限制
 *			  null 验证信息不存在
 */
function check_sms_code($phone, $code, $type) {
	$info = S($type . $phone);
	if ($info) {
		if ($info['code'] == $code) {
			S($type . $phone, null);
			return true;
		} elseif ($info['tries'] >= 5) {
			S($type . $phone, null);
			return 0;
		} else {
			$info['tries'] = $info['tries'] + 1;
			S($type . $phone, $info, 600);
			return false;
		}
	} else {
		return null;
	}
}
?>

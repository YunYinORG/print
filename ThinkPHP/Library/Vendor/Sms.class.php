<?php
// ===================================================================
// | FileName:  Sms.class.php 短信发送类
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014-2015 云印南开团队 All rights reserved.
// +------------------------------------------------------------------
/**
 * Class and Function List:
 * Function list:
 * - __construct()
 * - _connection()
 * - sendSms()
 * Classes list:
 * - Sms
 */
namespace Vendor;
class Sms {
	private $_supporter = '';
	private $_header = false;

	public function __construct($supporter = '')
	{
		$this->_supporter = $supporter;
		$softVersion = '2014-06-30';
		$baseUrl     = 'https://api.ucpaas.com/';
		date_default_timezone_set('Asia/Shanghai');
		$accountSid = C('SMS_ACCOUNT');
		$token      = C('SMS_TOKEN');
		$timestamp  = date('YmdHis');
		$sig        = strtoupper(md5($accountSid.$token.$timestamp));
		$this->_url = $baseUrl.$softVersion.'/Accounts/'.$accountSid.'/Messages/templateSMS?sig='.$sig;
		$auth = trim(base64_encode($accountSid.':'.$timestamp));
		$this->_header = array('Content-Type:application/json;charset=utf-8', 'Authorization:'.$auth);
	}

	/**
	 * 连接服务器回尝试curl和file_get_contents两种方法
	 * @param  $data          post数据
	 * @param  $type
	 * @param  $method        post或get
	 * @return mixed|string
	 */
	private function _connection($data, $type = 'json', $method = 'post')
	{
		if ($type == 'json')
		{
			$mine = 'application/json';
		}
		else
		{
			$mine = 'application/xml';
		}
		if (function_exists('curl_init'))
		{
			$ch = curl_init($this->_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_header);
			if ($method == 'post')
			{
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			}
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$result = curl_exec($ch);
			curl_close($ch);
		}
		else
		{
			$opts = array();
			$opts['http'] = array();
			$headers = array('method' => strtoupper($method));
			$headers[] = 'Accept:'.$mine;
			$headers['header'] = $this->_header ?: array();
			if ( ! empty($data))
			{
				$headers['header'][] = 'Content-Length:'.strlen($data);
				$headers['content'] = $data;
			}
			$opts['http'] = $headers;
			$result = file_get_contents($this->_url, false, stream_context_create($opts));
		}
		return $result;
	}

	/**
	 * 发送短信
	 * @param $phone      到达手机号
	 * @param $msg        短信参数
	 * @param $templateId 短信模板ID
	 */
	public function sendSMS($phone, $msg, $templateId)
	{
		$body_json = array(
			'templateSMS' => array(
				'appId'      => C('SMS_APPID'),
				'templateId' => $templateId,
				'to'         => $phone,
				'param'      => $msg));
		$data       = json_encode($body_json);
		$return_str = $this->_connection($data, 'json');
		$result     = json_decode($return_str);
		return isset($result->resp->respCode) ? ($result->resp->respCode == 0) : false;
	}

	/**
	 * 绑定手机
	 * @param $msgInfo 短信信息['code' 验证码]
	 */
	public function bindPhone($toPhone, $msgInfo)
	{
		$phone = $toPhone;
		$msg   = $msgInfo.',5';
		$templateId = C('SMSID_BIND');
		return $this->sendSMS($phone, $msg, $templateId);
	}

	/**
	 * 找回密码
	 * @param $msgInfo 短信信息['code' 验证码]
	 */
	public function findPwd($toPhone, $msgInfo)
	{
		$phone = $toPhone;
		$msg   = $msgInfo.',5';
		$templateId = C('SMSID_PWD');
		return $this->sendSMS($phone, $msg, $templateId);
	}

	/**
	 * 找回一卡通
	 * @param $msgInfo 短信信息['recv_name' 失主姓名	, 'send_name' 拾主姓名 , 'send_phone' 拾主手机号]
	 */
	public function findCard($toPhone, $msgInfo)
	{
		$phone = $toPhone;
		$msg   = $msgInfo['recv_name'].','.$msgInfo['send_name'].','.$msgInfo['send_phone'];
		$templateId = C('SMSID_CARD');
		return $this->sendSMS($phone, $msg, $templateId);
	}

	/**
	 * 文件已打印通知
	 * @param $msgInfo 短信信息['pri_name' 打印店 , 'fid' 文件名 , 'name' 打印的同学]
	 */
	public function printed($toPhone, $msgInfo)
	{
		$phone = $toPhone;
		$msg   = $msgInfo['pri_name'].','.$msgInfo['fid'].','.$msgInfo['name'];
		$templateId = C('SMSID_PRINTED');
		return $this->sendSMS($phone, $msg, $templateId);
	}
}

<?php

// ===================================================================
// | FileName:      /Print/Printer/PrinterController.class.php
// ===================================================================
// | Discription：   PrinterController 打印店信息管理控制器
//      <命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014 云印南开团队 All rights reserved.
// +------------------------------------------------------------------

/**
 * Class and Function List:
 * Function list:
 * - index()
 * - changePwd()
 * - logout()
 * - signup()
 * - add()
 * - auth()
 * - _empty()
 * Classes list:
 * - PrinterController extends Controller
 */
namespace Printer\Controller;
use Think\Controller;

class PrinterController extends Controller {

	/**
	 * index()
	 * 打印店信息管理页，
	 * 未登录时跳转至打印店入口首页（U（'Index/index'））
	 */
	public function index()
	{

		//Display profile and table to make a change
		$id = pri_id(U('Index/index'));
		if ($id)
		{

			$data = M('Printer')->field('account', 'password')->getById($id);
			$this->data = $data;
			$this->price = json_decode($data['price'], true);
			$this->display();
		}
		else
		{
			$this->error('请使用打印店管理账号登录！', U('Index/index'));
		}
	}

	/**
	 * 找回密码页面
	 * @param pri_id 打印店账号
	 */
	public function forget()
	{
		if (pri_id())
		{
			$this->redirect('index');
		}
		else
		{
			$this->display();
		}
	}

	/**
	 * 找回密码
	 * @param way     找回方式
	 * @param account 打印店账号
	 * @param phone   手机号
	 * @param email   邮箱
	 */
	function findPwd()
	{
		$account = I('account', false, C('REGEX_ACCOUNT'));
		if ( ! $account)
		{
			$this->error('账号无效！');
		}
		switch (I('way'))
		{
			case 'phone':
				$phone = I('post.phone', false, C('REGEX_PHONE'));
				if ( ! $phone)
				{
					$this->error('手机号无效！');
				}
				$printer_phone = M('Printer')->getFieldByAccount($account, 'phone');
				if ( ! empty($printer_phone))
				{
					if ($phone != $printer_phone)
					{
						$this->error('账号与手机号不匹配！');
					}
				}
				else
				{
					$this->error('账号未注册或未绑定手机！');
				}
				$result = send_sms_code($phone, 'findPwd'); 	//发送短信
				if ($result == true)
				{
					session('find_pwd_account', $account);
					session('find_pwd_phone', $phone);
					$this->success('发送成功');
				}
				elseif ($result === 0)
				{
					$this->error('发送次数过多');
				}
				else
				{
					$this->error('发送失败');
				}
				break;
			case 'email':
				$email = I('post.email', false, C('REGEX_EMAIL'));
				if ( ! $email)
				{
					$this->error('邮箱地址无效！');
				}
				$printer = M('Printer')->Field('id,email')->getByAccount($account);
				if ( ! empty($printer['email']))
				{
					if ($email != $printer['email'])
					{
						$this->error('账号与邮箱不匹配！');
					}
				}
				else
				{
					$this->error('账号未注册或未绑定邮箱！');
				}
				$data['use_id'] = $printer['id'];
				$data['type'] = 2; 	//密码找回类型为2
				$Code = M('code');
				$Code->where($data)->delete();
				$data['code'] = random(32);
				$data['content'] = $account;
				$cid = $Code->add($data);
				if ($cid)
				{
					$url = U('Printer/checkEmailCode', 'id='.$cid.'&code='.$data['code'], '', true);
					if (send_mail($email, $url, 2))
					{
						$this->success('验证邮件已发送到'.$email.'请及时到邮箱查收');
					}
					else
					{
						$this->error('验证邮件发送失败！');
					}
				}
				else
				{
					$this->error('信息生成失败！');
				}
				break;
			default:
				$this->error('类型未知！');
		}
	}

/**
 * 验证短信
 * @method checkSMSCode
 * @author 云小印[yunyin.org]
 *
 * @param phone
 * @param code
 */
	function checkSMSCode()
		{
		$code  = I('post.code', false, '/^\d{6}$/');
		$phone = session('find_pwd_phone');
		if (check_sms_code($phone, $code, 'findPwd'))
			{
			session('find_pwd_phone', null);
			session('reset_pwd_flag', 1);
			$this->success('验证成功！', '/Printer/Printer/resetPwd');
		}
	}

	/**
	 * @param id
	 * @param code
	 */
	function checkEmailCode()
		{
		$id   = I('id', false, 'int');
		$code = I('code', false, '/^\w{32}/');
		if ($id && $code)
			{
			$map['id'] = $id;
			$map['code'] = $code;
			$map['type'] = 2; //密码找回类型为2
			if ($info = M('Code')->where($map)->Field('use_id,content')->find())
				{
				M('Code')->where('id=%d', $id)->delete();
				session('find_pwd_account', $info['content']);
				session('reset_pwd_flag', 2);
				$this->display('resetPwd');
			}
				else
				{
				$this->error('验证信息已失效,请重新获取！', '/Printer/Printer/forget');
			}
		}
			else
			{
			$this->error('信息不完整！', '/Printer/Printer/forget');
		}
	}

	/**
	 * isMD5
	 * @param password
	 */
	function resetPwd()
		{
		$type = session('reset_pwd_flag');
		switch ($type)
			{
			case 1:
			case 2:
				$password = I('post.password');
				if ( ! $password)
				{
					$this->display();
					return;
				}
				$account = session('find_pwd_account');
				if ( ! I('isMD5'))
				{
					$password = md5($password);
				}
				if (false !== M('Printer')->where('account="%s"', $account)->setField('password', encode($password, $account)))
				{
					session(null);
					$this->success('密码重置成功！', '/Printer/Index/index');
				}
				else
				{
					$this->error('重置失败！', '/Printer/Printer/forget');
				}
				break;
			default:
				$this->error('验证失败！', '/Printer/Printer/forget');
		}
	}

	/**
	 * changePwd()
	 * 修改密码
	 * 注意字段过滤
	 */
	public function changePwd()
		{

		$id = pri_id(U('Index/index'));
		$old_password = I('deprecated_password');
		$password = I('password');
		$isMD5 = I('isMD5');
		if ($id && $old_password && $password)
			{
			if ( ! isMD5)
				{
				$old_password = md5($old_password);
				$password     = md5($password);
			}
			$Printer = M('Printer');
			$pri = $Printer->field('account,password')->cache(true)->getById($id);
			if ($pri['password'] == encode($old_password, $pri['account']))
				{
				if ($Printer->where('id='.$id)->setField('password', encode($password, $pri['account'])) !== fasle)
					{
					$this->success('修改成功', 1);
				}
					else
					{
					$this->error($Printer->getError());
				}
			}
				else
				{
				$this->error('原密码错误');
			}
		}
			else
			{
			$this->error('信息不完整');
		}
	}

/**
 * 修改信息
 * @method changeInfo
 * @author 云小印[yunyin.org]
 *
 * @param pri_id
 * @param 各种各样信息
 */
	public function changeInfo()
		{
		$id = pri_id(U('Index/index'));
		if ($id)
			{
			//$data = $_POST;
			$data['qq'] = I('qq');
			$data['phone'] = I('phone');
			$data['address'] = I('address');
			$data['profile'] = I('profile');
			$data['open_time'] = I('open_time');
			$data['email'] = I('email');

			$price['p_c_s'] = I('p_c_s', 0, 'float'); //color single
			$price['p_c_d'] = I('p_c_d', 0, 'float'); //color double
			$price['p_s'] = I('p_s', 0, 'float'); //no color single
			$price['p_d'] = I('p_d', 0, 'float'); //no color double

			$data['price'] = json_encode($price);
			$data['price_more'] = I('price_more'); //price more

			$result = M('Printer')->where('id='.$id)->save($data);
			if ($result)
				{
				$this->success('改好了');
			}
				else
				{
				$this->error('出错了');
			}
		}
			else
			{
			$this->error('信息不完整');
		}
	}

	/**
	 * 注销
	 */
	public function logout()
		{
		delete_token(cookie('token'));
		session(null);
		cookie(null);
		$this->redirect('Printer/Index/index');
	}

/**
 * 登录验证
 * @method auth()
 * @author 云小印[yunyin.org]
 *
 * @param pri_id
 * @param password
 */
	public function auth()
		{
		$Printer = M('Printer');
		$account = I('post.account', null, C('REGEX_ACCOUNT'));
		if ( ! $account)
			{
			$this->error('无效账号：'.I('post.account'));
		}
		$result = $Printer->where('account="%s"', $account)->field('id,password,status')->find();
		if ($result)
			{
			$key      = 'auth_p_'.$account;
			$times    = S($key);
			$password = encode(md5(I('post.password')), $account);
			if ($times > C('MAX_TRIES'))
				{
				\Think\Log::record('打印店爆破警告：ip:'.get_client_ip().',account:'.$account, 'NOTIC', true);
				$this->error('此账号尝试次数过多，已经暂时封禁，请于一小时后重试！（ps:你的行为已被系统记录）');
			}
				elseif ($result['password'] == $password)
				{
				session('pri_id', $result['id']);
				$token = update_token($result['id'], C('PRINTER_WEB'));
				cookie('token', $token, 3600 * 24 * 30);
				S($key, null);
				$this->redirect('Printer/File/index');
				return;
			}
				else
				{
				S($key, $times + 1, 3600);
			}
		}
		$this->error('验证失败');
	}

	/**
	 * 404页
	 */
	public function _empty()
		{
		$this->redirect('index');
	}
}
?>

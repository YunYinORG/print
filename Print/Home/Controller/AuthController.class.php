<?php
// ===================================================================
// | FileName:      AuthController.class.php
// ===================================================================
// | Discription：   AuthController 用户密码管理控制器
// 					验证逻辑和调整，没有任何页面
//      <命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014-2015 云印南天团队 All rights reserved.
// +------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +------------------------------------------------------------------
/**
 * Class and Function List:
 * Function list:
 * - index()
 * - token()
 * - findPwd Verify()
 * - logout()
 * - _checkHttps()
 * - _login()
 * - _verify()
 * - _checkTries()
 * - _empty()
 * Classes list:
 * - AuthController extends Controller
 */
namespace Home\Controller;
use Think\Controller;

class AuthController extends Controller {
	/**
	 * index()
	 * 登录和注册验证处理
	 * @param post.number   学号
	 * @param post.password 密码
	 */
	public function index()
	{

		$this->_checkHttps();

		$number   = I('post.number', null, C('REGEX_NUMBER'));
		$password = I('post.password');

		if ( ! $number ||  ! $password) //学号或者密码不存在
		{
			$this->error(L('WRONG_FORMAT'), C('BASE_URL'));
		}

		$User = M('User');
		$user = $User
			->where('student_number="%s"', $number)
			->field('id,password,status')
			->find();
		if ($user) //已经注册过，直接登录
		{

			$login_id = $this->_login($number, $password, $user);
			if ( ! $login_id) //登录失败
			{
				$this->error(L('LOGIN_FAIL'), C('BASE_URL'));
			}
			else
			{
				/*登录成功开始跳转*/
				S($key, null);
				$token = md5(token($login_id));
				S('AUTH_'.$token, $login_id, 300);
				redirect(C('BASE_URL').'/Auth/token?type=login&key='.$token);
			}
		}
		else
		{
			/*未注册尝试验证*/
			$data = $this->_verify($number, $password);
			if ( ! $data)
			{
				$this->error(L('VERIFY_FAIL'), C('BASE_URL'));
			}
			else
			{
				/*验证成功缓存验证信息并跳转*/
				S($key, null);
				$token = md5($number.token($number));
				S('REG_'.$token, $data, 300);
				redirect(C('BASE_URL').'/Auth/token?type=register&key='.$token);
			}
		}
	}

	/**
	 * 验证跳转
	 * @method token
	 *
	 * @author NewFuture[NewFuture@yunyin.org]
	 *
	 * @param get.type string 验证类型
	 * @param get.key  string 验证的key
	 */
	public function token()
	{
		$type = I('get.type');
		$key  = I('get.key');

		switch ($type)
		{
			case 'login':	//登录验证

			/* 登录，根据key读取缓存的id，写入session和cookie完成登录,跳转到信息页 */
				if ($id = S('AUTH_'.$key))
				{
					session('use_id', $id);
					$token = update_token($id, C('STUDENT'));
					cookie('token', $token, 3600 * 24 * 30);
					S('AUTH_'.$key, null);
					redirect('/User/', 0, L('AUTH_SUCCESS'));
				}
				break;
			case 'register':	//注册验证

			/* 登录，根据key读取缓存的注册数据，写入session,跳转到注册页面 */
				if ($data = S('REG_'.$key))
				{
					session('authData', $data);
					S('REG_'.$key, null);
					$this->redirect('/User/register');
				}
				break;
		}
		redirect(C('BASE_URL')); //所有其他情况或者信息获取，调转到首页
	}

	/**
	 * 再次认证找回密码
	 * @method findPwdVerify
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function findPwdVerify()
	{
		$this->_checkHttps();
		/*检查输入合法性*/
		$number       = I('post.number', false, C('REGEX_NUMBER_NKU'));
		$urp_password = I('post.urp_password');
		$password     = I('post.password');
		if ( ! $number)
		{
			$this->error(L('ACCOUNT_FORMAT_ERROR'));
		}
		elseif ( ! $urp_password ||  ! $password)
		{
			$this->error(L('PASSWORD_EMPTY'));
		}
		/*验证账号*/
		if ( ! $this->_verify($number, $urp_password))
		{
			$this->error(L('VERIFY_FAIL'));
		}
		else
		{
			if ( ! I('isMD5'))
			{
				$password = md5($password);
			}
			/*重置密码*/
			if (false === M('User')->where('student_number='.$number)->setField('password', encode($password, $number)))
			{
				$this->error(L('PASSWORD_RESET_ERROR'));
			}
			else
			{
				/*密码重置成功，跳转到首页*/
				redirect(C('BASE_URL'), 3, L('PASSWORD_RESET_SUCC'));
			}
		}
	}

	/**
	 * 注销
	 * @method logout
	 * @return 重定向
	 */
	public function logout()
	{
		$token = cookie('token');
		if ($token)
		{
			delete_token($token);
		}
		cookie(null);
		session(null);
		session('[destroy]');
		redirect(C('BASE_URL'));
	}

	/**
	 * 检测HTTPS是否开启
	 * 如果开启，非HTTPS请求则强制跳转
	 * @method _checkHttps
	 * @access private
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	private function _checkHttps()
	{
		if (C('HTTPS_ON') && $_SERVER['HTTPS'] != 'on')
		{
			/*开启https模式后，非https请求不进行处理*/
			$this->error(L('NOT_HTTPS_ERROR'), C('BASE_URL'));
		}
	}

	/**
	 * 登录函数
	 * @method _login
	 * @access private
	 *
	 * @author NewFuture[newfuture@yunyin.org]
	 *
	 * @param  [number]   $number      [学号]
	 * @param  [string]   $password    [原始密码]
	 * @param  array      $user        [用户信息]
	 * @return [bool/int] [用户id]
	 */
	private function _login($number, $password, $user = array())
	{

		$this->_checkTries($number);

		if ( ! $user)
		{
			$user = M('User')->field('id,password,status')->getByStudentNumber($number);
		}
		/*判断加密方式,验证密码*/
		if (strlen($user['password']) == 13)
		{
			/*旧版密码登录*/
			if ($user['password'] != encode_old($password, $number))
			{
				return false;
			}
			else
			{
				/*更新密码加密方式，过渡到新的加密方式*/
				$password = md5($password);
				$password = encode($password, $number);
				$user['password'] = $password;
				$User->save($user);
			}
		}
		else
		{
			/*新版加密*/
			$password = md5($password);
			$password = encode($password, $number);
			if ($user['password'] != $password)
			{
				return false;
			}
		}

		if ($user['status'] < 1)
		{
			/*账号已封禁*/
			$this->error(L('USER_BAN'), C('BASE_URL'));
			return false;
		}
		else
		{
			S('AUTH_'.$number, null);
			return $user['id'];
		}
	}

	/**
	 * 学校账号验证
	 * @method _verify
	 * @access private
	 *
	 * @author NewFuture[newfuture@yunyin.org]
	 *
	 * @param  [type]  $number                     [学号]
	 * @param  [type]  $password                   [密码]
	 * @return [array] [验证信息,失败null]
	 */
	private function _verify($number, $password)
	{
		/*尚未注册，先判断学校导入学校验证文件*/
		if (preg_match(C('REGEX_NUMBER_NKU'), $number))
		{
			if ( ! C('NKU_OPEN'))
			{
				$this->error(L('AUTH_NKU_CLOSE'));
			}
			$verify_way = C('VERIFY_NKU');
			$data['sch_id'] = 1;
		}
		elseif (preg_match(C('REGEX_NUMBER_TJU'), $number))
		{
			$verify_way = C('VERIFY_TJU');
			$data['sch_id'] = 2;
		}
		else
		{
			//	$this->error('你输入的学号'.$number.',不是南开或者天大在读学生的的学号，如果你是南开或者天大的在读学生请联系我们！');
			return false;
		}

		$this->_checkTries();
		/*导入验证文件开始验证*/
		import($verify_way, COMMON_PATH, '.php');
		$name = getName($number, $password);
		if ($name)
		{
			S('AUTH_'.$number, null); //清楚尝试次数

			$data['name'] = $name;
			$data['student_number'] = $number;
			$data['password'] = md5($password);
			return $data;
		}
		else
		{
			return null;
		}
	}

	/**
	 * 检查尝试次数限制
	 * 统计同一账号的尝试次数，
	 * 达到最大值跳转
	 * @method _checkTries
	 * @access private
	 *
	 * @author NewFuture[newfuture@yunyin.org]
	 *
	 * @param  [type] $number          [学号]
	 * @return [type] [直接跳转]
	 */
	private function _checkTries($number)
	{
		$key   = 'auth_'.$number;
		$times = S($key);
		if ($times > C('MAX_TRIES'))
		{
			\Think\Log::record('auth爆破警告：ip:'.get_client_ip().',number:'.$number, 'NOTIC', true);
			$this->error(L('TRIES_LIMIT'), C('BASE_URL'), 5);
			return false;
		}
		else
		{
			S($key, $times + 1, 3600);
			return true;
		}
	}

	/**
	 * 404页
	 */
	public function _empty()
	{
		redirect(C('BASE_URL'));
	}
}

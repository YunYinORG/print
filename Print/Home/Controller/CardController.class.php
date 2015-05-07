<?php

// ===================================================================
// | FileName:      CardController.class.php
// ===================================================================
// | Discription：   CardController 一卡通找回
//      <命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014-2015 云印南开团队 All rights reserved.
// +------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +------------------------------------------------------------------
/**
 * Class and Function List:
 * Function list:
 * - index()
 * - help()
 * - find()
 * - off()
 * - result()
 * - _empty()
 * Classes list:
 * - CardController extends Controller
 */
namespace Home\Controller;
use Think\Controller;

class CardController extends Controller {

	/**
	 * 应用首页
	 */
	public function index()
	{
		$uid = use_id(U('Index/index'));
		if ($uid)
		{
			$user = M('User')->cache(true)->field('phone,status')->getById($uid);
			$card = M('Card')->getById($uid);
			if ($user['status'] < 0)
			{
				$this->error('已封号');
			}
			elseif ( ! $user['phone'])
			{
				$this->error('使用此功能必须绑定手机号！', '/User/index');
			}
			elseif ( ! $card)
			{

				/*数据库中不存在，加入数据库*/
				$card['id'] = $uid;
				M('Card')->add($card);
				$this->display();
			}
			else
			{
				$this->new = M('Cardlog')->where("(find_id=$uid OR lost_id=$uid) AND status is null")->count();
				$this->display();
			}
		}
	}

	/**
	 * log
	 * 记录页
	 */
	public function log()
	{
		$id = use_id('User/index');
		$Cardlog = D('CardlogView');
		$this->lost = $Cardlog->where("lost_id=$id")->field('id,find_id,find_name,find_number,time,status')->order('id desc')->select();
		$this->find = $Cardlog->where("find_id=$id")->field('id,lost_id,lost_name,lost_number,time,status')->order('id desc')->select();
		$this->display();
	}

	/**
	 * find()
	 * 核实信息
	 * @param number 学号
	 * @param name   姓名
	 */
	public function find()
	{
		$uid    = use_id();
		$number = I('number', false, C('REGEX_NUMBER'));
		$name   = I('name', false, 'trim');

		$User = M('User');
		$Card = M('Card');
		$send_user = $uid ? $User->field('id,sch_id,student_number,name,phone,email')->getById($uid) : false;
		if ( ! $send_user)
		{
			$this->error('请登录！', '/');
		}
		elseif ( ! $send_user['phone'])
		{
			$this->error('尚未绑定手机', '/User/index');
		}
		elseif ($send_user['student_number'] == $number)
		{
			$this->error('不要用自己的做实验哦！');
		}
		elseif ($Card->cache(true)->getFieldById($uid, 'blocked'))
		{
			$this->error('由于恶意使用,您的此功能已被禁用', '/Card/help');
		}
		elseif ( ! $name &&  ! $number)
		{
			$this->error('信息不足');
		}
		else
		{
			$recv_user = $User->field('id,name,student_number,phone,email')->cache(true)->getByStudentNumber($number);
			$recv_off  = $Card->cache(true)->getFieldById($recv_user['id'], 'off');
			if ( ! $recv_user)
			{
				$this->error($number.'尚未加入此平台', '/Card/help');
			}
			elseif ($name !== $recv_user['name'])
			{
				$this->error('失主信息核对失败！');
			}
			elseif ($recv_off)
			{
				$this->error('对方关闭此功能', '/Card/help');
			}
			elseif ( ! $recv_user['phone'] &&  ! $recv_user['email'])
			{
				$this->error($name.'尚未绑定个人信息', '/Card/help');
			}
			else
			{

				/*验证成功 通知并记录*/
				$msg     = '';
				$success = false;
				import('Common.Encrypt', COMMON_PATH, '.php');
				$send_phone = decrypt_phone($send_user['phone'], $send_user['student_number'], $send_user['id']);
				$info = array('send_phone' => $send_phone, 'send_name' => $send_user['name'], 'recv_name' => $recv_user['name']);
				if ($recv_user['phone'])
				{
					$recv_phone = decrypt_phone($recv_user['phone'], $recv_user['student_number'], $recv_user['id']);
					$SMS = new \Vendor\Sms();
					$sms_result = $SMS->findCard($recv_phone, $info);
					$success |= $sms_result;
					if ($sms_result)
					{
						$msg = '短信已发送!<br/>';
					}
					else
					{
						$msg = '短信发送失败!<br/>';
					}
				}
				if ($recv_user['email'])
				{
					$recv_email = decrypt_email($recv_user['email']);
					$send_user['school'] = M('school')->cache(true)->getFieldById($send_user['sch_id'], 'name');
					if ($send_user['email'])
					{
						$send_user['email'] = decrypt_email($send_user['email']);
					}
					/*发送邮件通知*/
					$mail_result = send_mail($recv_email, L('MAIL_CARD', array('name' => $recv_user['name'], 'sender' => $send_user)), C('MAIL_NOTIFY'));

					$success |= $mail_result;
					if ($mail_result)
					{
						$msg .= '邮件已发送!<br/>';
					}
					else
					{
						$msg .= '邮件发送失败!';
					}
				}
				if ( ! $success)
				{
					$this->error('消息发送失败！请重试或者交由第三方平台！');
				}

				if ($recv_off === null) //该同学不在card记录之中
				{
					$Card->add(array('id' => $recv_user['id']));
				}
				$log['find_id'] = $send_user['id'];
				$log['lost_id'] = $recv_user['id'];
				if ( ! M('Cardlog')->add($log))
				{
					$this->error('记录失败!!!<br/>'.$msg);
				}
				else
				{
					$this->success($msg);
				}
			}
		}
	}

	/**
	 * off()
	 * 设关闭
	 * @param $value 权限值
	 */
	public function off()
	{
		$id = use_id();
		$value = I('value', null, 'int');
		if ( ! $id ||  ! $value)
		{
			$this->error('请登录！');
		}
		elseif (M('Card')->where('id=', $id)->setField('off', $value) !== false)
		{
			$this->error('修改失败!');
		}
		else
		{
			$this->success('修改成功！');
		}
	}

	/**
	 * result()
	 * 结果
	 * @param $id     记录id
	 * @param $status 结果状态举报-1;感谢1;忽略0;
	 */
	public function result()
	{
		$uid    = use_id();
		$id     = I('id', null, 'int');
		$status = I('status', null, 'int');
		if ( ! $uid)
		{
			$this->error('请登录！');
		}
		elseif ( ! $id)
		{
			$this->error('信息不足');
		}
		elseif ( ! in_array($status, array(-1, 0, 1)))
		{
			$this->error('参数不对！');
		}
		else
		{
			$log['id'] = $id;
			$log['lost_id'] = $uid;
			$Log = M('Cardlog');
			if ($Log->where($log)->setField('status', $status) !== false)
			{
				if ($status < 0)
				{
					$findId = $Log->getFieldById($id);
					if ($Log->where("find_id=$findId AND status<0")->count() >= 2)
					{

						/*恶意操作进行屏蔽*/
						M('Card')->where("id=$findId")->setField('blocked', 1);
					}
				}
				$this->success('操作成功');
			}
			else
			{
				$this->error('操作失败');
			}
		}
	}

	/**
	 * showPhone()
	 * 显示拾得者手机
	 * 若已感谢、举报或忽略，显示按钮失效
	 */
	public function showPhone()
	{
		$uid = use_id();
		$id  = I('id', null, 'int');
		$log['id'] = $id;
		$log['lost_id'] = $uid;
		if ($find_id = M('Cardlog')->where($log)->getField('find_id'))
		{
			$phone = get_phone_by_id($find_id);
			if (IS_AJAX)
			{
				$this->success(array('phone' => $phone));
			}
			else
			{
				echo $phone;
			}
		}
	}

	/**
	 * help
	 * 帮助说明
	 */
	public function help()
	{
		$uid = use_id();
		if ( ! $uid)
		{
			$this->error('请登录！', '/');
		}
		else
		{
			$this->recv_name = session('recv_name');
			$this->recv_number = session('recv_number');
		}
		session('find_id', $uid);
		$this->display();
	}

	/**
	 * 失主尚未加入平台,或已加入平台但未绑定信息
	 * 向第三方平台发送消息
	 * @param number	学号
	 * @param name	姓名
	 * @param add_msg         附加消息
	 */
	public function send()
	{
		$find_id    = session('find_id');
		$cache_name = 'send_'.$find_id;
		$times      = S($cache_name);
		if ($times > 5)
		{
			\Think\Log::record('第三方平台发送失败：ip:'.get_client_ip().',find_id'.$find_id);
			$this->error('发送次数过多!', '/Card/log');
		}
		else
		{
			S($cache_name, $times + 1, 3600);
		}
		$number   = session('recv_number');
		$name     = session('recv_name');
		$send_msg = '学号是'.$number.'的'.$name.'同学你好：';
		$send_msg .= I('add_msg');
		$recv_user_id = M('User')->getFieldByStudentNumber($number, 'id');
		$recv_off     = M('Card')->getFieldById($recv_user_id, 'off');
		if ($recv_user_id && ($recv_off != 1))
		{
			/*失主已加入平台但未绑定信息,且未关闭此功能,添加到丢失记录*/
			if ($recv_off === null)
			{
				$card['id'] = $recv_user_id;
				$Card->add($card);
			}
			$log['find_id'] = $find_id;
			$log['lost_id'] = $recv_user_id;
			M('Cardlog')->add($log);
		}
		/*post数据到API*/
		$post = function($url, $key, $send_msg)
		{
			$post_url = $url;
			$post_data = array(
				'key'    => $key,
				'status' => base64_encode($send_msg),
			);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $post_url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			$data = curl_exec($ch);
			curl_close($ch);
			return $data;
		};

		$url    = 'https://newfuturepy.sinaapp.com/broadcast';
		$key    = C('WEIBO_API_PWD');
		$data   = $post($url, $key, $send_msg);
		$result = json_decode($data);
		if ($result->renren)
		{
			echo '人人发送成功.';
		}
		else
		{
			echo '人人发送失败.';
		}
		switch ($result->weibo)
		{
			case 2:
				echo '微博发送成功.';
				break;
			case 0:
				\Think\Log::record('微博API调用出现错误或授权过期');
			default:
				echo '微博发送失败.';
		}
	}

	/**
	 * 404页
	 */
	public function _empty()
	{
		$this->redirect('index');
	}
}

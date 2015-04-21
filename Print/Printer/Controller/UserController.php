<?php

// ===================================================================
// | FileName:      /Print/Printer/UserController.class.php
// ===================================================================
// | Discription：   UserController 用户操作控制器
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
 * - send()
 * - getPhone()
 * - _empty()
 * Classes list:
 * - UserController extends Controller
 */
namespace Printer\Controller;
use Think\Controller;

class UserController extends Controller {

/**
 * @method getPhone
 * @author 云小印[yunyin.org]
 * @param  $pid
 * @param  $uid
 * @return $phone 用户手机号
 */
	public function getPhone()
	{
		$pid = pri_id(U('Index/index'));
		if ($pid)
		{
			$uid   = I('uid', null, 'intval');
			$phone = get_phone_by_id($uid);
			if ($phone)
			{
				$this->success($phone);
			}
			else
			{
				$this->error('未知错误');
			}
		}
		else
		{
			$this->error('请先登录');
		}
	}

/**
 * @method send
 * @author 云小印[yunyin.org]
 * @param  $pid,$fid
 */
	public function send()
	{
		$pid = pri_id(U('Index/index'));
		if ($pid)
		{
			$fid = I('fid', null, 'intval');
			$map['pri_id'] = $pid;
			$map['id'] = $fid;
			$map['status'] = array('eq', C('FILE_PRINTED'));
			$map['sended'] = 0;
			$File    = D('FileView');
			$info    = $File->where($map)->field('use_id,phone,name')->find();
			$Printer = M('Printer');
			$info['pri_name'] = M('Printer')->getFieldById($pid, 'name');
			if ($info['phone'] && $info['name'])
			{
				unset($info['phone']);
				if (mb_strlen($info['name']) > 18)
				{
					$info['name'] = mb_substr($info['name'], 0, 18);
				}
				$phone = get_phone_by_id($info['use_id']);
				unset($info['use_id']);
				$info['fid'] = $fid;
				$SMS = new \Vendor\Sms();
				if ($SMS->printed($phone, $info))
				{
					$File = M('File');
					$map['id'] = $fid;
					$result = $File->where($map)->setField('sended', 1);
					$this->success('提醒信息已发送');
				}
				else
				{
					$this->error('发送不成功');
				}
			}
			else
			{
				$this->success('已发送');
			}
		}
		else
		{
			$this->error('请先登录');
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

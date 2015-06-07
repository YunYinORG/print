<?php

// ===================================================================
// | FileName:      IndexController.class.php
// ===================================================================
// | Discription：   IndexController 默认控制器
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
 * - feedback()
 * - backfeed()
 * Classes list:
 * - IndexController extends Controller
 */
namespace Home\Controller;
use Think\Controller;

class IndexController extends Controller {

	/**
	 * 首页
	 * @method index
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function index()
	{
		if (use_id())
		{
			redirect('/User');
			// $this->status = 'login';
		}
		elseif (session('authData'))
		{
			$this->status = 'register';
		}
		elseif (C('HTTPS_ON'))
		{
			$this->status = C('SAFE_URL');
		}else{
			$this->status=__ROOT__;
		}
		$this->display('index');
	}

	/**
	 * 反馈处理
	 */
	public function feedback()
	{
		$Form = D('Feedback');
		$msg  = I('post.message');
		if ( ! $msg)
		{
			$this->error('内容不能为空！');
		}
		$_POST['message'] = $msg.'##FromStudentID:'.use_id();
		if ($Form->create())
		{
			$result = $Form->add();
			if ($result)
			{
				$this->success('操作成功！');
			}
			else
			{
				$this->error('操作错误！');
			}
		}
		else
		{
			$this->error($Form->getError());
		}
	}

	public function contact()
	{
		$this->display();
	}

	public function about()
	{
		$this->display();
	}

	public function privacy()
	{
		$this->display();
	}

	/**
	 * 404页
	 */
	public function _empty()
	{
		$this->redirect('index');
	}
}

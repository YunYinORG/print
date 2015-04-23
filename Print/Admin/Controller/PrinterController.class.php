<?php
// ===================================================================
// | FileName:      /Print/Admin/PrinterController.class.php
// ===================================================================
// | Discription：   PrinterController 后台信息管理控制器
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
 * - printerRegister()
 * - addPrinter()
 * Classes list:
 * - PrinterController extends Controller
 */
namespace Admin\Controller;
use Think\Controller;

class PrinterController extends Controller {

	public function index()
	{
		if ( ! admin_id())
		{
			$this->redirect('Admin/Index/login');
		}
		else
		{
			$this->display();
		}
	}

	/**
	 * 打印店注册
	 */
	public function register()
	{
		if ( ! admin_id())
		{
			$this->redirect('index');
		}
		else
		{
			$this->display();
		}
	}

	public function addPrinter()
	{
		if ( ! admin_id())
		{
			$this->redirect('index');
		}

		$data['account'] = I('post.account', null, C('REGEX_ACCOUNT'));
		if ($data['account'] == null)
		{
			$this->error('您输入的账号不合法！');
		}
		$data['password'] = encode(md5(I('post.password')), I('post.account'));
		$data['name'] = I('post.name');
		$data['sch_id'] = I('post.sch_id');
		$data['address'] = I('post.address');
		$data['phone'] = I('post.phone');

		$Printer = D('Printer');
		$result  = $Printer->add($data);
		if ($result)
		{
			$this->success('新增成功', '/Admin/Printer/index');
		}
		else
		{
			$this->error('数据插入失败'.$Printer->getError());
		}
	}

	public function manage()
	{
		if ( ! admin_id())
		{
			$this->redirect('index');
		}
		$Printer = D('Printer');
		$this->data = $Printer->select();
		$this->display();
	}
}
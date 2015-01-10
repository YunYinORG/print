<?php

// ===================================================================
// | FileName: 		/Print/Printer/IndexController.class.php
// ===================================================================
// | Discription：	IndexController 默认控制器
//		<命名规范：>
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
 * - _empty()
 * Classes list:
 * - IndexController extends Controller
 */
namespace Printer\Controller;
use Think\Controller;
class IndexController extends Controller
{
	
	/**
	 * index()
	 * 打印店管理入口页（包含登录界面）
	 */
	public function index() 
	{
		if (pri_id()) 
		{
			
			//已经登陆直接跳转
			$this->redirect('Printer/Printer/index');
		} else
		{
			
			// $this->redirect('Printer/Printer/signin');
			$this->display();
		}
	}
	
	public function feedback() 
	{
		$Form   = D('Home/Feedback');
		$_POST['message']        = $_POST['message'] . '##FromPrinterID:' . pri_id('index');
		if ($Form->create()) 
		{
			$result = $Form->add();
			if ($result) 
			{
				$this->success('操作成功！');
			} else
			{
				$this->error('写入错误！');
			}
		} else
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
	/**
	 *404页
	 */
	public function _empty() 
	{
		$this->redirect('index');
	}
}

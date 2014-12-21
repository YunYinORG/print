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
		if(pri_id())
		{
			//已经登陆直接跳转
			$this->redirect('Printer/Printer/index');
		}else{
			$this->redirect('Printer/Printer/signin');
		}
	}
}

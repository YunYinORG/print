<?php
// ===================================================================
// | FileName:      /Print/Admin/FileController.class.php
// ===================================================================
// | Discription：   FileController 后台文件信息管理控制器
//      <命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 201５ 云印南开团队 All rights reserved.
// +------------------------------------------------------------------

/**
 * Class and Function List:
 * Function list:
 * - index()
 * - search()
 * Classes list:
 * - FileController extends Controller
 */
namespace Admin\Controller;
use Think\Controller;

class FileController extends Controller {

	public function index()
	{
		if ( ! admin_id())
		{
			$this->redirect('Admin/Index/login');
		}
		else
		{	
			$condition['status'] = array('between', '1,5');
			$File  = D('FileView');
			$count = $File->where($condition)->count();
			$Page  = new \Think\Page($count, 10);
			$show  = $Page->show();
			$ppt_layout = C('PPT_LAYOUT');
			$result = $File->where($condition)->order('file.id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			foreach ($result as &$file)
			{
				$file['ppt_layout'] = $ppt_layout[$file['ppt_layout']];
			}
			unset($file);
			$this->data = $result;
			$this->assign('page', $show);
			$this->display();			
		}
	}
	public function search()
	{
		if ( ! admin_id())
		{
			$this->redirect('Admin/Index/login');
		}
		$status = I('post.status', null, 'int');
		switch ($status)
		{
			case 0:
			  $condition['status'] = array('between', '1,5');
			  break;  
			case 1:
			  $condition['status'] = 1;
			  break;
			case 2:
			  $condition['status'] = 2;
			  break;
			case 3:
			  $condition['status'] = 3;
			  break;
			case 4:
			  $condition['status'] = 4;
			  break;
			default:
			  $condition['status'] = array('between', '1,5');
		}
		
		$File  = D('FileView');
		$count = $File->where($condition)->count();
		$Page  = new \Think\Page($count, 10);
		$show  = $Page->show();
		$ppt_layout = C('PPT_LAYOUT');
		$result = $File->where($condition)->order('file.id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		foreach ($result as &$file)
		{
			$file['ppt_layout'] = $ppt_layout[$file['ppt_layout']];
		}
		unset($file);
		$this->data = $result;
		$this->assign('page', $show);
		$this->assign('status', $status);
		$this->display();
	}
}

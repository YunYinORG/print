<?php

// ===================================================================
// | FileName:      /Print/Printer/FileController.class.php
// ===================================================================
// | Discription：   FileController 文件管理控制器
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
 * - set()
 * Classes list:
 * - FileController extends Controller
 */
namespace Printer\Controller;
use Think\Controller;
class FileController extends Controller
{
	
	/**
	 *index()
	 *打印店文件列表
	 *@param $status 状态，为空全部显示
	 */
	public function index() 
	{
		$pid    = pri_id(U('Index/index'));
		$status = I('status', null, 'intval');
		if ($pid) 
		{
			$condition['pri_id'] = $pid;
			if ($status == 5) 
			{
				$condition['status'] = $status;
				$this->assign('history', true);
				$this->title = '已打印文件历史记录';
			} else
 			{
				$condition['status'] = array('between','1,4' );
				$this->assign('history', 0);
				$this->title = '打印任务列表';
			}
			$File        = D('FileView');
			$this->data  = $File->where($condition)->order('file.id desc')->select();
			$this->display();
		} else
		{
			$this->redirect('Printer/Printer/signin');
		}
	}
	
	/**
	 *set()
	 *更新文件状态（需要验证文件是否在此点打印）
	 *@param $fid    文件id
	 *@param $status 文件状态
	 */
	public function set() 
	{   

		$pid    = pri_id(U('Index/index'));
		$fid    = I('fid', null, 'intval');
		$status = I('status', null, 'intval');
	    $map['pri_id']        = $pid;
		$map['id']        = $fid;
		if ($pid && $fid && $status == 1 ) 
		{
			M('File')->where($map)->setField('status', 2);
			$success = array('response'=> 2);
            $success = json_encode($success);
            header("Content-type: application/json");
			echo $success;
		} 
		else if($pid && $fid && $status >= 2 && $status <= 3)
		{
		 	M('File')->where($map)->setField('status', 4);
			$success = array('response'=> 4);
            $success = json_encode($success);
            header("Content-type: application/json");
			echo $success;
		}
		else if($pid && $fid && $status == 4)
		{
		 	M('File')->where($map)->setField('status', 5);
		 	$success = array('response'=> 5);
            $success = json_encode($success);
            header("Content-type: application/json");
			echo $success;
		}
	}
}

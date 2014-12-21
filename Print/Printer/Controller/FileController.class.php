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
* - PrinterController extends Controller
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
		if (session('?pri_id'))
	    {
	        $File = M('File');
	        $condition['pri_id']=session('pri_id');
	        $condition['status']=array('neq',3);
            $this->data = $File->where($condition)->order('time')->select();
		    layout('layout');
		    $this->display();
	    }
	    else
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
	    if (session('?pri_id'))
	    {
	        
	    }
	    else
	    {
	        $this->redirect('Printer/Printer/signin');
	    }
	}
    
    public function history() 
	{
		if (session('?pri_id'))
	    {
	        $File = M('File');
	        $condition['pri_id']=session('pri_id');
	        $condition['status']=array('eq',3);
            $this->data = $File->where($condition)->order('time')->select();
		    layout('layout');
		    $this->display();
	    }
	    else
	    {
	        $this->redirect('Printer/Printer/signin');
	    }
	}
}

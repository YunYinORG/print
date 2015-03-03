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
 * - refresh()
 * - set()
 * - _empty()
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
		if ($pid) 
		{
			$condition['pri_id']        = $pid;
//				$cache_key   = cache_name('printer', $pid);
				$condition['status']             = array('between', '1,4');
				$this->assign('history', false);
				$this->title = '打印任务列表';
				$File        = D('FileView');
			$this->data  = $File->where($condition)->order('file.id desc')->select();//cache($cache_key, 10)->select();
			$this->display();
		} else
		{
			$this->redirect('Printer/Index/index');
		}
	}

	public function history() 
	{
		$pid    = pri_id(U('Index/index'));
		if ($pid) 
		{
			$condition['pri_id']        = $pid;
				$status = 5;
				$condition['status']        = $status;
				$this->assign('history', true);
				$this->title = '已打印文件历史记录';
			$File        = D('FileView');
			$count      = $File->where($condition)->count();
            $Page       = new \Think\Page($count,10);
            $show       = $Page->show();
			$this->data  = $File->where($condition)->order('file.id desc')->limit($Page->firstRow.','.$Page->listRows)->select();//cache($cache_key, 10)->select();
            $this->assign('page',$show);
			$this->display();
		} else
		{
			$this->redirect('Printer/Index/index');
		}
	}


	public function refresh() 
	{
		$pid        = pri_id(U('Index/index'));
		if ($pid) 
		{
			$map['pri_id']            = $pid;
			$map['id']            = array('gt', I('file_id', null, 'intval'));
			$map['status']            = array('between', '1,4');
			$File       = D('FileView');
			// $cache_key  = cache_name('printer', $pid);
			$this->data = $File->where($map)->order('file.id desc')->limit(10)->select();
			$this->display();
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
		$status = I('status');
		if ($status == C('FILE_DOWNLOAD') || $status == C('FILE_PRINTED') || $status == C('FILE_PAID')) 
		{
			$map['pri_id']        = $pid;
			$map['id']        = $fid;
			$map['status']        = array('gt', 0);
			$File   = M('File');
			$uid    = $File->where($map)->cache(true)->getField('use_id');
			if ($uid) 
			{
				
				$File->where('id="%d"', $fid)->cache(true)->setField('status', $status);
				
				//删除缓存
//				S(cache_name('printer', $pid), null);
//				S(cache_name('user', $uid), null);
				$this->success('更新成功');
			} else
			{
				$this->error('文件不存在(可能已删除)！');
			}
		} else
		{
			$this->error('状态不可设置');
		}
	}
	
	
	public function download()
    {
		$pid    = pri_id(U('Index/index'));
		$fid    = I('fid', null, 'intval');
		// $status = I('status');
        $map['pri_id']        = $pid;
        $map['id']        = $fid;
        $map['status']        = array('gt', 0);
        $File   = M('File');
        $info    = $File->where($map)->field('url,status')->find();

        if ($info) 
        {
        	if($info['status']==C('FILE_UPLOAD'))
        	{
        		$File->where('id=%d',$fid)->setField('status',C('FILE_DOWNLOAD'));
        	}
            redirect(download($info['url']));
        } else
        {
            $this->error('文件已删除，不能再下载！');
        }
    }
	/**
	 *404页
	 */
	public function _empty() 
	{
		$this->redirect('index');
	}
}

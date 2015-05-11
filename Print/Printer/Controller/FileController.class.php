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
 * - history()
 * - refresh()
 * - set()
 * - download()
 * - _empty()
 * Classes list:
 * - FileController extends Controller
 */
namespace Printer\Controller;
use Think\Controller;

class FileController extends Controller {

	/**
	 * index()
	 * 打印店文件列表
	 * @param $status 状态，为空全部显示
	 */
	public function index()
	{
		$pid = pri_id(U('Index/index'));
		if ($pid)
		{
			$condition['pri_id'] = $pid;
//				$cache_key   = cache_name('printer', $pid);
			$condition['status'] = array('between', '1,4');
			$this->assign('history', false);
			$this->title = '打印任务列表';
			$File       = D('FileView');
			$ppt_layout = C('PPT_LAYOUT');
			$result     = $File->where($condition)->order('file.id desc')->select(); //cache($cache_key, 10)->select();
			if ($result)
			{
				foreach ($result as &$file)
				{
					$file['ppt_layout'] = $ppt_layout[$file['ppt_layout']];
				}
				unset($file);
			}
			$this->data = $result;
			$this->display();
		}
		else
		{
			$this->redirect('Printer/Index/index');
		}
	}

	/**
	 * history()
	 * 打印店历史文件列表
	 * @param $pri_id
	 */
	public function history()
	{
		$pid = pri_id(U('Index/index'));
		if ($pid)
		{
			$condition['pri_id'] = $pid;
			$status = 5;
			$condition['status'] = $status;
			$this->assign('history', true);
			$this->title = '已打印文件历史记录';
			$File       = D('FileView');
			$count      = $File->where($condition)->count();
			$Page       = new \Think\Page($count, 10);
			$show       = $Page->show();
			$ppt_layout = C('PPT_LAYOUT');
			$result     = $File->where($condition)->order('file.id desc')->limit($Page->firstRow.','.$Page->listRows)->select(); //cache($cache_key, 10)->select();
			foreach ($result as &$file)
			{
				$file['ppt_layout'] = $ppt_layout[$file['ppt_layout']];
			}
			unset($file);
			$this->data = $result;
			$this->assign('page', $show);
			$this->display();
		}
		else
		{
			$this->redirect('Printer/Index/index');
		}
	}

	/**
	 * refresh()
	 * 文件列表刷新
	 * @param $file_id UI上最近一次更新最新的文件ID
	 */
	public function refresh()
	{
		$pid = pri_id(U('Index/index'));
		if ($pid)
		{
			$map['pri_id'] = $pid;
			$map['id'] = array('gt', I('file_id', null, 'intval'));
			$map['status'] = array('between', '1,4');
			$File = D('FileView');
			// $cache_key  = cache_name('printer', $pid);
			$ppt_layout = C('PPT_LAYOUT');
			$result = $File->where($map)->order('file.id asc')->limit(10)->select();

			if ($result)
			{
				foreach ($result as &$file)
				{
					$file['ppt_layout'] = $ppt_layout[$file['ppt_layout']];
					if ($file['copies'] == 0 && $file['status'] == C('FILE_DOWNLOAD'))
					{
						$file['operation'] = C('FILE_PRINTED');
					}
					else
					{
						$file['operation'] = $file['status'];
					}
				}
				unset($file);
				$this->success($result);
			}
			else
			{
				$this->error('未成功刷新');
			}

		}
	}

	/**
	 * set()
	 * 更新文件状态（需要验证文件是否在此点打印）
	 * @param $fid    文件id
	 * @param $status 文件状态
	 */
	public function set()
	{

		$pid      = pri_id(U('Index/index'));
		$fid      = I('fid', null, 'intval');
		$download = I('download', 0, 'intval');
		$File     = M('File');
		$result   = $File->where('id="%d"', $fid)->field('status,copies')->find();
		$status['copies'] = $result['copies'];
		if ($result['status'] == C('FILE_UPLOAD'))
		{
			$status['status'] = C('FILE_DOWNLOAD');
			if ($result['copies'] == 0)
			{
				$status['operation'] = C('FILE_PRINTED');
			}
			else
			{
				$status['operation'] = $status['status'];
				$status['sendDownloadMessage']=$this->sendDownloadMessage($pid,$fid);
			}
		}
		elseif (($result['status'] == C('FILE_DOWNLOAD')) && ($download != 1))
		{
			if ($result['copies'] == 0)
			{
				$status['status'] = C('FILE_PAID');
			}
			else
			{
				$status['status'] = C('FILE_PRINTED');
			}
			$status['operation'] = $status['status'];
		}
		elseif (($result['status'] == C('FILE_PRINTED')) && ($download != 1))
		{
			$status['status'] = C('FILE_PAID');
			$status['operation'] = $status['status'];
		}
		else
		{
			$status['status'] = $result['status'];
			if ($download == 1)
			{
				$status['operation'] = C('FILE_PRINTED');
			}
			else
			{
				$status['operation'] = $status['status'];
			}
		}

		if ($status['status'] != $result['status'])
		{
			$result = $File->where('id="%d"', $fid)->setField('status', $status['status']);

			if ($result)
			{
				$this->success($status);
			}
			else
			{
				$this->error('状态不可设置');
			}
		}
		else
		{
			$this->success($status);
		}
	}

/**
 * @method download
 *
 * @author 云小印[yunyin.org]
 *
 * @param  $pid,$fid
 * @return $download_url
 */
	public function download()
	{
		$pid = pri_id(U('Index/index'));
		$fid = I('fid', null, 'intval');
		$map['pri_id'] = $pid;
		$map['id'] = $fid;
		$map['status'] = array('gt', 0);
		$File = M('File');
		$info = $File->where($map)->field('url,status,copies,ppt_layout,color,double_side,name')->find();
		if ($info)
		{
			if ($info['copies'])
			{
				$file_name = $info['copies'].'份_'.($info['double_side'] ? '双面_' : '单面_').($info['color'] ? '彩印_' : '黑白_');
				if ($ppt_layout = $info['ppt_layout'])
				{
					$file_name .= C('PPT_LAYOUT')[$ppt_layout].'版_';
				}
			}
			else
			{
				$file_name = '到店打印_';
			}
			$file_name = $file_name."[$fid]".$info['name'];

			redirect(download_file($info['url'], 'attname='.urlencode($file_name)));
		}
		else
		{
			$this->error('文件已删除，不能再下载！');
		}
	}

	private function sendDownloadMessage($pid,$fid)
	{
		$File    = D('FileView');
		$map['id'] = $fid;
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
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
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

<?php

// ===================================================================
// | FileName: 		FileController.class.php
// ===================================================================
// | Discription：	FileController 文件操作接口
//		<命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014-2015 云印南天团队 All rights reserved.
// +------------------------------------------------------------------

/**
 * Class and Function List:
 * Function list:
 * - index()
 * - read()
 * - set()
 * - del()
 * - id()
 * - upload()
 * Classes list:
 * - FileController extends RestController
 */

namespace API\Controller;
use Think\Controller\RestController;
class FileController extends RestController
{
	
	// REST允许的请求类型列表
	protected $allowMethod     = array('get', 'delete', 'put', 'post');
	
	// REST允许请求的资源类型列表
	protected $allowType       = array('xml', 'json',);
	protected $defaultType     = 'json';
	protected $allowOutputType = array('json'                 => 'application/json', 'xml'                 => 'application/xml');
	
	/**
	 *index
	 *查询文件
	 * 支持操作get
	 *@return json,xml
	 *@author NewFuture
	 */
	public function index() 
	{
		$info            = auth();
		if ($info) 
		{
			switch ($info['type']) 
			{
				case C('PRINTER'):
				case C('PRINTER_WEB'):
					$field           = 'use_id,use_name,student_number,phone';
					$where['pri_id']                 = $info['id'];
					$File            = D('FileView');
					break;

				case C('STUDENT'):
				case C('STUDENT_API'):
					$field = 'pri_id,';
					$where['use_id']       = $info['id'];
					$File  = M('File');
					break;

				default:
					$data['err'] = 'unkown user type';
					break;
			}
			if (!isset($data)) 
			{
				$where['status']           = array('gt', 0);
				$start_id  = I('start', null, 'int');
				$page      = I('page', 1, 'int');
				if ($start_id) 
				{
					$where['id']           = array('gt', $start_id);
				}
				$cache_key = false;
				$field.= 'id,color,ppt_layout,double_side,copies,status,name,time,';
				$data['files']      = $File->field($field)->where($where)->page($page, 10)->cache($cache_key, 10)->select();
			}
		} 
		else
		{
			$data['err']      = 'unauthored';
		}
		$type = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
	
	/**
	 *read()
	 *read
	 *@return json,xml
	 *		查询返回，详细信息
	 *		修改，返回操作结果msg
	 *		出错返回err
	 *@author NewFuture
	 */
	public function read($id) 
	{
		$info = auth();
		$fid  = I('path.1', null, 'int');
		if (!$info) 
		{
			$data['err']      = 'unauthored';
		} 
		else
		{
			
			//请求身份判断
			switch ($info['type']) 
			{
				case C('STUDENT'):
				case C('STUDENT_API'):
					$where['use_id']      = $info['id'];
					break;

				case C('PRINTER'):
				case C('PRINTER_WEB'):
					$where['pri_id'] = $info['id'];
					break;

				default:
					$data['err']      = 'unkown user type';
			}
			if (!$fid) 
			{
				$data['err']      = '无效id';
			} 
			elseif (!$data) 
			{
				$where['id']      = $fid;
				$File = M('File');
				$where['status']      = array('gt', 0);
				$data = $File->where($where)->find();
				if ($data['url'] && isset($where['pri_id'])) 
				{
					$data['url']      = download($data['url']);
				} 
				else
				{
					unset($data['url']);
				}
			}
		}
		$type = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
	
	/**
	 *set()
	 *put
	 *@return json,xml
	 *		修改文件状态
	 *		修改，返回操作结果msg
	 *		出错返回err
	 *@author NewFuture
	 */
	public function set() 
	{
		$info        = auth();
		$fid         = I('path.1', null, 'int');
		if (!$info) 
		{
			$data['err']             = 'unauthored';
		} 
		elseif ($info['type'] != C('PRINTER') && $info['type'] != C('PRINTER_WEB')) 
		{
			$data['err']             = '此接口仅供打印店使用！';
		} 
		else
		{
			if (!$fid) 
			{
				$data['err']             = '无效id';
			} 
			else
			{
				$status      = I('status');
				switch ($status) 
				{
					case 'upload':
					case 1:
						$status_code = 1;
						break;

					case 'download':
					case 2:
						$status_code = 2;
						break;

					case 'printing':
					case 3:
						$status_code = 3;
						break;

					case 'printed':
					case 4:
						$status_code = 4;
						break;

					case 'payed':
					case 5:
						$status_code = 5;
						break;

					default:
						$data['err'] = '非法状态！';
						break;
				}
				if (!$data) 
				{
					$where['id']      = $fid;
					$where['pri_id']      = $info['id'];
					$where['status']      = array('between', '1,4');
					$file = $File->where($where)->field('status')->cache(false)->find();
					if (!$file) 
					{
						$data['err']      = '文件已删除或者已付款！';
					} 
					elseif ($status_code <= $file['status']) 
					{
						$data['err']      = '不允许逆向设置！';
					} 
					elseif ($File->where('id=' . $fid)->setField('status', $status_code)) 
					{
						$data['status']      = $status_code;
					} 
					else
					{
						$data['err']      = '状态设置失败';
					}
				}
			}
		}
		
		$type = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
	
	/**
	 *del(）
	 *delete
	 *@return json,xml
	 *		删除文件
	 *		修改，返回操作结果msg
	 *		出错返回err
	 *@author NewFuture
	 */
	public function del() 
	{
		$info = auth();
		$fid  = I('path.1', null, 'int');
		if (!$info) 
		{
			$data['err']      = 'unauthored';
		} 
		elseif (!$fid) 
		{
			$data['err']      = '无效文件';
		} 
		elseif ($info['type'] != C('STUDENT') && $info['type'] != C('STUDENT_API')) 
		{
			$data['err']      = '此接口只允许学生使用！';
		} 
		else
		{
			$where['id']      = $fid;
			$where['status']      = array(array('eq', 1), array('eq', 5), 'or');
			$file = $File->where($where)->field('url,pri_id')->find();
			if ($file) 
			{
				if (delete_file($file['url'])) 
				{
					$save['status']      = 0;
					$save['url']      = '';
					if ($File->where('id="%d"', $fid)->save($save)) 
					{
						$data['msg']      = '删除成功！';
					}
				} 
				else
				{
					$data['err']      = '删除失败！';
				}
			} 
			else
			{
				$data['err']      = '文件不存在！';
			}
		}
		
		$type = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
		
	/**
	 * upload
	 * 上传文件
	 * 通过post路由
	 */
	public function upload() 
	{
		
		// code...
		
		
	}
}

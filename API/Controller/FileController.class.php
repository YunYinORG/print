<?php

// ===================================================================
// | FileName: 		FileController.class.php
// ===================================================================
// | Discription：	NotificationController 消息查询接口
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
 * - id()
 * Classes list:
 * - FileController extends RestController
 */

namespace API\Controller;
use Think\Controller\RestController;
class FileController extends RestController
{
	
	// REST允许的请求类型列表
	protected $allowMethod = array('get', 'delete', 'put', 'post',);
	protected $defaultType = 'json';
	
	// REST允许请求的资源类型列表
	protected $allowType   = array('xml', 'json',);
	
	/**
	 *index
	 *查询文件
	 * 支持操作get
	 *@return json,xml
	 *@author NewFuture
	 */
	public function index() 
	{
		$info        = auth();
		if ($info) 
		{
			switch ($info['type']) 
			{
			case C('PRINTER'):
				$where['pri_id']             = $info['id'];
				break;

			case C('STUDENT'):
				$where['use_id'] = $info['id'];
				break;

			default:
				$data['err'] = '不支持类型';
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
				
				// cache_name('printer', $info['id']);
				$data['files']           = D('FileView')->where($where)->page($page, 10)->cache($cache_key, 10)->select();
			}
		} else
		{
			$data['err']           = '认证失败';
		}
		$type      = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
	
	/**
	 *id
	 *单个文件查看和修改
	 * 支持操作get，put
	 *@return json,xml
	 *		查询返回，详细信息列表
	 *		修改，返回操作结果msg
	 *		出错返回err
	 *@author NewFuture
	 */
	public function id($value = '') 
	{
		$info  = auth();
		if (!$info) 
		{
			$data['err']       = '认证失败';
		} else
		{
			if ($info['type'] == C('PRINTER')) 
			{
				
				//打印店客户端操作
				$where['pri_id']       = $info['id'];
				$where['id']       = I('get.id', null, 'intval');
				$File  = M('File');
				
				switch ($this->_method) 
				{
				case 'get':
					
					//获取文件信息
					$data  = $File->where($where)->cache(false)->find();
					break;

				case 'put':
				case 'post':
					
					//修改文件状态
					//对于不支持put的操作暂用post代替
					$where['status']             = array('between', '1,4');
					$file        = $File->where($where)->field('status,id')->cache(false)->find();
					if (!$file) 
					{
						$data['err']             = '文件已删除或者已付款！';
					} else
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
						if (!isset($data)) 
						{
							if ($status_code <= $file['status']) 
							{
								$data['err'] = '不允许逆向设置！';
							} elseif ($File->where('id="%d"', $file['id'])->cache(false)->setField('status', $status_code)) 
							{
								$data['msg'] = '修改完成！';
								
								//删除缓存
								S(cache_name('printer', $info['id']), null);
								S(cache_name('user', $file['use_id']), null);
							} else
							{
								$data['err'] = '修改失败';
							}
						}
					}
					break;

				default:
					
					$data['err'] = '不支持操作！';
					break;
				}
			} else
			{
				
				// $where['use_id']             = $info['id'];
				$data['err']      = '此接口暂只支持打印店客户端！';
			}
		}
		$type = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
}

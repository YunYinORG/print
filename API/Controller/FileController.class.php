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
	protected $allowMethod = array(
		'get',
		'delete',
		'put',
		'post',
	);
	protected $defaultType = 'json';
	
	// REST允许请求的资源类型列表
	protected $allowType   = array(
		'xml',
		'json',
	);
	
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
				$page = I('page', 1, 'intval');
				$where['status']=array('gt',0);
				$data['files'] = D('Home/FileView')->where($where)->page($page)->select();
			}
		} else
		{
			$data['err']      = '认证失败';
		}

		$type = ($this->_type == 'xml') ? 'xml' : 'json';
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
				$where['pri_id']       = $info['id'];
				$where['id']       = I('get.id',null,'intval');
				$File  = M('File')->where($where)->cache(true, 120);
				switch ($this->_method) 
				{
				case 'get':
					
					//获取文件信息
					$data  = $File->find();
					break;

				case 'put':
				case 'post':
					
					//修改文件状态
					//对于不支持put的操作暂用post代替
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
						if ($File->setField('status', $status_code) !== false) 
						{
							$data['msg'] = '修改完成！';
						} else
						{
							$data['err'] = '修改失败';
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
				$data['err']      = '此接口暂只支持打印店！';
			}
		}
		$type = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
}

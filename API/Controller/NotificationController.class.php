<?php
// ===================================================================
// | FileName: 		NotificationController.class.php
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
 * - NotificationController extends RestController
 */

namespace API\Controller;
use Think\Controller\RestController;
class NotificationController extends RestController
{
	
	// REST允许的请求类型列表
	protected $allowMethod = array(
		'get',
		'delete'
	);
	protected $defaultType = 'json';
	
	// REST允许请求的资源类型列表
	protected $allowType   = array(
		'xml',
		'json'
	);
	
	/**
	 *index
	 *信息查询
	 * 支持操作get
	 *@return json,xml
	 *@author NewFuture
	 */
	public function index() 
	{
		$info        = auth();
		if ($info) 
		{
			$where['oid']             = $info['oid'];
			$data        = M('Nofification')->where($where)->select();
		} else
		{
			$data['err']             = '认证失败';
		}
		$type        = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
	
	/**
	 *id
	 *单条信息查看和删除
	 * 支持操作get，delete
	 *@return json,xml
	 *@author NewFuture
	 */
	public function id($value        = '') 
	{
		$info         = auth();
		if (!$info) 
		{
			$data['err']              = '认证失败';
		} else
		{
			
			$where['oid']              = $info['oid'];
			$where['id']              = I('id',0,'int');
			
			$Nofification = M('Nofification')->cache(true, 60)->where($where);
			switch ($this->_method) 
			{
			case 'get':
				$data         = $Nofification->find();
				break;

			case 'delete':
				if ($Nofification->delete() !== false) 
				{
					$data['msg'] = '删除成功！';
				} else
				{
					$data['err'] = '删除失败！';
				}
			default:
				$data['err'] = '不支持操作！';
				break;
			}
		}
		$type = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
}

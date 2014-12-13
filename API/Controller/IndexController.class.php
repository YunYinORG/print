<?php
// ===================================================================
// | FileName: 		IndexController.class.php
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
 * - token()
 * Classes list:
 * - IndexController extends RestController
 */
namespace API\Controller;
use Think\Controller\RestController;
class IndexController extends RestController
{
	
	protected $allowMethod = array(
		
		'post',
		'delete',
	);
	protected $defaultType = 'json';
	
	// REST允许请求的资源类型列表
	protected $allowType   = array(
		'xml',
		'json'
	);
	
	/**
	 *index
	 *api令牌生成
	 * 支持操作post
	 *@return json,xml
	 *@author NewFuture
	 */
	public function index() 
	{
		$id          = I('post.id');
		$pwd         = I('post.pwd');
		$type        = I('post.type');
		
		switch ($type) 
		{
		case C('STUDENT'):
			$table       = 'user';
			break;

		case C('PRINTER'):
			$table = 'printer';
			break;

		default:
			$data['err']          = '未知类型';
		}
		
		if (!isset($data)) 
		{
			$password = M($table)->getFieldById($id, 'password');
			if ($password == encode($id, $pwd)) 
			{
				$data['token']          = token($oid);
				$data['oid']          = $id;
				$data['type']          = $type;
				$Token    = M('token');
				$Token->where($data)->delete();
				if (!$Token->add($data)) 
				{
					$data = array(
						'err'      => '创建令牌失败'
					);
				}
			} else
			{
				$data['err']      = '验证失败';
			}
		}
		$this->response($data, (($this->_type == 'xml') ? 'xml' : 'json'));
	}
	
	/**
	 *index
	 *api令牌管理
	 * 支持操作delete
	 *@return json,xml
	 *@author NewFuture
	 */
	public function token() 
	{
		$token = I('token');
		switch ($this->_method) 
		{
		case 'delete':
			if (M('token')->where('token="%s"', $token)->delete() === false) 
			{
				$data['msg']       = '删除成功！';
			} else
			{
				$data['err']       = '删除失败！';
			}
			break;

		default:
			$data['err'] = '非法操作！';
			break;
		}
		$this->response($data, (($this->_type == 'xml') ? 'xml' : 'json'));
	}
}

<?php

// ===================================================================
// | FileName: 		UserController.class.php
// ===================================================================
// | Discription：	UserController 用户信息查询接口
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
 * - UserController extends RestController
 */
namespace API\Controller;
use Think\Controller\RestController;
class UserController extends RestController
{
	
	// REST允许的请求类型列表
	protected $allowMethod = array('get',);
	protected $defaultType = 'json';
	
	// REST允许请求的资源类型列表
	protected $allowType   = array('xml', 'json',);
	
	/**
	 *index
	 *查询个人信息
	 *只允许用户查看自己的信息
	 * 支持操作get
	 *@return json,xml
	 *@author NewFuture
	 */
	public function index() 
	{
		$info        = auth();
		if ($info && ($info['type'] == C('STUDENT_API') || $info['type'] == C('STUDENT'))) 
		{
			$data        = D('User')->field('id,student_number,name,gender,phone,email')->getById($info['id']);
			
			//手机号和邮箱打码
			$data['email']             = $data['mask_email'];
			$data['phone']             = $data['mask_phone'];
			unset($data['mask_email']);
			unset($data['mask_phone']);
		} else
		{
			$data['err']      = '无权访问';
		}
		$type = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
	
	/**
	 *id
	 *根据id查看用户信息，
	 * 支持操作get，put
	 *@return json,xml
	 *		查询返回，详细信息列表
	 *		修改，返回操作结果msg
	 *		出错返回err
	 */
	public function id($id   = '') 
	{
		$info = auth();
		$id   = I('id', null, 'int');
		if ($info && $id) 
		{
			
			switch ($info['type']) 
			{
			case C('STUDENT_API'):
			case C('STUDENT'):
				if ($info['id'] != $id) 
				{
					$data['err']      = '只允许查看自己的信息';
				}
				break;

			case C('PRINTER'):
			case C('PRINTER_WEB'):
				$file['file.pri_id']=$info['id'];
				$file['file.use_id']=$id;
				$file['file.status']=array('gt',0);
				if(!M('file')->where($file)->getField('id'))
				{
					$data['err']='只允许查看当前在此打印的用户信息';
				}
				break;

			default:
				
				$data['err']='未知类型';
				break;
			}
			if (!isset($data)) 
			{
				$where['user.id']      = $id;
				$where['user.status']      = array('gt', 0);
				$data = M('User')->where($where)->field('id,name,student_number,gender,phone,email,status')->find();
				if ($data) 
				{
					import('Common.Encrypt',COMMON_PATH, '.php');
					if ($data['email']) 
					{
						decrypt_email($data['email']);
					}
					if ($data['phone']) 
					{
						decrypt_phone($data['phone'], $data['student_number'], $id);
					}
				}else{
					$data['err']='查询用户不存';
				}
			}
		} else
		{
			$data['err']      = '认证失败';
		}
		$type = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
	
	// /**
	//  *phone
	//  *查看用户手机号
	//  * 支持操作get，put
	//  *@return json,xml
	//  *		查询返回，详细信息列表
	//  *		修改，返回操作结果msg
	//  *		出错返回err
	//  */
	// public function phone($id   = '')
	// {
	// 	$info = auth();
	// 	if (!$info)
	// 	{
	// 		$data['err']      = '认证失败';
	// 	} else
	// 	{
	// 		switch ($info['type'])
	// 		{
	// 		case C('STUDENT'):
	// 		case C('STUDENT_API'):
	// 			$user = M('User')->field('student_number,phone')->getById($info['id']);
	// 			if ($user['phone'])
	// 			{
	// 				import('Encrypt', COMMON_PATH, 'php');
	// 				$data['phone'] = decrypt_phone($user['phone'], $user['student_number'], $info['id']);
	// 			} else
	// 			{
	// 				$data['phone'] = null;
	// 			}
	// 			break;
	
	// 		case C('PRINTER'):
	// 		case C('PRINTER_WEB'):
	
	// 			//todo
	// 			//验证是否有未删除文件在此
	// 			break;
	
	// 		default:
	
	// 			$data['err'] = '未定义类型';
	// 			break;
	// 		}
	// 	}
	// 	$type = ($this->_type == 'xml') ? 'xml' : 'json';
	// 	$this->response($data, $type);
	// }
	
	
}

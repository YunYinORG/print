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
 * - phone()
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
		if ($inf && $info['type'] == C('STUDENT_API')) 
		{
			$data        = D('User')->field('id,student_number,name,gender,phone,email,status')->getById($info['id']);
			
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
	 *根据id查看用户信息，供打印店使用
	 * 支持操作get，put
	 *@return json,xml
	 *		查询返回，详细信息列表
	 *		修改，返回操作结果msg
	 *		出错返回err
	 */
	public function id($id   = '') 
	{
		$info = auth();
		if (!$info) 
		{
			$data['err']      = '认证失败';
		} else
		{
			
			//查询用户信息;
			
			
		}
		$type = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
	
	/**
	 *phone
	 *查看用户手机号
	 * 支持操作get，put
	 *@return json,xml
	 *		查询返回，详细信息列表
	 *		修改，返回操作结果msg
	 *		出错返回err
	 */
	public function phone($id   = '') 
	{
		$info = auth();
		if (!$info) 
		{
			$data['err']      = '认证失败';
		} else
		{
			switch ($info['type']) 
			{
			case C('STUDENT'):
			case C('STUDENT_API'):
				$user = M('User')->field('student_number,phone')->getById($info['id']);
				if ($user['phone']) 
				{
					import('Encrypt', COMMON_PATH, 'php');
					$data['phone'] = decrypt_phone($user['phone'], $user['student_number'], $info['id']);
				} else
				{
					$data['phone'] = null;
				}
				break;

			case C('PRINTER'):
			case C('PRINTER_WEB'):
				
				//todo
				//验证是否有未删除文件在此
				break;

			default:
				
				$data['err'] = '未定义类型';
				break;
			}
		}
		$type = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
}

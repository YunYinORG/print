<?php

// ===================================================================
// | FileName: 		PrinterController.class.php
// ===================================================================
// | Discription：	PrinterController 打印店信息查询接口
//		<命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014~2015 云印南开团队 All rights reserved.
// +------------------------------------------------------------------
/**
 * Class and Function List:
 * Function list:
 * - index()
 * - id()
 * Classes list:
 * - PrinterController extends RestController
 */
namespace API\Controller;
use Think\Controller\RestController;
class PrinterController extends RestController
{
	
	// REST允许的请求类型列表
	protected $allowMethod = array('get',);
	protected $defaultType = 'json';
	
	// REST允许请求的资源类型列表
	protected $allowType   = array('xml', 'json',);
	
	/**
	 *index
	 *查询打印店列表
	 *支持操作get
	 *@return json,xml
	 *@author NewFuture
	 */
	public function index() 
	{
		$start_id    = I('get.start', null, 'int');
		$page        = I('get.page', 1, 'int');
		if ($start_id) 
		{
			$where['id']             = array('gt', $start_id);
		}
		$where['status']             = array('gt', 0);
		
		//设置1个小时的缓存时间
		$data['printers']             = M('Printer')->field('id,name,address')->where($where)->cache(3600)->page($page, 10)->select();
		$type        = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
	
	/**
	 *id
	 *查看打印店详情，
	 * 支持操作get
	 *@return json,xml
	 *		查询返回，详细信息列表
	 *		修改，返回操作结果msg
	 *		出错返回err
	 */
	public function id() 
	{
		$info = auth();
		$info['type']      = 4;
		$id   = I('id', 0, 'int');
		if ($info && $id && ($info['type'] == C('STUDENT_API') || $info['type'] == C('STUDENT'))) 
		{
			$data = M('Printer')->field('account,password', true)->getById($id);
			if (!$data) 
			{
				$data['err']      = '该店不存在';
			}
		} else
		{
			$data['err']      = '无权查看';
		}
		$type = ($this->_type == 'xml') ? 'xml' : 'json';
		$this->response($data, $type);
	}
}

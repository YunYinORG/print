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
// | Copyright (c) 2014-2015 云印南开团队 All rights reserved.
// +------------------------------------------------------------------
/**
 * Class and Function List:
 * Function list:
 * - index()
 * - test()
 * - get()
 * - post()
 * - put()
 * - delete()
 * - token()
 * Classes list:
 * - IndexController extends RestController
 */
namespace API\Controller;
use Think\Controller\RestController;
class IndexController extends RestController
{
	
	protected $allowMethod = array('get', 'post', 'put', 'delete');
	protected $defaultType = 'json';
	
	// REST允许请求的资源类型列表
	protected $allowType   = array('xml', 'json', 'html');
	
	/**
	 *index
	 *@author NewFuture
	 */
	public function index() 
	{
		$version     = C('API_VERSION');
		$api_doc     = 'https://github.com/nkumstc/print/blob/master/API.md';
		$BaseURL     = 'http://' . I('server.HTTP_HOST') . '/api.php';
		
		$header      = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>云印API</title><style rel="stylesheet" type="text/css">*{text-align:center} li{list-style:none}</style></head>';
		$body        = "<body><h1><b>云印</b><small><sup>南天</sup></small>开放API<small>【版本<u>$version</u>】</small></h1>";
		$main        = "<main><h4>最新API文档： <a href='$api_doc'>$api_doc</a><br/></h4>
<h4>云印API接口示意图</h4><img src='/Public/images/api.png' alt='云印API接口示意图'>
<ul><h5>API测试链接(支持json,xml,和html格式返回数据)</h5>
<li>查看请求信息$BaseURL/Index/test</li>
<li><code>GET</code>操作测试$BaseURL/Index/get</li>
<li><code>POST</code>操作测试$BaseURL/Index/post</li>
<li><code>PUT</code>操作测试$BaseURL/Index/put</li>
<li><code>DELETE</code>操作测试$BaseURL/Index/delete</li>
<li><code>Token</code>传递测试$BaseURL/Index/token</li>
<small>操作测试返回说明：<q>code为1表示操作方式正确,param为请求参数；token显示为0则传递失败</q></small></ul></main>";
		$footer      = '<footer>Copyright &copy; 2014-2015 云印南天</footer></body></html>';
		$this->show($header . $body . $main . $footer);
	}
	
	/**
	 *test
	 *请求信息信息测试
	 *@return json,xml
	 *@author NewFuture
	 */
	public function test() 
	{
		$data['METHOD'] = $this->_method;
		$data['TYPE'] = $this->_type;
		
		$data['GET'] = I('get.');
		$data['POST'] = I('post.');
		$data['PUT'] = I('put.');
		
		$data['PATHINFO'] = I('path.');
		$data['REQUEST'] = I('request.');
		$data['HEADER'] = getallheaders();
		
		unset($data['HEADER']['Cookie']);
		unset($data['HEADER']['cookie']);
		unset($data['HEADER']['COOKIE']);
		
		if ($this->_type == 'html') 
		{
			var_dump($data);
		} else
		{
			$this->response($data, $this->_type);
		}
	}
	
	/**
	 *get操作测试
	 */
	public function get($name = '') 
	{
		$data['code']      = $this->_method == 'get' ? 1 : 0;
		$data['param']      = I('get.');
		if ($this->_type == 'html') 
		{
			var_dump($data);
		} else
		{
			$this->response($data, $this->_type);
		}
	}
	
	/**
	 *post操作测试
	 */
	public function post() 
	{
		$data['code'] = $this->_method == 'post' ? 1 : 0;
		$data['param'] = I('post.');
		if ($this->_type == 'html') 
		{
			var_dump($data);
		} else
		{
			$this->response($data, $this->_type);
		}
	}
	
	/**
	 *put操作测试
	 */
	public function put() 
	{
		$data['code'] = $this->_method == 'put' ? 1 : 0;
		$data['param'] = I('put.');
		if ($this->_type == 'html') 
		{
			echo '此链接测试不适用浏览器直接测试！';
			var_dump($data);
		} else
		{
			$this->response($data, $this->_type);
		}
	}
	
	/**
	 *delete操作测试
	 */
	public function delete() 
	{
		$data['code'] = $this->_method == 'delete' ? 1 : 0;
		if ($this->_type == 'html') 
		{
			echo '此链接测试不适用浏览器直接测试！';
			var_dump($data);
		} else
		{
			$this->response($data, $this->_type);
		}
	}
	
	/**
	 *token测试
	 */
	public function token() 
	{
		$token = get_token();
		$data['token']=$token? substr_replace($token, '**********************', 4,24):0;
		if ($this->_type == 'html') 
		{
			echo $token ? :'0<br>请保证测试的token为34~48位的字母或数字构成的字符串，否则会被过滤掉！';
		} else
		{
			$this->response($data, $this->_type);
		}
	}
}

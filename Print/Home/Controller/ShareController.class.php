<?php

// ===================================================================
// | FileName:      ShareController.class.php
// ===================================================================
// | Discription：   共享用户控制器
//      <命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014-2015 云印团队 All rights reserved.
// +------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +------------------------------------------------------------------
/**
 * Class and Function List:
 * Function list:
 * - index()
 * Classes list:
 * - EmptyController extends Controller
 */
namespace Home\Controller;
use Think\Controller;

class ShareController extends Controller {


	/**
	 * 自己分享的文件列表页
	 * @method index
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function index()
	{
		# code...
	}

	/**
	 * 分享文件搜索页
	 * @method search
	 * @param  输入
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function search()
	{
		# code...
	}

	/**
	 * 文件详细信息页
	 * @method detail
	 * @param  输入
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function detail()
	{

	}


	/**
	 * 添加分享文件
	 * @method push
	 * @param  输入
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function add()
	{

	}


	/**
	 * 创建新的标签
	 * @method addTag
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function createTag()
	{

	}

	/**
	 * 查询标签列表
	 * @method getTags
	 * @return [type]  [description]
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function getTags()
	{

	}


	/**
	 * 为分享的文件添加标签
	 * @method addTags
	 * @param  string  $value [description]
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function addTags()
	{
		# code...
	}

	/**
	 * 分享文件重命名
	 * @method rename
	 * @param  string $value [description]
	 * @return [type]        [description]
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function rename()
	{
		# code...
	}

	public function _empty()
	{
		$this->redirect('Index/index');
	}
}

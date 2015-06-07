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
		$this->data = array( array('id' =>'3' ,'name'=>'fsdafadsf','uploader'=>'dafdsf','time'=>'1999-10-11' ),array('id' =>'1' ,'name'=>'fsdafadsf','uploader'=>'dafdsf','time'=>'1999-10-11' ),array('id' =>'2' ,'name'=>'fsdafadsf','uploader'=>'dafdsf','time'=>'1999-10-11' ) );
	    $this->display();	
	}

	/**
	 * 分享文件搜索api
	 * @method searchAPI
	 * @param  输入
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function searchAPI()
	{
		$data = array( array('id' =>'1' ,'name'=>'bla','uploader'=>'dafdsf','time'=>'1999-12-11' ),array('id' =>'3' ,'name'=>'fadsf','uploader'=>'dafdsf','time'=>'1999-10-11' ),array('id' =>'2' ,'name'=>'fsdafadsf','uploader'=>'dafdsf','time'=>'1999-10-11' ) );
	    $this->success($data);	
	}
	/**
	 * 文件详细信息页
	 * @method detail
	 * @param  输入
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function detail()
	{	
		$fid = I('id');
        $map['id'] = $fid;
		$File = M('File');
		$result = $File->where($map)->find();
		$this->data = array(
			'id' =>'3' ,
			'name'=>'fsdafadsf',
			'upload_user'=>'dafdsf',
			'time'=>'1999-10-11',
			'thumbnail'=>get_thumbnail_url($result['url'])
		);
		$this->label = array(
			array('id' => 18,'name'=>'fafdsa' ),
			array('id' => 1,'name'=>'fafdsddddda' ),
			array('id' => 13,'name'=>'fafdddsa' )
			);
		$this->display();
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
		$label = I('label');
        $label_list = array(['id'=>'1','name'=>$label],['id'=>'2','name'=>$label.rand()],['id'=>'3','name'=>$label.rand()]);
        $this->success($label_list);
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

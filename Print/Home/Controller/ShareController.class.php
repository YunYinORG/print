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
 * - search()
 * - searchAPI()
 * - detail()
 * - add()
 * - createTag()
 * - getTags()
 * - addTags()
 * - rename()
 * - _empty()
 * Classes list:
 * - ShareController extends Controller
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
		$this->data = array(array('id' => '3', 'name' => 'fsdafadsf', 'uploader' => 'dafdsf', 'time' => '1999-10-11'), array('id' => '1', 'name' => 'fsdafadsf', 'uploader' => 'dafdsf', 'time' => '1999-10-11'), array('id' => '2', 'name' => 'fsdafadsf', 'uploader' => 'dafdsf', 'time' => '1999-10-11'));
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
		$data = array(array('id' => '1', 'name' => 'bla', 'uploader' => 'dafdsf', 'time' => '1999-12-11'), array('id' => '3', 'name' => 'fadsf', 'uploader' => 'dafdsf', 'time' => '1999-10-11'), array('id' => '2', 'name' => 'fsdafadsf', 'uploader' => 'dafdsf', 'time' => '1999-10-11'));
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
		$fid = I('id',null,'int');
		$map['id'] = $fid;
		$File   = M('Share')->getById($fid);//field('name,time,fid,anomonity')
		$this->data = array('id' => '3', 'name' => 'fsdafadsf', 'upload_user' => 'dafdsf', 'time' => '1999-10-11', 'thumbnail' => get_thumbnail_url($result['url']));
		$this->tags=M('hastag')->where('sha_id=%d',$fid)->field('name','tag_id')->select();
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
		$uid  = use_id();
		$name = I('tag', null, 'trim');
		if ( ! $uid)
		{
			$this->error('未登录！', '/');
		}
		elseif ( ! $tag)
		{
			$this->error('无效标签');
		}
		else
		{
			$tag = array(
				'name'   => $name,
				'use_id' => $uid,
			);

			$tid = M('Tag')->add($tag);
			if ($tid)
			{
				$this->success($tid);
			}
			else
			{
				$this->error('添加失败!');
			}
		}
	}

	/**
	 * 查询标签列表
	 * @method getTags
	 * @return [type]  [description]
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function getTags()
	{
		if ( ! use_id())
		{
			$this->error('未登录!', '/');
		}
		else
		{
			$tag  = I('tag', null, 'trim');
			$tags = M('Tag')->where('name LIKE "%s"', $tag)->order('count desc')->limit(10)->select();
			$this->success($tags);
		}

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
		$this->redirect('/');
	}
}

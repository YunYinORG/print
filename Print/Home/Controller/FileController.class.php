<?php
// ===================================================================
// | FileName:      FileController.class.php
// ===================================================================
// | Discription：   FileController 文件管理控制器
//      <命名规范：>
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
 * - add()
 * - uploadOne()
 * - upload()
 * - getToken()
 * - deleteTempFile()
 * - multi()
 * - delete()
 * - _empty()
 * - _getExt()
 * Classes list:
 * - FileController extends Controller
 */
namespace Home\Controller;
use Think\Controller;

class FileController extends Controller {
	/**
	 * 文件列表页
	 */
	public function index()
	{
		$uid = use_id(U('Index/index'));
		if ($uid)
		{
			$condition['use_id'] = $uid;
			$condition['status'] = array('between', '1,5');
			$File  = D('FileView');
			$count = $File->where($condition)->count();
			$Page  = new \Think\Page($count, 10);
			$show  = $Page->show();
			//            $cache_key=cache_name('user',$uid);
			$ppt_layout = C('PPT_LAYOUT');
			$result = $File->where($condition)->order('file.id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			//cache($cache_key)->select();
			foreach ($result as &$file)
			{
				$file['ppt_layout'] = $ppt_layout[$file['ppt_layout']];
				$file['pdf'] = (substr($file['name'], -4) == '.pdf');
			}
			unset($file);
			$this->data = $result;
			$this->assign('page', $show);
			$this->display();
		}
		else
		{
			$this->redirect('Index/index');
		}
	}

	/**
	 * 上传页面
	 */
	public function add()
	{
		$uid = use_id(U('Index/index'));
		if ($uid)
		{
			$Printer = M('Printer');
			$User    = M('User');
			$user    = $User->Field('sch_id,phone')->getById($uid);
			$this->lock = $user['phone'] ? 1 : 0;
			$condition['sch_id'] = $user['sch_id'];
			$condition['status'] = 1;
			$this->data = $Printer->where($condition)->order('rank desc')->Field('id,name,address')->select();
			$this->ppt = C('PPT_LAYOUT');
			$this->display();
		}
		else
		{
			$this->redirect('/Index/index');
		}
	}

	/**
	 * 单文件上传
	 */
	public function uploadOne()
	{
		$uid = use_id(U('/Index/index'));
		if ($uid)
		{
			$info = upload_file();
			$name = isset($info['file']['name']) ? $info['file']['name'] : false;
			if ($info && $name)
			{
				if (mb_strlen($name) > 62)
				{
					$name = mb_substr($name, 0, 58).'.'.$info['file']['ext'];
				}
				// $data['status'] = 1;
				// $data['pri_id'] = I('post.pri_id', 0, 'int');
				$data['use_id'] = $uid;
				$data['name'] = $name;
				$data['url'] = $info['file']['savepath'].$info['file']['savename'];

				$File = D('File');
				if ($File->create($data) && $File->add()) //上传

				{
					$this->redirect('File/index', null, 0, '上传成功');
				}
				else
				{
					$this->error('保存信息出错啦！', '/File/add');
				}
			}
			else
			{
				$this->error('文件上传失败！', '/File/add');
			}
		}
		else
		{
			$this->error('请登录！', '/');
		}
	}

	/**
	 * 文件批量上传
	 * @method upload
	 * @return [type] [description]
	 * @author 孙卓豪[@yunyin.org]
	 * @author 修改NewFuture
	 */
	public function upload()
	{
		$uid   = use_id();
		$files = I('post.files');
		if ( ! $uid)
		{
			/*未登录*/
			$this->error(L('UNLOGIN'), __ROOT__);
		}
		elseif ( ! $files)
		{
			/*未登录*/
			$this->error('无文件');
		}
		else
		{
			/*批量获取文件名*/
			/*获取文件设置基本信息*/
			$File = D('File');
			$file_data = array();
			$file_data['use_id'] = $uid;
			$file_data = $File->create($file_data);
			/*获取和更新session中的缓存表信息*/
			$upload_list = session('uploads');
			session('uploads', null);

			$result = array();
			foreach ($files as $path)
			{
				$old_name = $upload_list[$path];
				if ($old_name)
				{
					$url = 'upload_'.$path;
					rename_file('temp_'.$path, $url);
					$file_data['url'] = $url;
					$file_data['name'] = $old_name;
					if ($File->add($file_data))
					{
						$result[] = array('name' => $old_name, 'r' => 1);
					}
					else
					{
						$result[] = array('name' => $old_name, 'r' => 0);
					}
				}
			}
			if (empty($result))
			{
				$this->error('上传失败');
			}
			else
			{
				$this->success($result);
			}
		}
	}

	/**
	 * 共享文件打印
	 * @method sharePrint
	 * @return [type]     [description]
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function sharePrint()
	{
		$uid = use_id();
		$sid = I('share_id', 0, 'int');
		if ( ! $uid)
		{
			$this->error(L('UNLOGIN'), '/');
		}
		elseif ( ! $sid)
		{
			$this->error(L('PARAM_ERROR'));
		}
		else
		{

			$data = D('ShareView')->where('share.id=%d', $sid)->field('url,name')->find();
			if ( ! $data['url'])
			{
				$this->error('文件已经删除！');
			}
			$data['use_id'] = $uid;
			$File = D('File');
			if ( ! $File->create($data))
			{
				$this->error('数据获取失败:'.$File->getError());
			}
			elseif ($File->add())
			{
				$this->success('传送成功！',U('File/index'));
			}
			else
			{
				$this->error('传送失败');
			}
		}
	}

	/**
	 * 获取上传token
	 * @method getToken
	 * @return [type]   [description]
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function getToken()
	{
		$uid = use_id();
		$file_name = I('post.filename');
		if ( ! $uid)
		{
			$this->error(L('UNLOGIN'), __ROOT__);
		}
		elseif ( ! $file_name)
		{
			$this->error('无效文件名');
		}
		else
		{
			/*获取token*/
			$suffix = $this->_getExt($file_name);
			$path   = date('Y-m-d').'_'.uniqid().'.'.$suffix;
			$token  = upload_token('temp_'.$path);

			if ( ! $token)
			{
				$this->error('获取token失败');
			}
			else
			{
				/*token获取成功*/
				/*更新上传缓存表映射*/
				$upload_list = session('uploads');
				$upload_list[$path] = $file_name;
				session('uploads', $upload_list);
				/*返回上传凭证*/
				header('Access-Control-Allow-Origin:http://upload.qiniu.com');
				/*最好返回上传url而不是name*/
				$token_info = array('name' => $path, 'token' => $token);
				$this->success($token_info);
			}
		}
	}

	/**
	 * 删除批量上传中的文件
	 * @method deleteTempFile
	 * @return [type]         [description]
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function deleteTempFile()
	{
		$path = I('post.path');
		$uid  = use_id();
		if ( ! $uid)
		{
			$this->error(L('UNLOGIN'), __ROOT__);
		}
		elseif ( ! $path)
		{
			$this->error('无效文件名');
		}
		else
		{
			/*检查文件是否是真正上传的文件*/
			$upload_list = session('uploads');
			if ( ! isset($upload_list[$path]))
			{
				$this->error('文件不存在');
			}
			else
			{
				/*更新上传映射*/
				unset($upload_list[$path]);
				session('uploads', $upload_list);
				/*删除文件*/
				if (delete_file($path, 'QINIU'))
				{
					$this->success(true);
				}
				else
				{
					$this->error('删除失败');
				}
			}
		}
	}

	/**
	 * 多文件上传页
	 * @method multi
	 * @return [type] [description]
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function multi()
	{
		$uid = use_id(U('Index/index'));
		if ($uid)
		{
			$Printer = M('Printer');
			$User    = M('User');
			$user    = $User->Field('sch_id,phone')->getById($uid);
			$this->lock = $user['phone'] ? 1 : 0;
			$condition['sch_id'] = $user['sch_id'];
			$condition['status'] = 1;
			$this->data = $Printer->where($condition)->order('rank desc')->Field('id,name,address')->select();
			$this->ppt = C('PPT_LAYOUT');
			$this->display();
		}
		else
		{
			$redirect(__ROOT__);
		}
	}

	/**
	 * 	删除文件记录
	 * @method delete
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function delete()
	{
		$uid = use_id(U('Index/index'));
		$fid = I('fid', null, 'int');
		if ($uid && $fid)
		{
			$map['id'] = $fid;
			$map['_string'] = 'status=1 OR status=5';
			$File = M('File');
			$file = $File->where($map)->Field('url,pri_id')->find();
			if ($file)
			{
				$url = $file['url'];
				if (delete_file($url))
				{
					$data['status'] = 0;
					$data['url'] = '';
					$result = $File->where($map)->save($data);
					if ($result)
					{
						$this->success($result);
					}
					$this->error('记录更新异常');
				}
				$this->error('文件不可删除');
			}
			$this->error('记录已不存在');
		}
		$this->error('当前状态不允许删除！');
	}

	public function _empty()
	{
		$this->redirect('index');
	}

	/**
	 * 获取文件后缀名
	 * @method _getExt
	 * @param  string  $filename [文件名]
	 * @return [type]            [扩展名不包含.]
	 * @access private
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	private function _getExt($filename)
	{
		$pos = strrpos($filename, '.');
		if ($pos > 0)
		{
			return strtolower(substr($filename, $pos + 1));
		}
		else
		{
			return '';
		}
	}
}

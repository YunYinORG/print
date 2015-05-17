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
 * - delete()
 * - _empty()
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

	public function paper()
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

				// $data['copies'] = I('post.copies', 0, 'int') < 0 ? 0 : I('post.copies', 0, 'int');
				// $data['double_side'] = I('post.double_side', 0, 'int');
				// $data['color'] = I('post.color', 0, 'int');
				// $requirements = I('post.requirements', 0, 'htmlspecialchars');
				// if ($requirements)
				// {
				// 	$data['requirements'] = $requirements;
				// }

				// if ($data['pri_id'] <= 0)
				// {
				// 	$this->error('请选择打印店！', '/File/add');
				// }
				// if ($info['file']['ext'] == 'ppt' || $info['file']['ext'] == 'pptx')
				// {
				// 	$data['ppt_layout'] = I('post.ppt_layout', 0, 'int');
				// }
				// else
				// {
				// 	$data['ppt_layout'] = 0;
				// }
				//

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

	public function upload()
	{
		$uid = use_id(U('/Index/index'));
		if ($uid)
		{
			$filenames = I('post.filenames');
			$newNames  = I('post.newNames');
			$suffixes  = I('post.suffixes');
			$number    = I('post.number', 0, int);
			$File      = D('File');
			$result    = array();
			for ($i = 0; $i < $number; $i++)
			{
				$name   = $filenames[i];
				$suffix = $suffixes[i];
				if (mb_strlen($name) > 62)
				{
					$name = mb_substr($name, 0, 58).'.'.$suffix;
				}
				$data['use_id'] = $uid;
				$data['name'] = $name;
				$data['url'] = $newNames[i];

				if ($File->create($data) && $File->add()) //上传
				{
					$result[$data['name']] = 1;
				}
				else
				{
					$result[$data['name']] = 0;
				}
			}
			$this->success($result);
		}
		else
		{
			$this->error('请登录！', '/');
		}
	}

	/**
	 * 删除文件记录
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

	public function getToken()
	{
		$uid = use_id(U('Index/index'));
		if ($uid)
		{
			$setting = C('UPLOAD_CONFIG_QINIU');
			$setting['timeout'] = 300;
			$new_name = 'temp_'.date('Y-m-d').'_'.uniqid();
			F($new_name, I('post.filename'));
			$data = array('scope' => $setting['bucket'].':'.$new_name, 'deadline' => $setting['timeout'] + time(), 'returnBody' => '{"rname":$(fname),"name":$(key)}');
			$uploadToken = \Think\Upload\Driver\Qiniu\QiniuStorage::SignWithData($setting['secretKey'], $setting['accessKey'], json_encode($data));
			header('Access-Control-Allow-Origin:http://upload.qiniu.com');
			$result = array('name' => $new_name, 'token' => $uploadToken);
			$this->success($result);
		}
		else
		{
			$this->error('login');
		}

	}

	public function deleteTempFile()
	{
		$uid = use_id(U('/Index/index'));
		if ($uid)
		{
			$path    = I('filename');
			$setting = C('UPLOAD_CONFIG_QINIU');
			$setting['timeout'] = 300;
			$url    = str_replace('/', '_', $path);
			$qiniu  = new \Think\Upload\Driver\Qiniu\QiniuStorage($setting);
			$result = $qiniu->del($url);
			if ($result)
			{
				$this->success($result);
			}
			else
			{
				$this->error('not');
			}
		}
		else
		{
			$this->error('请登录！', '/');
		}

	}

	public function renameTempFile()
	{
		$uid = use_id(U('/Index/index'));
		if ($uid)
		{
			$path    = I('filename');
			$setting = C('UPLOAD_CONFIG_QINIU');
			$setting['timeout'] = 300;
			$url     = str_replace('/', '_', $path);
			$newPath = str_replace('temp_', '', $path);
			$qiniu   = new \Think\Upload\Driver\Qiniu\QiniuStorage($setting);
			$result  = $qiniu->rename($url, $newPath);
			if ($result)
			{
				$this->success('deleted');
			}
			else
			{
				$this->error('not');
			}
		}
		else
		{
			$this->error('请登录！', '/');
		}

	}

	public function temp()
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

	public function _empty()
	{
		$this->redirect('index');
	}
}

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
 * - upload()
 * - delete()
 * - test()
 * Classes list:
 * - FileController extends Controller
 */
namespace Home\Controller;
use Think\Controller;
class FileController extends Controller
{
    
    /**
     *文件列表页
     *@TODO：分页 
     */
    public function index() 
    {
        $uid        = use_id(U('Index/index'));
        if ($uid) 
        {
            $condition['use_id']            = $uid;
            $condition['status']            = array('between', '1,5');
            $File       = D('FileView');
            $cache_key=cache_name('user',$uid);
            $this->data = $File->where($condition)->order('file.id desc')->cache($cache_key)->select();
            $this->display();
        } else
        {
            $this->redirect('Index/index');
        }
    }
    
    /**
     *上传页面
     */
    public function add() 
    {
        $uid = use_id(U('Index/index'));
        if ($uid) 
        {
            $this->display();
        } else
        {
            $this->redirect('/Index/index');
        }
    }
    
    /**
     *上传处理
     */
    
    public function upload() 
    {
    	/* 设置内部字符编码为 UTF-8 */
    	mb_internal_encoding("UTF-8");
        $uid              = use_id(U('/Index/index'));
        if ($uid) 
        {
            $sid=M('User')->cache(true)->getFieldById($uid,'student_number');
            $copies=I('post.copies',0,'int');
            $double=I('post.double_side',0,'int');
            $filename=($double+1).'X'.$copies.'_['.$sid.']_'.date('Y-m-d_H-i_U');
           
            $upload           = new \Think\Upload();
            $upload->maxSize  = 10485760;
            $upload->exts     = array('doc', 'docx', 'pdf', 'wps', 'ppt', 'pptx');
            $upload->rootPath = './Uploads/';
            $upload->savePath ='';
            $upload->saveName = $filename;
            $info             = $upload->upload();
            if (!$info) 
            {
                $this->error($upload->getError());
            } else
            {
                foreach ($info as $file) 
                {
                	$name=$file['name'];
                	if(mb_strlen($name)>62)
                	{
                		$name=mb_substr($name,0,58).'.'.$file['ext'];
                	}
                    $data['name']                      = $name;
                    $data['pri_id']                    = I('post.pri_id',0,'int');
                    // $data['requirements']              = I('post.requirements');
                    $data['url']                      = $file['savepath'] . $file['savename'];
                    $data['status']                      = 1;
                    $data['use_id']                      = $uid;
                    $data['copies']                      = $copies;
                    $data['double_side']                      = $double;
                    $File                 = M('File');
                    $result               = $File->cache(true)->add($data);
                    if ($result) 
                    {
                        $Notification         = M('Notification');
                        $Notification->fil_id = $result;
                        $Notification->to_id  = $data['pri_id'];
                        $Notification->type   = 1;
                        $Notification->add();
                        //删除缓存
                        S(cache_name('user',$uid),null);
                        S(cache_name('printer',$pid),null);
                        $this->redirect('File/index',null,0,'上传成功');
                    } else
                    {
                        $this->error($File->getError(),'/File/add',1);
                    }
                }
            }
        } else
        {
            $this->error('登录信息已失效', '/Index/index');
        }
    }
    
    /**
     *删除文件记录
     */
    public function delete() 
    {
        $uid          = use_id(U('Index/index'));
        $fid          = I('fid', null, 'int');
        if ($uid && $fid) 
        {
            $map['id']              = $fid;
            $map['_string']              = 'status=1 OR status=5';
            $File         = M('File');
            $file      = $File->where($map)->cache(true)->Field('url,pri_id')->find();
            if ($file) 
            {
                $url=$file['url'];
                if (delete_file("./Uploads/" . $url)) 
                {
                    $data['status'] = 0;
                    $data['url']    = '';
                    $result    = $File->where($map)->cache(true)->save($data);
                    if ($result) 
                    {
                        //删除缓存
                        S(cache_name('user',$uid),null);
                        S(cache_name('printer',$file['pri_id']),null);
                        $this->success($result);
                        return;
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
}

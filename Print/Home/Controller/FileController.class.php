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
class FileController extends Controller
{
    
    /**
     *文件列表页
     */
    public function index() 
    {
        $uid        = use_id(U('Index/index'));
        if ($uid) 
        {
            $condition['use_id']            = $uid;
            $condition['status']            = array('between', '1,5');
            $File       = D('FileView');
            $count      = $File->where($condition)->count();
            $Page       = new \Think\Page($count, 10);
            $show       = $Page->show();
            
            //            $cache_key=cache_name('user',$uid);
            $this->data = $File->where($condition)->order('file.id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
            
            //cache($cache_key)->select();
            $this->assign('page', $show);
            $this->display();
        } 
        else
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
            $Printer = M('Printer');
            $User = M('User');
            $user = $User->Field('sch_id,phone')->getById($uid);
            $this->lock = $user['phone'] ? 1 : 0;
            $condition['sch_id'] = $user['sch_id'];
            $condition['status'] = 1;
            $this->data = $Printer->where($condition)->order('rank desc')->Field('id,name,open_time,address')->select();
            $this->display();
        } 
        else
        {
            $this->redirect('/Index/index');
        }
    }
    
    /**
    *单文件上传
    */
    public function uploadOne() 
    {
        $uid  = use_id(U('/Index/index'));
        if ($uid) 
        {
            $info = upload_file('QINIU');
            $name = isset($info['file']['name']) ? $info['file']['name'] : false;
            if ($info && $name) 
            {                
                if (mb_strlen($name) > 62) 
                {
                    $name = mb_substr($name, 0, 58) . '.' . $info['file']['ext'];
                }
                $data['pri_id']      = I('post.pri_id', 0, 'int');
                $data['use_id']      = $uid;
                $data['name']      = $name;
                $data['url']      = $info['file']['savepath'] . $info['file']['savename'];
                $data['copies']      = I('post.copies', 0, 'int') < 0 ? 0 : I('post.copies',0,'int');
                $data['double_side'] = I('post.double_side', 0, 'int');
                $data['status'] = 1;
                $data['color'] = I('post.color', 0, 'int');
                if ($info['file']['ext']=="ppt" ||$info['file']['ext']=="pptx")
                {
                    $data['ppt_layout'] = I('post.ppt_layout', 0, 'int');
                }
                else
                {
                    $data['ppt_layout'] = 0;
                }    
                if (M('File')->add($data)) 
                {
                    
                    //判断通知
                    $this->redirect('File/index', null, 0, '上传成功');
                } 
                else
                {
                    $this->error('保存信息出错啦！','/File/add');
                }
            } 
            else
            {
                $this->error('文件上传失败！','/File/add');
            }
        } 
        else
        {
            $this->error('请登录！', '/');
        }
    }
    
    
    
    /**
     *删除文件记录
     */
    public function delete() 
    {
        $uid    = use_id(U('Index/index'));
        $fid    = I('fid', null, 'int');
        if ($uid && $fid) 
        {
            $map['id']        = $fid;
            $map['_string']        = 'status=1 OR status=5';
            $File   = M('File');
            $file   = $File->where($map)->cache(true)->Field('url,pri_id')->find();
            if ($file) 
            {
                $url    = $file['url'];
                if (delete_file($url)) 
                {
                    $data['status']        = 0;
                    $data['url']        = '';
                    $result = $File->where($map)->cache(true)->save($data);
                    if ($result) 
                    {
                        
                        //删除缓存
                        //                        S(cache_name('user',$uid),null);
                        //                        S(cache_name('printer',$file['pri_id']),null);
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
}

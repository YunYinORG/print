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
            $info = upload_file();
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
                $data['copies']      = I('post.copies', 0, 'int');;
                $data['double_side'] = I('post.double_side', 0, 'int');
                $data['status'] = 1;
                $data['color'] = I('post.color', 0, 'int');
                $data['ppt_layout'] = I('post.ppt_layout', 0, 'int');
                
                // $data['requirements']              = I('post.requirements');
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
    
    //垃圾代码
    //     /**
    //      *上传处理
    //      */
    
    //     public function upload()
    //     {
    //      /* 设置内部字符编码为 UTF-8 */
    // //       mb_internal_encoding("UTF-8");
    //         $uid              = use_id(U('/Index/index'));
    //         if ($uid)
    //         {
    // //            $filename=($double+1).'X'.$copies.'_['.$sid.']_'.date('Y-m-d_H-i_U');
    //             if(C('FILE_UPLOAD_TYPE') == 'QINIU')
    //             {
    //                 $setting = C('UPLOAD_SITEIMG_QINIU');
    //                 $upload  = new \Think\Upload($setting);
    //                 $upload->exts = array('doc', 'docx', 'pdf', 'wps', 'ppt', 'pptx');
    //             }
    //             else
    //             {
    //                 $upload           = new \Think\Upload();
    //                 $upload->saveName = array ('uniqid', '');
    //                 $upload->maxSize  = 10485760;
    //                 $upload->exts     = array('doc', 'docx', 'pdf', 'wps', 'ppt', 'pptx');
    //                 $upload->rootPath = './Uploads/';
    //                 $upload->savePath ='';
    //             }
    //             $info             = $upload->upload($_FILES);
    //             if (!$info)
    //             {
    //                 $this->error($upload->getError());
    //             }
    //             else
    //             {
    //                 if(session('files_'.$uid))
    //                 {
    //                     $fid = session('files_'.$uid);
    //                 }
    //                 else
    //                 {
    //                     $fid = 0;
    //                 }
    //                 session('name_'.$uid.'_'.$fid,$info['file']['name'],360);
    //                 session('url_'.$uid.'_'.$fid,$info['file']['savepath'].$info['file']['savename'],360);
    //                 session('ext_'.$uid.'_'.$fid,$info['file']['ext'],360);
    //                 session('files_'.$uid,$fid+1);
    //                 return;
    //             }
    
    //         } else
    //         {
    //             $this->error('登录信息已失效', '/Index/index');
    //         }
    //     }
    
    //     public function post()
    //     {
    //         $uid = use_id(U('/Index/index'));
    
    //         if ($uid && session('files_'.$uid))
    //         {
    //             $sid=M('User')->cache(true)->getFieldById($uid,'student_number');
    //             $copies=I('post.copies',0,'int');
    //             $double=I('post.double_side',0,'int');
    //             $pid = I('post.pri_id',0,'int');
    //             $fid = session('files_'.$uid);
    //             $i = 0;
    //             while($i<$fid)
    //             {
    //                 $name=session('name_'.$uid.'_'.$i);
    //                 if(mb_strlen($name)>62)
    //                 {
    //                     $name=mb_substr($name,0,58).'.'.session('ext_'.$uid.'_'.$i);
    //                 }
    //                 $data['name']                      = $name;
    //                 $data['pri_id']                    = $pid;
    //                 // $data['requirements']              = I('post.requirements');
    //                 $data['url']                      = session('url_'.$uid.'_'.$i);
    //                 $data['status']                      = 1;
    //                 $data['use_id']                      = $uid;
    //                 $data['copies']                      = $copies;
    //                 $data['double_side']                      = $double;
    //                 $File                 = M('File');
    //                 $result               = $File->cache(true)->add($data);
    //                 if ($result)
    //                 {
    //                     $Notification         = M('Notification');
    //                     $Notification->fil_id = $result;
    //                     $Notification->to_id  = $data['pri_id'];
    //                     $Notification->type   = 1;
    //                     $Notification->add();
    //                     //删除缓存
    //                     // S(cache_name('user',$uid),null);
    //                     // S(cache_name('printer',$pid),null);
    
    //                 } else
    //                 {
    //                     $this->error($File->getError(),'/File/add',1);
    //                 }
    //                 session('name_'.$uid.'_'.$fid,null);
    //                 session('url_'.$uid.'_'.$fid,null);
    //                 session('ext_'.$uid.'_'.$fid,null);
    //                 $i++;
    //             }
    //             session('files_'.$uid,null);
    //             $this->redirect('File/index',null,0,'上传成功');
    //         }
    //     }
    
    
    
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

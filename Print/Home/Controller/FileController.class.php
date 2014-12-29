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
            $this->data = $File->where($condition)->order('file.id desc')->select();
            $this->display();
        } else
        {
            $this->redirect('Home/Index/index');
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
            $this->redirect('Home/Index/index');
        }
    }
    
    /**
     *上传处理
     */
    
    public function upload() 
    {
        $uid              = use_id(U('Index/index'));
        if ($uid) 
        {
            $upload           = new \Think\Upload();
            $upload->maxSize  = 10485760;
            
            //10Mb
            $upload->exts     = array('doc', 'docx', 'pdf');
            $upload->rootPath = './Uploads/';
            $upload->savePath = '';
            $info             = $upload->upload();
            if (!$info) 
            {
                $this->error($upload->getError());
            } else
            {
                foreach ($info as $file) 
                {
                    $data['name']                      = $file['name'];
                    $data['pri_id']                      = I('post.pri_id');
                    $data['requirements']                      = "";
                    
                    //I('post.requirements');
                    $data['url']                      = $file['savepath'] . $file['savename'];
                    $data['status']                      = 1;
                    
                    //status = 1 means sended ,not downloaded yet
                    //status = 2 means downloaded ,not printed yet
                    //status = 3 means printing ,not printed yet
                    //status = 4 means printed ,not paid yet
                    //status = 5 means paid
                    $data['use_id']                      = $uid;
                    $data['copies']                      = I('post.copies');
                    $data['double_side']                      = I('post.double_side');
                    
                    $File                 = M('File');
                    $result               = $File->add($data);
                    if ($result) 
                    {
                        $Notification         = M('Notification');
                        $Notification->fil_id = $result;
                        $Notification->to_id  = $data['pri_id'];
                        $Notification->type   = 1;
                        $Notification->add();
                        $this->success('上传完成');
                    } else
                    {
                        $this->error($File->getError());
                    }
                }
            }
        } else
        {
            $this->error('登录信息已失效', 'Home/Index/index');
        }
    }
    
    /**
     *删除文件记录
     */
    public function delete() 
    {
        $uid    = use_id(U('Index/index'));
        $fid    = I('fid', null, 'intval');
        if ($uid && $fid) 
        {
            $map['id']        = $fid;
            $map['status']        = array('not between', '2,4');
            $result = M('File')->where($map)->setField('status', 0);
            if ($result) 
            {
                $this->success($result);
                return;
            }
        }
        $this->error('当前状态不允许删除！');
    }
}

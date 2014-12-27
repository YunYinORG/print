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
 * Classes list:
 * - FileController extends Controller
 */
namespace Home\Controller;
use Think\Controller;
class FileController extends Controller
{
    
    public function index() 
    {
        $uid        = use_id(U('Index/index'));
        if ($uid) 
        {
            $File       = D('FileView');
            $this->data = $File->where("use_id=" . $uid)->order('file.id desc')->select();
            $this->display();
        } else
        {
            $this->redirect('Home/Index/index');
        }
    }
    
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
    
    public function delete()
    {
    
	    $error = array('response'=> false);
        $error = json_encode($error);
        $success = array('response'=> true);
        $success = json_encode($success);
        
        $uid = use_id(U('Index/index'));
        $fid    = I('fid', null, 'intval');
		if ($uid && $fid ) 
		{
			$map['id'] = $fid;
			$map['status'] = array('not between','2,4' );
			$result = M('File')->where($map)->setField('status', 0);
			if($result)
			{   
			    echo $success;
            }
            else
            {
                echo $error;
		    }
        } 
        else
        {
            echo $error;
        }
    }
    
    public function upload() 
    {
        $uid              = use_id(U('Index/index'));
        if ($uid) 
        {
            $upload           = new \Think\Upload();
            $upload->maxSize  = 3145728;
             //3Mb
            $upload->exts     = array('doc', 'docx', 'pdf');
            $upload->rootPath = './Uploads/';
            $upload->savePath = '';
            $info             = $upload->upload();
            if (!$info) 
            {
                $this->error('Error when upload to /Uploads');
            } else
            {
                foreach ($info as $file) 
                {
                    $data['name']                      = $file['name'];
                    $data['pri_id']                      = I('post.pri_id');
                    $data['time']                      = date("Y-m-d H:i:s", time());
                     //This is the upload time...not the specify time
                    $data['requirements']                      = "It's OK";
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
                        $this->error("SQL: Can not insert info into File table");
                    }
                }
            }
        } else
        {
            $this->redirect('Home/Index/index');
        }
    }
}

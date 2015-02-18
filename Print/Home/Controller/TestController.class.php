<?php  

namespace Home\Controller;
use Think\Controller;

class TestController extends Controller
{
	public function gettest()
	{
        $this->display();
	}
	public function posttest()
	{
	    mb_internal_encoding("UTF-8");	    
	    $uid=1;
        $sid=M('User')->cache(true)->getFieldById($uid,'student_number');
	    $fid=0;
	    $pid = 1;
        $copies=I('post.copies_'.$fid,0,'int');
        $double=I('post.double_side_'.$fid,0,'int');

        if(C('FILE_UPLOAD_TYPE')=='QINIU')
        {
            $setting=C('UPLOAD_SITEIMG_'.C('FILE_UPLOAD_TYPE'));
            $Upload = new \Think\Upload($setting);
            var_dump(C('FILE_UPLOAD_TYPE'));
            echo "<br/>";

            var_dump($Upload);
            echo "<br/>";
        }
        else
        {
            $Upload = new \Think\Upload();
        }
        $Upload->saveName=($double+1).'X'.$copies.'_['.$sid.']_'.date('Y-m-d_H-i_U');
        $info = $Upload->upload($_FILES);
        var_dump($info);
        echo "<br/>";


    	if (!$info) 
        {
            return;
        } 
        else
        {
            foreach ($info as $file) 
            {
                $name=$file['name'];
                if(mb_strlen($name)>62)
                {
                    $name=mb_substr($name,0,58).'.'.$file['ext'];
                }
                $data['name']                      = $name;
                $data['pri_id']                    = $pid;
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
                } else
                {
                    var_dump($result);
                }
            }
        }
    }



    public function getanother()
    {
        $this->display();
    }
    public function upload()
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        @set_time_limit(60);

        $targetDir = '/Uploads';
        $cleanupTargetDir = true; // Remove old files
        $maxFileAge = 5 * 3600; // Temp file age in seconds


        // Get a file name
        if (isset($_REQUEST["name"])) {
	        $fileName = $_REQUEST["name"];
        } elseif (!empty($_FILES)) {
	        $fileName = $_FILES["file"]["name"];
        } else {
	        $fileName = uniqid("file_");
        }

        
        $Upload = new \Think\Upload();
        $info = $Upload->upload($_FILE);
        die(json_encode($info));
    }
    public function postanother()
   	{
        $count = 0; 
        foreach ($_POST as $name => $value) {
		echo(htmlentities(stripslashes($name)).'   ');
		echo(nl2br(htmlentities(stripslashes($value))));
		echo('<br/>');
		}
   	}






    public function getsplit()
    {
            $condition['use_id']            = 1;
            $condition['status']            = array('between', '1,5');
            $File       = D('FileView');
            $count      = $File->where($condition)->count();
            $Page       = new \Think\Page($count,5);
            $show       = $Page->show();
            $list= $File->where($condition)->order('file.id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('list',$list);// 赋值数据集
            $this->assign('page',$show);
            $this->display();

    }
    



    public function postupyun()
    {
        $setting=array ( 
                'maxSize' => 10 * 1024 * 1024,//文件大小
                'rootPath' => './',
                'saveName' => array ('uniqid', ''),
                'driver' => 'Upyun',
                'driverConfig' => array (
                        'host' => 'http://v0.api.upyun.com',
                        // 空间名称
                        'bucket' => '',
                        // 操作员名称
                        'username' => '',
                        // 密码
                        'password' => ''
                        )
            );
        $Upload = new \Think\Upload($setting);
        $info = $Upload->upload($_FILES);
        var_dump($Upload);
        var_dump($info);
    }


    public function delete()
    {   
        $uid   = 1;
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
                $result = delete_file($url);
                var_dump($result);
                if ($result) 
                {
                    /*
                    $data['status'] = 0;
                    $data['url']    = '';
                    $result    = $File->where($map)->cache(true)->save($data);
                    if ($result) 
                    {
                        //鍒犻櫎缂撳瓨
                        S(cache_name('user',$uid),null);
                        S(cache_name('printer',$file['pri_id']),null);
  //                      $this->success($result);
                        return;
                    }
                    $this->error('璁板綍鏇存柊寮傚父');
                    */
                    $this->success();
                }
  //              $this->error('A');
            }
  //          $this->error('B');
        }
  //      $this->error('C');
    }

    public function download()
    {
        $pid    = 1;
        $fid    = I('fid', null, 'intval');
        $status = I('status');

        $map['pri_id']        = $pid;
        $map['id']        = $fid;
        $map['status']        = array('gt', 0);
        $File   = M('File');
        $url    = $File->where($map)->getField('url');
        if ($url) 
        {
            echo download($url);
        } else
        {
            $this->error('鏂囦欢涓嶅瓨鍦?鍙兘宸插垹闄?锛?');
        }
    }

    public function test()
    {
        $setting=C('UPLOAD_SITEIMG_QINIU');
        $Upload = new \Think\Upload($setting);
        $info = $Upload->upload($_FILES);
    }

}

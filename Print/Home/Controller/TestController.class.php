<?php

// ===================================================================
// | FileName:      EmptyController.class.php
// ===================================================================
// | Discription：   404 用户控制器
//      <命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014 云印南开团队 All rights reserved.
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
class TestController extends Controller
{

	public function gettest()
	{
        $this->display();
	}
	
	public function getanother()
	{
        $this->display();
	}
	
    public function getsetting()
	{
	    var_dump(C(MAX_TRIES));
	    var_dump(C(UPLOAD_SITEIMG_QINIU));
    }

    public function getfunc()
	{
	    echo(encode('bla_bla+/++---___'));
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
    
	public function posttest()
	{
	    mb_internal_encoding("UTF-8");	    
	    $uid=1;
        $sid=M('User')->cache(true)->getFieldById($uid,'student_number');
	    $fid=0;
	    $pid = 1;//I('post.pri_id',0,'int');
	    foreach ($_FILES as $file)
        {
            if($fid<5&&$file['error']==0)
            {
            }
            else
            {
                $this->error('Upload error');
            }
            $fid++;
        }

	            $setting=array ( 
                'maxSize' => 10 * 1024 * 1024,//文件大小
                'rootPath' => './',
//                'saveName' => array ('uniqid', ''),
                'driver' => 'Qiniu',
                'driverConfig' => array (
                        'secrectKey' => '', 
                        'accessKey' => 'fhGmHO1_QHq01QVKuAyKQoWklslb88Uxd0rJLcko',
                        'domain' => 'nkumstc.qiniudn.com',
                        'bucket' => 'nkumstc'
                        )
                );
                $Upload = new \Think\Upload($setting);
                $Upload->exts = array('doc', 'docx', 'pdf', 'wps', 'ppt', 'pptx');
                $info = $Upload->upload($_FILES);
                $fid = 0;
        foreach($info as $file)
        {
                $copies=I('post.copies_'.$fid,0,'int');
                $double=I('post.double_side_'.$fid,0,'int');
                $savepath= str_replace('/','_',$info['file_'.$fid]['savepath']);
                $url = 'http://7vihnm.com1.z0.glb.clouddn.com/'.$savepath.$info['file_'.$fid]['savename'];
                $find = array('+', '/');
                $replace = array('-', '_');
                $duetime = NOW_TIME + 10086400;//下载凭证有效时间
                $DownloadUrl = $url . '?e=' . $duetime;
                $Sign = hash_hmac ( 'sha1', $DownloadUrl, $setting ["driverConfig"] ["secrectKey"], true );
                $EncodedSign = str_replace($find, $replace, base64_encode($Sign));
                $Token = $setting ["driverConfig"] ["accessKey"] . ':' . $EncodedSign;
                $RealDownloadUrl = $DownloadUrl . '&token=' . $Token;
                echo($RealDownloadUrl);
                echo('<br>');
                $name=$_FILES['file_'.$fid]['name'];
                	if(mb_strlen($name)>62)
                	{
                		$name=mb_substr($name,0,58).'.'.$file['ext'];
                	}
                $data['name']                      = $name;
                $data['pri_id']                    = $pid;
                $data['url']                      = $savepath.$info['file_'.$fid]['savename'];
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
                        S(cache_name('user',$uid),null);
                        S(cache_name('printer',$pid),null);
                    } else
                    {
                        echo('BAD UPLOAD');
                    }
                $fid++;
        }
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
        
        /*
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        // Chunking might be enabled
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


        // Remove old temp files	
        if ($cleanupTargetDir) {
	        if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
		        die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	        }

	        while (($file = readdir($dir)) !== false) {
		        $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		        // If temp file is current file proceed to the next
		        if ($tmpfilePath == "{$filePath}.part") {
			        continue;
		        }

		        // Remove temp file if it is older than the max age and is not the current file
		        if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
			        @unlink($tmpfilePath);
		        }
	        }
	        closedir($dir);
        }	


        // Open temp file
        if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
	        die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
        }

        if (!empty($_FILES)) {
	        if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
		        die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
	        }

	        // Read binary input stream and append it to temp file
	        if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
		        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	        }
        } else {	
	        if (!$in = @fopen("php://input", "rb")) {
		        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
	        }
        }

        while ($buff = fread($in, 4096)) {
	        fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        // Check if file has been uploaded
        if (!$chunks || $chunk == $chunks - 1) {
	        // Strip the temp .part suffix off 
	        rename("{$filePath}.part", $filePath);
        }

        // Return Success JSON-RPC response
        die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

        */
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
}

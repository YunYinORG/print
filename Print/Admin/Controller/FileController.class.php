<?php
// ===================================================================
// | FileName:      /Print/Admin/FileController.class.php
// ===================================================================
// | Discription：   FileController 后台文件信息管理控制器
//      <命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 201５ 云印南开团队 All rights reserved.
// +------------------------------------------------------------------

/**
 * Class and Function List:
 * Function list:
 * - index()
 * - search()
 * - notifyPrinters()
 * - notifyUsers()
 * Classes list:
 * - FileController extends Controller
 */
namespace Admin\Controller;
use Think\Controller;

class FileController extends Controller {

	public function index()
	{
		if ( ! admin_id())
		{
			$this->redirect('Admin/Index/login');
		}
		else
		{	
			$condition['status'] = array('between', '1,5');
			$File  = D('FileView');
			$count = $File->where($condition)->count();
			$Page  = new \Think\Page($count, 10);
			$show  = $Page->show();
			$ppt_layout = C('PPT_LAYOUT');
			$result = $File->where($condition)->order('file.id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
			
			foreach ($result as &$file)
			{
				$file['ppt_layout'] = $ppt_layout[$file['ppt_layout']];
			}
			unset($file);
			$this->data = $result;
			$this->assign('page', $show);
			$this->display();			
		}
	}

	public function search()
	{
		if ( ! admin_id())
		{
			$this->redirect('Admin/Index/login');
		}
		$status = I('post.status', null, 'int');
		switch ($status)
		{
			case 0:
			  $condition['status'] = array('between', '1,5');
			  break;  
			case 1:
			  $condition['status'] = 1;
			  break;
			case 2:
			  $condition['status'] = 2;
			  break;
			case 3:
			  $condition['status'] = 3;
			  break;
			case 4:
			  $condition['status'] = 4;
			  break;
			default:
			  $condition['status'] = array('between', '1,5');
		}
		
		$File  = D('FileView');
		$count = $File->where($condition)->count();
		$Page  = new \Think\Page($count, 10);
		$show  = $Page->show();
		$ppt_layout = C('PPT_LAYOUT');
		$result = $File->where($condition)->order('file.id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		
		foreach ($result as &$file)
		{
			$file['ppt_layout'] = $ppt_layout[$file['ppt_layout']];
		}
		unset($file);
		$this->data = $result;
		$this->assign('page', $show);
		$this->assign('status', $status);
		$this->display();
	}

	public function notifyPrinters()
	{
		//identify
		$verify_key = I('get.key');
		if ($verify_key != C('VERIFY_KEY'))
			return;
		$condition1['status'] = 1;
		$condition2['status'] = 2;
		$condition2['copies'] = array('gt', 0);
		$NotifyPrinted = D('NotifyPrinter');
		$result1 = $NotifyPrinted->where($condition1)->group('pri_name')->select();
		$result2 = $NotifyPrinted->where($condition2)->group('pri_name')->select();
		$result = array();
		for($k=0; $k<count($result1); $k++)
		{
			$item1 = $result1[$k];
			for ($i = 0; $i<count($result2); $i++)
			{	
				$item2 = $result2[$i];
				if ($item1['pri_id'] == $item2['pri_id'])
				{
					array_push($result, array($item1['phone'], $item1['pri_name'], $item1['count'], $item2['count']));
					break;
				}
				if ($i == (count($result2)-1))
					array_push($result, array($item1['phone'], $item1['pri_name'], $item1['count'], "0"));
			}
		}
		for($k=0; $k<count($result2); $k++)
		{
			$item2 = $result2[$k];
			for ($i = 0; $i < count($result1); $i++)
			{	
				$item1 = $result1[$i];
				if ($item1['pri_id'] == $item2['pri_id'])
				{
					break;
				}
				if ($i == (count($result1)-1))
					array_push($result, array($item1['phone'], $item2['pri_name'], "0", $item2['count']));
			}
		}
		echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
		echo "打印店名称，没下载个数，没打印个数<br />";	
		$SMS = new \Vendor\Sms();
		for($k=0; $k<count($result); $k++)
		{
			$item = $result[$k];
			$phone = $item[0];
			$msgInfo = array("pri_name"=>$item[1], "no_download"=>$item[2], "unprinted"=>$item[3]);
			if ($SMS->noticePrinter($phone, $msgInfo))
			{				
				echo "提醒信息已经发送";
			}
			else
			{
				echo "发送不成功";
			}
			echo $phone."\t\t".$msgInfo["pri_name"]."\t\t".$msgInfo["no_download"]."\t\t".$msgInfo["unprinted"]."<br />";
		}	
		
	}

	public function notifyUsers()
	{
		//identify
		$verify_key = I('get.key');
		if ($verify_key != C('VERIFY_KEY'))
			return;	
		$condition['_string'] = '(file.status=2 AND copies=0) OR file.status=4';
		$condition['time'] = array('lt', date('Y-m-d h:i:s',time()-3600*24));
		$NotifyUser = D('NotifyUser');
		$result = array();
		$result1 = $NotifyUser->field('user_id,stu_num,phone,use_name,file_name,count("use_name") as count,$status')->where($condition)->group('use_name')->select();
		$result3 = $NotifyUser->field('user_id,stu_num,phone,use_name,file_name,status')->where($condition)->select();
		for($i=0; $i<count($result1); $i++)
		{
			$item1 = $result1[$i];
			if ($item1['count'] == 1)
			{
				array_push($result, array($item1['phone'], $item1['use_name'], $item1['file_name'], "已经下载", $item1['user_id'], $item1['stu_num']));
			}
			else if ($item1['count'] > 1)
			{
				$count = 0;
				$info = "";
				for($k=0; $k<count($result3); $k++)
				{
					$item3 = $result3[$k];
					if ($item3['user_id'] == $item1['user_id'])
					{
						$count++;
						$info = $info.substr($item3['file_name'],0,4);
						if ($count == 2)break;
						else
						{
							$info = $info."、";
						}
					}
				}
				$info = $info."..等".$item1['count']."个文件";
				array_push($result, array($item1['phone'], $item1['use_name'], $info, "已经下载", $item1['user_id'], $item1['stu_num']));
			}	
		}	
		echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
		$SMS = new \Vendor\Sms();
		for($i=0; $i<count($result); $i++)
		{
			$item = $result[$i];
			import('Common.Encrypt', COMMON_PATH, '.php');
			echo $item[0];
			echo $item[5]."<br />";
			echo $item[4];
			$phone = decrypt_phone($item[0], $item[5], $item[4]);
			echo $phone;
			if (!empty($phone)) 
			{
				$msgInfo = array("user_name"=>$item[1], "info"=>$item[2], "status"=>$item[3]);
	//			if ($SMS->noticeUser($phone, $msgInfo))
				{				
					echo "提醒信息已经发送";
				}
	//			else
				{
					echo "发送不成功";
				}
				echo $phone."\t\t".$msgInfo["user_name"]."\t\t".$msgInfo["info"]."\t\t".$msgInfo["status"];
			}
		}
	}
}

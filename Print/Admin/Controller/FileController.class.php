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
			for ($i = 0; $i < count($result2); $i++)
			{	
				$item2 = $result2[$i];
				if ($item1['pri_name'] == $item2['pri_name'])
				{
					array_push($result, array($item1['pri_name'], $item1['count'], $item2['count']));
					break;
				}
				if ($i == (count($result2)-1))
					array_push($result, array($item1['pri_name'], $item1['count'], "0"));
			}
		}
		for($k=0; $k<count($result2); $k++)
		{
			$item2 = $result2[$k];
			for ($i = 0; $i < count($result1); $i++)
			{	
				$item1 = $result1[$i];
				if ($item1['pri_name'] == $item2['pri_name'])
				{
					for ($j = 0; $j < count($result); $j++)
					{
						$item = $result[$j];
						if ($item1['pri_name'] == $item[0])break;
						if ($j == (count($result) - 1))
							array_push($result, array($item1['pri_name'], $item1['count'], $item2['count']));
					}
					break;
				}
				if ($i == (count($result1)-1))
					array_push($result, array($item2['pri_name'], "0", $item2['count']));
			}
		}
		echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
		echo "打印店名称，没下载个数，没打印个数<br />";	
		for($k=0; $k<count($result); $k++)
		{
			$item = $result[$k];
			echo $item[0]."\t\t".$item[1]."\t\t".$item[2]."<br />";
		}	
		
	}

	public function notifyUsers()
	{
		//identify
		$verify_key = I('get.key');
		if ($verify_key != C('VERIFY_KEY'))
			return;	
		$condition['status'] = array('in','2,4');
		$condition['time'] = array('lt', date('Y-m-d h:i:s',time()-3600*24));
		$NotifyUser = D('NotifyUser');
		$result = array();
		$result = $NotifyUser->field('use_name,count("use_name") as count')->where($condition)->group('use_name')->select();
//		echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
		for($i=0; $i<count($result); $i++)
		{
			$item = $result[$i];
			echo $item['use_name'].$item['count']."<br />";
		}
	}
}

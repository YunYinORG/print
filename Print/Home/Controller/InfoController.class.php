<?php

// ===================================================================
// | FileName:      InfoController.class.php
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
 * - InfoController extends Controller
 */
namespace Home\Controller;
use Think\Controller;
class InfoController extends Controller
{
	public function index($id = 1)
	{
	    $Printer = M('Printer');
	    $list = $Printer->find();
	    if($list)
	    {
	        $this->data = $list;
	        $this->printer = $list[$id-1];
		    $this->display();
		}
		else
		{
		    $this->error('Sorry,something wrong here'); 
		}    
		    
	}
	
	public function detail()
	{
	    $id = I('detail');
	    
	    $Printer = M('Printer');
	    $result = $Printer->find(); 
	    
	    if($result)
	    {
	        $this->data = $result;
		    $this->success($result);
		}
		else
		{
		    $this->error('Sorry,something wrong here'); 
		}
	}
	
}

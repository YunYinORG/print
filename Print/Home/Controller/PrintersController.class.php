<?php

// ===================================================================
// | FileName:      PrintersController.class.php
// ===================================================================
// | Discription：  打印店信息
//      <命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014-2015 云印南开团队 All rights reserved.
// +------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +------------------------------------------------------------------
/**
 * Class and Function List:
 * Function list:
 * - index()
 * Classes list:
 * - PrintersController extends Controller
 */
namespace Home\Controller;
use Think\Controller;
class PrintersController extends Controller
{
	public function index($id = 1)
	{
	    $Printer = M('Printer');
	    $list = $Printer->select();
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

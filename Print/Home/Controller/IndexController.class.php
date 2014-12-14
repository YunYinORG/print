<?php
// ===================================================================
// | FileName: 		IndexController.class.php
// ===================================================================
// | Discription：	IndexController 默认控制器
//		<命名规范：>
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
* Classes list:
* - IndexController extends Controller
*/
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller
{
	public function index() 
	{
	    echo('<h1>Index Page</h1><br><br>');
        echo('<h3>For User</h3>');
	    echo("<a href='".U('Home/User/signinorup')."'>Sign in & Sign up</a><br><br>");
	    
	    echo('<h3>For Printer</h3>');
	    echo("<a href='".U('Printer/Index/index')."'>Printer's Index Page</a><br>");
	}
}

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

class EmptyController extends Controller {
	public function _empty()
	{
		$this->redirect('Index/index');
	}
}

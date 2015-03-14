<?php
// ===================================================================
// | FileName:      /Print/Admin/IndexController.class.php
// ===================================================================
// | Discription：   IndexController 后台信息管理主页控制器
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
 * - printerRegister()
 * - addPrinter()
 * - _empty()
 * Classes list:
 * - AdminController extends Controller
 */
namespace Admin\Controller;
use Think\Controller;

class IndexController extends Controller {

    public function index(){
        if (!admin_id()) {
            $this->redirect('login');
        } else {
            $this->display();
        }
    }

    public function login(){
        $this->display();
    }

    public function auth(){
        if(I('post.account' )==C('ADMIN_ACCOUNT') && I('post.password')==C('ADMIN_PWD'))
        {
            session('admin_id', '1');
            // $token = update_token('1', C('ADMIN'));
            // cookie('token', $token, 60 * 15);//15min
            $this->redirect('index');
        }
        else
        {
            $this->error("WRONG");
        }
    }
    
}
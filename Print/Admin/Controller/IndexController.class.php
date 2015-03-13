<?php
// ===================================================================
// | FileName:      /Print/Admin/IndexController.class.php
// ===================================================================
// | Discription：   AdminController 后台信息管理控制器
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
        $this->display();
    }

    /**
     *注册
     */
    public function printerRegister() {
        // if (pri_id()) {
        //     $this->redirect('index');
        // } else {
            $this->display();
        // }
    }


    public function addPrinter() {
        $Printer = D('Printer');
        
        $data['account']  = I('post.account');
        $data['password'] = encode(md5(I('post.password')), I('post.account'));
        $data['name']     = I('post.name');
        $data['sch_id']   = 1;
        $data['address']  = I('post.address');
        $data['phone']    = I('post.phone');
        $data['qq']       = I('post.qq');
        
        if ($Printer->create($data)) {
            $result  = $Printer->cache(true)->add();
            if ($result) {
                $this->success('新增成功', '/Admin/index');
            } else {
                $this->error('数据插入失败' . $Printer->getError());
            }
        } else {
            $this->error('数据创建失败:' . $Printer->getError());
        }
    }

}
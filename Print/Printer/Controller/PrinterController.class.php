<?php

// ===================================================================
// | FileName:      /Print/Printer/PrinterController.class.php
// ===================================================================
// | Discription：   PrinterController 打印店信息管理控制器
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
 * - change()
 * - logout()
 * - signup()
 * - add()
 * - auth()
 * Classes list:
 * - PrinterController extends Controller
 */
namespace Printer\Controller;
use Think\Controller;
class PrinterController extends Controller
{
    
    /**
     * index()
     * 打印店信息管理页，
     * 未登录时跳转至打印店入口首页（U（'Index/index'））
     */
    public function index() 
    {
        
        //Display profile and table to make a change
        $id         = pri_id(U('Index/index'));
        if ($id) 
        {
            
            // $Printer    = M('Printer');
            $data       = M('Printer')->getById($id);
            $this->data = $data;
            $this->display();
        } else
        {
            $this->error('请使用打印店管理账号登录！', U('Index/index'));
        }
    }
    
    /**
     *change()
     *修改资料
     *@param $key 修改的字段
     *@param $value 修改值
     *注意字段过滤
     */
    public function changePwd() 
    {
        
        $id           = pri_id(U('Index/index'));
        $old_password = I('deprecated_password');
        $password     = I('password');
        $re_password  = I('re_password');
        if ($id && $old_password && $password && $password == $re_password) 
        {
            $Printer      = M('Printer');
            $pri          = $Printer->field('account,password')->getById($id);
            if ($pri['password'] == encode($old_password, $pri['account'])) 
            {
                if ($Printer->where('id=' . $id)->setField('password', encode($password, $pri['account'])) )
                {
                    $this->success('修改成功');
                } else
                {
                    $this->error($Printer->getError());
                }
            } else
            {
                $this->error('原密码错误');
            }
        } else
        {
            $this->error("信息不完整");
        }
    }
    
    /**
     * 注销
     */
    public function logout() 
    {
        delete_token(cookie('token'));
        session(null);
        cookie(null);
        $this->redirect('Index/index');
    }
    
    //Still in plan
    /*
                public function detail(){
                    //ditail of file?
                    $this->display();
                }*/
    
    //Not available now
    
    
    
    /**
     *注册
     */
    public function signup() 
    {
        if (pri_id()) 
        {
            $this->redirect('index');
        } else
        {
            $this->display();
        }
    }
    
    public function add() 
    {
        $Printer = D('Printer');
        
        $data['account']         = I('post.account');
        $data['password']         = encode(I('post.password'), I('post.account'));
        $data['name']         = I('post.name');
        $data['address']         = I('post.address');
        $data['phone']         = I('post.phone');
        $data['qq']         = I('post.qq');
        
        if ($Printer->create($data)) 
        {
            $result  = $Printer->add();
            if ($result) 
            {
                $this->redirect('logout', '', 1, '注册完成请登录');
            } else
            {
                $this->error('数据插入失败' . $Printer->getError());
            }
        } else
        {
            $this->error('数据创建失败:' . $Printer->getError());
        }
    }
    
    public function auth() 
    {
        $Printer  = M('Printer');
        $account  = I('post.account');
        $password = encode(I('post.password'), $account);
        $result   = $Printer->where('account="%s"', $account)->find();
        if ($result["password"] == $password) 
        {
            session('pri_id', $Printer->id);
            $token = update_token($Printer->id, C('PRINTER_WEB'));
            cookie('token', $token, 3600 * 24 * 30);
            $this->redirect('Printer/File/index');
        } else
        {
            $this->error('验证失败');
        }
    }
}
?>

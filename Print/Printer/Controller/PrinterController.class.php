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
 * - changePwd()
 * - logout()
 * - signup()
 * - add()
 * - auth()
 * - _empty()
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
            
            $data       = M('Printer')->getById($id);
            $this->data = $data;
            $this->price = json_decode($data['price'],true); 
            $this->display();
        } else
        {
            $this->error('请使用打印店管理账号登录！', U('Index/index'));
        }
    }
    
    /**
     *changePwd()
     *修改密码
     *注意字段过滤
     */
    public function changePwd() 
    {
        
        $id           = pri_id(U('Index/index'));
        $old_password = I('deprecated_password');
        $password     = I('password');

        if ($id && $old_password && $password) 
        {
            $Printer      = M('Printer');
            $pri          = $Printer->field('account,password')->cache(true)->getById($id);
            if ($pri['password'] == encode($old_password, $pri['account'])) 
            {
                if ($Printer->where('id=' . $id)->setField('password', encode($password, $pri['account'])) !== fasle) 
                {
                    $this->success('修改成功', 1);
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
    
    
    
    public function changeInfo() 
    {
        $id           = pri_id(U('Index/index'));
        if ($id) 
        {
            //$data = $_POST;
             $data['qq'] = I('qq');
             $data['phone'] = I('phone');
             $data['address'] = I('address');
             $data['profile'] = I('profile');
             $data['open_time'] = I('open_time');
             
             
             $price['p_c_s'] = I('p_c_s',0,'float');//color single
             $price['p_c_d'] = I('p_c_d',0,'float');//color double
             $price['p_s'] = I('p_s',0,'float');//no color single
             $price['p_d'] = I('p_d',0,'float');//no color double
             
             
             $data['price'] = json_encode($price);
             $data['price_more'] = I('price_more');//price more
            
            $Printer      = D('Printer');
            $result       = $Printer->where('id='.$id)->save($data);
            if ($result) 
            {
                $this->success('改好了');
            } else
            {
                $this->error('出错了');
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
        $this->redirect('Printer/Index/index');
    }
    
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
        $data['password']         = encode(md5(I('post.password')), I('post.account'));
        $data['name']         = I('post.name');
        $data['sch_id']         =1;
        $data['address']         = I('post.address');
        $data['phone']         = I('post.phone');
        $data['qq']         = I('post.qq');
        
        if ($Printer->create($data)) 
        {
            $result  = $Printer->cache(true)->add();
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
        $account  = I('post.account', null, C('REGEX_ACCOUNT'));
        if(!$account)
        {
            $this->error('无效账号：'.I('post.account'));
        }
        $result   = $Printer->where('account="%s"', $account)->field('id,password,status')->find();
        if ($result) 
        {
            $key      = 'auth_p_' . $account;
            $times    = S($key);
            $password = encode(md5(I('post.password')), $account);
            if ($times > C('MAX_TRIES')) 
            {
                \Think\Log::record('打印店爆破警告：ip:' . get_client_ip() . ',account:' . $account, 'NOTIC', true);
                $this->error('此账号尝试次数过多，已经暂时封禁，请于一小时后重试！（ps:你的行为已被系统记录）');
            } elseif ($result["password"] == $password) 
            {
                session('pri_id', $result['id']);
                $token = update_token($result['id'], C('PRINTER_WEB'));
                cookie('token', $token, 3600 * 24 * 30);
                S($key, null);
                $this->redirect('Printer/File/index');
                return;
            } else
            {
                S($key, $times + 1, 3600);
            }
        }
        $this->error('验证失败');
    }
    
    /**
     *404页
     */
    public function _empty() 
    {
       $this->redirect('index');
    }
}
?>

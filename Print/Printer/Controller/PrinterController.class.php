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
* - signin()
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
        $id=pri_id(U('Index/index'));
        if ($id) 
        {
            // $Printer    = M('Printer');
            $data = M('Printer')->getById($id);
            $this->data = $data;
            layout('layout');
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
    public function change() 
    {
        
       $id=pri_id(U('Index/index'));

        if ($id) 
        {
            $Printer = M('Printer');
            if ($Printer->create()) 
            {
                $result  = $Printer->save();
                if ($result) 
                {
                    echo ("Success");
                } else
                {
                    $this->error('Unable to write');
                }
            } else
            {
                $this->error($Printer->getError());
            }
        } else
        {
            echo ("Unauth");
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
        $this->redirect('Printer/Printer/signin');
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
        if (session('?pri_id')) 
        {
            var_dump($_COOKIE);
        } else
        {
            if (cookie('?token')) 
            {
                
                $info = auth_token(cookie('token'));
                if ($info) 
                {
                    session('pri_id', $info['id']);
                     //Needed when file upload
                    var_dump($_COOKIE);
                } else
                {
                    $this->display();
                     //Fake token
                    
                }
            } else
            {
                $this->display();
                 //First time to sign up or in?
                
            }
        }
    }
    
    /**
    *登录
    */
    public function signin() 
    {
        if (session('?pri_id')) 
        {
            $this->redirect('Printer/File/index');
        } else
        {
            if (cookie('?token')) 
            {
                
                $info = auth_token(cookie('token'));
                if ($info) 
                {
                    session('pri_id', $info['id']);
                    $this->redirect('Printer/File/index');
                } else
                {
                    $this->display();
                    //Fake token
                }
            } else
            {
                $this->display();
                 //First time to sign up or in?
            }
        }
    }
    
    //Not available now
    
         public function add(){
             $Printer = D('Printer');
    
             $data['account'] = I('post.account');
             $data['password'] = encode(I('post.password'),I('post.account'));
             $data['name'] = I('post.name');
             $data['address'] = I('post.address');
             $data['phone'] = I('post.phone');
             $data['qq'] = I('post.qq');
    
             if($Printer->create())
             {
                 $result = $Printer->add($data);
                 if($result)
                 {
                     session('pri_id',$result);
                     $token = update_token($result,C('PRINTER'));
                     cookie('token',$token,3600*24*30);
                     var_dump($_COOKIE);
                 }
                 else
                 {
                     $this->error('Can not insert to database');
                 }
             }
             else
             {
                 $this->error('Can not create model');
             }
         }
    
         public function auth(){
             $Printer = D('Printer');
                 $account = I('post.account');
                 $password = encode(I('post.password'),$account);
                 $result = $Printer->where("account={$account}")->find();
                 if($result["password"]==$password)
                 {
                     session('pri_id',$Printer->id);
                     $token = update_token($Printer->id,C('PRINTER'));
                     cookie('token',$token,3600*24*30);
                     $this->redirect('Printer/File/index');
                 }
                 else
                 {
                     $this->error('Wrong password');
                 }
         }
           
    
}
?>

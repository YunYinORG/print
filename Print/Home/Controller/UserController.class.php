<?php

// ===================================================================
// | FileName:      UserController.class.php
// ===================================================================
// | Discription：   UserController 用户控制器
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
 * - auth()
 * - change()
 * - logout()
 * Classes list:
 * - UserController extends Controller
 */
namespace Home\Controller;
use Think\Controller;
import('Common.Urp', COMMON_PATH);

class UserController extends Controller
{
    
    /**
     *用户信息页
     */
    public function index() 
    {
        $id   = Use_id(U('Index/index'));
        if ($id) 
        {
            $User = M('User');
            $data = $User->where("id=" . $id)->find();
            session('student_number', $data['student_number']);
            $this->data = $data;
            $this->display();
        } else
        {
            $this->redirect('Home/Index/index');
        }
    }
    
    /**
     * auth()
     *登录和注册验证处理
     *@param student_number 学号
     *@param password 密码
     */
    public function auth() 
    {
        $User           = D('User');
        $student_number = I('post.student_number');
        $password       = encode(I('post.password'), $student_number);
        $result         = $User->where("student_number='$student_number'")->find();
        if ($result) 
        {
            if ($result['password'] == $password)
            
            //authed
            
            
            {
                session('use_id', $User->id);
                $token = update_token($User->id, C('STUDENT'));
                cookie('token', $token, 3600 * 24 * 30);
                $this->redirect('Home/File/index');
            } else
            {
                $this->error('密码验证错误！');
                
                //Wrong password
                
                
            }
        } else
        {
//            $this->error('未注册');
            if($User->create())
                                   {
                                       if($name = get_urp_name($student_number,I('post.password')))
                                       {
                                           $data['name']=$name;
                                           $data['student_number']=$student_number;
                                           $data['password']=$password;
                                           $result = $User->add($data);
                                           if($result) 
                                           {                
                                               session('use_id', $result);
                                               session('student_number', $student_number);
                                               session('name', $name);
//                                               $token = update_token($result,C('USER'));
//                                               cookie('token',$token,3600*24*30);
                                               $this->redirect('Home/User/notice');
                                           }
                                           else
                                           {
                                               $this->error('SQL: Can not insert into User table');
                                           }
                                       }
                                       else
                                       {
                                           $this->error('Urp verification failed');
                                       }
                                   }
                                   else
                                   {
                                       $this->error('Can not create User model');
                                    }
        }
    }
    

    public function notice()
    {
        $uid = use_id(U('Index/index'));
        $password = I('post.password');
        $re_password = I('post.re_password');
        if($password &&$re_password)
        {
            if($password==$re_password)
            {
                $result = M('User')->where('id='.$uid)->setField('password',encode($password,session('student_number')));
                if($result)
                {
                    $this->success("Successfully change password");
                }
            }
            else
            {
                $this->error("Password doesn't match with another one");
            }
        }
        else
        {
            $this->data = session('name');
            $this->display();
        }
    }
    
    /**
     *修改密码
     */
    public function change() 
    {
        $uid                 = use_id(U('Index/index'));
        $deprecated_password = I('post.deprecated_password');
        $password            = I('post.password');
        $re_password         = I('post.re_password');
        if ($uid) 
        {
            $condition['id']                     = $uid;
            $condition['password']                     = encode($deprecated_password, session('student_number'));
            $condition['student_number']                     = session('student_number');
            $User                = D('User');
            $result              = $User->where($condition)->select();
            if ($result) 
            {
                if ($password == $re_password) 
                {
                    $map['id']                     = $uid;
                    M('User')->where($map)->setField('password', encode($password, session('student_number')));
                    $this->success("Success");
                } else
                {
                    $this->error("Password dosen't match the reinput one");
                }
            } else
            {
                $this->error("Wrong Password");
            }
        }
    }
    
    /**
     *注销
     */
    public function logout() 
    {
        delete_token(cookie('token'));
        session(null);
        cookie(null);
        $this->redirect('Home/Index/index');
    }
}

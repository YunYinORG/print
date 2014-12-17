<?php
// ===================================================================
// | FileName: 		UserController.class.php
// ===================================================================
// | Discription：	UserController 用户控制器
//		<命名规范：>
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
* - UserController extends Controller
*/
namespace Home\Controller;
use Think\Controller;
import('Common.Urp',COMMON_PATH);

class UserController extends Controller
{

        public function signinorup(){
        if(session('?use_id'))
        {
            $this->redirect('Home/File/add');
        }
        else
        {
            if(cookie('?token'))
            {

                $info = auth_token(cookie('token'));
                if($info)
                {      
                    session('use_id',$info['id']);//Needed when file upload
                    $this->redirect('Home/File/add');
                }
                else
                {
                    $this->display();//Fake token
                }
            }
            else
            {
                $this->display();//First time to sign up or in?
            }
        }
    }
    
    
    public function addorauth(){
        $User = D('User');
            $student_number = I('post.student_number');
            $password = encode(I('post.password'),$student_number);
            $result = $User->where("student_number={$student_number}")->find();
            if($result) 
            {
                if($result['password'] == $password)//authed
                {
                    session('use_id',$User->id);
                    $token = update_token($User->id,1);
                    cookie('token',$token,3600);
                    $this->redirect('Home/File/add');
                }
                else
                {
                    $this->error('Wrong password');
                    //Wrong password
                }
            }
            else
            {
//                not sign up yet
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
                            $token = update_token($result,1);
                            cookie('token',$token,360000);
                            $this->redirect('Home/User/index');
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
    
    
    
    
    public function index(){
        if(session('?use_id'))
        {
            $User = M('User');
            $data = $User->where("id=".session('use_id'))->find();
            session('password',$data['password']);
            session('student_number',$data['student_number']);
            $this->data = $data;
            layout('layout');
            $this->display();
        }
        else
        {
            $this->redirect('Home/User/signinorup');
        }
    }
    
    public function change()
    {
        if(session('?use_id'))
        {
            if(session('password')==encode(I('post.deprecated_password'),session('student_number')))
            {
                $User = M('User');
                if($User->create()) 
                {
                    $result = $User->save();
                    if($result)
                    {
                        $this->success();
                    }
                    else
                    {
                        $this->redirect('Home/User/index');
                    }
                }
                else
                {
                    $this->error('Can not create User model');
                }
            }
            else
            {
                $this->error('Wrong password');
            }
        }
        else
        {
            $this->redirect('Home/User/signinorup');
        }
    }
    
    public function logout()
    {
        delete_token(cookie('token'));
        session(null);
        cookie(null);
        $this->redirect('Home/User/signinorup');
    }
}

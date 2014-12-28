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

    public function index(){
        if(session('?use_id'))
        {
            $User = M('User');
            $data = $User->where("id=".session('use_id'))->find();
            session('student_number',$data['student_number']);
            $this->assign('title','Index');
            $this->data = $data;
            $this->display();
        }
        else
        {
            $this->redirect('Home/Index/index');
        }
    }


    //     public function signinorup(){
    //     if(session('?use_id'))
    //     {
    //         $this->redirect('Home/File/add');
    //     }
    //     else
    //     {   
    //         if(cookie('?token'))
    //         {

    //             $info = auth_token(cookie('token'));
    //             if($info)
    //             {      
    //                 session('use_id',$info['id']);//Needed when file upload
    //                 $this->redirect('Home/File/add');
    //             }
    //             else
    //             {
    //                 $this->display();//Fake token
    //             }
    //         }
    //         else
    //         {
    //             $this->display();//First time to sign up or in?
    //         }
    //     }
    // }
    
    
    /**
    * auth()
    *登录和注册验证处理
    *@param student_number 学号
    *@param password 密码
    */
    public function auth(){
        $User = D('User');
            $student_number = I('post.student_number');
            $password = encode(I('post.password'),$student_number);
            $result = $User->where("student_number='$student_number'")->find();
            if($result) 
            {
                if($result['password'] == $password)//authed
                {
                    session('use_id',$User->id);
                    $token = update_token($User->id,C('USER'));
                    cookie('token',$token,3600*24*30);
                    $this->redirect('Home/File/add');
                }
                else
                {
                    $this->error('密码验证错误！');
                    //Wrong password
                }
            }
            else
            {
$this->error('未注册');
                 /*if($User->create()) 
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
                             $token = update_token($result,C('USER'));
                             cookie('token',$token,3600*24*30);
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
                  }*/
            }
    }
    
    public function autoload()
    {
        $table = array(
        array('1210403','Syn','18222936811'),);       
        foreach ($table as $user => $v)
        {
            $User = D('User');
            $User->create();
            $data['student_number'] = $v[0];
            $data['password'] = encode($v[2],$v[0]);
            $data['name'] = $v[1];
            $data['phone'] = $v[2];
            $result = $User->add( $data );
            if(!$result)
            {
                echo("Error with ".$v[0]."<br>");
            }
        }
    
    }
    
    
    public function change()
    {
        $uid    = use_id(U('Index/index'));
		$deprecated_password = I('post.deprecated_password');
	    $password = I('post.password');
		$re_password = I('post.re_password');
		if ($uid) 
		{
			$condition['id'] = $uid;
		    $condition['password'] = encode($deprecated_password,session('student_number'));
		    $condition['student_number']=session('student_number');
		    $User   = D('User');
			$result = $User->where($condition)->select();
			if($result)
			{
			    if($password==$re_password)
			    {
                    $map['id'] = $uid;
			        M('User')->where($map)->setField('password', encode($password,session('student_number')));
			        $this->success("Success");
			    }
			    else
			    {
			        $this->error("Password dosen't match the reinput one");
			    }
			}
			else
			{
//		        $this->error("Wrong Password");
                var_dump($result);
                                var_dump($User);
			}
		}
    }
    
    public function logout()
    {
        delete_token(cookie('token'));
        session(null);
        cookie(null);
        $this->redirect('Home/Index/index');
    }
}

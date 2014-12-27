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
            session('password',$data['password']);
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
        array('1312850','李佳钊','15620994401'),
        array('1310438','费磊','18604763636'),
        array('1310406','吴敏','15122052060'),
        array('2120140637','祁雪','15822857395'),
        array('2120140638','杨爱','13820406892'),
        array('1310899','崔晨','18222260620'),
        array('1110664','NewFuture','18222285110'),
        array('1310947','熊瑶','13682187022'),
        array('1212616','张志强','15222460733'),
        array('1411993','王雨晴','13072050398'),
        array('1310863','郑意为','13212126919'),
        array('1310410','戴静怡','15802245766'),
        array('1312636','刘越','15620946586'),
        array('1213092','梁爽','15022070440'),
        array('1310403','宋雅麟','13821844638'),
        array('1310430','张佳诣','18902152665'),
        array('1212737','李林翰','18222330515'),
        array('1210158','魏铭阳','15022680135'),
        array('1212734','白宇楠','15122877680'),
        array('1212752','孔繁尘','13821822512'),
        array('1312686','冯帆','13034326718'),
        array('1213091','李颖','13662132077'),
        array('1311848','张蕾','18920781891'),
        array('1212750','何亚玲','18222929804'),
        array('1312632','刘策','15620941886'),
        array('1311092','杨征宇','18902152880'),
        array('1212643','钱炜','18222332101'),
        array('1312455','孙梦凡','15122838320'),
        array('1412757','石悦人','13212138679'),
        array('1412767','张凯婷','15022079405'));
        
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
        if(session('?use_id'))
        {
            if((session('password')==encode(I('post.deprecated_password'),session('student_number')))
            &&(I('post.input')==I('post.password')))
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
                    $this->error('Can not create User model:'.$User->getError());
                }
            }
            else
            {
                $this->error('Wrong password');
            }
        }
        else
        {
           $this->redirect('Home/User/index');
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

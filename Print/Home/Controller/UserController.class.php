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
 * - notice()
 * - forget()
 * - change()
 * - bindPhone()
 * - verifyPhone()
 * - getPhone()
 * - getEmail()
 * - bindEmail()
 * - logout()
 * - _empty()
 * Classes list:
 * - UserController extends Controller
 */
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller
{
    
    /**
     *用户信息页
     */
    public function index() 
    {
        $id         = use_id(U('Index/index'));
        if ($id) 
        {
            $User       = M('User');
            $data       = $User->where("id=%d", $id)->find();
            
            // session('student_number', $data['student_number']);
            $this->data = $data;
            $this->display();
        } else
        {
            $this->redirect('Index/index', null, 0, '未登录！');
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
        
        $student_number = I('post.student_number', null, '/^\d{7}|\d{10}$/');
        if ($student_number) 
        {
            $key            = 'auth_' . $student_number;
            $times          = S($key);
            if ($times > C('MAX_TRIES')) 
            
            {
                \Think\Log::record('auth爆破警告：ip:' . get_client_ip() . ',number:' . $student_number, 'NOTIC', true);
                $this->error('此账号尝试次数过多，已经暂时封禁，请于一小时后重试！（ps:你的行为已被系统记录）', '/Index/index', 5);
            } else
            {
                S($key, $times + 1, 3600);
            }
        } else
        {
            $this->error('学号格式错误！');
        }
        $User     = D('User');
        $password = encode(I('post.password'), $student_number);
        $result   = $User->where("student_number='$student_number'")->find();
        if ($result) 
        {
            if ($result['password'] == $password) 
            {
                session('use_id', $User->id);
                $token = update_token($User->id, C('STUDENT'));
                cookie('token', $token, 3600 * 24 * 30);
                S($key, null);
                $this->redirect('/File/index');
            } else
            {
                $this->error('密码验证错误！');
            }
        } else
        {
            
            if ($User->create()) 
            {
                import(C('VERIFY_WAY'), COMMON_PATH, '.php');
                if ($name   = getName($student_number, I('post.password'))) 
                {
                    $data['name']        = $name;
                    $data['student_number']        = $student_number;
                    $data['password']        = $password;
                    $result = $User->add($data);
                    if ($result) 
                    {
                        session('use_id', $result);
                        session('student_number', $student_number);
                        session('first_name', $name);
                        S($key, null);
                        $this->redirect('User/notice');
                    } else
                    {
                        $this->error('注册失败：' . $User->getError());
                    }
                } else
                {
                    $this->error('学校账号实名认证失败！');
                }
            } else
            {
                $this->error('信息不合法：' . $User->getError());
            }
        }
    }
    
    //首次注册
    public function notice() 
    {
        $uid         = session('use_id');
        $stu_number  = session('student_number');
        
        if (session('first_name') && $stu_number) 
        {
            
            $password    = I('post.password');
            $re_password = I('post.re_password');
            if (!$password) 
            {
                $this->data  = session('first_name');
                $this->display();
            } elseif ($password != $re_password) 
            {
                $this->data = session('first_name') . '[密码不一致重新输入]';
                $this->display();
            } else
            {
                $result = M('User')->where('id=' . $uid)->setField('password', encode($password, $stu_number));
                if ($result) 
                {
                    session('first_name', null);
                    $this->redirect('File/add', null, 0, '密码修改成功！');
                } else
                {
                    $this->error('密码修改失败！');
                }
            }
        } else
        {
            $this->redirect('Index/index');
        }
    }
    
    //密码找回
    public function forget() 
    {
        if (use_id()) 
        {
            $this->redirect('index');
        }
        
        $student_number = I('post.student_number', false, '/^(\d{7}|\d{10})$/');
        if (!$student_number) 
        {
            $this->display();
        } else
        {
            $key   = 'auth_' . $student_number;
            $times = S($key);
            if ($times > C('MAX_TRIES')) 
            {
                \Think\Log::record('forget爆破警告：ip:' . get_client_ip() . ',number:' . $student_number, 'NOTIC', true);
                $this->error('此账号尝试次数过多，已经暂时封禁，请于一小时后重试！（提示：你的行为已被系统记录）');
            } else
            {
                S($key, $times + 1, 3600);
            }
            
            $urp_password = I('post.urp_password');
            $password     = I('post.password');
            $re_password  = I('post.re_password');
            if ($password && $re_password && $student_number && $urp_password) 
            {
                import(C('VERIFY_WAY'), COMMON_PATH, '.php');
                if (getName($student_number, $urp_password)) 
                {
                    if ($password == $re_password) 
                    {
                        
                        if (false !== M('User')->where('student_number=' . $student_number)->setField('password', encode($password, $student_number))) 
                        {
                            S($key, null);
                            $this->redirect('Index/index', null, 0, "密码重置成功！");
                        } else
                        {
                            $this->error('重置失败！');
                        }
                    } else
                    {
                        $this->error("密码不一致");
                    }
                } else
                {
                    $this->error($student_number . '校园账号验证失败！');
                }
            } else
            {
                $this->display();
            }
        }
    }
    
    /**
     *修改密码
     */
    public function change() 
    {
        $uid                 = use_id(U('Index/index'));
        $user                = M('User')->field('student_number,password')->getById($uid);
        $deprecated_password = I('post.deprecated_password');
        $password            = I('post.password');
        $re_password         = I('post.re_password');
        if ($user && $deprecated_password && $password) 
        {
            if ($user['password'] == encode($deprecated_password, $user['student_number'])) 
            {
                if ($password == $re_password) 
                {
                    M('User')->where('id=%d', $uid)->setField('password', encode($password, $user['student_number']));
                    $this->redirect('logout', null, 0, '密码修改成功重新登陆！');
                } else
                {
                    $this->error('两次密码输入不一致！');
                }
            } else
            {
                $this->error('原密码错误');
            }
        }
        $this->error('验证失败！');
    }
    
    /**
     *绑定手机号
     *给手机号发送验证码
     *@param 手机号 
     */
    public function bindPhone() 
    {
        $id    = use_id('/');
        $phone = I('phone', false, C('REGEX_PHONE'));
        if (!$phone || !$id) 
        {
            $this->error('手机号码无效！');
        }
        
        //手机号查重
        if (get_user_by_phone($phone)) 
        {
            $this->error('此手机号已经绑定过账号！');
        } else
        {
            $result = send_sms_code($phone, 'bind');
            if ($result == true) 
            {
                session('bind_phone', $phone);
            } elseif ($result === 0) 
            {
                $this->error(array('code' => - 1, 'msg' => '发送次数过多'));
            } else
            {
                $this->error(array('code'       => 0, 'msg'       => '发送失败'));
            }
        }
    }
    
    /**
     *验证短信并绑定手机号
     *@param code 验证码
     */
    public function verifyPhone() 
    {
        $phone = session('bind_phone');
        if (!$phone) 
        {
            $this->error('手机号不存在！');
        } elseif (get_user_by_phone($phone)) 
        {
            $this->error('此手机号已经绑定过账号！');
        }
        $code   = I('code', false, '/^\d{6}$/');
        $sid    = student_number();
        $uid    = use_id();
        if ($code && $sid && $uid) 
        {
            $result = check_sms_code($phone, $code, 'bind');
            if ($result) 
            {
                import('Common.Encrypt', COMMON_PATH, '.php');
                $phone = encrypt_phone($phone, $sid, $uid);
                M('User')->where('id=%d', $uid)->setField('phone', $phone);
            } elseif ($result === false) 
            {
                $this->error('验证失败，请重试！');
            } else
            {
                $this->error('验证信息过期！','/User/bindPhone');
            }
        } else
        {
            $this->error('验证信息错误');
        }
    }
    
    /**
     *getPhone()
     *查看手机号码
     */
    public function getPhone() 
    {
        $uid = use_id();
        if (!$uid) 
        {
            $this->error('请登录！');
        }
        $phone = get_phone_by_id($uid);
        if (IS_AJAX) 
        {
            $this->success(array('phone' => $phone));
        } else
        {
            echo $phone;
        }
    }
    
    /**
     *getEmail()
     *查看邮箱
     */
    public function getEmail() 
    {
        $uid = use_id();
        if (!$uid) 
        {
            $this->error('请登录！');
        }
        $email = M('User')->getFieldById($uid, 'email');
        import('Common.Encrypt', COMMON_PATH, '.php');
        decrypt_email($email);
        if (IS_AJAX) 
        {
            $this->success(array('email' => $email));
        } else
        {
            echo $email;
        }
    }
    
    public function bindEmail() 
    {
        $email = I('email');
        import('Common.Encrypt', COMMON_PATH, '.php');
        $email = encrypt_email($email);
        M('User')->where('id=%d', use_id())->setField('email', $email);
    }
    
    /**
     *注销
     */
    public function logout() 
    {
        $token = cookie('token');
        if ($token) 
        {
            delete_token($token);
        }
        cookie(null);
        session('[destroy]');
        session(null);
        $this->redirect('Index/index');
    }
    
    /**
     *404页
     */
    public function _empty() 
    {
        $this->redirect('index');
    }
}

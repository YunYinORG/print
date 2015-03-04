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
 * - register()
 * - forget()
 * - change()
 * - bindPhone()
 * - verifyPhone()
 * - getPhone()
 * - getEmail()
 * - bindEmail()
 * - verifyEmail()
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
            $User       = D('User');
            $data       = $User->where("id=%d", $id)->find();
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
        
        $number   = I('post.student_number', null, C('REGEX_NUMBER'));
        $password = I('post.password');
        
        if ($number && $password) 
        {
            $key      = 'auth_' . $number;
            $times    = S($key);
            if ($times > C('MAX_TRIES')) 
            {
                \Think\Log::record('auth爆破警告：ip:' . get_client_ip() . ',number:' . $number, 'NOTIC', true);
                $this->error('此账号尝试次数过多，已经暂时封禁，请于一小时后重试！（ps:你的行为已被系统记录）', '/', 5);
            } else
            {
                S($key, $times + 1, 3600);
            }
        } else
        {
            $this->error('学号格式错误！');
        }
        
        $User     = M('User');
        $info     = $User->where('student_number="%s"', $number)->field('id,password,status')->find();
        if ($info) 
        {
            
            //尝试登录
            if (strlen($info['password']) == 13) 
            {
                
                //更新密码加密方式，就加密过渡
                if ($info['password'] == encode_old($password, $number)) 
                {
                    $password = md5($password);
                    $password = encode($password, $number);
                    $info['password']          = $password;
                    $User->save($info);
                } else
                {
                    $this->error('密码或者账号错误！');
                }
            } else
            {
                $password = md5($password);
                $password = encode($password, $number);
            }
            
            if ($info['password'] != $password) 
            {
                $this->error('密码验证错误！');
            } elseif ($info['status'] < 1) 
            {
                $this->error('此账号已被封禁！');
            } else
            {
                session('use_id', $info['id']);
                $token = update_token($info['id'], C('STUDENT'));
                cookie('token', $token, 3600 * 24 * 30);
                S($key, null);
                $this->redirect('/File/index');
            }
        } else
        {
            
            //尚未注册，先判断学校导入学校验证文件
            $number = I('student_number', null, C('REGEX_NUMBER_NKU'));
            if (preg_match(C('REGEX_NUMBER_NKU'), $number)) 
            {
                import(C('VERIFY_WAY'), COMMON_PATH, '.php');
                $data['sch_id'] = 1;
            } elseif (preg_match(C('REGEX_NUMBER_TJU'), $number)) 
            {
                $this->error('北洋大学暂未开放注册！');
                $data['sch_id'] =2;
            } else
            {
                $this->error('你输入的学号' . $number . ',不是南开或者天大在读学生的的学号，如果你是南开或者天大的在读学生请联系我们！');
            }
            
            if ($name = getName($number, $password)) 
            {
                $data['name']      = $name;
                $data['student_number']      = $number;
                $data['password']      = $password;
                session('authData', $data);
                $this->data = $data;
                $this->display('notice');
            } else
            {
                $school=M('school')->cache('school',36000)->getFieldById($data['sch_id'],'name');
                $this->error($data['sch_id'] . '学校账号实名认证失败！');
            }
        }
    }
    
    /**
     *首次注册,设置密码
     *@param ignore int 是否使用认证的默认密码
     *@param password 密码
     */
    public function register() 
    {
        $data       = session('authData');
        if ($data) 
        {
            $is_ignore  = I('post.ignore', null, 'int');
            $password   = I('post.password');
            if ($is_ignore == 1) 
            {
                
                //使用默认密码
                $data['password']            = encode(md5($data['password']), $data['student_number']);
            } elseif ($is_ignore === 0 && $password && $password == I('post.re_password')) 
            {
                
                //重设密码
                $data['password']            = encode(md5($password), $data['student_number']);
            } else
            {
                
                //显示提示信息
                //设置密码
                $this->data = $data;
                $this->display('notice');
                return;
            }
            
            if ($uid = M('User')->add($data)) 
            {
                
                //注册成功！
                session('authData', null);
                session('use_id', $uid);
                $token = update_token($uid, C('STUDENT'));
                cookie('token', $token, 3600 * 24 * 30);
                $this->redirect('index', null, 0, '注册成功！');
            } else
            {
                
                //注册失败
                \Think\Log::record('注册失败：ip:' . get_client_ip() . ',number:' . $data['student_number']);
                $this->error('注册失败');
            }
        } else
        {
            $this->redirect('/');
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
                        
                        if (false !== M('User')->where('student_number=' . $student_number)->setField('password', encode(md5($password), $student_number))) 
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
            if ($user['password'] == encode(md5($deprecated_password), $user['student_number'])) 
            {
                if ($password == $re_password) 
                {
                    M('User')->where('id=%d', $uid)->setField('password', encode(md5($password), $user['student_number']));
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
     *@param phone 手机号
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
                $this->success('发送成功');
            } elseif ($result === 0) 
            {
                $this->error('发送次数过多');
            } else
            {
                $this->error('发送失败');
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
                session('bind_phone',null);
                $true_phone=$phone;
                import('Common.Encrypt', COMMON_PATH, '.php');
                $phone = encrypt_phone($phone, $sid, $uid);
                if(M('User')->where('id=%d', $uid)->setField('phone', $phone))
                {
                    $this->success($true_phone.'绑定成功！');

                }else{
                    $this->error('name');($phone.'绑定失败！');
                }
            } elseif ($result === false) 
            {
                $this->error('验证失败，请重试！');
            } else
            {
                $this->error('验证信息过期！', '/User/bindPhone');
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
    
    /**
     *bindEmail()
     *绑定邮箱，给邮箱发送验证邮件
     *@param email 邮件
     */
    public function bindEmail() 
    {
        $uid   = use_id();
        $email = I('email', false, C('REGEX_EMAIL'));
        if ($email && $uid) 
        {
            if (get_user_by_email($email)) 
            {
                $this->error('此邮箱已经绑定过账号！');
            } else
            {
                $data['use_id']      = $uid;
                $data['type']      = 1;
                $Code = M('code');
                $Code->where($data)->delete();
                
                $data['code']     = random(32);
                $data['content']     = $email;
                $cid = $Code->add($data);
                if ($cid) 
                {
                    $url = U('User/verifyEmail', 'id=' . $cid . '&code=' . $data['code'], '', true);
                    if (send_mail($email, $url, 1)) 
                    {
                        $this->success('验证邮件已发送到' . $email.'请及时到邮箱验证查收');
                    } else
                    {
                        $this->error('验证邮件发送失败！');
                    }
                } else
                {
                    $this->error('信息生成失败！');
                }
            }
        } else
        {
            $this->error('信息不足！');
        }
    }
    
    /**
     *veifyEmail()
     *绑定邮箱，给邮箱发送验证邮件
     *@param id 邮件验证id
     *@param code 验证码
     */
    public function verifyEmail() 
    {
        $id   = I('id', false, 'int');
        $code = I('code', false, '/^\w{32}/');
        if ($id && $code) 
        {
            $map['id']      = $id;
            $map['code']      = $code;
            $map['type']      = 1;
            $info = M('Code')->where($map)->Field('use_id,content')->find();
            if ($info) 
            {
                M('Code')->where('id=%d', $id)->delete();
                $email = $info['content'];
                
                import('Common.Encrypt', COMMON_PATH, '.php');
                if (get_user_by_email($email)) 
                {
                    $this->error('此邮箱已经绑定过账号！');
                } elseif (M('User')->where('id=%d', $info['use_id'])->setField('email', encrypt_email($email))) 
                {
                    $this->success('绑定成功！', '/');
                } else
                {
                    $this->error('邮箱绑定失败！');
                }
            } else
            {
                $this->error('验证信息已不存在！');
            }
        } else
        {
            $this->error('信息不完整');
        }
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

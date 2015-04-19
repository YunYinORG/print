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
 * - register()
 * - signup()
 * - bindPhone()
 * - verifyPhone()
 * - getPhone()
 * - getEmail()
 * - bindEmail()
 * - verifyEmail()
 * - forget()
 * - findPwd()
 * - checkSMSCode()
 * - checkEmailCode()
 * - resetPwd()
 * - changePwd()
 * - _empty()
 * Classes list:
 * - UserController extends Controller
 */
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller
{
    /**
     * 用户信息页
     * @method index
     * @author NewFuture[newfuture@yunyin.org]
     */
    public function index() 
    {
        $id         = use_id();
        if ($id) 
        {
            $data       = D('User')->field('student_number,sch_id,name,phone,email,status')->getById($id);
            $this->data = $data;
            $this->display();
        } 
        else
        {
            $this->error(L('UNLOGIN'), '/Index/index');
        }
    }
    /**
     * 注册页
     * @method register
     */
    public function register() 
    {
        if (session('authData')) 
        {
            $this->display();
        } 
        else
        {
            $this->error(L('REG_INVALID'), '/');
        }
    }
    /**
     * 首次注册,设置置密码
     * @method signup
     * @param ignore   int      是否使用认证的默认密码
     * @param password 密码
     */
    public function signup() 
    {
        $reg_data = session('authData');
        if (!$reg_data) 
        {
            $this->error(L('REG_INVALID'));
        }
        /*重设密码或者使用密码*/
        if (I('post.ignore')) 
        {
            //使用默认密码
            $reg_data['password']          = encode($reg_data['password'], $reg_data['student_number']);
        } 
        else
        {
            /*获取设置的秘密并重置*/
            $password = I('post.password');
            if (!$password) 
            {
                $this->error(L('PASSWORD_EMPTY'));
            }
            if (!I('isMD5')) 
            {
                $password = md5($password);
            }
            
            $reg_data['password']          = encode(md5($password), $reg_data['student_number']);
        }
        
        if ($uid      = M('User')->add($reg_data)) 
        {
            //注册成功！
            session('authData', null);
            session('use_id', $uid);
            $token = update_token($uid, C('STUDENT'));
            cookie('token', $token, 3600 * 24 * 30);
            $this->success(L('REG_SUCC'), 'index');
        } 
        else
        {
            //注册失败
            \Think\Log::record('注册失败：ip:' . get_client_ip() . ',number:' . $reg_data['student_number']);
            $this->error(L('REG_ERROR'));
        }
    }
    /**
     * 绑定手机号
     * 给手机号发送验证码
     * @param phone 手机号
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
        } 
        else
        {
            $result = send_sms_code($phone, 'bind');
            if (true == $result) 
            {
                session('bind_phone', $phone);
                $this->success('发送成功');
            } 
            elseif (0 === $result) 
            {
                $this->error('发送次数过多');
            } 
            else
            {
                $this->error('发送失败');
            }
        }
    }
    /**
     * 验证短信并绑定手机号
     * @param code 验证码
     */
    public function verifyPhone() 
    {
        $phone = session('bind_phone');
        if (!$phone) 
        {
            $this->error('手机号不存在！');
        } 
        elseif (get_user_by_phone($phone)) 
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
                session('bind_phone', null);
                $true_phone = $phone;
                import('Common.Encrypt', COMMON_PATH, '.php');
                $phone = encrypt_phone($phone, $sid, $uid);
                if (M('User')->where('id=%d', $uid)->setField('phone', $phone)) 
                {
                    $this->success($true_phone . '绑定成功！');
                } 
                else
                {
                    $this->error('name');
                    ($phone . '绑定失败！');
                }
            } 
            elseif (false === $result) 
            {
                $this->error('验证失败，请重试！');
            } 
            else
            {
                $this->error('验证信息过期！', '/User/bindPhone');
            }
        } 
        else
        {
            $this->error('验证信息错误');
        }
    }
    /**
     * getPhone()
     * 查看手机号码
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
        } 
        else
        {
            echo $phone;
        }
    }
    /**
     * getEmail()
     * 查看邮箱
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
        } 
        else
        {
            echo $email;
        }
    }
    /**
     * bindEmail()
     * 绑定邮箱，给邮箱发送验证邮件
     * @param email 邮件
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
            } 
            else
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
                    if (send_mail($email, L('MAIL_BIND', array('mail' => $email, 'link' => $url)), C('MAIL_VERIFY'))) 
                    {
                        $this->success('验证邮件已发送到' . $email . '请及时到邮箱验证查收!注意垃圾箱哦o(^▽^)o');
                    } 
                    else
                    {
                        $this->error('验证邮件发送失败！');
                    }
                } 
                else
                {
                    $this->error('信息生成失败！');
                }
            }
        } 
        else
        {
            $this->error('信息不足！');
        }
    }
    /**
     * veifyEmail()
     * 绑定邮箱，给邮箱发送验证邮件
     * @param id   邮件验证id
     * @param code 验证码
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
                } 
                else
                {
                    $User = M('User');
                    $user = $User->field('name,email')->getById($info['use_id']);
                    if ($User->where('id=%d', $info['use_id'])->setField('email', encrypt_email($email))) 
                    {
                        //首次绑定邮件
                        if (!$user['email']) 
                        {
                            send_mail($email, L('MAIL_FIRST', array('name' => $user['name'])), C('MAIL_NOTIFY'));
                        }
                        $this->success('绑定成功！', '/');
                    } 
                    else
                    {
                        $this->error('邮箱绑定失败！');
                    }
                }
            } 
            else
            {
                $this->error('验证信息已不存在！');
            }
        } 
        else
        {
            $this->error('信息不完整');
        }
    }
    /**
     * 找回密码
     * @method forget
     */
    public function forget() 
    {
        if (use_id()) 
        {
            $this->redirect('index');
        } 
        else
        {
        	$this->baseurl=null;
        	if(C('HTTPS_ON'))
        	{
        		$this->baseurl=C('SAFE_URL');
        	}
            $this->display();
        }
    }
    /**
     * 找回密码
     * @param way    找回方式
     * @param number 学号
     * @param phone  手机号
     * @param email  邮箱
     */
    public function findPwd() 
    {
        $number = I('number', false, C('REGEX_NUMBER'));
        if (!$number) 
        {
            $this->error('学号无效！');
        }
        switch (I('way')) 
        {
            case 'phone':
                $phone = I('post.phone', false, C('REGEX_PHONE'));
                if (!$phone) 
                {
                    $this->error('手机号无效！');
                }
                $user = M('User')->Field('id,phone')->getByStudentNumber($number);
                if (!empty($user['phone'])) 
                {
                    import('Common.Encrypt', COMMON_PATH, '.php');
                    decrypt_phone($user['phone'], $number, $user['id']);
                    if ($phone != $user['phone']) 
                    {
                        $this->error('学号与手机号不匹配！');
                    }
                } 
                else
                {
                    $this->error('学号未注册或未绑定手机！');
                }
                $result = send_sms_code($phone, 'findPwd'); //发送短信
                if (true == $result) 
                {
                    session('find_pwd_number', $number);
                    session('find_pwd_phone', $phone);
                    $this->success('发送成功');
                } 
                elseif (0 === $result) 
                {
                    $this->error('发送次数过多');
                } 
                else
                {
                    $this->error('发送失败');
                }
            break;
            case 'email':
                $email = I('post.email', false, C('REGEX_EMAIL'));
                if (!$email) 
                {
                    $this->error('邮箱地址无效！');
                }
                $user = M('User')->Field('id,email')->getByStudentNumber($number);
                if (!empty($user['email'])) 
                {
                    import('Common.Encrypt', COMMON_PATH, '.php');
                    decrypt_email($user['email']);
                    if ($email != $user['email']) 
                    {
                        $this->error('学号与邮箱不匹配！');
                    }
                } 
                else
                {
                    $this->error('学号未注册或未绑定邮箱！');
                }
                $data['use_id']      = $user['id'];
                $data['type']      = 2; //密码找回类型为2
                $Code = M('code');
                $Code->where($data)->delete();
                $data['code']     = random(32);
                $data['content']     = $number;
                $cid = $Code->add($data);
                if ($cid) 
                {
                    $url = U('User/checkEmailCode', 'id=' . $cid . '&code=' . $data['code'], '', true);
                    if (send_mail($email, L('MAIL_FINDPWD', array('link' => $url)), C('MAIL_VERIFY'))) 
                    {
                        $this->success('验证邮件已发送到' . $email . '请及时到邮箱查收!注意垃圾箱哦o(^▽^)o', '/', 5);
                    } 
                    else
                    {
                        $this->error('验证邮件发送失败！');
                    }
                } 
                else
                {
                    $this->error('信息生成失败！');
                }
            break;
            default:
                $this->error('类型未知！');
        }
    }
    /**
     * @param code
     */
    public function checkSMSCode() 
    {
        $code  = I('post.code', false, '/^\d{6}$/');
        $phone = session('find_pwd_phone');
        if (check_sms_code($phone, $code, 'findPwd')) 
        {
            session('find_pwd_phone', null);
            session('reset_pwd_flag', 1);
            $this->success('验证成功！', '/User/resetPwd');
        }
    }
    /**
     * @param id
     * @param code
     */
    public function checkEmailCode() 
    {
        $id   = I('id', false, 'int');
        $code = I('code', false, '/^\w{32}/');
        if ($id && $code) 
        {
            $map['id']      = $id;
            $map['code']      = $code;
            $map['type']      = 2; //密码找回类型为2
            if ($info = M('Code')->where($map)->Field('use_id,content')->find()) 
            {
                M('Code')->where('id=%d', $id)->delete();
                session('find_pwd_number', $info['content']);
                session('reset_pwd_flag', 2);
                $this->display('resetPwd');
            } 
            else
            {
                $this->error('验证信息已失效,请重新获取！', '/User/forget');
            }
        } 
        else
        {
            $this->error('信息不完整！', '/User/forget');
        }
    }
    /**
     * 重置密码
     * @method resetPwd
     * @param password
     * @param isMD5
     */
    public function resetPwd() 
    {
        
        $number = session('find_pwd_number');
        if (!$number) 
        {
            $this->redirect('/User/index');
        }
        
        $password = I('post.password');
        if (!$password) 
        {
            $this->display();
            return;
        }
        
        if (!I('post.isMD5')) 
        {
            //未md5的先md5
            $password = md5($password);
        }
        
        if (false === M('User')->where('student_number=' . $number)->setField('password', encode($password, $number))) 
        {
            $this->error(L('PASSWORD_RESET_ERROR'));
        } 
        else
        {
            session(null);
            $this->success(L('PASSWORD_RESET_SUCC'), '/Index/index');
        }
    }
    /**
     * 修改密码
     */
    public function changePwd() 
    {
        $uid                 = use_id(U('Index/index'));
        $user                = M('User')->field('student_number,password')->getById($uid);
        $deprecated_password = I('post.deprecated_password');
        $password            = I('post.password');
        if ($user && $deprecated_password && $password) 
        {
            $isMD5               = I('isMD5');
            if (!$isMD5) 
            {
                $deprecated_password = md5($deprecated_password);
                $password            = md5($password);
            }
            if (encode($deprecated_password, $user['student_number']) == $user['password']) 
            {
                M('User')->where('id=%d', $uid)->setField('password', encode($password, $user['student_number']));
                $this->success('密码修改成功重新登陆！', 'logout');
            } 
            else
            {
                $this->error('原密码错误');
            }
        }
        $this->error('验证失败！');
    }
    /**
     * 404页
     */
    public function _empty() 
    {
        $this->redirect('index');
    }
}

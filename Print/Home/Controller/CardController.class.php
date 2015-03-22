<?php

// ===================================================================
// | FileName:      CardController.class.php
// ===================================================================
// | Discription：   CardController 一卡通找回
//      <命名规范：>
// ===================================================================
// +------------------------------------------------------------------
// | 云印南开
// +------------------------------------------------------------------
// | Copyright (c) 2014-2015 云印南开团队 All rights reserved.
// +------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +------------------------------------------------------------------
/**
 * Class and Function List:
 * Function list:
 * - index()
 * - help()
 * - find()
 * - off()
 * - result()
 * - _empty()
 * Classes list:
 * - CardController extends Controller
 */
namespace Home\Controller;
use Think\Controller;

class CardController extends Controller
{
    
    /**
     *应用首页
     */
    public function index() 
    {
        $uid  = use_id(U('Index/index'));
        if ($uid) 
        {
            $user = M('User')->cache(true)->field('phone,status')->getById($uid);
            $card = M('Card')->getById($uid);
            if ($user['status'] < 0) 
            {
                $this->error('已封号');
            } elseif (!$user['phone']) 
            {
                $this->error('使用此功能必须绑定手机号！', '/User/index');
            } elseif (!$card) 
            {
                
                //数据库中不存在，咋加入数据库
                $card['id'] = $uid;
                M('Card')->add($card);
                $this->display();
            } else
            {
                $this->new = M('Cardlog')->where("(find_id=$uid OR lost_id=$uid) AND status is null")->count();
                $this->display();
            }
        }
    }

    /**
     *help
     *帮助说明
     */
    public function help() 
    {
        $this->display();
    }


    /**
     *log
     *记录页
     */    
    public function log()
    {
        $id=use_id('User/index');
        $Cardlog=D('CardlogView');        
        $this->lost=$Cardlog->where("lost_id=$id")->field('id,find_id,find_name,find_number,time,status')->order('id desc')->select();
        $this->find=$Cardlog->where("find_id=$id")->field('id,lost_id,lost_name,lost_number,time,status')->order('id desc')->select();
        $this->display();
    }
    
    /**
     *find()
     *核实信息
     *@param number 学号
     *@param name 姓名
     */
    public function find() 
    {
        $uid       = use_id();
        $number    = I('number', false, C('REGEX_NUMBER'));
        $name      = I('name', false, 'trim');
        
        $User      = M('User');
        $Card      = M('Card');
        $send_user = $uid ? $User->field('id,sch_id,student_number,name,phone,email')->getById($uid) : false;
        if (!$send_user) 
        {
            $this->error('请登录！', '/');
        } elseif (!$send_user['phone']) 
        {
            $this->error('尚未绑定手机', '/User/index');
        } elseif ($send_user['student_number'] == $number) 
        {
            $this->error('不要用自己的做实验哦！');
        } elseif ($Card->cache(true)->getFieldById($uid, 'blocked')) 
        {
            $this->error('由于恶意使用,您的此功能已被禁用', '/Card/help');
        } elseif (!$name && !$number) 
        {
            $this->error('信息不足');
        } else
        {
            $recv_user = $User->field('id,name,student_number,phone,email')->cache(true)->getByStudentNumber($number);
            $recv_off  = $Card->cache(true)->getFieldById($recv_user['id'], 'off');
            if (!$recv_user) 
            {
                $this->error($number . '尚未加入此平台', '/Card/help');
            } elseif ($name !== $recv_user['name']) 
            {
                $this->error('失主信息核对失败！');
            } elseif ($recv_off) 
            {
                $this->error('对方关闭此功能', '/Card/help');
            } elseif (!$recv_user['phone'] && !$recv_user['email']) 
            {
                $this->error($name . '尚未绑定个人信息', '/Card/help');
            } else
            {
                
                //验证成功 通知并记录
                $msg     = '';
                $success = false;
                import('Common.Encrypt', COMMON_PATH, '.php');
                $send_phone = decrypt_phone($send_user['phone'], $send_user['student_number'], $send_user['id']);
				$info = array("send_phone"=>$send_phone,"send_name"=>$send_user['name'],"recv_name"=>$recv_user['name']);
                if ($recv_user['phone']) 
                {
                    $recv_phone = decrypt_phone($recv_user['phone'], $recv_user['student_number'], $recv_user['id']);
                    $sms_result = send_sms($recv_phone, $info, 3);
                    $success|= $sms_result;
                    if ($sms_result) 
                    {
                        $msg        = '短信已发送!\n';
                    } else
                    {
                        $msg        = '短信发送失败!\n';
                    }
                }
                if ($recv_user['email']) 
                {
                    $recv_email = decrypt_email($recv_user['email']);
                    $content    = '亲爱的<i>' . $recv_user['name'] . '</i>同学：<br/>';
                    $school=M('school')->cache(true)->getFieldById($send_user['sch_id'],'name');
                    $content.= $school . '的<i>' . $send_user['name'] . '</i>同学说TA捡到了你的学子卡<br/>';
                    $content.= "TA的手机号:<b> <a herf='tel:$send_phone'>$send_phone</a></b>;<br/>";
                    if ($send_user['email']) 
                    {
                        $send_email = decrypt_email($send_user['email']);
                        $content.= "TA的邮箱: <b><a href='mailto:$send_email'>$send_email</a></b>;<br/>";
                    }
					$content .= '请尽快与其联系并认领吧。^_^';
                    $mail_result = send_mail($recv_email, $content, 3);
                    $success|= $mail_result;
                    if ($mail_result) 
                    {
                        $msg.= '邮件已发送!\n';
                    } else
                    {
                        $msg.= '邮件发送失败!';
                    }
                }
                if (!$success) 
                {
                    $this->error('消息发送失败！请重试或者交由第三方平台！');
                }
                
                if ($recv_off === null) 
                {
                    
                    //该同学不在card记录之中
                    $Card->add(array('id' => $recv_user['id']));
                }
                $log['find_id'] = $send_user['id'];
                $log['lost_id'] = $recv_user['id'];
                if (!M('Cardlog')->add($log)) 
                {
                    $this->error('记录失败!!!\n' . $msg);
                } else
                {
                    $this->success($msg);
                }
            }
        }
    }
    
    /**
     *off()
     *设关闭
     *@param $value 权限值
     */
    public function off() 
    {
        $id    = use_id();
        $value = I('value', null, 'int');
        if (!$id || !$value) 
        {
            $this->error('请登录！');
        } elseif (M('Card')->where('id=', $id)->setField('off', $value) !== false) 
        {
            $this->error('修改失败!');
        } else
        {
            $this->success('修改成功！');
        }
    }
    
    /**
     *result()
     *结果
     *@param $id 记录id
     *@param $status 结果状态举报-1;感谢1;忽略0;
     */
    public function result() 
    {
        $uid    = use_id();
        $id     = I('id', null, 'int');
        $status = I('status', null, 'int');
        if (!$uid) 
        {
            $this->error('请登录！');
        } elseif (!$id) 
        {
            $this->error('信息不足');
        } elseif (!in_array($status, array(-1, 0, 1))) 
        {
            $this->error('参数不对！');
        } else
        {
            $log['id']        = $id;
            $log['lost_id']        = $uid;
            $Log    = M('Cardlog');
            if ($Log->where($log)->setField('status', $status) !== false) 
            {
                if ($status < 0) 
                {
                    $findId = $Log->getFieldById($id);
                    if ($Log->where("find_id=$findId AND status<0")->count() >= 2) 
                    {
                        
                        //恶意操作进行屏蔽
                        M('Card')->where("id=$findId")->setField('blocked', 1);
                    }
                }
                $this->success('操作成功');
            } else
            {
                $this->error('操作失败');
            }
        }
    }
    
    /**
     *404页
     */
    public function _empty() 
    {
        $this->redirect('index');
    }
}

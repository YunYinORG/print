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

class CardController extends Controller {

	/**
	 * 应用首页
	 */
	public function index()
	{
		$uid = use_id(U('Index/index'));
		if ($uid)
		{
			$user = M('User')->field('phone,status')->getById($uid);
			$card = M('Card')->getById($uid);
			if ($user['status'] < 0)
			{
				$this->error('已封号');
			}
			elseif ( ! $user['phone'])
			{
				$this->error('使用此功能必须绑定手机号！', '/User/index');
			}
			elseif ( ! $card)
			{

				/*数据库中不存在，加入数据库*/
				$card['id'] = $uid;
				M('Card')->add($card);
				$this->display();
			}
			else
			{
				$this->new = M('Cardlog')->where("(find_id=$uid OR lost_id=$uid) AND status is null")->count();
				$this->display();
			}
		}
	}

	/**
	 * log
	 * 记录页
	 */
	public function log()
	{
		$id = use_id('User/index');
		$Cardlog = D('CardlogView');
		$this->lost = $Cardlog->where("lost_id=$id")->field('id,find_id,find_name,find_number,time,status')->order('id desc')->select();
		$this->find = $Cardlog->where("find_id=$id")->field('id,lost_id,lost_name,lost_number,time,status')->order('id desc')->select();
		$this->display();
	}

	/**
	 * find()
	 * 核实信息
	 * @param number 学号
	 * @param name   姓名
	 */
	public function find()
	{
		$uid       = use_id();
		$number    = I('number', false, C('REGEX_NUMBER'));
		$name      = I('name', false, 'trim');
		$User      = M('User');
		$Card      = M('Card');
		
		$send_user = $uid ? $User->field('id,sch_id,student_number,name,phone,email')->getById($uid) : false;
		if ( ! $send_user)//判断登录信息
		{
			$this->error('请登录！', '/');
		}
		elseif ( ! $send_user['phone'])//未绑定手机
		{
			$this->error('尚未绑定手机', '/User/index');
		}
		elseif ($send_user['student_number'] == $number)//自己给自己发
		{
			$this->error('不要用自己的做实验哦！');
		}
		elseif ($Card->cache(true)->getFieldById($uid, 'blocked'))//已经被屏蔽
		{
			$this->error('由于恶意使用,您的此功能已被禁用', '/Card/help');
		}
		elseif ( ! $name &&  ! $number)//找卡人信息不足	
		{
			$this->error('信息不足');
		}
		else
		{
			/*尝试 验证 匹配 通知*/

			$School =M('School');
			$recv_user = $User->field('id,name,student_number AS number,sch_id,phone,email')->getByStudentNumber($number);
			if(!$recv_user)//判断是否存加入平台
			{
				/* 判断学校*/
				if(preg_match(C('REGEX_NUMBER_NKU'), $number))//南开
				{
					$this->_saveReciever($name, $number, 1,false);
				}elseif (preg_match(C('REGEX_NUMBER_TJU'), $number)) {//天大
					$this->_saveReciever($name, $number, 2,false);
				}else{//其他
					$this->error('对不起，目前平台仅对南开大学和天津大学在校生开放，其他需求或者学校请联系我们！');
				}
				$this->error($name."($number)尚未加入，你可以在此广播到社交网络", '/Card/broadcast');		
			}elseif ($name !== $recv_user['name'])//验证姓名
			{
				$this->error('失主信息核对失败！');
			}
			elseif($recv_off  = $Card->cache(120)->getFieldById($recv_user['id'], 'off'))//接受者是否关闭此功能
			{
				$this->error('对方关闭了此功能,不希望你打扰TA，我们爱莫能助╮(╯-╰)╭');
			}elseif ( !($recv_user['phone']||$recv_user['email']))//判断邮箱和手机是否存在
			{
				$this->_saveReciever($recv_user['name'], $recv_user['number'], $recv_user['sch_id'],$recv_user['id']);
				$this->error($name."($number)尚未绑定联系方式，你可以在此广播到社交网络", '/Card/broadcast');
			}
			{
				/*验证成功 ，手机或者邮箱存在 通知并记录*/

				$msg     = '';//提示消息
				$success = false;
				import('Common.Encrypt', COMMON_PATH, '.php');
				$send_phone = decrypt_phone($send_user['phone'], $send_user['student_number'], $send_user['id']);
			
				if ($recv_user['phone'])//手机存在
				{
					/*发送短信通知*/
					$recv_phone = decrypt_phone($recv_user['phone'], $recv_user['number'], $recv_user['id']);
					$SMS = new \Vendor\Sms();
					$info = array('send_phone' => $send_phone, 'send_name' => $send_user['name'], 'recv_name' => $recv_user['name']);
					$sms_result = $SMS->findCard($recv_phone, $info);
				
					$success |= $sms_result;
					if ($sms_result)
					{
						$msg = '短信已发送!<br/>';
					}
					else
					{
						$msg = '短信发送失败!<br/>';
					}
				}

				if ($recv_user['email'])//检查邮箱是否存在
				{
					/*发送邮件通知*/

					$recv_email = decrypt_email($recv_user['email']);
					$send_user['school'] = $School->cache(true)->getFieldById($send_user['sch_id'], 'name');
					if ($send_user['email'])
					{
						$send_user['email'] = decrypt_email($send_user['email']);
					}

					/*拼装邮件*/
					$mail_msg=L('MAIL_CARD', array('name' => $recv_user['name'], 'school' => $send_user['school'], 'sender_name' => $send_user['name'], 'phone' => $send_user['phone'], 'email' => $send_user['email']));
					$mail_result = send_mail($recv_user,$mail_msg , C('MAIL_NOTIFY'));
					$success |= $mail_result;
					if ($mail_result)
					{
						$msg .= '邮件已发送!<br/>';
					}
					else
					{
						$msg .= '邮件发送失败!';
					}
				}

				if ( ! $success)//判断发送结果
				{				
					$this->_saveReciever($recv_user['name'], $recv_user['number'], $recv_user['sch_id'],$recv_user['id']);
					$this->error('消息发送失败！请重试或者交由第三方平台！','/Card/broadcast');
				}else{

				/*记录招领信息*/
		
				if ($recv_off === null) 
				{
					//该同学不在card记录之中,则先创建
					$Card->add(array('id' => $recv_user['id']));
				}

				$log=array('find_id'=>$send_user['id'],'lost_id'=> $recv_user['id']);
				if ( ! M('Cardlog')->add($log))
				{
					$this->error('记录失败!!!<br/>'.$msg);
				}
				else
				{
					$this->success($msg);
				}

				}
				
			}
		}
	}

	/**
	 * off()
	 * 设关闭
	 * @param $value 权限值
	 */
	public function off()
	{
		$id = use_id();
		$value = I('value', null, 'int');
		if ( ! $id ||  ! $value)
		{
			$this->error('请登录！');
		}
		elseif (M('Card')->where('id=', $id)->setField('off', $value) !== false)
		{
			$this->error('修改失败!');
		}
		else
		{
			$this->success('修改成功！');
		}
	}

	/**
	 * result()
	 * 结果
	 * @param $id     记录id
	 * @param $status 结果状态举报-1;感谢1;忽略0;
	 */
	public function result()
	{
		$uid    = use_id();
		$id     = I('id', null, 'int');
		$status = I('status', null, 'int');
		if ( ! $uid)
		{
			$this->error('请登录！');
		}
		elseif ( ! $id)
		{
			$this->error('信息不足');
		}
		elseif ( ! in_array($status, array(-1, 0, 1)))
		{
			$this->error('参数不对！');
		}
		else
		{
			$log['id'] = $id;
			$log['lost_id'] = $uid;
			$Log = M('Cardlog');
			if ($Log->where($log)->setField('status', $status) !== false)
			{
				if ($status < 0)
				{
					$findId = $Log->getFieldById($id,'find_id');
					if ($Log->where("find_id=$findId AND status<0")->count() >= 2)
					{

						/*恶意操作进行屏蔽*/
						M('Card')->where("id=$findId")->setField('blocked', 1);
					}
				}
				$this->success('操作成功');
			}
			else
			{
				$this->error('操作失败');
			}
		}
	}

	/**
	 * showPhone()
	 * 显示拾得者手机
	 * 若已感谢、举报或忽略，显示按钮失效
	 */
	public function showPhone()
	{
		$uid = use_id();
		$id  = I('id', null, 'int');
		$log['id'] = $id;
		$log['lost_id'] = $uid;
		if ($find_id = M('Cardlog')->where($log)->getField('find_id'))
		{
			$phone = get_phone_by_id($find_id);
			if (IS_AJAX)
			{
				$this->success(array('phone' => $phone));
			}
			else
			{
				echo $phone;
			}
		}
	}

	/**
	 * 广播显示页
	 * @method broadcast
	 * @return [type]    [description]
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	public function broadcast()
	{
		$uid = use_id();
		if ( ! $uid)//未登录
		{
			$this->error('请登录！', '/');
		}

		$reciever=session('reciever');
		if ( !$reciever)//没有传递session信息
		{
			$this->error('此页仅供招领广播使用，请填写失主信息！', '/Card');
		}
		
		/*获取发送者和接收者信息*/
		$School=M('School');
		$finder=M('User')->field('name,sch_id')->getById($uid);
		$msg_info=array(
			'card_number'=>$reciever['number'],
			'card_name'=>$reciever['name'],
			'card_school'=>$School->cache(true)->getFieldById($reciever['sch_id'],'name'),
			'finder_name'=>$finder['name'],
			'finder_school'=>$School->cache(true)->getFieldById($finder['sch_id'],'name'),
			'msg'=>'',
			);

		if($reciever['uid'])//接收者是平台成员
		{
			$this->send_msg = L('CARD_MSG_IN',$msg_info);
		}
		else
		{
			$this->send_msg = L('CARD_MSG_OUT',$msg_info);
		}
		$this->display();
	}

	/**
	 * 失主尚未加入平台,或已加入平台但未绑定信息
	 * 向微博、人人平台发送消息
	 * @param send_msg		要发送的信息
	 * @param add_msg                        附加消息
	 */
	public function send()
	{
		$uid=use_id('/');

		/*判断是否有接收者*/
		$reciever=session('reciever');
		if(!$reciever)
		{
			$this->error('禁止乱发信息！！！');
		}

		/*判断尝试次数*/
		$cache_name = 'send_'.$uid;
		$times      = S($cache_name);
		$User       = M('User');
		if ($times > 5)
		{
			\Think\Log::record('第三方平台发送失败：ip:'.get_client_ip().',find_id'.$find_id);
			$this->error('发送次数过多!', '/Card/log');
		}
		else
		{
			S($cache_name, $times + 1, 3600);
		}

		/*获取拾主和失主的信息*/
		$School=M('School');
		$reciever['school']=$School->cache(true)->getFieldById($reciever['sch_id'],'name');
		$finder=M('User')->field('name,student_number AS number,sch_id')->getById($uid);
		$finder['school']=$School->cache(true)->getFieldById($finder['sch_id'],'name');
		$finder['msg']  = I('add_msg');

		if(!$reciever['uid'])
		{
			$msg= L('CARD_MSG_OUT',array('reciever' =>$reciever ,'finder'=>$finder ));
		}
		else
		{

			/*失主已加入平台但未绑定信息,且未关闭此功能,添加到丢失记录*/		
			M('Card')->add(array('id'=>$reciever['uid']));
			$log=array('find_id'=>$send_user['id'],'lost_id'=> $recv_user['id']);
			M('Cardlog')->add($log);
			$msg= L('CARD_MSG_IN',array('reciever' =>$reciever ,'finder'=>$finder ));
		}
	
		/*post数据到API*/
		$url    = 'https://newfuturepy.sinaapp.com/broadcast';
		$data = array(
			'key' =>C('WEIBO_API_PWD') ,
			'status'=>base64_encode($msg)
			);
		$result = json_decode($this->_post($url,$dat));	
		
		if($result)
		{
			$result_info='人人发送成功'.($result->renren).'条；微博发送'.($result->weibo).'条';
			$this->success($result_info,'/Card/log');
		}else
		{
			$this->error('网路故障，请联系我们');
		}

		// if(IS_AJAX)
		// {
		// 	$this->success($result);
		// }else
		// {
		
		// }
	
		
	}

	/**
	 * 404页
	 */
	public function _empty()
	{
		$this->redirect('index');
	}


	/**
	 * 保存接收者信息
	 * @method _saveReciever
	 * @param  [type]        $name   [description]
	 * @param  [type]        $number [description]
	 * @param  [type]        $sch_id [description]
	 * @return [type]                [description]
	 * @access private
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	private function _saveReciever($name,$number,$sch_id,$uid=false)
	{
		$reciever=array('name'=>$name,'number'=>$number,'sch_id'=>$sch_id,'uid'=>$uid);
		session('reciever',$reciever);
	}

	/**
	 * post 数据
	 * @method _post
	 * @param  [type]  $url  [description]
	 * @param  array   $data [description]
	 * @return [type]        [description]
	 * @access private
	 * @author NewFuture[newfuture@yunyin.org]
	 */
	private function _post($url, $data=array())
	{

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
	}
}

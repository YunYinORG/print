<?php
/**
* Class and Function List:
* Function list:
* Classes list:
* - UserModel extends Model
*/
namespace Home\Model;
use Think\Model;

class UserModel extends Model
{
	//字段
	protected $fields    = array(
		'id', 'student_number', 'password', 'name', 'gender', 'phone', 'email', 'status',
		'_pk'           => 'id',
		'_type'         => array(
	  		'id'            => 'bigint',
	  		'student_number'=> 'char(10)',
	  		'password'		=> 'char(32)', 
	  		'name'          => 'char(8)',
	  		'gender'        => 'char(2)', 
	  		'phone'         => 'char(16)',
	  		'email'         => 'char(64)',
	  		'status'        => 'tinyint',
	  	)
	 );
	
	//自动验证
	protected $_validate = array(
		array('student_number', 'require', 'Require student_number'),
		array('password', 'require', 'Require password'),
		 );
	
	/**
	*查询成功的回调方法
	*自动对邮箱和手机号进行打码处理
    */
    protected function _after_find(&$result,$options) {
    	if (isset($result['email'])&&$result['email'])
    	{
    		$email=$result['email'];
    		$at=strpos($email, '@')
    		$result['mask_email']=substr_replace($email, '***',1,$at-1);
		}else{
			$result['mask_email']=null;
		}
    	
    	if(isset($result['phone'])&&$result['phone'])
    	{
    		$result['mask_phone']=substr_replace($result['phone'], '********',-8);
    	}else{
    		$result['mask_phone']=null;
    	}
    	return true;
    }

}
?>